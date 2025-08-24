<?php

namespace alextuersley\Userlevels\Services;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use App\Models\Route;

class RouteAccessService
{
    protected Authenticatable|null $user = null;

    protected array $ignoreRoutes = [
        'dashboard', 'locale', 'logout', 'livewire',
        'sanctum'
    ];

    public function __construct(Authenticatable|null $user = null)
    {
        $this->user = $user;
    }

    /**
     * Check if the user can access a given route and action
     *
     * @param  string  $route
     * @param  string  $action
     * @return bool
     */
    protected function canAccess(string $route, string $action): bool
    {
        if (is_null($this->user)) {
            return false;
        }

        return $this->user->level->hasRouteAccess($route, $action);
    }

    /**
     * Check if a route and action exists in the routes table
     *
     * @param  string  $route
     * @param  string  $action
     * @return bool
     */
    protected function routeActionExists(string $route, string $action) : bool
    {
        return Route::where('route', $route)->where('action', $action)->select('id')->count() > 0;
    }

     /**
     * Check Access for multiple routes in one query
     *
     * @param array $routes array of routes to check users access for
     * @return array array of routes with the users access permission for the route
     */
    protected function checkAccessForArray(array $routes): array
    {
        $access = [];
        if(!$this->user){
            foreach ($routes as $route){
                $access[$route] = false;
            }
        }
        $whereIn = "";
        foreach ($routes as $route){
            $whereIn .= "'$route',";
        }
        $whereIn = substr_replace($whereIn, "", -1);

            $dbRoutes = DB::table('user_levels')
                ->selectRaw("CONCAT(routes.route,'.', routes.action) as route")
                ->leftJoin('routables', 'routables.routable_id', '=', 'user_levels.id')
                ->leftJoin('routes', 'routables.route_id', '=', 'routes.id')
                ->where('routable_type','=', 'App\\Models\\UserLevels')
                ->where('user_levels.id','=', $this->user->user_level_id)
                ->whereRaw("CONCAT(routes.route,'.', routes.action) IN (".$whereIn.")")->get()->pluck('route')->toArray();
        foreach ($routes as $route){
            $access[$route] = in_array($route, $dbRoutes);
        }
        return $access;
    }

    /**
     * Check if the user has access to a given route name
     *
     * @param  string|null  $routeName Full route name e.g. users.index
     * @param  string|null  $defaultRouteName Default route name to check if no action found e.g. users.show
     * @param  string|null  $customAction Custom action to append to the route action e.g. export
     * @param  string|null  $access Predefined access array to check against instead of the user's access
     * @param  Authenticatable|null  $user User to check access for, if null uses the user set in the service
     * @return bool
     */
    public function checkAccess(?string $routeName = null, ?string $defaultRouteName = null,
                                       ?string $customAction = null, ?string $access = null, ?Authenticatable $user = null) : bool
    {
        if(!$routeName){
            return true;
        }
        if($user === null){
            $user = $this->user;
        }

        if(!$this->user){
            return false;
        }

        $route = explode('.',$routeName);
        $checkRoute = $route[0];
        $checkAction = null;
        if(isset($route[1]))
        {
            unset($route[0]);
            if(! empty($customAction))
            {
                $route[] = $customAction;
            }
            $route = [...$route];
            $indexKey = array_search("index", $route);
            $routeWithoutIndex = null;
            if(is_numeric($indexKey))
            {
                $routeWithoutIndex = [...array_filter($route, function($item){
                    return $item !== "index";
                })];
            }

            $actionToCheck = count($route);
            for($i = $actionToCheck; $i > 0; $i--)
            {
                $checkAction = implode(".", $route);
                if($this->routeActionExists($checkRoute, $checkAction))
                {
                    break;
                }
                else
                {
                    if($indexKey < $i && is_array($routeWithoutIndex))
                    {
                        $checkAction = implode(".", $routeWithoutIndex);
                        if($this->routeActionExists($checkRoute, $checkAction))
                        {
                            break;
                        }
                        else
                        {
                            unset($routeWithoutIndex[$i - 2]);
                        }
                    }
                    $checkAction = null;
                }
                unset($route[$i - 1]);
                $route = [...$route];
            }
            if(! $checkAction && ! empty($defaultRouteName))
            {
                $explodedDefaultRoute = explode(".", $defaultRouteName);
                if(count($explodedDefaultRoute) > 1)
                {
                    unset($explodedDefaultRoute[0]);
                    $checkAction = implode(".", $explodedDefaultRoute);
                }
            }
        }
        if($customAction){
            if($checkAction){
                if(!str_contains($checkAction, $customAction)){
                    $checkAction = $checkAction.'.'.$customAction;
                }
            } else{
                $checkAction = $customAction;
            }
        }
        $result = false;

        if(!$checkAction){
            if(in_array($checkRoute, $this->ignoreRoutes)
            )
            {
                $result = true;
            }
            return $result;
        }
        if($checkRoute == 'livewire')
        {
            return true;
        }
        if(is_null($access))
        {
            $access = $this->user->staff_access;
        }

        if(!empty($access))
        {
            if(isset($access[$checkRoute]) )
            {

                if(str_contains($checkAction, '.')){
                    $actions = explode('.', $checkAction);
                    $result = $access[$checkRoute];
                    foreach ($actions as $key){
                        if(isset($result[$key])){
                            $result = $result[$key];
                        } else{
                            $result = false;
                        }
                        if(is_bool($result)){
                            break;
                        }
                    }
                } else{
                    $result = isset($access[$checkRoute]) && isset($access[$checkRoute][$checkAction]) ? $access[$checkRoute][$checkAction] : false;
                }
                if(is_array($result)){
                    return true;
                } else{
                    return $result;
                }
            } else {
                return false;
            }
        }
        else {
            if($this->user->userLevel->hasRouteAccess($checkRoute, $checkAction))
            {
                $result = true;
            }
        }
        return $result;
    }
}

<?php

namespace alextuersley\Userlevels\Helpers;

use Illuminate\Support\Facades\Route;

class RouteHelper
{

    /**
     * Paths to ignore when seeding routes
     *
     * @return array
     */
    public static function ignorePaths(): array
    {
        return [
           'sanctum/csrf-cookie',
           'storage/{path}',
           'test'
        ];
    }

    /**
     * Get all the routes in the application
     *
     * @param string $type The type of route information to return. Options are 'name', 'method', 'actions', 'middleware', 'path', 'routes'
     * @return array
     */
    public static function getRoutes(string $type = 'path'){
        $routes = Route::getRoutes();
        $ignorePaths = self::ignorePaths();
        $routeList = [];
        foreach($routes as $route){
            switch($type){
                case 'name':
                    if($routeName = $route->getName()){
                        $routeList[] = $routeName;
                    }
                    break;
                case 'method':
                    $routeList[] = $route->getMethods();
                    break;
                case 'actions':
                    $routeList[] = $route->getActionName();
                    break;
                case 'middleware':
                    $routeList[] = $route->gatherMiddleware();
                    break;
                case 'path':
                    $uri = $route->uri();
                    if(in_array($uri, $ignorePaths) || str_contains($uri, '{')) break;
                    $routeList[] = str_starts_with($uri, '/') ? $uri : '/' . $uri;
                    break;
                case 'routes':
                    $uri = $route->uri();
                    if(in_array($uri, $ignorePaths) || str_contains($uri, '{')) break;
                    $routeList[] = str_starts_with($uri, '/') ? config('app.url').$uri : config('app.url').'/' . $uri;
                    break;
            }
        }
        return array_unique(array_filter($routeList));
    }
}

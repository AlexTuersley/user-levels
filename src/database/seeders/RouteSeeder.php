<?php

namespace Database\Seeders;

use App\Helpers\RouteHelper;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Route;

class RouteSeeder extends Seeder
{
    private array $ignoreRoutes = [
        'register',
        'login',
        'login',
        'logout',
        'storage',
        'password',
        'password-confirm',
        'sanctum',
        'vendor',
        '_ignition',
        'horizon',
        'telescope',
        'nova',
        'livewire'
    ];

    /**
     * Pulls all the named routes in the application and seeds them into the routes table
     * while ignoring the routes defined in the $ignoreRoutes array
     */
    public function run(): void
    {
        $routes = RouteHelper::getRoutes('name');
        foreach ($routes as $route) {
            $exploded = explode('.', $route);
            $skip = false;
            if(count($exploded) == 2){
                foreach($this->ignoreRoutes as $ignoreRoute){
                    if(str_contains($route, $ignoreRoute)){
                        $skip = true;
                    }
                }
                if(!$skip){
                    $insertRoute = [
                        'route' => $exploded[0],
                        'action' => $exploded[1]
                    ];
                    $this->command->info('Seeding route: ' . $insertRoute['route'] . ' with action: ' . $insertRoute['action']);
                    Route::updateOrCreate(
                        ['route' => $insertRoute['route'], 'action' => $insertRoute['action']],
                        $insertRoute
                    );        
                }
            }
        }
    }
}

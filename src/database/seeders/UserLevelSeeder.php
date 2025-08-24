<?php

namespace alextuersley\Userlevels\Database\Seeders;


use alextuersley\Userlevels\Models\Route;
use alextuersley\Userlevels\Models\UserLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserLevelSeeder extends Seeder
{
    private array $userLevels = [
        [
            'slug' => 'admin',
            'level_name' => 'Admin',
        ],
    ];

    
    /**
     *  Create Admin User Level with Access to All Routes
     *  
     */
    public function run(): void
    {
        foreach ($this->userLevels as $level) {
            UserLevel::updateOrCreate(
                ['slug' => $level['slug']],
                $level
            );
        }
        $userLevels = UserLevel::all();
        foreach (Route::all() as $route) {
            foreach($userLevels as $userLevel) {
                    $insertArray = [
                        'route_id' => $route->id,
                        'routable_id' => $userLevel->id,
                        'routable_type' => UserLevel::class,
                    ];
                  
                    DB::table('routables')->updateOrInsert(
                        ['route_id', 'routable_id', 'routable_type'],
                        $insertArray
                    );
            }
        }
    }
}

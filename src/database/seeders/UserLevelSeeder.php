<?php

namespace alextuersley\Userlevels\Database\Seeders;


use App\Models\Route;
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
            \App\Models\UserLevel::updateOrCreate(
                ['slug' => $level['slug']],
                $level
            );
        }
        $userLevels = \App\Models\UserLevel::all();
        foreach (Route::all() as $route) {
            foreach($userLevels as $userLevel) {
                    $insertArray = [
                        'route_id' => $route->id,
                        'routable_id' => $userLevel->id,
                        'routable_type' => \App\Models\UserLevel::class,
                    ];
                  
                    DB::table('routables')->updateOrInsert(
                        ['route_id', 'routable_id', 'routable_type'],
                        $insertArray
                    );
            }
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Route extends Model
{
    use HasFactory;

    protected $fillable = ['route', 'action'];

    /**
     * User Levels that have access to this route
     *
     * @return MorphToMany
     */
    public function userLevels(): MorphToMany
    {
        return $this->morphedByMany(UserLevel::class, 'routable');
    }
}

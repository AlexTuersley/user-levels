<?php

namespace alextuersley\Userlevels\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use alextuersley\Userlevels\Traits\Sluggable;

class UserLevel extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = ['level_name', 'label_color'];

    public string $sluggable = 'level_name';

    /**
     * The route key used when the model is bound to a route
     *
     * @return string
     */
    public function getRouteKeyName() : string
    {
        return 'slug';
    }

    /**
     * Users associated with the Level
     *
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_level_id')->withTrashed();
    }

    /**
     * Routes the level has access to
     *
     * @return MorphToMany
     */
    public function routes(): MorphToMany
    {
        return $this->morphToMany(Route::class, 'routable');
    }

    /**
     * Find whether Level has access to the  Route
     *
     * @param  string  $route
     * @param  string  $action
     * @return bool
     */
    public function hasRouteAccess(string $route, string $action) : bool
    {
        return $this->routes()
            ->where('route', $route)
            ->where('action', $action)
            ->exists();
    }
}

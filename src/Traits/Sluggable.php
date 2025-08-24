<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Trait to add sluggable functionality to a model
 */
trait Sluggable
{
    /**
     * When a model is being saved generate a slug based on the title
     */
    protected static function bootSluggable()
    {
        static::saving(function (Model $model) {
            $slug = Str::slug($model[$model->sluggable]);
            $iteration = 0;

            while (
                $existing = $model->newQuery()
                    ->where('slug', ($iteration > 0 ? $slug.'-'.$iteration : $slug))
                    ->where('id', '!=', $model->id)
                    ->first()
            ) {
                preg_match('/-([0-9]+$)/', $existing->slug, $matches);

                if (empty($matches)) {
                    $iteration++;
                } else {
                    if ($iteration === 0) {
                        $iteration++;
                        continue;
                    }

                    $iteration = ((int) $matches[1]) + 1;
                }
            }

            $model->slug = ($iteration === 0 ? $slug : $slug.'-'.($iteration ?? '1'));
        });
    }
}

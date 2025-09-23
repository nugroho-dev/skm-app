<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Optional model properties:
     * protected $slugSource = 'name';
     * protected $slugField  = 'slug';
     */
    protected static function bootHasSlug()
    {
        static::saving(function ($model) {
            $source = property_exists($model, 'slugSource') ? $model->slugSource : 'name';
            $field  = property_exists($model, 'slugField') ? $model->slugField : 'slug';

            if ($model->isDirty($source) || empty($model->{$field})) {
                $base = Str::slug($model->{$source} ?? '');
                $slug = $base ?: Str::uuid();
                $counter = 1;

                $exists = function ($slug) use ($model, $field) {
                    $q = $model->withTrashed()->where($field, $slug);
                    if ($model->exists) {
                        $q->where($model->getKeyName(), '!=', $model->getKey());
                    }
                    return $q->exists();
                };

                while ($exists($slug)) {
                    $slug = $base . '-' . $counter++;
                }

                $model->{$field} = $slug;
            }
        });
    }
}
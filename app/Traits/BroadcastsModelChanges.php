<?php

namespace App\Traits;

use App\Events\ModelUpdated;

trait BroadcastsModelChanges
{
    protected static function bootBroadcastsModelChanges()
    {
        static::created(function ($model) {
            event(new ModelUpdated(class_basename($model), 'created', $model->id));
        });

        static::updated(function ($model) {
            event(new ModelUpdated(class_basename($model), 'updated', $model->id));
        });

        static::deleted(function ($model) {
            event(new ModelUpdated(class_basename($model), 'deleted', $model->id));
        });
    }
}

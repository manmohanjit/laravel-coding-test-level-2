<?php


namespace App\Models\Uuid;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\AssignOp\Mod;

trait HasUuid
{
    /**
     * Boot method to disable incrementing PK, and assign
     * UUID to PK only if not already set
     *
     * @returns void
     */
    public static function bootHasUuid() : void
    {
        static::creating(function (Model $model) {
            if (!$model->{ $model->getKeyName() }) {
                $model->{ $model->getKeyName() } = Str::orderedUuid()->toString();
            }
        });
    }

}

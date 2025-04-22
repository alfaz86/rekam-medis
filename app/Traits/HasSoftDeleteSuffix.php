<?php

namespace App\Traits;

trait HasSoftDeleteSuffix
{
    public static function bootHasSoftDeleteSuffix(): void
    {
        static::deleting(function ($model) {
            if (method_exists($model, 'user') && $model->user) {
                $user = $model->user;
                $timestamp = $model->deleted_at?->timestamp ?? now()->timestamp;

                $user->email = $user->email . '_deleted_' . $timestamp;
                $user->username = $user->username . '_deleted_' . $timestamp;
                $user->saveQuietly();

                $user->delete();
            }
        });
    }
}

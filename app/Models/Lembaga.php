<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembaga extends Model
{
    use HasFactory;

    protected $table = 'lembaga';
    protected $primaryKey = 'id_lb';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id_lb', 'nama'];

    protected static function boot()
    {
        parent::boot();
        // Event ini akan membuat ID kustom secara otomatis sebelum data disimpan
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = self::generateCustomId();
            }
        });
    }

    private static function generateCustomId()
    {
        $latest = self::latest('id_lb')->first();
        if (!$latest) {
            return 'lb001';
        }
        $lastNumber = (int) substr($latest->id_lb, 2);
        $newNumber = $lastNumber + 1;
        return 'lb' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}

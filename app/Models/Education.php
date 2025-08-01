<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;
use Illuminate\Support\Str;

class Education extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->slug = Str::slug($model->level);
        });
    }
    protected $table = 'educations';
    protected $fillable = ['level','slug'];
}

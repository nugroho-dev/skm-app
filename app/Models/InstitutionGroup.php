<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Institution;
use App\Traits\HasUuid;
use Illuminate\Support\Str;

class InstitutionGroup extends Model
{
     use HasFactory, SoftDeletes, HasUuid;
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }
    protected $fillable = ['name', 'slug'];

    public function institutions()
    {
        return $this->hasMany(Institution::class);
    }
}

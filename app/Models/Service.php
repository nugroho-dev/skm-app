<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Response;
use App\Traits\HasUuid;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class Service extends Model
{
    use HasFactory, SoftDeletes, HasUuid, HasRoles;

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }

    protected $fillable = ['name', 'institution_id', 'slug'];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }
}

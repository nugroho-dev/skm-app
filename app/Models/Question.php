<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Question extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->slug = Str::slug($model->text);
        });
    }

    protected $fillable = ['text', 'unsur_id' , 'slug'];

    public function unsur()
    {
        return $this->belongsTo(Unsur::class);
    }
    public function choices()
    {
        return $this->hasMany(Choice::class);
    }
}

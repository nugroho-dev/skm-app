<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Service;
use Illuminate\Support\Str;

class Institution extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected static function booted()
    {
        static::saving(function ($model) {
            // Regenerate slug jika nama berubah atau slug kosong
            if ($model->isDirty('name') || empty($model->slug)) {
                $base = Str::slug($model->name);
                $slug = $base;
                $counter = 1;

                // Pada update, abaikan record saat ini agar tidak mendeteksi duplikat sendiri
                $exists = function($slug) use ($model) {
                    $q = static::withTrashed()->where('slug', $slug);
                    if ($model->exists) {
                        $q->where('id', '!=', $model->id);
                    }
                    return $q->exists();
                };

                while ($exists($slug)) {
                    $slug = $base . '-' . $counter++;
                }

                $model->slug = $slug;
            }
        });
    }
    protected $fillable = ['name', 'mpp_id', 'institution_group_id', 'slug'];

    protected $dates = ['deleted_at'];
    
    public function mpp()
    {
        return $this->belongsTo(Mpp::class);
    }

    public function group()
    {
        return $this->belongsTo(InstitutionGroup::class, 'institution_group_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}

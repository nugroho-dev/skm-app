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
        static::saving(function ($model) {
            // Regenerate slug jika nama berubah atau slug kosong
            if ($model->isDirty('name') || empty($model->slug)) {
                $base = Str::slug($model->name);
                $slug = $base;
                $counter = 1;

                // Abaikan record saat ini ketika mengecek duplikat (termasuk soft deleted)
                $exists = function ($slug) use ($model) {
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

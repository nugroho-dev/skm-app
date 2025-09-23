<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Service;
use Illuminate\Support\Str;
use App\Traits\HasSlug;

class Institution extends Model
{
    use HasFactory, SoftDeletes, HasUuid, HasSlug;

    // optional: protected $slugSource = 'name'; protected $slugField = 'slug';

    // hapus method booted yang sekarang (trait sudah menangani)

    // supaya route model binding menggunakan slug:
    public function getRouteKeyName()
    {
        return 'slug';
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

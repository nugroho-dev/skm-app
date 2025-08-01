<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Answer;
use App\Traits\HasUuid;

class Response extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
       'gender', 'age',
        'education_id', 'occupation_id', 'institution_id',
        'service_id', 'suggestion'
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function education()
    {
        return $this->belongsTo(Education::class);
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}

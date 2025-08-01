<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Choice extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $fillable = ['question_id', 'label', 'score'];
    protected $table = 'choices';
    public function questions()
    {
        return $this->belongsTo(Question::class);
    }
}

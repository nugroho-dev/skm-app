<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Answer extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = ['response_id', 'question_id', 'score'];

    public function questionnaire()
    {
        return $this->belongsTo(Response::class, 'response_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}

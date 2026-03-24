<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiJob extends Model
{
    use HasFactory;

    protected $table = 'ai_jobs';

    protected $fillable = [
        'request_id',
        'exam_type',
        'location',
        'file_path',
        'result_route',
        'status',
        'result',
        'error_message',
    ];
}

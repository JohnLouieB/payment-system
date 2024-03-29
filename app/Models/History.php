<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'name',
        'file',
        'meta',
        'status'
    ];

    protected $casts = [
        'meta' => 'json',
    ];
}

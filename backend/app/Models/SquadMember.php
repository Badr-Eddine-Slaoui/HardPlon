<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SquadMember extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'squad_members';
    protected $fillable = [
        'squad_id',
        'student_id'
    ];
}

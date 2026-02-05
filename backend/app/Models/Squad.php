<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Squad extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'squads';
    protected $fillable = [
        'sprint_id',
        'name',
        'member_count',
    ];
}

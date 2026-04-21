<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BriefVersion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'brief_id',
        'version',
        'architecture_rules',
        'test_config'
    ];

    protected $casts = [
        'architecture_rules' => 'array',
        'test_config' => 'array',
    ];

    public function brief()
    {
        return $this->belongsTo(Brief::class);
    }
}

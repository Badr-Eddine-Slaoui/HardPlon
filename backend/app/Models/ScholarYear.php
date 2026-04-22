<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ScholarYear extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'scholar_years';
    protected $fillable = [
        'start_year',
        'end_year',
        'capacity',
    ];

    protected $appends = [
        'grade_levels_count',
        'students_count',
        'is_active',
        'is_current',
        'is_past',
        'is_upcoming',
    ];

    protected static function booted()
    {
        static::deleted(function ($scholarYear) {
            $scholarYear->grade_levels()->delete();
        });

        static::restored(function ($scholarYear) {
            $scholarYear->grade_levels()->restore();
        });

    }

    public function grade_levels(){
        return $this->hasMany(GradeLevel::class);
    }

    public function getGradeLevelsCountAttribute()
    {
        return $this->grade_levels()->count();
    }

    public function getStudentsCountAttribute()
    {
        return DB::table('users')
            ->join('class_groups', 'users.id_class', '=', 'class_groups.id')
            ->join('formations', 'class_groups.formation_id', '=', 'formations.id')
            ->join('grade_levels', 'formations.grade_level_id', '=', 'grade_levels.id')
            ->where('grade_levels.scholar_year_id', $this->getKey())
            ->where('role', 'STUDENT')
            ->count();
    }

    public function getIsActiveAttribute()
    {
        return !$this->trashed();
    }

    public function getIsCurrentAttribute()
    {
        return $this->is_current();
    }

    public function getIsPastAttribute()
    {
        return $this->is_past();
    }

    public function getIsUpcomingAttribute()
    {
        return $this->is_upcoming();
    }


    public function is_active(): bool{
        return $this->deleted_at === null;
    }

    public function is_upcoming(): bool{
        return $this->start_year > date("Y");
    }

    public function is_past(): bool{
        return $this->end_year < date("Y");
    }

    public function is_current(): bool{
        return $this->start_year <= date("Y") && $this->end_year >= date("Y");
    }
}

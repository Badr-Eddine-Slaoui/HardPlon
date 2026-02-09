<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class GradeLevel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "grade_levels";
    protected $fillable = [
        'name',
        'capacity',
        'description',
        'scholar_year_id'
    ];

    protected $appends = [
        'formations_count',
        'students_count',
        'is_active',
        'scholar_year',
        'is_full',
        'is_empty',
        'is_almost_full',
        'is_nearly_empty'
    ];

    public function is_full(): bool{
        return $this->students_count >= $this->capacity;
    }

    public function is_empty(): bool{
        return $this->students_count === 0;
    }

    public function is_almost_full(): bool{
        return $this->students_count >= $this->capacity * 0.8 && !$this->is_full();
    }

    public function is_nearly_empty(): bool{
        return $this->students_count <= $this->capacity * 0.8 && !$this->is_empty();
    }

    public function getFormationsCountAttribute()
    {
        return $this->formations()->count();
    }

    public function getStudentsCountAttribute()
    {
        return DB::table('users')
            ->join('class_groups', 'users.id_class', '=', 'class_groups.id')
            ->join('formations', 'class_groups.formation_id', '=', 'formations.id')
            ->where('formations.grade_level_id', $this->getKey())
            ->where('role', 'STUDENT')
            ->count();
    }

    public function getIsActiveAttribute()
    {
        return !$this->trashed();
    }

    public function getScholarYearAttribute(){
        $year = $this->scholarYear()->first();
        return "{$year->start_year} - {$year->end_year}";
    }

    public function getIsFullAttribute(){
        return $this->is_full();
    }

    public function getIsEmptyAttribute(){
        return $this->is_empty();
    }

    public function getIsAlmostFullAttribute(){
        return $this->is_almost_full();
    }

    public function getIsNearlyEmptyAttribute(){
        return $this->is_nearly_empty();
    }

    public function scholarYear()
    {
        return $this->belongsTo(ScholarYear::class);
    }

    public function formations(){
        return $this->hasMany(Formation::class);
    }
}

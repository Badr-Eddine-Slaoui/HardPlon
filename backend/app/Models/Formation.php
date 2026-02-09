<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Formation extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'formations';
    protected $fillable = [
        'grade_level_id',
        'title',
        'description',
        'duration',
        'capacity',
    ];

    protected $appends = [
        'class_groups_count',
        'students_count',
        'grade_name',
        'is_active',
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

    public function getClassGroupsCountAttribute()
    {
        return $this->class_groups()->count();
    }

    public function getStudentsCountAttribute()
    {
        return DB::table('users')
            ->join('class_groups', 'users.id_class', '=', 'class_groups.id')
            ->where('class_groups.formation_id', $this->getKey())
            ->where('role', 'STUDENT')
            ->count();
    }

    public function getGradeNameAttribute() {
        return $this->grade_level->name;
    }

    public function getIsActiveAttribute()
    {
        return !$this->trashed();
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

    public function grade_level(){
        return $this->belongsTo(GradeLevel::class);
    }

    public function class_groups(){
        return $this->hasMany(ClassGroup::class);
    }
}

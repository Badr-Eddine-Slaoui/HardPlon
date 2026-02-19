<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BriefController;
use App\Http\Controllers\Api\ClassGroupController;
use App\Http\Controllers\Api\CorrectionController;
use App\Http\Controllers\Api\FormationController;
use App\Http\Controllers\Api\GradeLevelController;
use App\Http\Controllers\Api\LevelController;
use App\Http\Controllers\Api\ScholarYearController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\SprintController;
use App\Http\Controllers\Api\SubmittingController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('guest:api');

Route::middleware('auth:api')->group(function () {
    /* Auth Routes */

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /* Admin Routes */
    Route::prefix('admin')->middleware('is_admin')->group(function () {
        Route::get('/', [AdminController::class,'index']);

        /* Scholar Year Routes */
        Route::controller(ScholarYearController::class)->group(function () {
            Route::get('/scholar-years', 'index');
            Route::get('/scholar-years/{scholar_year}', 'show');
            Route::post('/scholar-years', 'store');
            Route::put('/scholar-years/{scholar_year}', 'update');
            Route::delete('/scholar-years/{scholar_year}', 'destroy');
            Route::post('/scholar-years/{id}/restore','restore');
        });

        /* Grade Level Routes */
        Route::controller(GradeLevelController::class)->group(function () {
            Route::get('/grade-levels', 'index');
            Route::get('/grade-levels/{grade_level}', 'show');
            Route::post('/grade-levels', 'store');
            Route::put('/grade-levels/{grade_level}', 'update');
            Route::delete('/grade-levels/{grade_level}', 'destroy');
            Route::post('/grade-levels/{id}/restore','restore');
        });

        /* Formation Routes */
        Route::controller(FormationController::class)->group(function () {
            Route::get('/formations', 'index');
            Route::get('/formations/{formation}', 'show');
            Route::post('/formations', 'store');
            Route::put('/formations/{formation}', 'update');
            Route::delete('/formations/{formation}', 'destroy');
            Route::post('/formations/{id}/restore','restore');
        });

        /* Class Group Routes */
        Route::controller(ClassGroupController::class)->group(function () {
            Route::get('/class-groups', 'index');
            Route::get('/class-groups/{class_group}', 'show');
            Route::post('/class-groups', 'store');
            Route::put('/class-groups/{class_group}', 'update');
            Route::delete('/class-groups/{class_group}', 'destroy');
            Route::post('/class-groups/{id}/restore','restore');
        });

        /* User Routes */
        Route::controller(UserController::class)->group(function () {
            Route::get('/users', 'index');
            Route::get('/users/main_teachers/{class_group?}', 'get_main_teachers');
            Route::get('/users/sub_teachers/{class_group?}', 'get_sub_teachers');
            Route::get('/users/students/{class_group?}', 'get_students');
            Route::get('/users/{role}/{id}', 'show');
            Route::post('/users', 'store');
            Route::put('/users/{user}', 'update');
            Route::delete('/users/{user}', 'destroy');
            Route::post('/users/{id}/restore','restore');
        });

        /* Level Routes */
        Route::controller(LevelController::class)->group(function () {
            Route::get('/levels', 'index');
            Route::get('/levels/{level}', 'show');
            Route::post('/levels', 'store');
            Route::put('/levels/{level}', 'update');
            Route::delete('/levels/{level}', 'destroy');
            Route::post('/levels/{id}/restore','restore');
        });

        /* Skill Routes */
        Route::controller(SkillController::class)->group(function () {
            Route::get('/skills', 'index');
            Route::get('/skills/formation/{id}', 'get_by_formaction_id');
            Route::get('/skills/{skill}', 'show');
            Route::post('/skills', 'store');
            Route::put('/skills/{skill}', 'update');
            Route::delete('/skills/{skill}', 'destroy');
            Route::post('/skills/{id}/restore','restore');
        });

        /* Sprint Routes */
        Route::controller(SprintController::class)->group(function () {
            Route::get('/sprints', 'index');
            Route::get('/sprints/{sprint}', 'show');
            Route::post('/sprints', 'store');
            Route::put('/sprints/{sprint}', 'update');
            Route::delete('/sprints/{sprint}', 'destroy');
            Route::post('/sprints/{id}/restore','restore');
        });
    });

    /* Teacher Routes */
    Route::prefix('teacher')->middleware('is_teacher')->group(function () {

        /* Brief Routes */
        Route::controller(BriefController::class)->group(function () {
            Route::get('/briefs', 'index');
            Route::get('/briefs/{brief}', 'show');
            Route::post('/briefs', 'store');
            Route::put('/briefs/{brief}', 'update');
            Route::delete('/briefs/{brief}', 'destroy');
            Route::post('/briefs/{id}/restore','restore');
        });

        /* Sprint Routes */
        Route::controller(SprintController::class)->group(function () {
            Route::get('/sprints/formation/{id}', 'get_sprints_by_formation_id');
            Route::get('/sprints/{id}/skills', 'get_sprint_skills');
        });

        /* Class Group Routes */
        Route::controller(ClassGroupController::class)->group(function () {
            Route::get('/class-groups', 'get_teacher_class_groups');
        });

        /* Students Routes */
        Route::controller(UserController::class)->group(function () {
            Route::get('/class-groups/{id}/students', 'get_class_group_students');
        });

        /* Submitting Routes */
        Route::controller(SubmittingController::class)->group(function () {
            Route::get('/submittings/student/{id}', 'get_student_submittings');
        });

        /* Correction Routes */
        Route::controller(CorrectionController::class)->group(function () {
            Route::get('/corrections/students/{id}','get_student_corrections');
            Route::get('/corrections/{brief_id}/{student_id}', 'get_student_correction');
            Route::post('/corrections', 'store');
            Route::put('/corrections/{correction}', 'update');
        });
    });

    /* Student Routes */
    Route::prefix('student')->middleware('is_student')->group(function () {

        /* Brief Routes */
        Route::controller(BriefController::class)->group(function () {
            Route::get('/briefs', 'get_student_briefs');
        });

        /* Submitting Routes */
        Route::controller(SubmittingController::class)->group(function () {
            Route::get('/submittings', 'get_student_submittings');
            Route::get('/submittings/{submitting}', 'show');
            Route::post('/submittings', 'store');
        });

        /* Correction Routes */
        Route::controller(CorrectionController::class)->group(function () {
            Route::get('/corrections', 'get_student_corrections');
            Route::get('/corrections/{id}', 'show');
        });
    });
});

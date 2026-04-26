<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BriefController;
use App\Http\Controllers\Api\BriefSkillLevelController;
use App\Http\Controllers\Api\BriefVersionController;
use App\Http\Controllers\Api\ClassGroupController;
use App\Http\Controllers\Api\CorrectionController;
use App\Http\Controllers\Api\EvaluationJobController;
use App\Http\Controllers\Api\FormationController;
use App\Http\Controllers\Api\GradeLevelController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\LevelController;
use App\Http\Controllers\Api\ProblemController;
use App\Http\Controllers\Api\ProblemSubmissionController;
use App\Http\Controllers\Api\ProblemSubmissionJobController;
use App\Http\Controllers\Api\ProblemTestCaseController;
use App\Http\Controllers\Api\RunnerController;
use App\Http\Controllers\Api\RunnerVersionController;
use App\Http\Controllers\Api\ScholarYearController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\SprintController;
use App\Http\Controllers\Api\StackController;
use App\Http\Controllers\Api\StackRunnerController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Controllers\Api\TeacherController;
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
            Route::get('/scholar-years/{id}', 'show');
            Route::post('/scholar-years', 'store');
            Route::put('/scholar-years/{id}', 'update');
            Route::delete('/scholar-years/{id}', 'destroy');
            Route::post('/scholar-years/{id}/restore','restore');
        });

        /* Grade Level Routes */
        Route::controller(GradeLevelController::class)->group(function () {
            Route::get('/grade-levels', 'index');
            Route::get('/grade-levels/{id}', 'show');
            Route::post('/grade-levels', 'store');
            Route::put('/grade-levels/{id}', 'update');
            Route::delete('/grade-levels/{id}', 'destroy');
            Route::post('/grade-levels/{id}/restore','restore');
        });

        /* Formation Routes */
        Route::controller(FormationController::class)->group(function () {
            Route::get('/formations', 'index');
            Route::get('/formations/{id}', 'show');
            Route::post('/formations', 'store');
            Route::put('/formations/{id}', 'update');
            Route::delete('/formations/{id}', 'destroy');
            Route::post('/formations/{id}/restore','restore');
        });

        /* Class Group Routes */
        Route::controller(ClassGroupController::class)->group(function () {
            Route::get('/class-groups', 'index');
            Route::get('/class-groups/{id}', 'show');
            Route::post('/class-groups', 'store');
            Route::put('/class-groups/{id}', 'update');
            Route::delete('/class-groups/{id}', 'destroy');
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
            Route::put('/users/{id}', 'update');
            Route::delete('/users/{id}', 'destroy');
            Route::post('/users/{id}/restore','restore');
        });

        /* Level Routes */
        Route::controller(LevelController::class)->group(function () {
            Route::get('/levels', 'index');
            Route::get('/levels/{id}', 'show');
            Route::post('/levels', 'store');
            Route::put('/levels/{id}', 'update');
            Route::delete('/levels/{id}', 'destroy');
            Route::post('/levels/{id}/restore','restore');
        });

        /* Skill Routes */
        Route::controller(SkillController::class)->group(function () {
            Route::get('/skills', 'index');
            Route::get('/skills/formation/{id}', 'get_by_formaction_id');
            Route::get('/skills/{id}', 'show');
            Route::post('/skills', 'store');
            Route::put('/skills/{id}', 'update');
            Route::delete('/skills/{id}', 'destroy');
            Route::post('/skills/{id}/restore','restore');
        });

        /* Sprint Routes */
        Route::controller(SprintController::class)->group(function () {
            Route::get('/sprints', 'index');
            Route::get('/sprints/{id}', 'show');
            Route::post('/sprints', 'store');
            Route::put('/sprints/{id}', 'update');
            Route::delete('/sprints/{id}', 'destroy');
            Route::post('/sprints/{id}/restore','restore');
        });

        /* Runners Routes */
        Route::controller(RunnerController::class)->group(function () {
            Route::get('/runners', 'index');
            Route::get('/runners/all', 'getAllRunners');
            Route::get('/runners/{id}', 'show');
            Route::post('/runners', 'store');
            Route::put('/runners/{id}', 'update');
            Route::delete('/runners/{id}', 'destroy');
        });

        /* Runner Versions Routes */
        Route::controller(RunnerVersionController::class)->group(function () {
            Route::get('/runner-versions', 'index');
            Route::get('/runner-versions/all', 'getAllRunnerVersions');
            Route::get('/runner-versions/{id}', 'show');
            Route::post('/runner-versions', 'store');
            Route::delete('/runner-versions/{id}', 'destroy');
        });

        /* Stack Routes */
        Route::controller(StackController::class)->group(function () {
            Route::get('/stacks', 'index');
            Route::get('/stacks/{id}', 'show');
            Route::post('/stacks', 'store');
            Route::put('/stacks/{id}', 'update');
            Route::delete('/stacks/{id}', 'destroy');
            Route::post('/stacks/{id}/restore', 'restore');
        });

        /* Language Routes */
        Route::controller(LanguageController::class)->group(function () {
            Route::get('/languages', 'index');
            Route::get('/languages/{id}', 'show');
            Route::post('/languages', 'store');
            Route::put('/languages/{id}', 'update');
            Route::delete('/languages/{id}', 'destroy');
            Route::post('/languages/{id}/restore', 'restore');
        });

        /* Stack Runners Routes */
        Route::controller(StackRunnerController::class)->group(function () {
            Route::get('/stack-runners', 'index');
            Route::get('/stack-runners/{id}', 'show');
            Route::post('/stack-runners', 'store');
            Route::put('/stack-runners/{id}', 'update');
            Route::delete('/stack-runners/{id}', 'destroy');
        });

        /* Brief Routes */
        Route::controller(BriefController::class)->group(function () {
            Route::get('/get_teacher_briefs', 'index');
            Route::get('/briefs/{id}', 'show');
            Route::post('/briefs', 'store');
            Route::put('/briefs/{id}', 'update');
            Route::delete('/briefs/{id}', 'destroy');
            Route::post('/briefs/{id}/restore','restore');
        });
    });

    /* Teacher Routes */
    Route::prefix('teacher')->middleware('is_teacher')->group(function () {
        Route::get('/', [TeacherController::class, 'index']);

        /* Stack Runners Routes */
        Route::controller(StackController::class)->group(function () {
            Route::get('/stacks', 'getAllStacks');
        });

        /* Brief Routes */
        Route::controller(BriefController::class)->group(function () {
            Route::get('/get_teacher_briefs', 'get_teacher_briefs');
            Route::get('/briefs/{id}', 'show');
            Route::post('/briefs', 'store');
            Route::put('/briefs/{id}', 'update');
            Route::delete('/briefs/{id}', 'destroy');
            Route::post('/briefs/{id}/restore','restore');
            Route::get('/briefs/class-group/{id}', 'get_class_group_briefs');
        });

        /* Brief Versions Routes */
        Route::controller(BriefVersionController::class)->group(function () {
            Route::get('/brief-versions', 'index');
            Route::get('/briefs/{id}/versions', 'get_brief_versions');
            Route::get('/brief-versions/{id}', 'show');
            Route::post('/brief-versions', 'store');
            Route::delete('/brief-versions/{id}', 'destroy');
            Route::post('/brief-versions/{id}/restore', 'restore');
        });

        /* Problems Routes */
        Route::controller(ProblemController::class)->group(function () {
            Route::get('/problems', 'index');
            Route::get('/problems/{id}', 'show');
            Route::post('/problems', 'store');
            Route::put('/problems/{id}', 'update');
            Route::delete('/problems/{id}', 'destroy');
            Route::post('/problems/{id}/restore', 'restore');
        });

        /* Problem Test Cases Routes */
        Route::controller(ProblemTestCaseController::class)->group(function () {
            Route::get('/problem-test-cases', 'index');
            Route::get('/problem-test-cases/{id}', 'show');
            Route::post('/problem-test-cases', 'store');
            Route::delete('/problem-test-cases/{id}', 'destroy');
            Route::post('/problem-test-cases/{id}/restore', 'restore');
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

        /* Submission Routes */
        Route::controller(SubmissionController::class)->group(function () {
            Route::get('/submissions/student/{id}', 'get_student_submissions');
            Route::get('/briefs/{brief_id}/students/{student_id}/submission', 'get_student_brief_submission');
        });

        /* Correction Routes */
        Route::controller(CorrectionController::class)->group(function () {
            Route::get('/corrections/students/{id}','get_student_corrections');
            Route::get('/corrections/{brief_id}/{student_id}', 'get_student_correction');
        });

        /* Language Routes */
        Route::controller(LanguageController::class)->group(function () {
            Route::get('/languages', 'getAllLanguages');
        });

        /* Brief Skill Level Routes */
        Route::controller(BriefSkillLevelController::class)->group(function () {
            Route::get('/briefs/{id}/skill-levels', 'getBriefSkillLevelsByBriefId');
        });
    });

    /* Student Routes */
    Route::prefix('student')->middleware('is_student')->group(function () {
        /* Dashboard Routes */
        Route::controller(StudentController::class)->group(function () {
            Route::get('/dashboard', 'getDashboardData');
        });

        /* Brief Routes */
        Route::controller(BriefController::class)->group(function () {
            Route::get('/briefs', 'get_student_briefs');
            Route::get('/briefs/{id}', 'show');
        });

        /* Submission Routes */
        Route::controller(SubmissionController::class)->group(function () {
            Route::get('/submissions', 'get_student_submissions');
            Route::get('/submissions/{id}', 'show');
            Route::post('/submissions', 'store');
        });

        /* Problem Submission Routes */
        Route::controller(ProblemSubmissionController::class)->group(function () {
            Route::get('/problem-submissions', 'index');
            Route::post('/problem-submissions', 'store');
            Route::get('/problem-submissions/{id}', 'show');
        });

        /* Correction Routes */
        Route::controller(CorrectionController::class)->group(function () {
            Route::get('/corrections', 'get_student_corrections');
            Route::get('/corrections/{id}', 'show');
        });
    });
});

Route::prefix('worker')->group(function () {
    Route::controller(EvaluationJobController::class)->group(function () {
        Route::get('/evaluation-jobs/{id}', 'show');
        Route::put('/evaluation-jobs/{id}', 'update');
    });
    Route::controller(ProblemSubmissionJobController::class)->group(function () {
        Route::get('/problem-submission-jobs/{id}', 'show');
        Route::put('/problem-submission-jobs/{id}', 'update');
    });
});

/* Health */

Route::get('/health', fn () => response()->json(['status' => 'ok']));

<?php

use Illuminate\Http\Request;
use App\Http\Controllers\UserCrudController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::fallback(function () {
    return response(['message' => 'API resource not found.', 'code' => 403], 403);
});

Route::post('/login', [UserCrudController::class, 'login']);    //Login User
Route::post('/user/register', [UserCrudController::class, 'store']); //Register user

//Following functions can be executed by logged in user only
Route::group(['middleware' => ['json.response','auth:api']], function () {
    //Notification functions
    Route::get('/user/notifications', [UserCrudController::class, 'fetchAllNotifications']);
    Route::get('/user/notifications/unread', [UserCrudController::class, 'fetchUnread']);
    Route::get('/user/notifications/read', [UserCrudController::class, 'markRead']);
    Route::get('/user/notifications/delete', [UserCrudController::class, 'destroyNotifications']);
    //User CRUD routes
    Route::get('/user/{id}', [UserCrudController::class, 'show']);
    Route::get('/logout', [UserCrudController::class, 'logout']);
    Route::get('/logoutAll', [UserCrudController::class, 'logoutAll']);
    Route::post('/user/update/{id}', [UserCrudController::class, 'update']);
    Route::post('/user/delete/{id}', [UserCrudController::class, 'destroy']);
});

//Following operations can be done only by administrators
Route::middleware('json.response','auth:api', 'admin')->prefix('/admin')->group(function () {
    Route::get('/show/approved', [AdminController::class, 'showApproved']);
    Route::get('/show/approved/students', [AdminController::class, 'showApprovedStudents']);
    Route::get('/show/approved/teachers', [AdminController::class, 'showApprovedTeachers']);
    Route::get('/show/notApproved', [AdminController::class, 'showNotApproved']);
    Route::get('/show/notApproved/students', [AdminController::class, 'showNotApprovedStudents']);
    Route::get('/show/notApproved/teachers', [AdminController::class, 'showNotApprovedTeachers']);
    Route::post('/assign', [AdminController::class, 'Assign']);
    Route::get('/teacher/approve/{id}', [AdminController::class, 'ApproveTeacher']);
});

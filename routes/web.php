<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\DashboardController;
use App\Jobs\AccountApproval;
use App\Jobs\digest;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'guest'], function () {
    Route::get('/', function () {
        return view('welcome');
    });
    Route::get('/login', [UserController::class, 'index'])->name('login');
    Route::post('/login', [UserController::class, 'store']);
    Route::get('/register', [RegistrationController::class, 'index'])->name('register');
    Route::post('/register', [RegistrationController::class, 'store']);
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', function(){
        return redirect('/dashboard');
    });
    Route::get('/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'store']);
    Route::get('/profile/student', [StudentController::class, 'index'])->name('student');
    Route::post('/profile/student', [StudentController::class, 'store']);
    Route::get('/profile/teacher', [TeacherController::class, 'index'])->name('teacher');
    Route::post('/profile/teacher', [TeacherController::class, 'store']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    //Edit routes
    Route::get('/edit/userprofile/{id}', [UserController::class, 'edit'])->name('update');
    Route::post('/edit/userprofile/{id}', [UserController::class, 'update']);
    Route::get('/edit/profile/{id}', [ProfileController::class, 'edit'])->name('editProfile');
    Route::post('/edit/profile/{id}', [ProfileController::class, 'update']);
    Route::get('/edit/profile/teacher/{id}', [TeacherController::class, 'edit'])->name('editTeacher');
    Route::post('/edit/profile/teacher/{id}', [TeacherController::class, 'update']);
    Route::get('/edit/profile/student/{id}', [StudentController::class, 'edit'])->name('editStudent');
    Route::post('/edit/profile/student/{id}', [StudentController::class, 'update']);
    //Delete profile route
    Route::get('/edit/userprofile/delete/{id}', [UserController::class, 'destroy'])->name('destroy');
});

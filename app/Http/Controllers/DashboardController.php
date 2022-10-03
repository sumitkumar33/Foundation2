<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models;
use App\Models\User;
use Illuminate\Support\Facades\Auth as Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $role = session()->get('role_id');

        //Check if user is logged in
        $isLoggedIn = session()->get('isLoggedIn');
        if (!$isLoggedIn) {
            return redirect('login');
        }
        //Check if logged in user has filled their profile data
        $user_id = session()->get('user_id');

        $profile = Models\Profile::where('user_id', '=', $user_id)->first();
        if ($profile == null) {
            return redirect('profile');
        }
        session()->put($profile->toArray());
        if ($role == 1) {
            $extendedProfile = Models\Student::where('profile_id', '=', $profile['profile_id'])->first();
            $redirection = '/profile/student';
        } else if ($role == 2) {
            $extendedProfile = Models\Teacher::where('profile_id', '=', $profile['profile_id'])->first();
            $redirection = '/profile/teacher';
        } else {
            return redirect('/dashboard/admin');
        }
        //Check if logged in user has filled their extended Profile
        if ($extendedProfile == null) {
            return redirect($redirection);  //get filled if not filled
        }
        session()->put($extendedProfile->toArray());
        // dd(session()->all());
        $status = session()->get('isApproved');
        if ($status === 0) {
            return view('dashboard')->with('approvalStatus', 'Hello ' . session()->get('name') . ',<br>Your account is not approved yet. Please try again later.');
        } else {
            return view('dashboard')->with('approvalStatus', 'Hello ' . session()->get('name') . ',<br> Your account is successfully approved.');
        }
    }
}

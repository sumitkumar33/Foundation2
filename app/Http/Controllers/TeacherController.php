<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Teacher;
use Illuminate\Support\Facades\Redirect;

class TeacherController extends Controller
{
    public function index()
    {
        $loginCheck = session()->get('isLoggedIn');
        $user_type = session()->get('role_id');
        if ($loginCheck === true && $user_type == 2){   //Check logins and usertype then render
            $url = route('teacher');
            $title = "Enter professional information for your profile:";
            $d = compact('url', 'title');
            return view('teacher')->with($d);
        } else if ($loginCheck === true && $user_type == 1){
            return redirect('/profile/student');
        } else {
            return redirect('/login');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'expertise_subject' => 'required',
            'experience' => 'required|numeric',
        ]);
        $profile_id = session()->get('profile_id');
        $data = new Teacher;
        $data->expertise_subject = $request['expertise_subject'];
        $data->experience = $request['experience'];
        $data->profile_id = $profile_id;
        try {
            $result = $data->save();
            if (!$result) {
                throw new \Exception('Could not save Teacher Details.');
            }
            $request->session()->put($data->toArray());
            return redirect('/dashboard');
        }
        catch (QueryException $qe) {
            return view('teacher')->with('error', $qe->getMessage());
        }
        catch (\Exception $e) {
            return view('teacher')->with('error', $e->getMessage());
        }

    }

    public function edit($id)
    {
        if(session()->get('user_id') != $id || session()->get('role_id') != 2) {
            return Redirect('dashboard');
        }
        $url = route('editTeacher', [$id]);
        $title = "Update professional details!";
        $teacher_id = session()->get('teacher_id');
        $data = Teacher::find($teacher_id);
        $d = compact('url', 'title', 'data');
        return view('teacher')->with($d);
    }

    public function update($id, Request $request) {
        if(session()->get('user_id') != $id || session()->get('role_id') != 2) {
            return Redirect('dashboard');
        }
        $data = Teacher::find(session()->get('teacher_id'));
        $data->expertise_subject = $request['expertise_subject'];
        $data->experience = $request['experience'];
        try {
            $result = $data->save();
            if(!$result) {
                throw new \Exception('Could not save data, try again later.');
            }
            return redirect('dashboard');
        }
        catch (\Exception $e) {
            echo "Error: ".$e->getMessage();
            return view('teacher');
        }
    }
}

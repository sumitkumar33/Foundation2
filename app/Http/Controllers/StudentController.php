<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Student;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(session()->all());
        $loginCheck = session()->get('isLoggedIn');
        $user_type = session()->get('role_id');
        if ($loginCheck === true && $user_type == 1){   //Return view if logged in user is student and authenticated
            $url = route('student');
            $title = "Enter your extended student information.";
            $d = compact('url', 'title');
            return view('student')->with($d);
        } else if ($loginCheck === true && $user_type == 2){
            return redirect('/profile/teacher');
        } else {
            return redirect('/login');
        }
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'parent_name' => 'required',
            'parent_contact' => 'required|numeric',
        ]);
        $data = new Student;
        $data->parent_name = $request['parent_name'];
        $data->parent_contact = $request['parent_contact'];
        $profile_id = session()->get('profile_id');
        $data->profile_id = $profile_id;
        try {
            $result = $data->save();
            if (!$result) {
                throw new \Exception('Could not save student details.');
            }
            $request->session()->put($data->toArray());
            return redirect('/dashboard');
        }
        catch (QueryException $qe) {
            return view('student')->with('error', $qe->getMessage());
        }
        catch (\Exception $e) {
            return view('student')->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        if(session()->get('user_id') != $id || session()->get('role_id') != 1) {
            return Redirect('dashboard');
        }
        $url = route('editStudent', [$id]);
        $title = "Update student details!";
        $student_id = session()->get('student_id');
        $data = Student::find($student_id);
        $d = compact('url', 'title', 'data');
        return view('student')->with($d);
    }

    public function update($id, Request $request) {
        if(session()->get('user_id') != $id || session()->get('role_id') != 1) {
            return Redirect('dashboard');
        }
        $data = Student::find(session()->get('student_id'));
        $data->parent_name = $request['parent_name'];
        $data->parent_contact = $request['parent_contact'];
        try {
            $result = $data->save();
            if(!$result) {
                throw new \Exception('Could not save data, try again later.');
            }
            return redirect('dashboard');
        }
        catch (\Exception $e) {
            echo "Error: ".$e->getMessage();
            return view('student');
        }
    }

}

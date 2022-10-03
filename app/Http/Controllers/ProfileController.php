<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function index()
    {
        $value = session()->get('isLoggedIn');
        if ($value === true){
            $url = route('profile');
            $title = "Enter Profile Data: ";
            $data = compact('url','title');
            return view('profile')->with($data);
        } else {
            return redirect('/login');
        }
    }

    public function store(Request $request)
    {
        // print_r($request->session()->all());
        $request->validate([
            'address' => 'required',
        ]);
        $data = new Profile;
        $data->address = $request['address'];
        $data->profile_image = $request['profile_image']??'';
        $data->current_school = $request['current_school']??'';
        $data->previous_school = $request['previous_school']??'';
        $user_id = session()->get('user_id');
        $data->user_id = $user_id;
        try {
            $result = $data->save();
            if (!$result) {
                throw new \Exception('Error: Failed to save profile.');
            }
            $request->session()->put($data->toArray());
            $user_type = session()->get('role_id');
            if ($user_type == 1) {
                return redirect('/profile/student');
            } else {
                return redirect('/profile/teacher');
            }
        }
        catch (\Exception $e) {
            return view('profile')->with('error', $e->getMessage());
        }
        catch (QueryException $qe) {
            return view('profile')->with('error', $qe->getMessage());
        }
    }

    public function edit($id) {
        if($id != session()->get('user_id')) {
            return redirect('dashboard');
        }
        // dd(session()->all());
        $profile_id = session()->get('profile_id');
        $data = Profile::find($profile_id);
        if (is_null($data)){
            return redirect('dashboard');
        }
        $url = route('editProfile', [$id]);
        $title = "Update Profile Data";
        $d = compact('data', 'title', 'url');
        return view('profile')->with($d);
    }

    public function update(Request $request) {
        $data = Profile::find(session()->get('profile_id'));
        $data->address = $request['address'];
        $data->profile_image = $request['profile_image'];
        $data->current_school = $request['current_school'];
        $data->previous_school = $request['previous_school'];
        try {
            $result = $data->save();
            if(!$result) {
                throw new \Exception('Could not save data, try again later.');
            }
            return redirect('dashboard');
        }
        catch (\Exception $e) {
            echo "Error: ".$e->getMessage();
            return view('profile');
        }
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return view 'Login' on get requests
        $value = session()->get('isLoggedIn');
        if ($value == false || $value == null) {
            return view('login');
        } else {
            return redirect('dashboard');
        }
    }

    public function store(Request $request)
    {
        //take login post request here
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt(['email' => $request['email'], 'password' => $request['password']])) {
            $data = User::where('email', '=', $request['email'])->first();
            $request->session()->regenerate();
            $request->session()->put($data->toArray());
            $request->session()->put('isLoggedIn', true);
            return redirect('/dashboard');
        } else {
            return view('login')->with('login_error', 'Email or Password is incorrect.');
        }
    }

    public function logout()
    {
        session()->invalidate();
        session()->regenerate();
        // dd(session()->all());
        return redirect('/login');
    }

    public function edit($id)
    {
        // dd(session()->all());
        $session_id = session()->get('user_id') ?? null;
        //Will be used for update user data (Name, Email, Password)
        if ($id == null || $id == '' || $id <= 0 || $session_id != $id) {   //check if logged in user = edit user
            return redirect('logout')->with('error', 'Cannot update user data.');
        }
        $data = User::find($id);
        if (is_null($data)) {
            return redirect()->back()->with('error', 'User data does not exist.');
        }
        $url = route('update', [$id]);
        $title = 'Edit User Profile';
        $d = compact('url', 'data', 'title', 'id');
        return view('register')->with($d);
    }

    public function update($id, Request $request)
    {
        // dd($id);
        $request->validate([
            'email' => 'email',
        ]);
        $data = User::find($id);
        $data->name = $request['name'];
        $data->email = $request['email'];
        $data->updated_at = now();
        try {
            $result = $data->save();
            if (!$result) {
                throw new \Exception('Could not save data, try again later.');
            }
            return redirect('dashboard');
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
            return view('register');
        }
    }

    public function destroy($id)
    {
        //Will be used when user want to delete their account
        if (session()->get('user_id') != $id) {
            return redirect('dashboard');
        }
        $data = User::find($id);
        if (is_null($data)) {
            return redirect('/dashboard');
        } else {
            $data->delete();
            return redirect('/logout');
        }
    }
}

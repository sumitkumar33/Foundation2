<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function index()
    {
        $value = session()->get('isLoggedIn');
        if ($value == null || $value == false){
            $url = route('register');
            $title = "Customer Registration";
            $data = compact('url', 'title');
            return view('register')->with($data);
        } else {
            return redirect('dashboard');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "email" => "required|email",
            "password" => "required",
            "confirm_password" => "required|same:password",
            "role_id" => "required|lte:2|gt:0",
        ]);
        $data = new User;
        $data->name = $request['name'];
        $data->email = $request['email'];
        $data->password = Hash::make($request['password']);
        $data->role_id = $request['role_id'];
        try {
            $result = $data->save();
            if (!$result) {
                throw new \Exception("Registration failed, please try again later.");
            }
            Auth::attempt(['email' => $data->email, 'password' => $request['password']]);
            $request->session()->regenerate();
            $request->session()->put($data->toArray());
            $request->session()->put('isLoggedIn', true);
            return redirect('/dashboard');
        }
        catch (QueryException $qe) {
            return view('register')->with('error', 'Email already registered. Please try again later.');
        }
        catch (\Exception $e) {
            return view('register')->with('error', $e->getMessage());
        }
    }
}

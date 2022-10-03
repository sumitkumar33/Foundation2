<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UserStudentResource;
use App\Http\Resources\UserTeacherResource;

class UserCrudController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            // Authentication fails...
            return response()->json(array('Error' => 'Authentication failed', 'statusCode' => '401'));
        }

        $token = auth()->user()->createToken(Auth::user())->accessToken;
        return response(['message' => 'Authentication successfull', 'token' => $token], 200);
    }

    public function logout()
    {
        auth()->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function logoutAll()
    {
        Token::where('user_id', Auth::user()->user_id)->update(['revoked' => true]);
        return response(['message' => 'Logout from all devices successful and all user tokens are revoked'], 200);
    }


    public function store(Request $request)
    {
        //Create new user
        $data = new User;
        $data->name = $request['name'];
        $data->email = $request['email'];
        $data->password = bcrypt($request['password']);
        $data->role_id = $request['role_id'];
        $data->created_at = now();
        $data->save();
        //Create profile
        $data->profile()->create([
            'address' => $request['address'],
            'profile_image' => $request['profile_image'],
            'current_school' => $request['current_school'],
            'previous_school' => $request['previous_school'],
            'isApproved' => 0,
            'user_id' => $data->user_id,
            'created_at' => $data->created_at,
        ]);
        //Create data according to roles
        if ($data->role_id == '1') {
            $extend = $data->extendedStudent()->create([
                "parent_name" => $request['parent_name'],
                "parent_contact" => $request['parent_contact'],
                "profile_id" => $data->profile->profile_id,
                'created_at' => $data->created_at,
            ]);
        } elseif ($data->role_id == '2') {
            $extend = $data->extendedTeacher()->create([
                "expertise_subject" => $request['expertise_subject'],
                "experience" => $request['experience'],
                "profile_id" => $data->profile->profile_id,
                'created_at' => $data->created_at,
            ]);
        }

        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            // Authentication fails...
            return response()->json(array('Error' => 'Authentication failed', 'statusCode' => '401'));
        }
        $token = auth()->user()->createToken(Auth::user())->accessToken;

        return response()->json(array(
            "User" => [$data],
            "extended_profile" => [$extend],
            "token" => $token,
        ));
    }

    public function show($id)
    {
        $data = User::with('role', 'profile', 'extendedStudent', 'extendedTeacher')->find($id);
        //Check if user is not found
        if (is_null($data)) {
            return response()->json(
                [
                    'error' => [
                        "reason" => "required",
                        "message" => "User not found.",
                        "locationType" => "header",
                        "location" => "API",
                    ],
                    "code" => 404,
                    "message" => "User not found in the database",
                ],
            );
        }

        //Return data according to roles (Student, Teacher, Administrator)
        if ($data->role_id == 1) {
            $var = User::with('role', 'profile', 'extendedStudent')->find($id);
            return new UserStudentResource($var);
        } elseif ($data->role_id == 2) {
            $var = User::with('role', 'profile', 'extendedTeacher')->find($id);
            return new UserTeacherResource($var);
        } else {
            return new UserResource($data);
        }
    }

    public function update(Request $request)
    {
        $id = Auth::user()->user_id;
        $data = User::with('role', 'profile', 'extendedTeacher', 'extendedStudent')->find($id);
        if (is_null($data)) {
            return response()->json(
                [
                    'error' => [
                        "reason" => "required",
                        "message" => "User not found.",
                        "locationType" => "header",
                        "location" => "API",
                    ],
                    "code" => 404,
                    "message" => "User not found in the database",
                ],
            );
        }
        $data->update([
            "name" => $request['name'] ?? $data->name,
            "email" => $request['email'] ?? $data->email,
            "password" => $request['password'] ?? $data->password,
            "role_id" => $data->role_id,
            "updated_at" => now(),
        ]);
        if (!is_null($data->profile)) {
            $data->profile()->update([
                "address" => $request['address'] ?? $data->profile->address,
                "profile_image" => $request['profile_image'] ?? $data->profile->profile_image,
                "current_school" => $request['current_school'] ?? $data->profile->current_school,
                "previous_school" => $request['previous_school'] ?? $data->profile->previous_school,
                "user_id" => $data->profile->user_id,
                "updated_at" => now(),
            ]);
        }
        if (!is_null($data->extendedTeacher) || !is_null($data->extendedStudent)) {
            if ($data->role_id == 1) {
                $data->extendedStudent()->update([
                    "parent_name" => $request['parent_name'] ?? $data->extendedStudent->parent_name,
                    "parent_contact" => $request['parent_contact'] ?? $data->extendedStudent->parent_contact,
                ]);
            } else {
                $data->extendedTeacher()->update([
                    "expertise_subject" => $request['expertise_subject'] ?? $data->extendedTeacher->expertise_subject,
                    "experience" => $request['experience'] ?? $data->extendedTeacher->experience,
                ]);
            }
        }

        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $data = User::with('profile', 'extendedTeacher', 'extendedStudent')->find(Auth::user()->user_id);
        $data->delete();
        return response()->json(array(
            "status" => "success",
            "statusCode" => (string)200,
        ));
    }

    
    public function fetchAllNotifications()
    {
        $data = auth()->user()->notifications;
        return response()->json($data, 200);
    }

    public function fetchUnread()
    {
        $data = auth()->user()->unreadNotifications;
        return response()->json($data, 200);
    }

    public function markRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['Message' => 'Marked all unread notifications Read.'], 200);
    }

    public function destroyNotifications()
    {
        auth()->user()->notifications()->delete();
        return response()->json(['Message' => 'All notifications have been deleted.'], 200);
    }
}

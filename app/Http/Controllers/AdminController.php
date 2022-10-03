<?php

namespace App\Http\Controllers;

use App\Jobs\AccountApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Jobs\Notify as notify;
use App\Notifications\NotifyAssign;
use Illuminate\Database\QueryException;

class AdminController extends Controller
{
    public function assign(Request $request)
    {
        try {
            $request->validate([
                'student_user_id' => 'required|numeric',
                'teacher_user_id' => 'required|numeric',
            ]);
            $req = $request->only('student_user_id', 'teacher_user_id');
            $dataStudent = User::with('role', 'profile', 'extendedStudent', 'getAssignStudent')->find($req['student_user_id']);
            $dataTeacher = User::with('role', 'profile', 'extendedTeacher', 'getAssignTeacher')->find($req['teacher_user_id']);
            if($dataStudent->role_id != 1 || $dataTeacher->role_id != 2){
                throw new \ErrorException('User roles does not matche');
            }
            $dataStudent->getAssignStudent()->insert([
                "student_id" => $dataStudent->extendedStudent->student_id ?? $dataStudent->getAssignStudent->student_id ?? '',
                "teacher_id" => $dataTeacher->extendedTeacher->teacher_id ?? $dataTeacher->getAssignTeacher->teacher_id ?? '',
                "created_at" => now(),
                "updated_at" => now(),
            ]);
            $dataStudent->profile()->update([
                "isApproved" => 1,
            ]);
            $data = [
                'msg' => "StudentAssigned",
                'id' => $dataStudent->extendedStudent->student_id,
                'name' => $dataStudent->name,
                'email' => $dataStudent->email,
            ];
            //Notify Teacher for assign of student
            $dataTeacher->notify(new NotifyAssign($data));
            //Notify Student for approval of account
            $data2 = [
                'user_id' => $dataStudent->user_id,
                'name' => $dataStudent->name,
                'admin_name' => Auth::user()->name,
                'url' => url('/dashboard'),
            ];
            dispatch(new notify($data2));
            return response()->json($dataStudent);
        } catch (\ErrorException $e) {
            return response()->json(['Message' => 'User roles do not match', 'Error' => $e->getMessage()]);
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) {
                return response()->json([
                    "Message" => $dataStudent->name . ' has already been assigned to ' . $dataTeacher->name,
                    "Error" => $e->getCode(),
                ]);
            } else {
                return response()->json(['Message' => 'User has not completed his profile.', 'ErrorCode' => $e->getCode()]);
            }
        }
    }

    public function ApproveTeacher($id)
    {
        $data = User::with('role', 'profile', 'extendedTeacher')->find($id);
        try {
            if (is_null($data)) {
                throw new \ErrorException('UserID is not found in database.');
            }
            if ($data->role_id != 2) {
                throw new \ErrorException('Provided user id belongs to ' . $data->role->role);
            }
            $data->profile()->update([
                "isApproved" => 1,
            ]);
            dispatch(new AccountApproval($data, Auth::user()));
            return response()->json(["message" => "Teacher has been successfully approved."]);
        } catch (\ErrorException $e) {
            return response()->json(["message" => $e->getMessage()]);
        }
    }


    public function showApproved()
    {
        $data = User::with('getApproved')->get();
        if (is_null($data)) {
            return response(['message' => 'Approved list is empty'], 200);
        }
        $response = array();
        foreach ($data as $d) {
            if (is_null($d->getApproved) || is_null($d->getApproved->profile_id))
                continue;
            else
                array_push($response, $d);
        }
        return response()->json($response);
    }

    public function showApprovedStudents()
    {
        $data = User::with('getApproved')->get();
        if (is_null($data)) {
            return response(['message' => 'Approved list is empty'], 200);
        }
        $response = array();
        foreach ($data as $d) {
            if (is_null($d->getApproved) || is_null($d->getApproved->profile_id) || $d->role_id != 1)
                continue;
            else
                array_push($response, $d);
        }
        return response()->json($response);
    }

    public function showApprovedTeachers()
    {
        $data = User::with('getApproved')->get();
        if (is_null($data)) {
            return response(['message' => 'Approved list is empty'], 200);
        }
        $response = array();
        foreach ($data as $d) {
            if (is_null($d->getApproved) || is_null($d->getApproved->profile_id) || $d->role_id != 2)
                continue;
            else
                array_push($response, $d);
        }
        return response()->json($response);
    }

    public function showNotApproved()
    {
        $data = User::with('getNotApproved')->get();
        if (is_null($data)) {
            return response(['message' => 'Approved list is empty'], 200);
        }
        $response = array();

        foreach ($data as $d) {
            if (($d->getNotApproved->isApproved ?? '') != 0) {
                continue;
            }
            array_push($response, $d);
        }
        return response()->json($response);
    }

    public function showNotApprovedStudents()
    {
        $data = User::with('getNotApproved')->get();
        if (is_null($data)) {
            return response(['message' => 'Approved list is empty'], 200);
        }
        $response = array();

        foreach ($data as $d) {
            if (($d->getNotApproved->isApproved ?? '') != 0 || $d->role_id != 1) {
                continue;
            }
            array_push($response, $d);
        }
        return response()->json($response);
    }

    public function showNotApprovedTeachers()
    {
        $data = User::with('getNotApproved')->get();
        if (is_null($data)) {
            return response(['message' => 'Approved list is empty'], 200);
        }
        $response = array();

        foreach ($data as $d) {
            if (($d->getNotApproved->isApproved ?? '') != 0 || $d->role_id != 2) {
                continue;
            }
            array_push($response, $d);
        }
        return response()->json($response);
    }
}

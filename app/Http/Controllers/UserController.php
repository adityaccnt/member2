<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserHobby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        try {
            $data   = array();
            $users  = collect(User::all()); // get list user

            // add user hobby
            foreach ($users as $user) {
                $tmp = array();

                // looping hobby list to get hobby name
                foreach ($user->userHobby as $val_hobby)
                    array_push($tmp, $val_hobby->hobby->name);

                // add hobby name to collection
                $user = collect($user)->merge(['hobby' => $tmp])->forget('user_hobby');
                array_push($data, $user);
            }

            // success output
            return response()->json([
                'status'    => true,
                'message'   => 'success',
                'data'      => $data
            ]);
        } catch (\Exception $e) {

            // error output
            return response()->json([
                'status'    => false,
                'message'   => 'error',
                'data'      => $e->getMessage()
            ], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            // validator
            $validator = Validator::make($request->all(), [
                'name'      => 'required|string|min:2|max:50',
                'email'     => 'required|email:dns|unique:users',
                'phone'     => 'required|starts_with:0|digits_between:7,14',
                'hobby_id'  => 'required|array|exists:Hobbies,id'
            ]);

            // error output
            if ($validator->fails())
                return response()->json([
                    'status'    => false,
                    'message'   => 'error',
                    'data'      => $validator->errors()
                ], 400);

            // get user id
            $id = User::create($request->all())->id;

            // insert Hobby to UserHobby
            foreach ($request->hobby_id as $value)
                UserHobby::create([
                    'user_id'   => $id,
                    'hobby_id'  => $value
                ]);

            // add id to output
            $data = collect($request->all())->merge(['id' => $id])->sort();

            // success output
            return response()->json([
                'status'    => true,
                'message'   => 'success',
                'data'      => $data
            ]);
        } catch (\Exception $e) {

            // error output
            return response()->json([
                'status'    => false,
                'message'   => 'error',
                'data'      => $e->getMessage()
            ], 400);
        }
    }

    public function show(User $user)
    {
        try {
            $tmp    = array();

            // looping hobby list to get hobby name
            foreach ($user->userHobby as $val_hobby)
                array_push($tmp, $val_hobby->hobby->name);

            // add hobby name to collection
            $data = collect($user)->merge(['hobby' => $tmp])->forget('user_hobby');

            // success output
            return response()->json([
                'status'    => true,
                'message'   => 'success',
                'data'      => $data
            ]);
        } catch (\Exception $e) {

            // error output
            return response()->json([
                'status'    => false,
                'message'   => 'error',
                'data'      => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            
            // validator
            $validator = Validator::make($request->all(), [
                'name'      => 'required|string|min:2|max:50',
                'email'     => 'required|email:dns|unique:users',
                'phone'     => 'required|starts_with:0|digits_between:7,14',
                'hobby_id'  => 'required|array|exists:Hobbies,id'
            ]);

            // failed output
            if ($validator->fails())
                return response()->json([
                    'status'    => false,
                    'message'   => 'error',
                    'data'      => $validator->errors()
                ], 400);

            // get user id
            User::find($user->id)->update($request->except(['hobby_id']));

            // select and delete from user selected
            UserHobby::where('user_id', $user->id)->delete();

            // insert Hobby to UserHobby
            foreach ($request->hobby_id as $value)
                UserHobby::create([
                    'user_id'   => $user->id,
                    'hobby_id'  => $value
                ]);

            // add id to output
            $data = collect($request->all())->merge(['id' => $user->id])->sort();

            // success output
            return response()->json([
                'status'    => true,
                'message'   => 'success',
                'data'      => $data
            ]);
        } catch (\Exception $e) {

            // error output
            return response()->json([
                'status'    => false,
                'message'   => 'error',
                'data'      => $e->getMessage()
            ], 400);
        }
    }

    public function destroy(User $user)
    {
        try {
            // select and delete from user selected
            User::find($user->id)->delete();
            UserHobby::where('user_id', $user->id)->delete();

            // success output
            return response()->json([
                'status'    => true,
                'message'   => 'success',
                'data'      => null
            ], 200);
        } catch (\Exception $e) {

            // error output
            return response()->json([
                'status'    => false,
                'message'   => 'error',
                'data'      => $e->getMessage()
            ], 400);
        }
    }
}

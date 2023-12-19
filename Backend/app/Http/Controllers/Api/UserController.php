<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (is_null($user)) {
            return response([
                'message' => 'User Not Found'
            ], 404);
        }
        return response([
            'message' => 'Retrieve User Success',
            'data' => $user
        ], 200);
    }

    public function update(Request $request)
    {
        $user = User::find(auth()->user()->id);
        if (is_null($user)) {
            return response([
                'message' => 'User Not Found'
            ], 404);
        }
        $updateData = $request->all();

        $validate  = Validator::make($updateData, [
            'name' => 'required|max:60',
            'handle' => 'required|max:20',
            'email' => 'required|email:rfc,dns|unique:users,email,' . $user->id,
            'bio' => 'string',
        ]);
        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $user->update($updateData);
        return response([
            'message' => 'User Updated',
            'data' => $user
        ], 200);
    }

    public function updateFotoDanBio(Request $request) {
        $user = User::find(Auth::user()->id);

        $validation = Validator::make($request->all(), [
            'bio' => 'required|string',
            'handle' => 'required|string',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validation->fails()) {
            return response(['message' => "Gambar atau bio belum diisi"], 400);
        }

        if ($request->hasFile('avatar')) {
            $uploadFolder = 'contents';
            $image = $request->file('avatar');
            $image_uploaded_path = $image->store($uploadFolder, 'public');
            $uploadedImageResponse = basename($image_uploaded_path);

            $user->avatar = $uploadedImageResponse;
        }
        $user->handle = $request->handle;
        $user->bio = $request->bio;

        if ($user->save()) {
            return response([
                'message' => 'Profile berhasil diubah',
                'data' => $user
            ], 200);
        } else {
            return response([
                'message' => 'Profile gagal diubah',
                'data' => null
            ], 400);
        }
    }
}


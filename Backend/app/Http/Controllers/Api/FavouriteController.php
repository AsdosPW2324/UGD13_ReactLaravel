<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contents;
use App\Models\User;
use App\Models\Favourite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FavouriteController extends Controller
{
    public function showUserFavouriteList(){
        // $user = User::find(Auth::user()->id);
        // $favourites = Favourite::where('id_user', Auth::user()->id)->get();

        $favourites = DB::table('favourites')
        ->join('contents', 'favourites.id_content', '=', 'contents.id')
        ->join('users', 'contents.id_user', '=', 'users.id')
        ->select('contents.*', 'users.handle as Content Creator')
        ->where('favourites.id_user', Auth::user()->id)
        ->get();

    return response([
        'message' => Auth::user()->handle.'\'s Favourite List',
        'data' => $favourites
    ], 200);

    }

    public function store(Request $request){
        $storeData = $request->all();

        $validate = Validator::make($storeData,[
            'id_content' => 'required',
        ]);
        if ($validate->fails()) {
            return response(['message'=> $validate->errors()],400);
        }

        $user = User::find(Auth::user()->id);
        if(is_null($user)){
            return response([
                'message' => 'User Not Found'
            ],404);
        }
        $content = Contents::find($storeData['id_content']);
        if(is_null($content)){
            return response([
                'message' => 'Content Not Found'
            ],404);
        }
        $checkContent = Favourite::where('id_user', Auth::user()->id)->where('id_content', $storeData['id_content'])->first();
        if(!is_null($checkContent)){
            return response([
                'message' => 'Content already in your favourite list'
            ],404);
        }

        $favourite = Favourite::create([
            'id_user' => $user->id,
            'id_content' => $content->id,
        ]);
        return response([
            'message' => 'Added to Your Favourite List',
            'data' => $favourite
        ],200);
    }

    public function deletefromFavourites($id){

        $content = Contents::find($id);
        if(is_null($content)){
            return response([
                'message' => 'Content Not Found'
            ],404);
        }

        $favourite = Favourite::where('id_user', Auth::user()->id)->where('id_content', $id)->first();
        if(is_null($favourite)){
            return response([
                'message' => 'No such contents in your favourite list'
            ],404);
        }
        $favourite->delete();
        return response([
            'message' => 'Removed from Your Favourite List',
            'data' => $favourite
        ],200);

    }

}

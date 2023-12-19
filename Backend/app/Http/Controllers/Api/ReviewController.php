<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contents;
use App\Models\User;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{

    public function showReviewofContent($id){
        $content = Contents::find($id);
        if(is_null($content)){
            return response([
                'message' => 'Content Not Found'
            ],404);
        }
        $reviews = DB::table('reviews')
        ->join('users', 'reviews.id_user', '=', 'users.id')
        ->join('contents','reviews.id_content','=','contents.id')
        ->select('reviews.*', 'users.handle as Reviewer',)
        ->where('reviews.id_content', $id)
        ->get();

        return response([
            'message' => 'Review of '.$content->title,
            'data' => $reviews
        ],200);
    }


    public function store(Request $request){
        $storeData = $request->all();

        $validate = Validator::make($storeData,[
            'id_content' => 'required',
            'comment' => 'required',
        ]);
        if ($validate->fails()) {
            return response(['message'=> "Review must be filled."],400);
        }

        $content = Contents::find($storeData['id_content']);
        if(is_null($content)){
            return response([
                'message' => 'Content Not Found'
            ],404);
        }

        if($content->id_user == Auth::user()->id){
            return response([
                'message' => 'You are not allowed to review your own content'
            ],400);
        }

        $review = Review::create([
            'id_user' => Auth::user()->id,
            'id_content' => $storeData['id_content'],
            'comment' => $storeData['comment'],
        ]);

        return response([
            'message' => 'Review Added',
            'data' => $review
        ],200);
    }

    public function update(Request $request, $id){
        $review = Review::find($id);
        if(is_null($review)){
            return response([
                'message' => 'Review Not Found'
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'comment' => 'required',
        ]);
        if ($validate->fails()) {
            return response(['message'=> $validate->errors()],400);
        }

        $review->comment = $updateData['comment'];
        if($review->save()){
            return response([
                'message' => 'Review Updated',
                'data' => $review
            ],200);
        }
        return response([
            'message' => 'Update Review Failed'
        ],400);
    }

    public function destroy($id){

        $review = Review::find($id);
        if($review->id_user != Auth::user()->id){
            return response([
                'message' => 'You are not allowed to delete this review'
            ],400);
        }

        if(is_null($review)){
            return response([
                'message' => 'Review not Found'
            ],404);
        }

        if($review->delete()){
            return response([
                'message' => 'Review Deleted'
            ],200);
        }
    }
}

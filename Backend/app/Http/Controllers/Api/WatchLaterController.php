<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Contents;
use App\Models\User;
use App\Models\WatchLater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WatchLaterController extends Controller
{
    public function showUserWatchLaterList(Request $request)
    {
        // $user = User::find(Auth::user()->id);
        // $favourites = Favourite::where('id_user', Auth::user()->id)->get();

        // Filter [BONUS]
        $filter = $request->query('filter');
        $filterQuery = ["", ""]; // WHERE x
        switch ($filter) {
            case 'today':
                $filterQuery = ["date_added", date("Y-m-d")];
                break;
            case 'yesterday':
                $filterQuery = ["date_added", date("Y-m-d", strtotime("-1 day"))];
                break;
            case 'this_month':
                $filterQuery = ["date_added", date("Y-m")];
                break;
            case "this_year":
                $filterQuery = ["date_added", date("Y")];
                break;
        }

        $wlListQuery = DB::table('watch_laters')
            ->join('contents', 'watch_laters.id_content', '=', 'contents.id')
            ->join('users', 'contents.id_user', '=', 'users.id')
            ->select('contents.*', 'users.handle as Content Creator', 'watch_laters.date_added as DateAdded')
            ->where('watch_laters.id_user', Auth::user()->id)
            ->orderBy('watch_laters.date_added', 'desc');

        if ($filterQuery[0] != "") {
            $wlListQuery = $wlListQuery->whereDate($filterQuery[0], $filterQuery[1]);
        }

        $wlList = $wlListQuery->get();

        return response([
            'message' => Auth::user()->handle . '\'s Watch Later List',
            'data' => $wlList
        ], 200);
    }

    public function store(Request $request)
    {
        $storeData = $request->all();

        $validate = Validator::make($storeData, [
            'id_content' => 'required',
        ]);
        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $user = User::find(Auth::user()->id);
        if (is_null($user)) {
            return response([
                'message' => 'User Not Found'
            ], 404);
        }
        $content = Contents::find($storeData['id_content']);
        if (is_null($content)) {
            return response([
                'message' => 'Content Not Found'
            ], 404);
        }
        $checkContent = WatchLater::where('id_user', Auth::user()->id)->where('id_content', $storeData['id_content'])->first();
        if (!is_null($checkContent)) {
            return response([
                'message' => 'Content already in your Watch Later List'
            ], 404);
        }

        if ($content->id_user == Auth::user()->id) {
            return response([
                'message' => 'Can\'t add your own content to Watch Later'
            ], 400);
        }

        $wlList = WatchLater::create([
            'id_user' => $user->id,
            'id_content' => $content->id,
            'date_added' => date("Y-m-d H:i:s"),
        ]);
        return response([
            'message' => 'Added to Your Watch Later List',
            'data' => $wlList
        ], 200);
    }

    public function deletefromWatchLater($id)
    {

        $content = Contents::find($id);
        if (is_null($content)) {
            return response([
                'message' => 'Content Not Found'
            ], 404);
        }

        $favourite = WatchLater::where('id_user', Auth::user()->id)->where('id_content', $id)->first();
        if (is_null($favourite)) {
            return response([
                'message' => 'No such contents in your Watch Later List'
            ], 404);
        }
        $favourite->delete();
        return response([
            'message' => 'Removed from Your Watch Later List',
            'data' => $favourite
        ], 200);
    }
}

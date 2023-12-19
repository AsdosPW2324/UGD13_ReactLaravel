<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Contents;
use App\Models\Komentar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KomentarController extends Controller
{
    public function showCommentofContent($id)
    {
        $content = Contents::find($id);
        if (is_null($content)) {
            return response([
                'message' => 'Konten tidak ditemukan'
            ], 404);
        }
        $komentars = DB::table('komentars')
            ->join('users', 'komentars.id_user', '=', 'users.id')
            ->join('contents', 'komentars.id_content', '=', 'contents.id')
            ->select('komentars.*', 'users.handle as Reviewer', 'komentars.date_added as DateAdded')
            ->where('komentars.id_content', $id)
            ->get();

        return response([
            'message' => 'Komentar dari ' . $content->title,
            'data' => $komentars
        ], 200);
    }


    public function store(Request $request)
    {
        $storeData = $request->all();

        $validate = Validator::make($storeData, [
            'id_content' => 'required',
            'comment' => 'required',
        ]);
        if ($validate->fails()) {
            return response(['message' => "Komentar Tidak Boleh Kosong."], 400);
        }

        $content = Contents::find($storeData['id_content']);
        if (is_null($content)) {
            return response([
                'message' => 'Konten tidak ditemukan'
            ], 404);
        }

        $komentar = Komentar::create([
            'id_user' => Auth::user()->id,
            'id_content' => $storeData['id_content'],
            'comment' => $storeData['comment'],
            'date_added' => date("Y-m-d H:i:s"),
        ]);

        return response([
            'message' => 'Komentar berhasil ditambahkan',
            'data' => $komentar
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $komentar = Komentar::find($id);
        if (is_null($komentar)) {
            return response([
                'message' => 'Komentar tidak ditemukan'
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'comment' => 'required',
        ]);
        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $komentar->date_added = date("Y-m-d H:i:s");
        $komentar->comment = $updateData['comment'];
        if ($komentar->save()) {
            return response([
                'message' => 'Komentar berhasil diupdate',
                'data' => $komentar
            ], 200);
        }
        return response([
            'message' => 'Komentar gagal diupdate'
        ], 400);
    }

    public function destroy($id)
    {
        $komentar = Komentar::find($id);
        if ($komentar->id_user != Auth::user()->id) {
            return response([
                'message' => 'Kamu tidak diperbolehkan menghapus komentar orang lain'
            ], 400);
        }

        if (is_null($komentar)) {
            return response([
                'message' => 'Komentar tidak ditemukan'
            ], 404);
        }

        if ($komentar->delete()) {
            return response([
                'message' => 'Komentar berhasil dihapus'
            ], 200);
        }
    }
}

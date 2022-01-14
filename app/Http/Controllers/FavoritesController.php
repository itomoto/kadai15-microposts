<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    //
    public function store($id) {
        
        //add favorite
        //認証済みのユーザが、idの投稿をお気に入り登録する
        \Auth::user()->favorite($id);
        
        return back();
    }
    
    public function destroy($id) {
        \Auth::user()->unfavorite($id);
        
        return back();
    }
}

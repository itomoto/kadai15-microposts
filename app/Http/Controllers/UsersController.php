<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class UsersController extends Controller
{
    //
    public function index() {
        //ユーザー一覧をidの降順で取得
        $users = User::orderBy('id', 'desc') ->paginate(10);
        
        //ユーザー一覧ビューでそれを表示
        return view('users.index', ['users' => $users,]);
    }
    
    public function show($id)
    {
        //idの値でユーザーを検索して取得
        $user = User::findOrFail($id);
        
        $user->loadRelationshipCounts();
        
        $microposts = $user->microposts()->orderBy('created_at','desc')->paginate(10);
        
        return view('users.show', ['user' => $user,
            'microposts' => $microposts,
            ]);
    }
    
    public function followings($id) {
        //idの値でユーザを検索して取得
        $user = User::findOrFail($id);
        
        //関係するモデルの件数をロード
        $user->loadRelationshipCounts();
        
        //ユーザフォローの一覧を取得
        $followings = $user->followings()->paginate(10);
        
        return view('users.followings', [
            'user' => $user,
            'users' => $followings,
            ]);
    }
    
    public function followers($id) {
        
        $user = User::findOrFail($id);
        
        $user->loadRelationshipCounts();
        
        $followers =$user->followers()->paginate(10);
        
        return view('users.followers', [
            'user' => $user,
            'users' => $followers,
            ]);
    }
}
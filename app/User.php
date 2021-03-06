<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    
    public function microposts() {
        return $this->hasMany(Micropost::class);
    }
    
    public function followings() {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    public function followers() {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function loadRelationshipCounts() {
        $this->loadCount(['microposts', 'followings', 'followers', 'favorites']);
    }
    
    public function follow($userId) {
        //すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        //対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me) {
            //すでにフォローしていれば何もしない
            return false;
        } else {
            //未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId) {
        //すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        //対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;
        
        if ($exist && !$its_me) {
            //すでにフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            //未フォローであれば何もしない
            return false;
        }
    }
    
    public function is_following($userId) {
        //フォロー中のユーザの中に、$userIDのものが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts() {
        $userIds = $this->followings()->pluck('users.id')->toArray();
        
        $userIds[] = $this->id;
        
        return Micropost::whereIn('user_id', $userIds);
    }
    
    public function favorites() {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();      
    }
    
    public function favorite($micropostIds) {
        $exist = $this->is_favorite($micropostIds);
        $its_me = $this->id == $micropostIds;
        
        if ($exist || $its_me) {
            // すでにお気に入り追加していれば何もしない
            return false;
        } else {
            // お気に入り追加であればフォローする
            $this->favorites()->attach($micropostIds);
            return true;
        }
        
    }
    
    public function unfavorite($micropostIds) {
        //すでにお気に入りしているかの確認
        $exist = $this->is_favorite($micropostIds);
        //対象が自分自身かどうかの確認
        $its_me = $this->id == $micropostIds;
        
        if ($exist && !$its_me) {
            //すでにお気に入りしていればフォローを外す
            $this->favorites()->detach($micropostIds);
            return true;
        } else {
            //未お気に入りであれば何もしない
            return false;
        }
        
    }
    
        public function is_favorite($micropostIds) {
        
        return $this->favorites()->where('micropost_id', $micropostIds)->exists();
    }

}

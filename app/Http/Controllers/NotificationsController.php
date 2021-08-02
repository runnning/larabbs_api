<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    public function index(){
        //获取所有用户的所有通知
        $notifications=Auth::user()->notifications()->paginate(20);
        //标记为已读,未读数清零
        Auth::user()->markAsRead();
        return view('notifications.index',compact('notifications'));
    }
}

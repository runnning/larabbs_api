@extends('layouts.app')
@section('title',$user->name.'的个人中心')
@section('content')
    <div class="row">
        <div class="col-lg-3 col-md-3 hidden-sm hidden-xs user-info">
            <div class="card">
                <img src="{{$user->avatar}}" alt="{{$user->name}}" class="card-img-top">
                <div class="card-body">
                    <h5><strong>个人简介</strong></h5>
                    <p>{{$user->introduction}}</p>
                    <hr>
                    <h5><strong>注册于</strong></h5>
                    <p>{{$user->created_at->diffForHumans()}}</p>
                    <hr>
                    <h5><strong>最后活跃时间</strong></h5>
                    <p title="{{$user->last_actived_at}}">{{$user->last_actived_at->diffForHumans()}}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-body">
                    <h1 class="mb-0" style="font-size: 22px">{{$user->name}} <small>{{$user->email}}</small></h1>
                </div>
            </div>
            <hr>
            {{-- 用户发布内容--}}
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link  bg-transparent {{active_class(if_query('tab',null))}}" href="{{route('users.show',$user->id)}}">
                                Ta的话题
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link bg-transparent {{active_class(if_query('tab','replies'))}}" href="{{route('users.show',[$user->id,'tab'=>'replies'])}}">
                                Ta的回复
                            </a>
                        </li>
                    </ul>
                    @if(if_query('tab','replies'))
                        @include('users._replies',['replies'=>$user->replies()->with('topic')->recent()->paginate(5)])
                    @else
                        @include('users._topics',['topics'=>$user->topics()->recent()->paginate(5)])
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
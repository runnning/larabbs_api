<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReplyRequest;
use Illuminate\Support\Facades\Auth;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

	public function store(ReplyRequest $request,Reply $reply): \Illuminate\Http\RedirectResponse
  {
	    $reply->content=$request->input('content');
	    $reply->user_id=Auth::id();
	    $reply->topic_id=$request->topic_id;
	    $reply->save();
		  return redirect()->to($reply->topic->link())->with('success', '评论创建成功!');
	}

  /**
   * @throws \Illuminate\Auth\Access\AuthorizationException
   */
  public function destroy(Reply $reply): \Illuminate\Http\RedirectResponse
  {
		$this->authorize('destroy', $reply);
		$reply->delete();

		return redirect()->to($reply->topic->link())->with('success', '删除成功！');
	}
}

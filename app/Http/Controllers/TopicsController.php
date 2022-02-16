<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\Category;
use App\Models\Link;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use Illuminate\Support\Facades\Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request,Topic $topic,User $user,Link $link)
	{
            $topics =$topic->withOrder($request->order)
                ->with('user','category')//预加载防止n+1问题
                ->paginate(20);
            //缓存从读取数据
            $active_users=$user->getActiveUsers();
            $links=$link->getAllCached();

		return view('topics.index', compact('topics','active_users','links'));
	}

    public function show(Request $request,Topic $topic)
    {
        //URL矫正
        if(!empty($topic->slug)&&$topic->slug!==$request->slug){
            return redirect($topic->link(),301);
        }
        return view('topics.show', compact('topic'));
    }

	public function create(Topic $topic)
	{
	    $categories=Category::all();
		  return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function store(TopicRequest $request,Topic $topic): \Illuminate\Http\RedirectResponse
  {
        $topic->fill($request->all());
        $topic->user_id = Auth::id();
        $topic->save();

		return redirect()->to($topic->link())->with('success', '帖子创建成功！');
	}

  /**
   * @throws \Illuminate\Auth\Access\AuthorizationException
   */
  public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
        $categories=Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

  /**
   * @throws \Illuminate\Auth\Access\AuthorizationException
   */
  public function update(TopicRequest $request, Topic $topic): \Illuminate\Http\RedirectResponse
  {
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->to($topic->link())->with('success', '更新成功！');
	}

  /**
   * @throws \Illuminate\Auth\Access\AuthorizationException
   */
  public function destroy(Topic $topic): \Illuminate\Http\RedirectResponse
  {
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('success', '删除成功!');
	}

	public function uploadImage(Request $request,ImageUploadHandler $uploader): array
    {
        //初始化返回数据,默认时失败的
        $data=[
          'success'=>false,
          'msg'=>'上传失败!',
          'file_path'=>''
        ];
        //判断是否有上传文件,并赋予给$file
        if($file=$request->upload_file){
            //保存图片到本地
            $result=$uploader->save($file,'topics',\Auth::id(),1024);
            //保存图片成的话
            if($result){
                $data['file_path']=$result['path'];
                $data['msg']='上传成功!';
                $data['success']=true;
            }
        }
        return $data;
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TopicRequest;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use Illuminate\Http\Request;

class TopicsController extends Controller
{

    public function index()
    {
        //
    }

    public function store(TopicRequest $request,Topic $topic)
    {
        $topic->fill($request->all());
        $topic->user_id=$request->user()->id;
        $topic->save();
        return new TopicResource($topic);
    }


    public function show($id)
    {
        //
    }


    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(TopicRequest $request, Topic $topic): TopicResource
    {
        $this->authorize('update', $topic);

        $topic->update($request->all());

        return new TopicResource($topic);
    }


    public function destroy($id)
    {
        //
    }
}

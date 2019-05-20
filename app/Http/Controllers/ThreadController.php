<?php

namespace App\Http\Controllers;

use App\Filters\ThreadFilters;
use App\Channel;
use App\Thread;
use App\Reply;
use Illuminate\Http\Request;

class ThreadController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth')->only(['create', 'store']);
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel/*$channelSlug = null*/, ThreadFilters $filters)
    {
        //$this->validate(request(), [
        //    'title' => 'required'
        //]);

        //if ($channelSlug) {
        //    //Channel::whereSlug($channelSlug);
        //    //$channelId = Channel::where('slug', $channelSlug)->first();
        //    $channelId = Channel::where('slug', $channelSlug)->first()->id;
        //    $threads = Thread::where('channel_id', $channelId)->latest()->get();
        //    get the id with the slug from the Channel, then get the Channel_id from the slug
        //} else {
        //    $threads = Thread::latest()->get();
        //}

        //added myself
        //$channels = Channel::all();
        //$threads = $this->getThreads($channel);

        //$threads = Thread::filter($filters)->get();
        $threads = $this->getThreads($channel, $filters);

        // interchangeable

        //if ($channel->exists) {
        //    //$threads = $channel->threads()->latest()->get();
        //    $threads = $channel->threads()->latest();
        //} else {
        //    //$threads = Thread::latest()->get();
        //    $threads = Thread::latest();
        //}

            // if request('by'), we should filter by given usename
//        if ($username = request('by')) {
//            $user = \App\User::where('name', $username)->firstOrFail();
//            $threads->where('user_id', $user->id);
//        }

        if (request()->wantsJson()) {
            return $threads;
        }

        return view('threads.index', compact('threads'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //dd(request()->all());

        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'channel_id' => 'required|exists:channels,id' 
        ]);

        $thread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'body' => request('body')
        ]);
        
        return redirect($thread->path())
            ->with('flash', 'Your thread has been published!');
        //dd($thread->path());
    }

    /**
     * Display the specified resource.
     *
     * @param @channelId
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show($channelId, Thread $thread)
    {
        // return $thread;
        // Route Model Binding
        // return $thread->load('replies');
        // dd($thread);
        return view('threads.show', [
            'thread' => $thread,
            'replies' => $thread->replies()
        ]);
        //return view('threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Thread $thread)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy($channel, Thread $thread)
    {
        //this portion is updated in lieu of another method
        //$thread->replies()->delete();
        //if ($thread->user_id != auth()->id()){
        //    //abort(403);
        //    //if (request()->wantsJson()) {
        //    //    return response(['status' => 'Permission Denied'], 403);
        //    //}
        //    //return redirect('/login');
        //    abort(403, 'You do not have permission to do this.');
        //}
        $this->authorize('update', $thread);
        //authorize -> policy -> update -> $thread

        //Reply::where('thread_id', $thread->id)->delete();
        $thread->delete();

        if (request()->wantsJson()) {
            return response([], 204);
        }
    
        return redirect('/threads');
    }

    //protected function getThreads(Channel $channel)
    //{
    //    if ($channel->exists) {
    //        //$threads = $channel->threads()->latest()->get();
    //        $threads = $channel->threads()->latest();
    //    } else {
    //        //$threads = Thread::latest()->get();
    //        $threads = Thread::latest();
    //    }
    //    // if request('by'), we should filter by given usename
    //    if ($username = request('by')) {
    //        $user = \App\User::where('name', $username)->firstOrFail();
    //        $threads->where('user_id', $user->id);
    //    }
    //    $threads = $threads->get();
    //    return $threads;
    //}
    protected function getThreads(Channel $channel, ThreadFilters $filters)
    {
        $threads = Thread::filter($filters)->latest();

        if($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }

        //$threads = $threads->filter($filters)->get();
        
        //dd($threads->toSql());

        return $threads->get();
    }
}

<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Channel;
use Carbon\Carbon;
use App\Rules\SpamFree;
use App\Inspections\Spam;
use Illuminate\Http\Request;
use App\Filters\ThreadFilters;

class ThreadsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

     public function index(Channel $channel, ThreadFilters $filters)
    {
        $threads = $this->getThreads($channel, $filters);
        
        if (request()->wantsJson()) {
            return $threads;
        }        
        

        return view('threads.index', [
            'threads' => $threads
            
        ]);
    }

/**
     * Fetch all relevant threads.
     *
     * @param Channel       $channel
     * @param ThreadFilters $filters
     * @return mixed
     */
    protected function getThreads(Channel $channel, ThreadFilters $filters)
    {
        $threads = Thread::latest()->filter($filters);

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }
        return $threads->paginate(25);
        // return $threads->paginate(25);
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
    public function store(Request $request, Spam $spam)
    {
        $this->validate($request, [
            'title' => ['required', new SpamFree],
            'body' => ['required', new SpamFree],
            'channel_id' => 'required|exists:channels,id'            
            ]);
        
        $spam->detect(request('body'));

        $thread = Thread::create([
            'title' => request('title'),
            'body'  => request('body'),
            'user_id' => auth()->id(),
            'channel_id' => request('channel_id'),
            ]);
        // dd($thread);
      // dd($thread->path());
        return redirect($thread->path())
                ->with('flash', 'Your thread has been published!');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show($channelId, Thread $thread)
    {
        // dd($thread->replies);
        $key = sprintf("users.%s.visits.%s", auth()->id(), $thread->id);

        cache()->forever($key, Carbon::now());
        
        return view('threads.show', compact('thread'));
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
        $this->authorize('update', $thread);
        
        if($thread->user_id != auth()->id()){
            if(request()->wantsJson()){
                return response(['status' => 'Permission denied!', 403]);
            }

            return redirect('/login');
        }

        // $thread->replies()->delete();
        $thread->delete();

        if(request()->wantsJson()){

            return response([], 204);
        }

        return redirect('/threads');
    }
}

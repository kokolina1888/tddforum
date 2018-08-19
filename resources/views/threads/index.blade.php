@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Forum Threads</div>

                <div class="panel-body">
                    @forelse($threads as $thread)
                    
                    <article>
                        <h4>
                           <a href= "{{ url($thread->path())}}">
                           @if($thread->hasUpdatesFor(auth()->user()))

                            <strong>
                                 {{ $thread->title }}
                            </strong>
        
                           @else 

                            {{ $thread->title }} 

                           @endif
                           </a>
                        </h4>
                        <a href="{{ $thread->path() }}">{{ $thread->replies_count}} {{ str_plural('reply', $thread->replies_count )}}</a>
                        <div class="body">
                            {{ $thread->body }} 
                        </div>
                    </article>
                    <hr>
                    @empty
                        <p>
                            There are no relevant results at this time.
                        </p>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

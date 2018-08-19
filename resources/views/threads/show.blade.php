@extends('layouts.app')

@section('content')
<thread-view :initial-replies-count="{{ $thread->replies_count}}" inline-template>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="flex">
                        <a href='{{ url("/profiles/{$thread->creator->name}") }}'>
                            {{ $thread->creator->name }} 
                        </a> 
                        posted:
                        {{ $thread->title }}
                    </span>
                    @can('update', $thread)
                    <form method="POST" action="{{ url($thread->path()) }}">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button class="btn btn-default" type="submit">Delete Thread</button>
                    </form>
                    @endcan
                </div>

                <div class="panel-body">

                    {{ $thread->body }}

                </div>
            </div>
            <replies @added="repliesCount++" @removed="repliesCount--"></replies>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
            <div class="panel-body">
                <p>This thread was published {{ $thread->created_at->diffForHumans() }} by                 
                    <a href='{{ url("/profiles/{$thread->creator->name}") }}'>
                        {{ $thread->creator->name }} 
                    </a>
                    and currently has <span v-text="repliesCount"></span> {{ str_plural('comment', $thread->reply_count) }}.
                </p>
                <p>
                    <subscribe-button :active="{{ json_encode($thread->isSubscribedTo)}}"></subscribe-button>                    
                </p>
            </div>
        </div>
    
   </div>
</div>
</div>
</thread-view>
@endsection

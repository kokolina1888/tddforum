@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Create New Thread</div>

                <div class="panel-body">
                   <form action="{{ route('threads.store') }}" method="POST">
                   {{ csrf_field() }}
                   <div class="form-group">
                                <label for="channel_id">Choose a Channel:</label>
                                <select name="channel_id" id="channel_id" class="form-control" required>
                                    <option value="">Choose One...</option>

                                    @foreach ($channels as $channel)
                                        <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>
                                            {{ $channel->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                       <div class="form-group">
                           <label for="title">
                               Title:
                           </label>
                           <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}">
                       </div>
                       <div class="form-group">
                           <label for="body">
                               Body:
                           </label>
                           <textarea id="body" name="body" class="form-control" rows="8">
                           {{ old('body') }}
                           </textarea>
                       </div>
                       <button class="btn btn-default">Submit</button>
                       @if(count($errors))
                       <ul class="alert alert-danger">
                         @foreach($errors->all() as $error)
                          {{ $error }}
                         @endforeach
                       </ul>
                       @endif
                   </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <form action="{{ url('post') }}" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <textarea class="form-control" name="content" rows="2" placeholder="Your complaint goes here...">{{ old('content') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm pull-right clearfix" name="button">Post</button>
                            <div class="clearfix"></div>
                        </form>
                    </div>

                    <div class="panel-body">
                        @if ($posts->count() > 0)
                            @foreach ($posts as $post)
                                <div class="media">
                                    <div class="media-left media-middle">
                                        <a href="{{ url('u/' . $post->user->username ) }}">
                                            <img class="media-object" src="{{ $post->user->gravatar_url }}" alt="foto de perfil de {{ $post->user->name }}">
                                        </a>
                                    </div>
                                    <div class="media-body">
                                        <h3 class="media-heading">{{ $post->content }}</h3>
                                        <p>
                                            <a href="{{ url('post/' . $post->id ) }}">{{ $post->created_at->diffForHumans() }}</a> by <a href="{{ url('u/' . $post->user->username ) }}">{{ '@' . $post->user->username }}</a>
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <h2>No posts yet.</h2>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

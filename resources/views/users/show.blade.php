@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">

                    <div class="row">
                        <div class="col-md-2">
                            <img src="{{ $user->gravatar_url }}" alt="Foto de perfil de {{ $user->name }}" class="img-circle" style="width: 100%"/>
                        </div>
                        <div class="col-md-8">
                            <h2 style="margin-top: 0px">{{ $user->name }}</h2>
                            <h3 style="margin-top: 0px">{{ '@' . $user->username }}</h3>
                            <h4 style="margin-top: 0px">{{ $user->followers->count() }} {{ str_plural('follower', $user->followers->count())}}. Member since {{ $user->created_at->format('d/m/y') }}.</h4>
                        </div>
                        <div class="col-md-2">
                            @if (Auth::user())
                                @if ($user->id != Auth::user()->id)
                                    @if ($user->isFollower(Auth::user()->id))
                                        <form action="{{ url('u/' . $user->id . '/unfollow') }}" method="post">
                                            {{ csrf_field() }}
                                            <button type="submit" name="button" class="btn btn-block btn-primary">Following</button>
                                        </form>
                                    @else
                                        <form action="{{ url('u/' . $user->id . '/follow') }}" method="post">
                                            {{ csrf_field() }}
                                            <button type="submit" name="button" class="btn btn-block btn-primary">Follow</button>
                                        </form>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>

                </div>

                <div class="panel-body">
                    @if ($user->posts->count() > 0)
                        @foreach ($user->posts as $post)
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
                        <h2 class="text-center">No posts yet.</h2>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

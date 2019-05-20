@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="page-header">
                <h1>
                    {{ $profileUser->name }}
                    <small>Since {{ $profileUser->created_at->diffForHumans() }}</small>
                </h1>
            </div>
            {{-- @foreach ($profileUser->threads as $thread) --}}
            {{-- @foreach ($threads as $thread) --}}

            @foreach ($activities as $activity)
                @if (view()->exists("profiles.activities.{$activity->type}"))
                @include ("profiles.activities.{$activity->type}")
                @else <p>There is no activity for this user yet.</p>
                @endif
            @endforeach
                {{-- $threads->links() --}}


        </div>
    </div>


</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">

                    @if($user->user_type_id == 1)
                        You are an Athlete!
                        <p> {{$user}} </p>
                        <p> {{$team}} </p>
                        <p> {{$season}} </p>
                        <p> {{$org}} </p>

                        @foreach($events as $event)
                            <p> {{$event}} </p>
                        @endforeach

                        <p> Your parent is: {{$parentUser->name}} </p>

                        @foreach($runs as $run)
                            <p> {{$run}} </p>
                        @endforeach
                    @endif

                    @if($user->user_type_id == 2)
                        You are a Parent!
                        <p> {{$user}} </p>
                        
                        @foreach($childArray as $items)
                            @foreach($items as $item)
                                <p> {{$item}} </p>
                            @endforeach
                        @endforeach

                    @endif

                    @if($user->user_type_id == 3)
                        You are a Coach!
                        <p> {{$user}} </p>
                        <p> {{$team}} </p>
                        <p> {{$season}} </p>
                        <p> {{$events}} </p>

                        @foreach($teamArray as $items)
                            @foreach($items as $item)
                                <p> {{$item}} </p>
                            @endforeach
                        @endforeach

                    @endif

                    @if($user->user_type_id == 4)
                        You are a Head Coach!
                        <p> {{$user}} </p>
                        <p> {{$org}} </p>
                        
                        @foreach($seasons as $season)
                            <p> {{$season}} </p>
                        @endforeach

                        @foreach($teamArray as $items)
                            @foreach($items as $item)
                                <p> {{$item}} </p>
                            @endforeach
                        @endforeach             

                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

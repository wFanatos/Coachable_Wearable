@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">

                       <h1> User Info </h1>
                       <p> Name: {{$collection[0]->name}}</p>
                       <p> Email: {{$collection[0]->email}}</p>

                       <h1> Device Info </h1>
                       <p> Device in use: {{$collection[1]->device_name}}</p>

                       <h1> Team Info </h1>
                       <p> Apart of team: {{$collection[2]->name}}</p>

                       <h1> Season Info </h1>
                       <p> Current Season: {{$collection[3]->season_name}}</p>
                       <p> Description: {{$collection[3]->season_description}}</p>
                       <p> Start of Season: {{$collection[3]->season_start}}</p>
                       <p> End of Season: {{$collection[3]->season_end}}</p>

                       <h1> Org Info </h1>
                       <p> Organization: {{$collection[4]->org_name}}</p>

                       <h1> Parent Info </h1>
                       @foreach($collection[5] as $items)
                            @foreach($items as $parent)
                                <p> Parent: {{$parent->name}} </p>
                            @endforeach
                       @endforeach

                       <h1> Event Info </h1>
                       <!-- Loop through each event/run pairing -->
                       @for($i = 0; $i < count($collection[6]); $i++)
                            <h2> Event: </h2>
                            <p>{{$collection[6][$i][0]}} </p>

                            @foreach($collection[6][$i][1] as $run)
                                <h2> Run: </h2>
                                <p> {{$run}} </p>
                            @endforeach
                       @endfor                              
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

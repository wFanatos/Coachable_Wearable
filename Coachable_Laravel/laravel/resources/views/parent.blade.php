@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">

                      <h1> Parent Info </h1>
                       <p> Name: {{$collection[0]->name}}</p>
                       <p> Email: {{$collection[0]->email}}</p>

                       <h1> Children Info </h1>
                  
                       @foreach($collection[1] as $children)
                            <h2> Child </h2>
                            <p> Name: {{$children[0]->name}}
                            <p> Team: {{$children[1]->name}}

                            <h3> Season Info </h3>
                            <p> Current Season: {{$children[2]->season_name}}</p>
                            <p> Description: {{$children[2]->season_description}}</p>
                            <p> Start of Season: {{$children[2]->season_start}}</p>
                            <p> End of Season: {{$children[2]->season_end}}</p>

                            <h3> Event Info </h3>
                            <!-- Loop through each event/run pairing -->
                            @for($i = 0; $i < count($children[3]); $i++)
                                <h4> Event: </h4>
                                <p>{{$children[3][$i][0]}} </p>

                                @foreach($children[3][$i][1] as $run)
                                    <h4> Run: </h4>
                                    <p> {{$run}} </p>
                                @endforeach
                            @endfor                        
                        @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

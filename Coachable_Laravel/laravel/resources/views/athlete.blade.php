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

                       @if($collection[1] == null)
                            <p> No device connected to user</p>
                        @else
                            <p> Device in use: {{$collection[1]->device_name}}</p>
                        @endif

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

                       <div class="accordion" id="accordionExample">
                       <!-- Loop through each event/run pairing -->
                       @for($i = 0; $i < count($collection[6]); $i++)                      
                            <div class="card">
                                <div class="card-header" id="heading{{$i}}">
                                    <h2 class="mb-0">
                                        <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#collapse{{$i}}">
                                            <h2> Event: </h2>
                                            <p>{{$collection[6][$i][0]->event_name}} </p>
                                            <p>Date: {{$collection[6][$i][0]->event_date}} </p>
                                        </button>									
                                    </h2>
                                </div>
                                <div id="collapse{{$i}}" class="collapse" aria-labelledby="heading{{$i}}" data-parent="#accordionExample">
                                    <div class="card-body">
                                        @foreach($collection[6][$i][1] as $run)
                                            <!-- PULL OUT RUN DETAILS HERE-->
                                            <h2> Run: </h2>
                                            <p> Length of run: {{$run->duration}} </p>
                                            <p> Run started: {{$run->start_time}} </p>
                                            <p> Run ended: {{$run->end_time}} </p>
                                            <p> Start altitude: {{$run->start_altitude}} </p>
                                            <p> End altitude: {{$run->end_altitude}} </p>
                                            <p> Average speed: {{$run->avg_speed}} </p>
                                            <p> Distance from start to end: {{$run->distance}} </p>
                              
                                            <!-- DECODE JSON AND PUT ALL INFO INTO GRAPH -->
                                            @php
                                                $dataObj = json_decode($run->other_data);                               
                                            @endphp
                                
                                            @foreach($dataObj as $dataEntry)
                                                <h3> Other Data </h3>
                                                <p> Latitude: {{$dataEntry->Latitude}} </p>
                                                <p> Longitude: {{$dataEntry->Longitude}} </p>
                                                <p> Speed: {{$dataEntry->Speed}} </p>
                                                <p> Altitude: {{$dataEntry->Altitude}} </p>
                                                <p> Time: {{$dataEntry->Time}} </p>
                                            @endforeach                            
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        @endfor                                                                                                                                                                                                  
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

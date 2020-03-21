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

                    <h1> Team Info </h1>
                    <p> Apart of team: {{$collection[1]->name}}</p>

                    <h1> Season Info </h1>
                    <p> Current Season: {{$collection[2]->season_name}}</p>
                    <p> Description: {{$collection[2]->season_description}}</p>
                    <p> Start of Season: {{$collection[2]->season_start}}</p>
                    <p> End of Season: {{$collection[2]->season_end}}</p>

                    @foreach($collection[3] as $member)
                        
                        <h2> Team Member Info </h2>

                        <h3> User </h3>
                        <p> Name: {{$member[1]->name}}</p>
                        <p> Email: {{$member[1]->email}}</p>

                        <h3> Device Info </h3>
                        <p> Device in use: {{$member[0]->device_name}}</p>
                   
                        @for($i = 0; $i < count($member[2]); $i++)
                            <h2> Event: </h2>
                            <p>{{$member[2][$i][0]->event_name}} </p>
                            <p>Date: {{$member[2][$i][0]->event_date}} </p>

                            @foreach($member[2][$i][1] as $run)
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
                        @endfor       
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

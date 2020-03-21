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
                                <p>{{$children[3][$i][0]->event_name}} </p>
                                <p>{{$children[3][$i][0]->event_date}} </p>

                                @foreach($children[3][$i][1] as $run)
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

@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Home</div>
                <div class="card-body">
                    
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-overview-tab" data-toggle="pill" href="#pills-overview" role="tab" aria-controls="pills-overview" aria-selected="true">Overview</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-roster-tab" data-toggle="pill" href="#pills-roster" role="tab" aria-controls="pills-roster" aria-selected="false">Roster</a>
                        </li>                        
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-overview" role="tabpanel" aria-labelledby="pills-overview-tab">
                            <div class="accordion" id="accordionExample">
                                @for($i = 0; $i < count($collection[0]); $i++)      
                                    <div class="card text-center">
                                        <div class="card-header" id="heading{{$i}}">
                                            <h2 class="mb-0">
                                                <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#collapse{{$i}}">
                                                    <h2> Event: {{$collection[0][$i][0][0]->event_name}} </h2>
                                                    <p>Date: {{$collection[0][$i][0][0]->event_date}} </p>
                                                </button>                                       								
                                            </h2>
                                        </div>
                                        <div id="collapse{{$i}}" class="collapse" aria-labelledby="heading{{$i}}" data-parent="#accordionExample">
                                            <div class="card-body">
                                                <div class ="accordion" id="runExample">
                                                    @for($j = 0; $j < count($collection[0][0][0][1]); $j++)
                                                    <div class="card text-center">
                                                        <div class="card header" id="heading2{{$j}}">
                                                            <h2 class="mb-0">
                                                                <button type="button" class="btn btn-link" data-toggle="collapse" data-target= "#collapse2{{$j}}">
                                                                    <h2> Summary of Run {{$j + 1}} </h2>
                                                                </button>
                                                            </h2>
                                                        </div>

                                                        <div id="collapse2{{$j}}" class="collapse" aria-labelledby="heading2{{$j}}" data-parent="#runExample">
                                                            <div class="card-body">
                                                                @php
                                                                    $curRun = $collection[0][0][0][1][$j];
                                                                @endphp

                                                                <h2>Summary of Run </h2>
                                                                <p>Distance Travelled: {{$curRun->distance}}km </p>
                                                                <p>Average Speed: {{$curRun->avg_speed}}km/h
                                                                <p>Duration: {{$curRun->duration}} </p>                                                   
                                                                <button class="btn btn-primary">Detailed Info </button>
                                                            </div>
                                                        </div>                 
                                                    </div>  
                                                    @endfor 
                                                </div>                                  
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-roster" role="tabpanel" aria-labelledby="pills-roster-tab">
                            <div class="accordion" id="rosterExample">
                                @for($k = 0; $k < count($collection[1]); $k++)                      
                                    <div class="card text-center">
                                        <div class="card-header" id="heading3{{$i}}">
                                            <h2 class="mb-0">
                                                <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#collapse3{{$i}}">
                                                    <h2> {{$collection[1][$k][0]->name}} </h2>
                                                </button>                                       								
                                            </h2>
                                        </div>
                                        <div id="collapse3{{$i}}" class="collapse" aria-labelledby="heading3{{$i}}" data-parent="#rosterExample">
                                            <div class="card-body">
                                                @foreach($collection[1][$k][1] as $user)
                                                    <p>{{$user->name}} - {{$user->user_type_id}} </p>                                                                                      
                                                @endforeach                                       
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

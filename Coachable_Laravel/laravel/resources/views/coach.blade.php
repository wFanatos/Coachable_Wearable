@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Team Overview</div>
            <div class="accordion" id="teamAccordion">
                @for($i = 0; $i < count($collection); $i++)                      
                    <div class="card text-center">
                        <div class="card-header" id="heading{{$i}}" style="background-color: #6dcdeb;">
                            <h2 class="mb-0">
                                <button type="button" class="btn" data-toggle="collapse" data-target="#collapse{{$i}}">
                                    <h2> {{$collection[$i][0]->name}} </h2>
                                </button>                                                                       
                            </h2>
                        </div>
                        <div id="collapse{{$i}}" class="collapse" aria-labelledby="heading{{$i}}" data-parent="#teamAccordion">
                            <div class ="accordion" id="eventAccordion">
                                @for($j = 0; $j < count($collection[$i][1]); $j++)
                                    <div class="card text-center">
                                        <div class="card header" id="heading2{{$j}}" style="background-color: #0191C8;">
                                            <h2 class="mb-0">
                                                <button type="button" class="btn" data-toggle="collapse" data-target= "#collapse2{{$j}}">
                                                    <h3>Event: {{$collection[$i][1][$j][0]->event_name}}</h3>
                                                    <h6>Date: {{$collection[$i][1][$j][0]->event_date}}</h6>
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapse2{{$j}}" class="collapse" aria-labelledby="heading2{{$j}}" data-parent="#eventAccordion">
                                            <div class ="accordion" id="memberAccordion">
                                                @for($k = 0; $k < count($collection[$i][1][$j][1]); $k++)
                                                    <div class="card text-center">
                                                        <div class="card header" id="heading3{{$k}}" style="background-color: #74C2E1;">
                                                            <h2 class="mb-0">
                                                                <button type="button" class="btn" data-toggle="collapse" data-target= "#collapse3{{$k}}">
                                                                    <h4> {{$collection[$i][1][$j][1][$k][0]->name}}</h4>
                                                                </button>
                                                            </h2>
                                                        </div>
                                                        <div id="collapse3{{$k}}" class="collapse" aria-labelledby="heading3{{$k}}" data-parent="#memberAccordion">
                                                            <div class="card-body">
                                                                @php
                                                                    $totalDistance = 0;
                                                                @endphp
                                                                @foreach($collection[$i][1][$j][1][$k][1] as $run)
                                                                    @php
                                                                        $totalDistance = $totalDistance + $run->distance;
                                                                    @endphp
                                                                @endforeach
                                                                <p> Total distance travelled: {{$totalDistance}} km</p>
                                                                <p> Total # of Runs: {{$collection[$i][1][$j][1][$k][2]}} <p>
                                                                <a class="btn btn-primary" href="{{ route('event', ['eventid' => $collection[$i][1][$j][0]->id, 'userid' => $collection[$i][1][$j][1][$k][0]->id]) }}">
                                                                    Detailed Info
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>  
                                                @endfor 
                                            </div>
                                        </div>
                                    </div>
                                @endfor 
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>
@endsection

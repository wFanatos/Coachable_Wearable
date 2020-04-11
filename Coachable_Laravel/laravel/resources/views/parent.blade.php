@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Children Dashboard</div>
                <div class="accordion" id="childAccordion">
                    @for($i = 0; $i < count($collection); $i++)                      
                        <div class="card text-center">
                            <div class="card-header" id="heading{{$i}}" style="background-color: #6dcdeb;">
                                <h2 class="mb-0">
                                    <button type="button" class="btn" data-toggle="collapse" data-target="#collapse{{$i}}" style="width:100%">
                                        <h2>{{$collection[$i][0]->name}}</h2>
                                    </button>                                                                       
                                </h2>
                            </div>
                            <div id="collapse{{$i}}" class="collapse" aria-labelledby="heading{{$i}}" data-parent="#childAccordion">
                                <div class="accordion" id="eventAccordion">
                                    @for($j = 0; $j < count($collection[$i][2]); $j++)                      
                                        <div class="card text-center">
                                            <div class="card-header" id="heading2{{$j}}" style="background-color: #0191C8;">
                                                <h2 class="mb-0">
                                                    <button type="button" class="btn" data-toggle="collapse" data-target="#collapse2{{$j}}" style="width:100%">
                                                        <h3>Event: {{$collection[$i][2][$j][0]->event_name}}</h3>
                                                        <h6>Date: {{$collection[$i][2][$j][0]->event_date}}</h6>
                                                    </button>                                                                       
                                                </h2>
                                            </div>
                                            <div id="collapse2{{$j}}" class="collapse" aria-labelledby="heading2{{$j}}" data-parent="#eventAccordion">
                                                <div class="card-body">                                                           
                                                    @php
                                                        $totalDistance = 0;
                                                    @endphp
                                                    @foreach($collection[$i][2][$j][1] as $run)
                                                        @php
                                                            $totalDistance = $totalDistance + $run->distance;
                                                        @endphp
                                                    @endforeach
                                                    @if ($collection[$i][2][$j][2] > 0)
                                                        <p> Total # of Runs: {{$collection[$i][2][$j][2]}} </p>
                                                        <p> Total distance travelled: {{$totalDistance}} km</p>
                                                        <a class="btn btn-primary" href="{{ route('event', ['eventid' => $collection[$i][2][$j][0]->id, 'userid' => $collection[$i][0]->id]) }}">
                                                            Detailed Info
                                                        </a>
                                                    @else
                                                        <p> There are currently no runs for this event. </p>
                                                    @endif
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
</div>
@endsection

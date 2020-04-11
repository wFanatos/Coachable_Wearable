@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills mb-3 nav-justified" id="pills-tab" role="tablist">
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
                                @for($i = 0; $i < count($collection[0][0]); $i++)
                                    <div class="card text-center">
                                        <div class="card-header" id="heading{{$i}}" style="background-color: #6dcdeb;">
                                            <h2 class="mb-0">
                                                <button type="button" class="btn" data-toggle="collapse" data-target="#collapse{{$i}}" style="width:100%">
                                                    <h2>Event: {{$collection[0][0][$i][0]->event_name}}</h2>
                                                    <h6>Date: {{$collection[0][0][$i][0]->event_date}}</h6>                                                  
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapse{{$i}}" class="collapse" aria-labelledby="heading{{$i}}" data-parent="#accordionExample">
                                            <div class="card text-center">
                                                <div class="card-body">                                                           
                                                    @php
                                                        $totalDistance = 0;
                                                    @endphp
                                                    @foreach($collection[0][0][$i][1] as $run)
                                                        @php
                                                            $totalDistance = $totalDistance + $run->distance;
                                                        @endphp
                                                    @endforeach
                                                    @if ($collection[0][0][$i][2] > 0)
                                                        <p> Total # of Runs: {{$collection[0][0][$i][2]}} </p>
                                                        <p> Total distance travelled: {{$totalDistance}} km</p>
                                                        <a class="btn btn-primary" href="{{ route('event', ['eventid' => $collection[0][0][$i][0]->id, 'userid' => $id]) }}">
                                                            Detailed Info
                                                        </a>
                                                    @else
                                                        <p> There are currently no runs for this event. </p>
                                                    @endif
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
                                        <div class="card-header" id="heading3{{$k}}" style="background-color: #6dcdeb;">
                                            <h2 class="mb-0">
                                                <button type="button" class="btn" data-toggle="collapse" data-target="#collapse3{{$k}}">
                                                    <h2> {{$collection[1][$k][0]->name}} </h2>
                                                </button>                                                                       
                                            </h2>
                                        </div>
                                        <div id="collapse3{{$k}}" class="collapse" aria-labelledby="heading3{{$k}}" data-parent="#rosterExample">
                                            <div class="card-body">
                                                @foreach($collection[1][$k][1] as $user)
                                                    @if ($user->user_type_id == 1)
                                                        <p>{{$user->name}} - Athlete</p>
                                                    @elseif ($user->user_type_id == 3)
                                                        <p>{{$user->name}} - Coach</p>
                                                    @endif
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

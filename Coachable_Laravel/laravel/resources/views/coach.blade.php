@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Team Overview</div>
            <div class="card-body">
                <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
                    @for($i = 0; $i < count($collection); $i++)
                        <li class="nav-item">
                            <a class="nav-link @if ($i == 0) active @endif" id="team{{$i}}-tab" data-toggle="tab" href="#team{{$i}}-tab" role="tab" aria-controls="team{{$i}}-tab" aria-selected="@if ($i == 0) true @else false @endif">{{$collection[$i][0]->name}}</a>
                        </li>
                    @endfor
                </ul>
                <div class="tab-content" id="v-pills-tabContent">
                    @for($i = 0; $i < count($collection); $i++)
                        <div class="tab-pane fade @if($i == 0) show active @endif" id="team{{$i}}-panel" role="tabpanel" aria-labelledby="team{{$i}}-tab">
                            <div class="card text-center">
                                <div class="card-body">
                                    <ul class="nav flex-row nav-pills mb-3 justify-content-center" id="dropdown{{$i}}" role="tablist">
                                        <li class="nav-item dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="team{{$i}}Button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                               Events
                                            </button>
                                            <div class="dropdown-menu nav nav-tabs" aria-labelledby="team{{$i}}Button">                                    
                                                @for($j = 0; $j < count($collection[$i][1]); $j++)
                                                    <a class="dropdown-item" data-toggle="tab" href="#team{{$i}}event{{$j}}">{{$collection[$i][1][$j][0]->event_name}}</a>
                                                @endfor
                                            </div>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        @for($j = 0; $j < count($collection[$i][1]); $j++)
                                        <div id="team{{$i}}event{{$j}}" class="tab-pane fade @if($j == 0) show active @endif" role="tabpanel">
                                            <h2 class="mb-0">
                                                <h2>Event: {{$collection[$i][1][$j][0]->event_name}}</h3>
                                                <h5>Date: {{$collection[$i][1][$j][0]->event_date}}</h6>
                                            </h2>
                                            <div class ="accordion" id="memberAccordion{{$i}}-{{$j}}">
                                            @for($k = 0; $k < count($collection[$i][1][$j][1]); $k++)
                                                <div class="card text-center">
                                                    <div class="card header" id="heading3-{{$i}}-{{$j}}-{{$k}}" style="background-color: #74C2E1;">
                                                        <h2 class="mb-0">
                                                            <button type="button" class="btn" data-toggle="collapse" data-target="#collapse3-{{$i}}-{{$j}}-{{$k}}" style="width:100%">
                                                                <h3> {{$collection[$i][1][$j][1][$k][0]->name}}</h3>
                                                            </button>
                                                        </h2>
                                                    </div>
                                                    <div id="collapse3-{{$i}}-{{$j}}-{{$k}}" class="collapse" aria-labelledby="heading3-{{$i}}-{{$j}}-{{$k}}" data-parent="#memberAccordion{{$i}}-{{$j}}">
                                                        <div class="card-body">
                                                            @if ($collection[$i][1][$j][1][$k][2] > 0)
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
                                                            @else
                                                                <p> There are currently no runs for this event. </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>  
                                            @endfor 
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
            </div>
        </div>
    </div>
</div>
@endsection

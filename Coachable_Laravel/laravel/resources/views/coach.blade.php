@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Team Overview</div>
            <div class="card-body">
            <ul class="nav nav-pills" id="myTab" role="tablist">
                @for($i = 0; $i < count($collection); $i++)
                    <li class="nav-item">
                        <a class="nav-link @if ($i == 0) active @endif" id="team{{$i}}-tab" data-toggle="tab" href="#team{{$i}}-tab" role="tab" aria-controls="team{{$i}}-tab" aria-selected="@if ($i == 0) true @else false @endif">{{$collection[$i][0]->name}}</a>
                    </li>
                @endfor
            </ul>

            <div class="tab-content" id="v-pills-tabContent">
                @for($i = 0; $i < count($collection); $i++)
                    <div class="tab-pane fade @if($i == 0) show active @endif" id="team{{$i}}-tab" role="tabpanel" aria-labelledby="team{{$i}}-tab">
                        <!-- DROPDOWN CONTAINING EACH EVENT-->
                            <!-- WITHIN EACH EVENT, GET EACH PERSON, SUMMARY, AND DETAILED INFO BUTTON-->
                        <ul class="nav nav-tabs" id="tabTest{{$i}}" role="tablist">
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
                                <div class="card text-center">
                                <div class="card-body">
                                    <h2 class="mb-0">
                                        <h3>Event: {{$collection[$i][1][$j][0]->event_name}}</h3>
                                        <h6>Date: {{$collection[$i][1][$j][0]->event_date}}</h6>
                                    </h2>
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
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                @endfor
            </div>
            </div>
            <!--<div class="accordion" id="teamAccordion">
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
            </div>-->
        </div>
    </div>
</div>
@endsection

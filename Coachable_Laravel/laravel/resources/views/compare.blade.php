@extends('layouts.app')

@section('content')
<div class="container" style="width:100%">
    <div class="row justify-content-center" style="width:100%">
        <div style="width:48%;">
            <ul class="nav flex-row nav-pills mb-3 justify-content-center" role="tablist">
                <li class="nav-item dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="compareEvent1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Events
                    </button>
                    <div class="dropdown-menu nav nav-tabs" aria-labelledby="compareEventDropdown1">                                    
                        @for($i = 0; $i < count($eventData); $i++)
                            <a class="dropdown-item" data-toggle="tab" href="#compare1-event{{$i}}">{{$eventData[$i][0]->event_name}}</a>
                        @endfor
                    </div>
                </li>
            </ul>
            <div class="tab-content">
                @for($i = 0; $i < count($eventData); $i++)
                <div class="tab-pane fade" id="compare1-event{{$i}}" role="tabpanel" aria-labelledby="compare1-event{{$i}}-tab">
                    <div class="card-header text-center">
                        <h1>{{$eventData[$i][0]->event_name}}</h1>
                        <h2>{{$eventData[$i][0]->event_date}}</h2>
                    </div>
                    <ul class="nav flex-row nav-pills mb-3 justify-content-center" role="tablist">
                        @for($j = 0; $j < count($eventData[$i][1]); $j++)
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#compare1-event{{$i}}-run{{$j}}" role="tab" aria-controls="compare1-event{{$i}}-run{{$j}}-tab" aria-selected="false" onclick="createCharts('compare1-chart1', 'compare1-chart2', JSON.parse('{{ json_encode($eventData[$i][1][$j][1]) }}'), JSON.parse('{{ json_encode($eventData[$i][1][$j][2]) }}'), JSON.parse('{{ json_encode($eventData[$i][1][$j][3]) }}'));">Run {{ $j + 1 }}</a>
                        </li>
                        @endfor
                    </ul>
                    <br/>
                    <div class="tab-content">
                    @for($j = 0; $j < count($eventData[$i][1]); $j++)
                        <div class="tab-pane fade" id="compare1-event{{$i}}-run{{$j}}" role="tabpanel" aria-labelledby="compare1-event{{$i}}-run{{$j}}">
                            <div class="text-center">
                                <h1> Run Statistics: </h1>
                                <p> Run started: {{$eventData[$i][1][$j][0]->start_time}} </p>
                                <p> Run ended: {{$eventData[$i][1][$j][0]->end_time}} </p>
                                <p> Length of run: {{$eventData[$i][1][$j][0]->duration}} </p>            
                                <p> Starting altitude: {{$eventData[$i][1][$j][0]->start_altitude}} m </p>
                                <p> Ending altitude: {{$eventData[$i][1][$j][0]->end_altitude}} m </p>
                                <p> Average speed: {{$eventData[$i][1][$j][0]->avg_speed}} km/h </p>
                                <p> Distance from start to end: {{$eventData[$i][1][$j][0]->distance}} km </p>     
                            </div>
                        </div>
                    @endfor
                    </div>
                    <hr/>
                </div>
                @endfor
            </div>
            <div style="height:300px">
                <canvas id="compare1-chart1"></canvas>
                <hr/>
                <canvas id="compare1-chart2"></canvas>
                <br/>
            </div>
        </div>

        <div style="width:4%"></div>

        <div style="width:48%">
            <ul class="nav flex-row nav-pills mb-3 justify-content-center" role="tablist">
                <li class="nav-item dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="compareEvent2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Events
                    </button>
                    <div class="dropdown-menu nav nav-tabs" aria-labelledby="compareEventDropdown2">                                    
                        @for($i = 0; $i < count($eventData); $i++)
                            <a class="dropdown-item" data-toggle="tab" href="#compare2-event{{$i}}">{{$eventData[$i][0]->event_name}}</a>
                        @endfor
                    </div>
                </li>
            </ul>
            <div class="tab-content">
                @for($i = 0; $i < count($eventData); $i++)
                <div class="tab-pane fade" id="compare2-event{{$i}}" role="tabpanel" aria-labelledby="compare2-event{{$i}}-tab">
                    <div class="card-header text-center">
                        <h1>{{$eventData[$i][0]->event_name}}</h1>
                        <h2>{{$eventData[$i][0]->event_date}}</h2>
                    </div>
                    <ul class="nav flex-row nav-pills mb-3 justify-content-center" role="tablist">
                        @for($j = 0; $j < count($eventData[$i][1]); $j++)
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#compare2-event{{$i}}-run{{$j}}" role="tab" aria-controls="compare2-event{{$i}}-run{{$j}}-tab" aria-selected="false" onclick="createCharts('compare2-chart1', 'compare2-chart2', JSON.parse('{{ json_encode($eventData[$i][1][$j][1]) }}'), JSON.parse('{{ json_encode($eventData[$i][1][$j][2]) }}'), JSON.parse('{{ json_encode($eventData[$i][1][$j][3]) }}'));">Run {{ $j + 1 }}</a>
                        </li>
                        @endfor
                    </ul>
                    <br/>
                    <div class="tab-content">
                    @for($j = 0; $j < count($eventData[$i][1]); $j++)
                        <div class="tab-pane fade" id="compare2-event{{$i}}-run{{$j}}" role="tabpanel" aria-labelledby="compare2-event{{$i}}-run{{$j}}">
                            <div class="text-center">
                                <h1> Run Statistics: </h1>
                                <p> Run started: {{$eventData[$i][1][$j][0]->start_time}} </p>
                                <p> Run ended: {{$eventData[$i][1][$j][0]->end_time}} </p>
                                <p> Length of run: {{$eventData[$i][1][$j][0]->duration}} </p>            
                                <p> Starting altitude: {{$eventData[$i][1][$j][0]->start_altitude}} m </p>
                                <p> Ending altitude: {{$eventData[$i][1][$j][0]->end_altitude}} m </p>
                                <p> Average speed: {{$eventData[$i][1][$j][0]->avg_speed}} km/h </p>
                                <p> Distance from start to end: {{$eventData[$i][1][$j][0]->distance}} km </p>     
                            </div>
                        </div>
                    @endfor
                    </div>
                    <hr/>
                </div>
                @endfor
            </div>
            <div style="height:300px">
                <canvas id="compare2-chart1"></canvas>
                <hr/>
                <canvas id="compare2-chart2"></canvas>
                <br/>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')

<body onload="createCharts('chart1', 'chart2', JSON.parse('{{ json_encode($timeArray) }}'), JSON.parse('{{ json_encode($speedArray) }}'), JSON.parse('{{ json_encode($altitudeArray) }}'));"/>
<div class="container">
  <div class="row">
    <div class="col-sm text-center">
        <h1> Run Statistics: </h1>
        <p> Run started: {{$run[0]->start_time}} </p>
        <p> Run ended: {{$run[0]->end_time}} </p>
        <p> Length of run: {{$run[0]->duration}} </p>            
        <p> Starting altitude: {{$run[0]->start_altitude}}m </p>
        <p> Ending altitude: {{$run[0]->end_altitude}}m </p>
        <p> Average speed: {{$run[0]->avg_speed}}km/h </p>
        <p> Distance from start to end: {{$run[0]->distance}}km </p>     
    </div>
    <div class="col-sm" style="height:300px">
        <canvas id="chart1"></canvas>
        <hr/>
        <canvas id="chart2"></canvas>
        <br/>
    </div>
  </div>
</div>
@endsection

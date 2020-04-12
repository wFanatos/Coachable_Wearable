@extends('layouts.app')

@section('content')

<body onload="createCharts('chart1', 'chart2', JSON.parse('{{ json_encode($runData[0][1]) }}'), JSON.parse('{{ json_encode($runData[0][2]) }}'), JSON.parse('{{ json_encode($runData[0][3]) }}'));"></body>
<div class="container">
  <div class="row justify-content-center" style="width:100%">
      <div class="tab-content" id="v-pills-tabContent" style="width:80%">
        <div class="card-header text-center">
            <h1>{{$event->event_name}}</h1>
            <h2>{{$event->event_date}}</h2>
        </div>
        <ul class="nav flex-row nav-pills mb-3 justify-content-center" id="pills-tab" role="tablist">
          @for($i = 0; $i < count($runData); $i++)
          <li class="nav-item">
            <a class="nav-link @if ($i == 0) active @endif" id="pills-run{{$i}}-tab" data-toggle="pill" href="#pills-run{{$i}}" role="tab" aria-controls="pills-run{{$i}}" aria-selected="@if ($i == 0) true @else false @endif" onclick="createCharts('chart1', 'chart2', JSON.parse('{{ json_encode($runData[$i][1]) }}'), JSON.parse('{{ json_encode($runData[$i][2]) }}'), JSON.parse('{{ json_encode($runData[$i][3]) }}'));">Run {{ $i + 1 }}</a>
          </li>
          @endfor
        </ul>
        <br/>
        @for($i = 0; $i < count($runData); $i++)
          <div class="tab-pane fade @if($i == 0) show active @endif" id="pills-run{{$i}}" role="tabpanel" aria-labelledby="pills-run{{$i}}-tab">
            <div class="text-center">
                <h1> Run Statistics: </h1>
                <p> Run started: {{$runData[$i][0]->start_time}} </p>
                <p> Run ended: {{$runData[$i][0]->end_time}} </p>
                <p> Length of run: {{$runData[$i][0]->duration}} </p>
                <p> Starting altitude: {{$runData[$i][0]->start_altitude}} m </p>
                <p> Ending altitude: {{$runData[$i][0]->end_altitude}} m </p>
                <p> Average speed: {{$runData[$i][0]->avg_speed}} km/h </p>
                <p> Distance from start to end: {{$runData[$i][0]->distance}} km </p>
                <a class="btn btn-primary" href="{{ route('compare', ['userid' => $runData[$i][0]->user_id,'runid' => $runData[$i][0]->id]) }}">Compare</a>
            </div>
          </div>
        @endfor
        <hr/>
        <div style="height:300px">
            <canvas id="chart1"></canvas>
            <hr/>
            <canvas id="chart2"></canvas>
            <br/>
        </div>
      </div>
  </div>
</div>

@endsection

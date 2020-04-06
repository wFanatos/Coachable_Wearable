@extends('layouts.app')

@section('content')

<div class="container">
  <div class="row justify-content-center" style="width:100%">
      <ul class="nav flex-column nav-pills mb-3" id="pills-tab" role="tablist">
        @for($i = 0; $i < count($runData); $i++)
          <li class="nav-item">
            <a class="nav-link @if ($i == 0) active @endif" id="pills-run{{$i}}-tab" data-toggle="pill" href="#pills-run{{$i}}" role="tab" aria-controls="pills-run{{$i}}" aria-selected="@if ($i == 0) true @else false @endif">Run {{ $i + 1 }}</a>
          </li>
        @endfor
      </ul>
      <div class="card" style="width:75%">
      <div class="card-body">
      <div class="tab-content" id="pills-tabContent">
        @for($i = 0; $i < count($runData); $i++)
          <div class="tab-pane fade @if ($i == 0) show active @endif" id="pills-run{{$i}}" role="tabpanel" aria-labelledby="pills-run{{$i}}-tab">
            <div class="column">
                <div class="col-sm text-center">
                    <h1> Run Statistics: </h1>
                    <p> Run started: {{$runData[$i][0]->start_time}} </p>
                    <p> Run ended: {{$runData[$i][0]->end_time}} </p>
                    <p> Length of run: {{$runData[$i][0]->duration}} </p>            
                    <p> Starting altitude: {{$runData[$i][0]->start_altitude}}m </p>
                    <p> Ending altitude: {{$runData[$i][0]->end_altitude}}m </p>
                    <p> Average speed: {{$runData[$i][0]->avg_speed}}km/h </p>
                    <p> Distance from start to end: {{$runData[$i][0]->distance}}km </p>     
                </div>
                <div class="col-sm">
                    <div>
                        {!! $runData[$i][1]->container() !!}
                        {!! $runData[$i][1]->script() !!}
                    </div>
                    <div>
                        {!! $runData[$i][2]->container() !!}
                        {!! $runData[$i][2]->script() !!}
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
@endsection

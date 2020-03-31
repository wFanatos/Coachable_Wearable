@extends('layouts.app')

@section('content')

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
    <div class="col-sm">
        <div>                                  
            {!! $firstChart->container() !!}                   
            {!! $firstChart->script() !!}                   
        </div>
        <div>                                  
            {!! $secondChart->container() !!}                   
            {!! $secondChart->script() !!}                   
        </div>
    </div>
  </div>
</div>
@endsection

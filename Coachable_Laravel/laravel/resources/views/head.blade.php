@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Organization: {{$collection[1]->org_name}}</div>

                        <div class="tab-content" id="pills-tabContent" style="background-color: #6dcdeb;">
                            <div class="tab-pane fade show active" id="pills-overview" role="tabpanel" aria-labelledby="pills-overview-tab">
                                <div class="accordion" id="accordionExample">
                                    @for($i = 0; $i < count($collection[2]); $i++)    
                                        <div class="card text-center">
                                            <div class="card-header" id="heading{{$i}}" style="background-color: #6dcdeb;">
                                                <h2 class="mb-0">
                                                    <button type="button" class="btn" data-toggle="collapse" data-target="#collapse{{$i}}" >
                                                        <h2>{{$collection[2][$i][1]->name}} - {{$collection[2][$i][0]->season_name}} </h2>
                                                        <p> {{$collection[2][$i][0]->season_start}} - {{$collection[2][$i][0]->season_end}}</p> 
                                                    </button>                                       								
                                                </h2>
                                            </div>
                                        </div>

                                        <div id="collapse{{$i}}" class="collapse" aria-labelledby="heading{{$i}}" data-parent="#accordionExample">
                                                <div class ="accordion" id="runExample">

                                                    @for($j = 0; $j < count($collection[2][$i][2]); $j++)
                                                        <div class="card text-center">
                                                                <div class="card header" id="heading2{{$j}}" style="background-color: #0191C8;">
                                                                    <h2 class="mb-0">
                                                                        <button type="button" class="btn" data-toggle="collapse" data-target= "#collapse2{{$j}}">
                                                                            <h2> Current Events: {{$collection[2][$j][2][0]->event_name}}</h2>
                                                                        </button>
                                                                    </h2>
                                                                </div>

                                                            <div id="collapse2{{$j}}" class="collapse" aria-labelledby="heading2{{$j}}" data-parent="#runExample">
                                                                <div class="card-body">
                                                                    <p> Event Date: {{$collection[2][$j][2][0]->event_date}} </p>                                               
                                                                    <button class="btn btn-primary">Detailed Info </button>
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
    </div>

@endsection

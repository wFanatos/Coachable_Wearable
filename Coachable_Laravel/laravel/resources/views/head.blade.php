@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">

                <h1> User Info </h1>                   
                <p> Name: {{$collection[0]->name}}</p>
                <p> Email: {{$collection[0]->email}}</p>

                <h1> Org Info </h1>
                <p> Apart of org: {{$collection[1]->org_name}}</p>

                @foreach($collection[2] as $orgs)
                    <h2> Team Info </h2>
                    <p> Name: {{$orgs[1]->name}}</p>

                    <h2> Season Info </h2>
                    <p> Current Season: {{$orgs[0]->season_name}}</p>
                    <p> Description: {{$orgs[0]->season_description}}</p>
                    <p> Start of Season: {{$orgs[0]->season_start}}</p>
                    <p> End of Season: {{$orgs[0]->season_end}}</p>

                    @foreach($orgs[2] as $events)
                        <h2> Event Info </h2>
                        <p> Name: {{$events->event_name}} </p>
                        <p> Date: {{$events->event_date}} </p>
                    @endforeach                    
                @endforeach                  
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

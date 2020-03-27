@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Settings Dashboard</div>

                <div class="card-body text-center">
                  
                <label for="device">Current Device:</label>

                @if($device == null)                  
                    <label>No device connected </label>
                    <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">Add</a>                                    
                @else                       
                    <label>{{$device->device_name}} </label>
                    <form action="{{ url('settings') }}" method="POST">
                    {{ csrf_field() }}
                        <button type="submit" id="remove" name = "submit" class="btn btn-primary" value="Remove">Remove</button>   
                    </form>                   
                                      
                @endif

                <div class="collapse" id="collapseExample">
                    <br>
                    <form action="{{ url('settings') }}" method="POST">
                    {{ csrf_field() }}
                        <label for="devName">New Device: </label>
                        <input text="input" name="devName">
                        <button type="submit" id="save" name = "submit" class="btn btn-primary" value="Save">Save</button>
                    </form>                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

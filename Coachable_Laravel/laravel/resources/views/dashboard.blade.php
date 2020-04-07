@extends('layouts.app')

@section('content')
    <athlete-data :data='{{$data ?? ''}}'></athlete-data>
@endsection
@extends('layouts.master')
@section('content')


    @foreach($categories as $key => $value)
        {{ $value }}
    @endforeach

@endsection

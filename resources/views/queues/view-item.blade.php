@extends('layouts.app')

@section('content')

    <div class="container">
        @include('queues/_queue-items')
        @include('queues/_form')
    </div>

@endsection

@extends('layouts.app')

@section('content')

    <div class="container">
        <h4>Restore this item?</h4>
        <form action="/web/finished-goods/items/{{$code}}/restore-continue" method="post">
            {{csrf_field()}}
            @include('printer/_series') <br />
            <input type="submit" value="Restore" class="btn btn-primary">
        </form>
    </div>

@endsection

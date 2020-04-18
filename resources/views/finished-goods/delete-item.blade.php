@extends('layouts.app')

@section('content')
    <div class="container">
        <h5>Delete item {{$force ? 'permanently' : ''}} ?</h5>
        <hr class="mt-0">

        @include('printer/_series', ['code' => $serialNumber])

        <form method="post" action="/web/finished-goods/items/{{$serialNumber}}/delete-continue/{{$force}}">
            {{csrf_field()}}
            <div class="form-group">
                <input type="submit" value="Delete {{$force ? 'permanently' : ''}}" class="btn btn-{{$force ? 'danger' : 'warning'}}">
                <a href="{{ route('scan.any', $serialNumber) }}" class="btn btn-info">Cancel</a>
            </div>
        </form>

    </div>
@endsection

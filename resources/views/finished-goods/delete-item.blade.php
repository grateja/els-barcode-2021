@extends('layouts.app')

@section('content')
    <div class="container">
        <h5>Move item</h5>
        <hr class="mt-0">

        <form method="post">
            {{csrf_field()}}
            <div class="form-group text-center">
                <a href="/scan/finished-goods/{{$code}}" class="btn btn-info">Cancel</a>
                <input formaction="/web/finished-goods/{{$id}}/delete-continue" type="submit" value="delete" class="btn btn-warning">
                <input formaction="/web/finished-goods/{{$id}}/delete-pemanently" type="submit" value="delete permanently" class="btn btn-danger">
            </div>
        </form>

    </div>
@endsection

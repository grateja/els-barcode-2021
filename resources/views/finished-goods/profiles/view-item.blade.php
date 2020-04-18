@extends('layouts.app')

@section('content')

    <div class="container">
        @if($data == null)
            <h4>No record found for this serial number</h4>
            <div class="card">

                <div class="text-center p-2">
                    @include('printer/_series', ['code' => $model])
                </div>
            </div>
            <a class="m-2 btn btn-block btn-info" href="/web/finished-goods/{{$model}}/add-profile">Add profile</a>
        @else
            <h5>Finished good</h5>
            <hr class="mt-0 mb-3">
            <dl class="row">
                <dt class="col-5 text-right">Model:</dt>
                <dd class="col-7">
                    @include('printer/_series', ['code' => $model])
                </dd>

                <dt class="col-5 text-right">Description:</dt>
                <dd class="col-7">{{$data['description']}}</dd>

                <dt class="col-5 text-right">Specs:</dt>
                <dd class="col-7">{{$data['specs']}}</dd>

                <dt class="col-5 text-right">Supplier:</dt>
                <dd class="col-7">{{$data['supplier']}}</dd>

                <dt class="col-5 text-right">Total items:</dt>
                <dd class="col-7">{{$data['totalItems']}}</dd>

                @if($data['deletedAt'])
                    <dt class="col-5 text-right">Deleted at:</dt>
                    <dd class="col-7">{{$data['deletedAt']}}</dd>
                @endif
            </dl>
            <div class="text-center">
                <a href="/web/finished-goods/{{$model}}/add-profile" class="btn btn-sm btn-info">edit</a>
            </div>
        @endif
    </div>

@endsection

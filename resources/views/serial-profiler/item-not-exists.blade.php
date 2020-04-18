@extends('layouts.app')

@section('content')

    <div class="container">

        <h4>No record found for this series</h4>

        <div class="card">
            <div class="text-center p-2">
                @include('printer/_barcode', ['code' => $code])
                <br />
                {{$code}}
            </div>
        </div>
        <div class="text-center">
            <a class="btn-block btn btn-info my-2" href="/web/queues/{{$code}}/add-to-queue">Add to queues</a>
            <a class="btn-block btn btn-info my-2" href="/web/fixed-assets/items/{{$code}}/add-to-inventory">Add to fixed assets</a>
            <a class="btn-block btn btn-info my-2" href="/web/finished-goods/items/{{$code}}/add-to-inventory">Add to finished goods</a>
            <a class="btn-block btn btn-info my-2" href="/web/spare-parts/items/{{$code}}/add-to-inventory">Add to spare parts</a>
            <a class="btn-block btn btn-info my-2" href="/web/spare-parts/{{$code}}/add-profile">Add as spare part's part number</a>
            <a class="btn-block btn btn-info my-2" href="/web/finished-goods/{{$code}}/add-profile">Add as finished good's model</a>
        </div>
    </div>

@endsection

@extends('layouts.app')

@section('content')

    <div class="container">
        @if($model == null)
            <div class="alert alert-warning">Item does not exist. <span class="code btn btn-small btn-default">{{$code}}</span>
                <a href="/web/finished-goods/{{$code}}/add-to-inventory" class="btn btn-primary">Add to inventory</a>
                <a href="/web/queues/{{$code}}/add-to-queue/finished-goods" class="btn btn-primary">Add to queue</a>
            </div>
        @else
            <h5>Item info</h5>
            <hr class="mt-0 mb-0">
            <dl class="row">
                <dt class="col-5 text-right">Model:</dt>
                <dd class="col-7">
                    {{$model['model']}}
                    @include('printer/_barcode', ['code' => $model['model']])
                </dd>

                <dt class="col-5 text-right">Description:</dt>
                <dd class="col-7">{{$model['description']}}</dd>

                <dt class="col-5 text-right">Serial number:</dt>
                <dd class="col-7">
                    {{$model['serialNumber']}}
                    @include('printer/_barcode', ['code' => $code])
                </dd>

                <dt class="col-5 text-right">Specs:</dt>
                <dd class="col-7">{{$model['specs']}}</dd>

                <dt class="col-5 text-right">Supplier:</dt>
                <dd class="col-7">{{$model['supplier']}}</dd>

                <dt class="col-5 text-right">Date scanned:</dt>
                <dd class="col-7">{{$model['dateScanned']}}</dd>

                <dt class="col-5 text-right">Warehouse:</dt>
                <dd class="col-7">{{$model['warehouse']}}</dd>

                <dt class="col-5 text-right">Location:</dt>
                <dd class="col-7">{{$model['currentLocation']}}</dd>
            </dl>
            <div class="text-center">
                <a href="/web/finished-goods/{{$model['serialNumber']}}/add-remarks" class="btn btn-sm btn-primary">add remarks</a>
                <a href="/web/finished-goods/{{$model['serialNumber']}}/add-to-inventory" class="btn btn-sm btn-info">edit</a>
                <a href="/web/finished-goods/{{$model['serialNumber']}}/move" class="btn btn-sm btn-info">move</a>
                <a href="/web/finished-goods/{{$model['serialNumber']}}/delete" class="btn btn-sm btn-warning">delete</a>
            </div>
            <hr>
            @if($model['client'])
            <h6>Client info</h6>
            <hr class="mt-0 mb-0">
            <dl class="row">
                <dt class="col-5 text-right">Owner name:</dt>
                <dd class="col-7">{{$model['client']->owner_name}}</dd>

                <dt class="col-5 text-right">Shop name:</dt>
                <dd class="col-7">{{$model['client']->shop_name}}</dd>

                <dt class="col-5 text-right">Address:</dt>
                <dd class="col-7">{{$model['client']->address}}</dd>
            </dl>
            @endif

            @if($model['subdealer'])
            <h6>Client info</h6>
            <hr class="mt-0 mb-0">
            <dl class="row">
                <dt class="col-5 text-right">Name:</dt>
                <dd class="col-7">{{$model['subdealer']->subdealer_name}}</dd>

                <dt class="col-5 text-right">Company:</dt>
                <dd class="col-7">{{$model['subdealer']->company_name}}</dd>
            </dl>

            @endif

            @if($model['activityLogs'])
            <h6>Activity log</h6>
            <hr class="mt-0 mb-0">
            <ul>
                @foreach($model['activityLogs'] as $activityLog)
                <li>{{$activityLog->remarks}}
                    <div class="small">{{$activityLog->created_at}}</div>
                </li>
                @endforeach
            </ul>
            @endif
        @endif
    </div>

@endsection

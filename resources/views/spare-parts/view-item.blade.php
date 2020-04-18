@extends('layouts.app')

@section('content')

    <div class="container">
        @if($model == null)
            <h4>No record found for this serial number</h4>
            <div class="card">

                <div class="text-center p-2">
                    @include('printer/_series', ['code' => $serialNumber])
                </div>
            </div>
            <a class="m-2 btn btn-block btn-info" href="/web/spare-parts/items/{{$serialNumber}}/add-to-inventory">Add to inventory</a>
            <a class="m-2 btn btn-block btn-info" href="/web/queues/{{$serialNumber}}/add-to-queue/spare-parts">Add to queue</a>
        @else
            <h5>Spare part</h5>
            <hr class="mt-0 mb-3">
            <dl class="row">
                <dt class="col-5 text-right">Part number:</dt>
                <dd class="col-7">
                    @include('printer/_series', ['code' => $model['partNumber']])
                </dd>

                <dt class="col-5 text-right">Description:</dt>
                <dd class="col-7">{{$model['description']}}</dd>

                <dt class="col-5 text-right">Serial number:</dt>
                <dd class="col-7">
                    @include('printer/_series', ['code' => $serialNumber])
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

                @if($model['deletedAt'])
                    <dt class="col-5 text-right">Deleted at:</dt>
                    <dd class="col-7">{{$model['deletedAt']}}</dd>
                @endif
            </dl>
            <div class="text-center">
                @if($model['deletedAt'])
                    <a id="btnRestore" href="/web/spare-parts/items/{{$serialNumber}}/restore" class="btn btn-sm btn-info">restore</a>
                    <a id="btnForceDelete" href="/web/spare-parts/items/{{$serialNumber}}/delete" class="btn btn-sm btn-danger">delete permanently</a>
                @else
                    <a href="/web/spare-parts/items/{{$serialNumber}}/add-remarks" class="btn btn-sm btn-primary">add remarks</a>
                    <a href="/web/spare-parts/items/{{$serialNumber}}/add-to-inventory" class="btn btn-sm btn-info">edit</a>
                    <a href="/web/spare-parts/items/{{$serialNumber}}/move" class="btn btn-sm btn-info">move</a>
                    <a href="/web/spare-parts/items/{{$serialNumber}}/delete" class="btn btn-sm btn-warning">delete</a>
                @endif
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

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
            <a class="m-2 btn btn-block btn-info" href="/web/fixed-assets/items/{{$serialNumber}}/add-to-inventory">Add to inventory</a>
            <a class="m-2 btn btn-block btn-info" href="/web/queues/{{$serialNumber}}/add-to-queue/fixed-assets">Add to queue</a>
        @else
            <h5>Finished good</h5>
            <hr class="mt-0 mb-3">
            <dl class="row">
                <dt class="col-5 text-right">Account name:</dt>
                <dd class="col-7">{{$model['accountName']}}</dd>

                <dt class="col-5 text-right">Department:</dt>
                <dd class="col-7">{{$model['department']}}</dd>

                <dt class="col-5 text-right">Serial number:</dt>
                <dd class="col-7">
                    @include('printer/_series', ['code' => $serialNumber])
                </dd>

                <dt class="col-5 text-right">Description:</dt>
                <dd class="col-7">{{$model['description']}}</dd>

                <dt class="col-5 text-right">Specs:</dt>
                <dd class="col-7">{{$model['specs']}}</dd>

                <dt class="col-5 text-right">Date issued:</dt>
                <dd class="col-7">{{$model['dateIssued']}}</dd>

                @if($model['deletedAt'])
                    <dt class="col-5 text-right">Deleted at:</dt>
                    <dd class="col-7">{{$model['deletedAt']}}</dd>
                @endif
            </dl>
            <div class="text-center">
                @if($model['deletedAt'])
                    <a id="btnRestore" href="/web/fixed-assets/items/{{$serialNumber}}/restore" class="btn btn-sm btn-info">restore</a>
                    <a id="btnForceDelete" href="/web/fixed-assets/items/{{$serialNumber}}/delete" class="btn btn-sm btn-danger">delete permanently</a>
                @else
                    <a href="/web/fixed-assets/items/{{$serialNumber}}/add-remarks" class="btn btn-sm btn-primary">add remarks</a>
                    <a href="/web/fixed-assets/items/{{$serialNumber}}/add-to-inventory" class="btn btn-sm btn-info">edit</a>
                    <a href="/web/fixed-assets/items/{{$serialNumber}}/delete" class="btn btn-sm btn-warning">delete</a>
                @endif
            </div>

            @if($model['remarks'])
            <h6>Remarks</h6>
            <hr class="mt-0 mb-0">
            <ul>
                @foreach($model['remarks'] as $activityLog)
                <li>{{$activityLog->content}}
                    <div class="small">{{$activityLog->created_at}}</div>
                </li>
                @endforeach
            </ul>
            @endif
        @endif
    </div>

@endsection

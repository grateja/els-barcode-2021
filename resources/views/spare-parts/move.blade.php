@extends('layouts.app')

@section('content')
    <div class="container">
        <h5>Move item from</h5>
        <hr class="mt-0">

        <form action="/web/spare-parts/items/{{$serialNumber}}/move-continue" method="post">
            {{csrf_field()}}

            @include('printer/_series', ['code' => $serialNumber]) <br />
            <dl class="row">
                <dt class="col-5 text-right">Warehouse :</dt>
                <dd class="col-7">{{$currentWarehouse}}</dd>

                <dt class="col-5 text-right">Specific location :</dt>
                <dd class="col-7">{{$currentLocation}}</dd>
            </dl>

            <h5 class="mt-5">Move to</h5>
            <hr class="mb-3 mt-0">

            <div class="form-group">
                <label for="warehouse">Warehouse:</label>
                <input type="text" name="warehouse" id="warehouse" value="{{old('warehouse') ? old('warehouse') : $currentWarehouse}}" class="form-control">
                @if ($errors->has('warehouse'))
                    <span class="text-danger">{{ $errors->first('warehouse') }}</span>
                @endif
            </div>


            <div class="form-group">
                <label for="locationTo">Specific location:</label>
                <input type="text" name="locationTo" id="locationTo" value="{{ old('locationTo') ? old('locationTo') : $currentLocation }}" class="form-control">
                @if ($errors->has('locationTo'))
                    <span class="text-danger">{{ $errors->first('locationTo') }}</span>
                @endif
            </div>

            <div class="form-group">
                <input type="submit" value="Save" class="btn btn-primary">
                <a href="{{ route('scan.any', $serialNumber) }}" class="btn btn-info">Cancel</a>
            </div>
        </form>

    </div>
@endsection

@section('scripts')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#warehouse').autocomplete({
            source: [
                'GOLDLAND TOWER',
                'WH1',
                'WH2',
                'WH3',
                'WH4',
                'WH5',
                'OTHER'
            ]
        });
    });
</script>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}">
@endsection

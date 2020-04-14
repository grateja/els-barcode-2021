@extends('layouts.app')

@section('content')
    <div class="container">
        <h5>Move item</h5>
        <hr class="mt-0">

        <form action="/web/finished-goods/{{$id}}/move-continue" method="post">
            {{csrf_field()}}
            <div class="form-group">
                <label for="currentLocation">Current location:</label>
                <input type="text" name="currentLocation" id="currentLocation" value="{{old('currentLocation') ? old('currentLocation') : $currentLocation}}" class="form-control">
                @if ($errors->has('currentLocation'))
                    <span class="text-danger">{{ $errors->first('currentLocation') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="locationTo">Mote to:</label>
                <input type="text" name="locationTo" id="locationTo" value="{{ old('locationTo') }}" class="form-control">
                @if ($errors->has('locationTo'))
                    <span class="text-danger">{{ $errors->first('locationTo') }}</span>
                @endif
            </div>

            <div class="form-group">
                <input type="submit" value="Save" class="btn btn-primary">
                <a href="/scan/finished-goods/{{$code}}" class="btn btn-info">Cancel</a>
            </div>
        </form>

    </div>
@endsection

@section('scripts')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#locationTo').autocomplete({
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

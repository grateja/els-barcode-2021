@extends('layouts.app')

@section('content')
    <div class="container">
        <h5>Add remarks</h5>
        <hr class="mt-0">

        @include('printer/_series', ['code' => $serialNumber])

        <form action="/web/fixed-assets/items/{{$serialNumber}}/add-remarks-continue" method="post">
            {{csrf_field()}}
            <div class="form-group">
                <label for="remarks">Remarks:</label>
                <textarea name="remarks" id="remarks" rows="10" class="form-control">{{ old('remarks') }}</textarea>
                @if ($errors->has('remarks'))
                    <span class="text-danger">{{ $errors->first('remarks') }}</span>
                @endif
            </div>

            <div class="form-group">
                <input type="submit" value="Save" class="btn btn-primary">
                <a href="{{ route('scan.any', $serialNumber) }}" class="btn btn-info">Cancel</a>
            </div>
        </form>

    </div>
@endsection

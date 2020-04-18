@extends('layouts.app')

@section('content')

<div class="container">
    <h4>Spare part profile</h4>
    <form action="/web/spare-parts/{{$action}}" method="POST">
        {{csrf_field()}}

        <div class="form-group">
            <label for="partNumber">Part number:</label>
            <input type="text" name="partNumber" id="partNumber" value="{{old('partNumber') ? old('partNumber') : $partNumber}}" class="form-control">
            @if ($errors->has('partNumber'))
                <span class="text-danger">{{ $errors->first('partNumber') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <input type="text" name="description" id="description" value="{{old('description') ? old('description') : $description}}" class="form-control">
            @if ($errors->has('description'))
                <span class="text-danger">{{ $errors->first('description') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="specs">Specs:</label>
            <input type="text" name="specs" id="specs" value="{{old('specs') ? old('specs') : $specs}}" class="form-control">
            @if ($errors->has('specs'))
                <span class="text-danger">{{ $errors->first('specs') }}</span>
            @endif
        </div>

        <div class="form-group">
            <label for="supplier">Supplier:</label>
            <input type="text" name="supplier" id="supplier" value="{{old('supplier') ? old('supplier') : $supplier}}" class="form-control">
            @if ($errors->has('supplier'))
                <span class="text-danger">{{ $errors->first('supplier') }}</span>
            @endif
        </div>

        <div class="form-group">
            <input type="submit" value="Save" class="btn btn-primary">
            <a href="{{ route('scan.any', ['code' => $partNumber]) }}" class="btn btn-info">Cancel</a>
        </div>

    </form>
</div>

@endsection

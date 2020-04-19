@extends('layouts.app')

@section('content')

<div class="container">
    <h4>Finished good profile</h4>
    <form action="/web/finished-goods/{{$action}}" method="POST">
        {{csrf_field()}}

        <div class="form-group">
            <label for="model">Model:</label>
            <input type="text" name="model" id="model" value="{{old('model') ? old('model') : $model}}" class="form-control">
            @if ($errors->has('model'))
                <span class="text-danger">{{ $errors->first('model') }}</span>
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
            <a href="{{ route('scan.any', ['code' => $serialNumber ? $serialNumber : $model]) }}" class="btn btn-info">Cancel</a>
        </div>

    </form>
</div>

@endsection

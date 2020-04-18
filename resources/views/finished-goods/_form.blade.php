<form action="/web/finished-goods/items/{{$action}}" method="POST">
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
        <label for="serialNumber">Serial number:</label>
        <input type="text" name="serialNumber" id="serialNumber" value="{{old('serialNumber') ? old('serialNumber') : $serialNumber}}" class="form-control">
        @if ($errors->has('serialNumber'))
            <span class="text-danger">{{ $errors->first('serialNumber') }}</span>
            @endif
    </div>

    <div class="form-group">
        <label for="warehouse">Warehouse:</label>
        <input type="text" name="warehouse" id="warehouse" value="{{old('warehouse') ? old('warehouse') : $warehouse}}" class="form-control">
        @if ($errors->has('warehouse'))
            <span class="text-danger">{{ $errors->first('warehouse') }}</span>
        @endif
    </div>

    <div class="form-group">
        <label for="currentLocation">Current location:</label>
        <input type="text" name="currentLocation" id="currentLocation" value="{{old('currentLocation') ? old('currentLocation') : $currentLocation}}" class="form-control">
        @if ($errors->has('currentLocation'))
            <span class="text-danger">{{ $errors->first('currentLocation') }}</span>
        @endif
    </div>

    <div class="form-group">
        <input type="submit" value="Save" class="btn btn-primary">
        <a href="{{ route('scan.any', $serialNumber) }}" class="btn btn-info">Cancel</a>
    </div>

</form>

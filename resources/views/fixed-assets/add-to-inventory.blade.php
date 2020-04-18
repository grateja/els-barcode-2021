@extends('layouts.app')

@section('content')

    <div class="container">
        <form action="/web/fixed-assets/items/{{$action}}" method="post">
            {{ csrf_field() }}
            <h4>Fixed asset</h4>
            <hr class="mt-0 mb-4">
            <div class="form-group">
                <label for="dateIssued">Date issued:</label>
                <input type="date" name="dateIssued" id="dateIssued" value="{{old('dateIssued') ? old('dateIssued') : $dateIssued}}" class="form-control">
                @if ($errors->has('dateIssued'))
                    <span class="text-danger">{{ $errors->first('dateIssued') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="accountName">Account name:</label>
                <input type="text" name="accountName" id="accountName" value="{{old('accountName') ? old('accountName') : $accountName}}" class="form-control">
                @if ($errors->has('accountName'))
                    <span class="text-danger">{{ $errors->first('accountName') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="department">Department:</label>
                <input type="text" name="department" id="department" value="{{old('department') ? old('department') : $department}}" class="form-control">
                @if ($errors->has('department'))
                    <span class="text-danger">{{ $errors->first('department') }}</span>
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
                <label for="serialNumber">Serial number:</label>
                <div class="input-group">
                    <input type="text" name="serialNumber" id="serialNumber" value="{{old('serialNumber') ? old('serialNumber') : $serialNumber}}" class="form-control">
                    <div class="input-group-append">
                        <button id="btnGenerateSeries" class="btn btn-info" type="button">Generate series</button>
                    </div>
                </div>
                @if ($errors->has('serialNumber'))
                    <span class="text-danger">{{ $errors->first('serialNumber') }}</span>
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
                <label for="tags">Tags:</label>
                <input placeholder="Enter tags separated by comma.(Ex. Pen, Screw driver, Motorcycle...)" type="text" name="tags" id="tags" value="{{old('tags') ? old('tags') : $tags}}" class="form-control">
                @if ($errors->has('tags'))
                    <span class="text-danger">{{ $errors->first('tags') }}</span>
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
        $('#accountName').autocomplete({
            source: function(request, response) {
                $.ajax({
                    type: "GET",
                    contentType: "application/json; charset=utf-8",
                    url: "/api/autocomplete/accounts",
                    data: {keyword: request.term.toLowerCase() },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        result = data.data.map(item => {
                            return {
                                value: item.id,
                                label: item.display,
                                accountName: item.name,
                                department: item.department
                            };
                        });
                        response(result);
                    },
                    error: function (result) {}
                });
            },
            select: function(e, u) {
                $('#accountName').val(u.item.accountName);
                $('#department').val(u.item.department);
                return false;
            }
        });

        $('#btnGenerateSeries').on('click', function(e) {
            $.ajax({
                type: "GET",
                contentType: "application/json; charset=utf-8",
                url: "/api/fixed-assets/generate-serial",
                data: {accountName: $('#accountName').val(), department: $('#department').val(), description: $('#description').val() },
                dataType: "json",
                success: function (data) {
                    $('#serialNumber').val(data);
                    console.log( $('#accountName').val());
                },
                error: function (result) {}
            });
        });
    });
</script>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}">
@endsection

@extends('layouts.app')

@section('content')

<div class="container">
    <hr>

    @include('spare-parts/_form')
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#partNumber').autocomplete({
            source: function(request, response) {
                $.ajax({
                    type: "GET",
                    contentType: "application/json; charset=utf-8",
                    url: "/api/autocomplete/spare-parts",
                    data: {keyword: request.term.toLowerCase() },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        result = data.data.map(item => {
                            return {
                                value: item.id,
                                label: item.display,
                                partNumber: item.partNumber,
                                specs: item.specs,
                                description: item.description,
                                supplier: item.supplier
                            };
                        });
                        response(result);
                    },
                    error: function (result) {}
                });
            },
            select: function(e, u) {
                $('#partNumber').val(u.item.partNumber);
                $('#description').val(u.item.description);
                $('#specs').val(u.item.specs);
                $('#supplier').val(u.item.supplier);
                return false;
            }
        });

        $('#supplier').autocomplete({
            source: [
                'LG',
                'ELECTROLUX',
                'CSI',
                'OTHER'
            ]
        });

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

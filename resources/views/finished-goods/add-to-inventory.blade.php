@extends('layouts.app')

@section('content')

<div class="container">
    <hr>

    @include('finished-goods/_form')
</div>

@endsection

@section('scripts')
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#model').autocomplete({
            source: function(request, response) {
                $.ajax({
                    type: "GET",
                    contentType: "application/json; charset=utf-8",
                    url: "/api/autocomplete/finished-goods",
                    data: {keyword: request.term.toLowerCase() },
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        result = data.data.map(item => {
                            return {
                                value: item.id,
                                label: item.display,
                                model: item.model,
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
                $('#model').val(u.item.model);
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

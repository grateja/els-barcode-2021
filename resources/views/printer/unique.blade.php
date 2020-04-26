@extends('layouts.print')

@section('content')

<div class="actions">
    <button onclick="window.print()" class="btn-print">Print ({{count($items)}})</button>
</div>

@foreach($items as $item)
    <div class="barcode-print">
        <div class="barcode-wrapper">
            <div class="barcode_label">{{ $item['barcode_label'] ?? $item['id'] }}  </div>
            <div class="img-wrapper">
                <img src="{{ asset('img/logo.jpg') }}" alt="logo" class="logo">
                @include('printer._barcode', ['code' => $item['id']])
            </div>
            <div class="code">{{ $item['id'] }}</div>
        </div>
        <div class="barcode-wrapper">
            <div class="barcode_label">{{ $item['barcode_label'] ?? $item['id'] }}</div>
            <div class="img-wrapper">
                <img src="{{ asset('img/logo.jpg') }}" alt="logo" class="logo">
                @include('printer._barcode', ['code' => $item['id']])
            </div>
            <div class="code">{{ $item['id'] }}</div>
        </div>
    </div>
@endforeach

@endsection

@section('styles')

    <style>
        * {
            box-sizing: border-box;
            padding: 0in;
            margin: 0in;
        }
        .barcode-print {
            height: 2in;
            width: 3in;
            margin: 20px auto;
            border: 1px dashed silver;
            border-radius: 6px;
            font-family: arial;
            position: relative;
        }
        .barcode-wrapper {
            margin: .15in auto;
            position: relative;
            display: block;
        }
        .barcode-img {
            height: .3in;
            width: 77%;
        }
        .logo {
            width: 16%;
            margin-top: -0.6in;
        }
        .barcode_label,
        .code {
            text-align: center;
            border: 1px solid transparent;
        }
        .code {
            margin-top: 0.03in;
            margin-bottom: 0.01in;
        }
        .barcode_label {
            margin-bottom: 0.03in;
            margin-top: 0.05in;
        }
        .btn-print {
            position: fixed;
            top: 20px;
            left: 20px;
        }
        .img-wrapper {
            /* border: 1px solid red; */
            width: 95%;
            display: block;
            margin: 0px auto;
        }
        @media print {
            @page {
                margin: 0;
                width: 3in;
                height: 2in;
            }
            .barcode-print {
                border: 1px solid transparent;
                margin: 0in;
                width: 100%;
                height: 100%;
            }
            .barcode-wrapper {
                border: 1px solid transparent;
                display: block;
                margin: .05in auto;
            }
            .actions {
                display: none;
            }
        }
    </style>

@endsection

<script>
    window.print();
</script>

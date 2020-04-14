<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <style>
        body, html {
            background: #eee;
        }
        .button-container {
            position: fixed;
            right: 10px;
            top: 10px;
        }
        .large {
            font-size: 1.5em;
        }
        .table td{
            padding: 0px;
        }
        @media print {
            .button-container {
                display: none;
            }
            .main {
                min-width: 100%!important;
                margin: 0px!important;
            }
        }
        .main {
            background: white;
            border: 1px solid #eee;
            box-shadow: 10px 10px 10px #ddd;
            padding: .5cm;
            max-width: 80%;
            margin: 20px auto;
        }
    </style>
    <title>Print Job Order</title>
</head>
<body>
    <div class="button-container">
        <button class="btn btn-primary" onclick="window.print()">PRINT</button>
    </div>


    <div class="main">
        <div class="header">
            <div class="text-center large">{{$shop_name}}</div>
            <div class="text-center">{{$shop_address}}</div>
            <div class="text-center">{{$shop_number}} / {{$shop_email}}</div>
            <hr>
            <dl class="row">
                <dt class="col-sm-4 col-6 text-right">Job order:</dt>
                <dd class="col-sm-8 col-6">
                    {{$job_order}}
                </dd>

                <dt class="col-sm-4 col-6 text-right">Date:</dt>
                <dd class="col-sm-8 col-6">
                    {{$date}}
                </dd>

                <dt class="col-sm-4 col-6 text-right">Customer name:</dt>
                <dd class="col-sm-8 col-6">
                    {{$customer_name}}
                </dd>

                <dt class="col-sm-4 col-6 text-right">Staff name:</dt>
                <dd class="col-sm-8 col-6">
                    {{$staff_name}}
                </dd>
            </dl>
        </div>
        <table class="table-sm table">
            @if(count($posServiceItems))
                <tr>
                    <th colspan="4">Services</th>
                </tr>
                <tr>
                    <th>NAME</th>
                    <th class="text-center">UNIT PRICE</th>
                    <th class="text-center">QUANTITY</th>
                    <th class="text-center">TOTAL</th>
                </tr>

                @foreach($posServiceItems as $item)
                    <tr>
                        <td class="pl-4">{{$item['name']}}</td>
                        <td class="text-center">{{$item['unit_price'] ? 'P ' . number_format($item['unit_price'], 2) : 'FREE'}}</td>
                        <td class="text-center">
                            {{$item['quantity']}}
                        </td>
                        <td class="text-center">{{$item['total_price'] ? 'P ' . number_format($item['total_price'], 2) : 'FREE'}}</td>
                    </tr>
                @endforeach
                <tr class=" font-weight-bold">
                    <td colspan="2" class="pl-1">Total</td>
                    <td class="text-center">{{$posServiceSummary['total_quantity']}}</td>
                    <td class="text-center">P {{number_format($posServiceSummary['total_price'], 2)}}</td>
                </tr>
            @endif

            @if(count($posProductItems))
                <tr>
                    <th colspan="4">Products</th>
                </tr>
                <tr>
                    <th>NAME</th>
                    <th class="text-center">UNIT PRICE</th>
                    <th class="text-center">QUANTITY</th>
                    <th class="text-center">TOTAL</th>
                </tr>
                @foreach($posProductItems as $item)
                    <tr>
                        <td class="pl-4">{{$item['name']}}</td>
                        <td class="text-center">{{$item['unit_price'] ? 'P ' . number_format($item['unit_price'], 2) : 'FREE'}}</td>
                        <td class="text-center">
                            {{$item['quantity']}}
                        </td>
                        <td class="text-center">{{$item['total_price'] ? 'P ' . number_format($item['total_price'], 2) : 'FREE'}}</td>
                    </tr>
                @endforeach
                <tr class=" font-weight-bold">
                    <td colspan="2" class="pl-1">Total</td>
                    <td class="text-center">{{$posProductSummary['total_quantity']}}</td>
                    <td class="text-center">P {{number_format($posProductSummary['total_price'], 2)}}</td>
                </tr>
            @endif
            <tr class="font-weight-bold large">
                <td>Grand total</td>
                <td>&nbsp;&nbsp;&nbsp;</td>
                <td class="text-center">{{$posProductSummary['total_quantity'] + $posServiceSummary['total_quantity']}}</td>
                <td class="text-center">P {{number_format($total_amount, 2)}}</td>
            </tr>
        </table>

        <dl class="row">
            <dt class="col-sm-4 col-6 text-right">Date paid:</dt>
            <dd class="col-sm-8 col-6">
                {{$date_paid}}
            </dd>

            <dt class="col-sm-4 col-6 text-right">Paid to:</dt>
            <dd class="col-sm-8 col-6">
                {{$paid_to}}
            </dd>

            <dt class="col-sm-4 col-6 text-right">Cash:</dt>
            <dd class="col-sm-8 col-6">
                P {{number_format($cash, 2)}}
            </dd>

            <dt class="col-sm-4 col-6 text-right">Change:</dt>
            <dd class="col-sm-8 col-6">
                P {{number_format($change, 2)}}
            </dd>
            @if($points)
                <dt class="col-sm-4 col-6 text-right">Points used:</dt>
                <dd class="col-sm-8 col-6">
                    P {{number_format($points_in_peso, 2)}} ({{number_format($points, 1)}} points)
                </dd>
            @endif
            @if($discount)
                <dt class="col-sm-4 col-6 text-right">Discount:</dt>
                <dd class="col-sm-8 col-6">
                    P {{number_format($discount_in_peso, 2)}} ({{number_format($discount, 1)}}%)
                </dd>
            @endif
            @if($rfid)
                <dt class="col-sm-4 col-6 text-right">RFID:</dt>
                <dd class="col-sm-8 col-6">
                    P {{number_format($card_load_used, 2)}} ({{$rfid}})
                </dd>
            @endif
        </dl>

        <hr>

        <div class="footer text-center">
            <div>This is not an official receipt</div>
            <div>This is not a sales invoice</div>
            <div>*** CUSTOMER COPY ***</div>
        </div>

    </div>
    </body>
</html>

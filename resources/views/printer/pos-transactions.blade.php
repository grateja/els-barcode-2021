<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <style>
        body, html {
            width: 11in;
            margin: 20px auto;
        }



        @media print{@page {size: landscape}}
    </style>
    <title>Transactions</title>
</head>
<body>
    <table class="table table-bordered table-sm">
        <tr>
            <td colspan="2" class="text-right">Paid: </td>
            <td class="text-left">{{$summary['paidCount']}}</td>
            <td colspan="2" class="text-left">P {{number_format($summary['totalCollections'], 2)}}</td>
        </tr>
        <tr>
            <td colspan="2" class="text-right">Unpaid: </td>
            <td class="text-left">{{$summary['unpaidCount']}}</td>
            <td colspan="3" class="text-left">P {{number_format($summary['totalUnpaid'], 2)}}</td>
        </tr>
        <tr>
            <td colspan="2" class="text-right">Total: </td>
            <td class="text-left">{{$summary['totalCount']}}</td>
            <td colspan="3" class="text-left">P {{number_format($summary['totalSales'], 2)}}</td>
        </tr>
        <tr class="">
            <th>JO #</th>
            <th>Customer</th>
            <th>Date & Time</th>
            <th>Paid</th>
            <th>Items</th>
        </tr>
        @foreach($result as $item)
            <tr>
                <td class="{{$item->date_paid ? 'text-primary' : 'text-danger'}}">{{$item->job_order}}</td>
                <td class="top-left customer-name">{{$item->customer_name}}</td>
                <td class="top-left date">{{$item->dateStr}}</td>
                <td class="top-left date-paid">{{$item->datePaidStr}}</td>
                <td class="top-left">
                    <table class="table table-borderless table-sm mb-0">
                        @if(count($item->posServiceItems()))
                            <tr class="text-center border-bottom">
                                <th class="sub-title">Name</th>
                                <th class="sub-title">Unit price</th>
                                <th class="sub-title">Quantity</th>
                                <th class="sub-title">Amount</th>
                            </tr>
                            @foreach($item->posServiceItems() as $serviceItem)
                                <tr class="text-center">
                                    <td class="text-left">{{$serviceItem->name}}</td>
                                    <td>P {{ number_format($serviceItem->unit_price, 2) }}</td>
                                    <td>{{ $serviceItem->quantity }}</td>
                                    <td>P {{ number_format($serviceItem->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        @endif
                        @if(count($item->posProductItems()))
                            @foreach($item->posProductItems() as $productItem)
                                <tr class="text-center">
                                    <td class="text-left">{{$productItem->name}}</td>
                                    <td>P {{number_format($productItem->unit_price, 2)}}</td>
                                    <td>{{$productItem->quantity}}</td>
                                    <td>P {{number_format($productItem->total_price, 2)}}</td>
                                </tr>
                            @endforeach
                            <tr class="font-weight-bold border-top border-dark">
                                <td colspan="3">Total</td>
                                <td class="text-right">P {{number_format($item->total_price, 2)}}</td>
                            </tr>
                        @endif
                    </table>
                </td>
            </tr>
        @endforeach
    </table>
</body>
</html>



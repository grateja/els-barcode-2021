@extends('layouts.app')

@section('content')

<div class="container">
    <hr>
    @include('queues/_queue-items')

    @if($queue)
        <hr>
        <form method="post">
            {{csrf_field()}}
            <input type="submit" value="Complete" class="btn btn-primary" formaction="/web/queues/complete">
            <input type="submit" value="Clear" class="btn btn-danger" formaction="/web/queues/clear">
        </form>
    @endif
</div>

@endsection

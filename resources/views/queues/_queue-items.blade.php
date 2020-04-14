@if($queue != null)

    <h4 class="title">{{$queue->name}}</h4>

    @if($queue->queueItems)
    <form method="post">
        {{csrf_field()}}
        <ul>

            @foreach($queue->queueItems as $queueItem)

                <li>{{$queueItem->code}} <input type="submit" value="remove" class="btn btn-small btn-default" formaction="/web/queues/queue-items/{{$queueItem->id}}/remove-item"></li>

            @endforeach
        </ul>
    </form>

    @endif

@else

    <div class="alert alert-info">Queue is completed/empty</div>

@endif

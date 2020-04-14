<form action="/web/queues/save-to-queue" method="POST">
    {{csrf_field()}}
    @if($queue == null)
        <h4>Create new queues</h4>
        <hr>

        <div class="form-group">
            <label for="name">Name:</label> <br />
            <input type="text" name="name" id="name" value="{{ old('name') ? old('name') : $prefName }}" class="form-control">
            @if($errors->has('name'))
                <span class="text-danger">{{$errors->first('name')}}</span>
            @endif
        </div>
    @endif

    <div class="form-group">
        <label for="code">Code:</label> <br />
        <input type="text" name="code" id="code" value="{{ old('code') ? old('code') : $code }}" class="form-control">
        @if($errors->has('code'))
            <span class="text-danger">{{$errors->first('code')}}</span>
        @endif
    </div>

    <hr>

    <div class="form-group">
        <input type="submit" value="Save" class="btn btn-primary">
        <a href="/scan/{{$origin}}/{{$code}}" class="btn btn-info">cancel</a>
    </div>
</form>

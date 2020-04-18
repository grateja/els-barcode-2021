<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Queue;
use App\QueueItem;
use Illuminate\Support\Facades\DB;

class QueuesController extends Controller
{
    public function clear() {
        $queue = Queue::where([
            'user_id' => auth()->id(),
            'done' => false,
        ])->first();

        $queue->delete();
        return redirect(route('queues.default'));
    }

    public function complete() {
        $queue = Queue::where([
            'user_id' => auth()->id(),
            'done' => false,
        ])->orderByDesc('created_at')->firstOrFail();

        $queue->update([
            'done' => true,
        ]);

        return redirect(route('queues.default'));
    }

    public function saveToQueue(Request $request) {
        $rules = [
            'code' => 'required|unique:queue_items,id'
        ];

        $queue = Queue::where([
            'user_id' => auth()->id(),
            'done' => false,
        ])->orderByDesc('created_at')->first();

        if($queue == null) {
            $rules['name'] = 'required|unique:queues';
        }

        if($request->validate($rules)) {
            return DB::transaction(function () use ($request, $queue) {
                if($queue == null) {
                    $queue = Queue::create([
                        'name' => $request->name,
                        'user_id' => auth()->id(),
                    ]);
                }

                $queueItem = QueueItem::create([
                    'id' => $request->code,
                    'queue_id' => $queue->id,
                ]);

                return redirect(route('queues.default'));
            });
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $queue = Queue::with('queueItems')->where([
            'user_id' => auth()->id(),
            'done' => false,
        ])->orderByDesc('created_at')->first();

        return view('queues.index', [
            'queue' => $queue,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $code
     * @return \Illuminate\Http\Response
     */
    public function show($code, $origin = null)
    {
        $errors = [];
        $queue = Queue::with('queueItems')->where([
            'user_id' => auth()->id(),
            'done' => false,
        ])->orderByDesc('created_at')->first();

        $queueItem = QueueItem::find($code);

        if($queueItem != null) {
            $errors = [
                'code' => 'This item is already in the queues'
            ];
        }

        return view('queues.view-item', [
            'queue' => $queue,
            'model' => $queueItem,
            'code' => $code,
            'prefName' => "scan.$origin." . date('M-d:h-i'),
        ])->withErrors($errors);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\NotificationOrder;
use Illuminate\Http\Request;
use stdClass;
use TCG\Voyager\Models\User;

class NotificationOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $this->request = $request;

        $data = NotificationOrder::where(
            function ($query) {
                if (
                    $this->request->sender_id
                ) {
                    return  $query->where(
                        [
                            ['sender_id', '=', $this->request->sender_id]
                        ]
                    );
                } elseif (
                    $this->request->reciver_id
                ) {
                    return  $query->where(
                        [
                            ['reciver_id', '=', $this->request->reciver_id]
                        ]
                    );
                }
            }
        )->get();
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $obj = new stdClass();
                $obj->id = $value->id;
                $obj->sender_id = $value->sender_id;
                $obj->sender_name = User::where('id', $value->sender_id)->first()->name;
                $obj->reciver_id = $value->reciver_id;
                $obj->reciver_name =  User::where('id', $value->reciver_id)->first()->name;
                $obj->order_id = $value->order_id;
                $obj->title = $value->title;
                $obj->body = $value->body;
                $obj->active = $value->active;
                $obj->created_at = $value->created_at;
                $obj->updated_at = $value->updated_at;
                $result[] = $obj;
            }
            return $result;
        } else {
            return [];
        }
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
     * @param  \App\Models\NotificationOrder  $notificationOrder
     * @return \Illuminate\Http\Response
     */
    public function show(NotificationOrder $notificationOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NotificationOrder  $notificationOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(NotificationOrder $notificationOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NotificationOrder  $notificationOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NotificationOrder $notificationOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NotificationOrder  $notificationOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(NotificationOrder $notificationOrder)
    {
        //
    }
}

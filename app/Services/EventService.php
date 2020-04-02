<?php

namespace App\Services;

use App\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventService
{
    public function createEvent($data)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::guard('api')->id();
            $event = Event::create([
                'user_id' => $userId,
                'name' => $data['name'],
                'description' => $data['description'],
                'date_event' => $data['date_event'],
                'location' => $data['location'],
            ]);
            $event->save();
            DB::commit();
            return $event;
        } catch (\Throwable $exception) {
            DB::rollBack();
            return $event = false;
        }
    }

    public function updateEvent($data, $id)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::guard('api')->id();
            $event = Event::whereId($id)->update([
                'user_id' => $userId,
                'name' => $data['name'],
                'description' => $data['description'],
                'date_event' => $data['date_event'],
                'location' => $data['location'],
            ]);
            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
        return $event;
    }

    public function destroyEvent($id = null)
    {
        $userId = Auth::guard('api')->id();
        $findId = Event::whereId($id)->get()->toArray();
        if (isset($id) && !$findId) {
            return $event = 0;
        }
        if (!isset($id)) {
            return $event = Event::where('user_id', $userId)->delete();
        }
        return $event = Event::whereId($id)->where('user_id', $userId)->delete();
    }

    public function filterEvent($fixedDate, $from, $to, $sort)
    {
        $event = DB::table('events');
        if (isset($from) && isset($to)) {
            $event = $event->whereBetween('date_event', [$from, $to]);
            $from = null;
            $to = null;
            $fixedDate = null;
        }
        if (isset($from)) {
            $event = $event->where('date_event', '>=', $from);
            $fixedDate = null;
        }
        if (isset($to)) {
            $event = $event->where('date_event', '<=', $to);
            $fixedDate = null;
        }
        if (isset($fixedDate)) {
            $event = $event->where('date_event', '=', $fixedDate);
            $from = null;
            $to = null;
        }
        if (isset($sort)) {
            $event = $event->orderBy('date_event', $sort);
        }
        $event = $event->get()->toArray();
        return $event;

    }
}

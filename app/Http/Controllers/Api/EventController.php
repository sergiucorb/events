<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use App\Services\ValidationService;
use DateTime;
use Illuminate\Http\Request;


class EventController extends Controller
{
    const EVENT_NOT_FOUND = 'Event not found!';
    protected $event;
    protected $validation;


    public function __construct(EventService $event, ValidationService $validation)
    {
        $this->event = $event;
        $this->validation = $validation;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validator = $this->validation->validateEvent($data);
        if ($validator->fails()) {
            return $response = response()->json($validator->errors(), 417);
        }
        $event = $this->event->createEvent($data);
        if ($event == false) {
            return $response = response()->json(['message' => 'Something got wrong! Try again!', 'event' => $event], 404);
        }
        return $response = response()->json(['message' => 'Event successfully created!', 'event' => $event], 200);

    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = $this->validation->validateEvent($data);
        if ($validator->fails()) {
            return $response = response()->json($validator->errors(), 417);
        }
        $updateEvent = $this->event->updateEvent($data, $id);
        if ($updateEvent != 1) {
            return $response = response()->json(['message' => static::EVENT_NOT_FOUND], 404);
        }
        return $response = response()->json(['message' => 'Event successfully updated!'], 200);

    }

    public function delete($id = null)
    {
        $deleteEvent = $this->event->destroyEvent($id);
        if ($deleteEvent != 1) {
            return $response = response()->json(['message' => static::EVENT_NOT_FOUND], 404);
        }
        $response = response()->json(['message' => 'Event successfully deleted!'], 200);
        return $response;
    }


    public function filter()
    {
        $from = request('from');
        $to = request('to');
        $fixedDate = request('fixed_date');
        $sort = request('sort');
        $dateValidator = $this->validation->validateDate($from, $to, $fixedDate);
        $sortValidator = $this->validation->validateSort($sort);
        $errors = array_merge($dateValidator, $sortValidator);
        if (!empty($errors)) {
            return $response = response()->json($errors, 417);
        }
        $event = $this->event->filterEvent($fixedDate, $from, $to, $sort);
        return response()->json($event, 200);
    }
}

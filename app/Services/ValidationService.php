<?php


namespace App\Services;


use DateTime;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;

class ValidationService
{
    public function validateEvent($data)
    {
        $message = [
            'name.required' => 'The :attribute field is required.',
            'name.max' => 'The :attribute must be less than :value characters.',
            'name.string' => 'The :attribute must be string type.',
            'description.required' => 'The :attribute field is required.',
            'description.max' => 'The :attribute must be less than :value characters.',
            'description.string' => 'The :attribute must be string type.',
            'date_event.required' => 'The :attribute field is required.',
            'date_event.date_format' => 'The :attribute must be the  yyyy-mm-dd format.',
            'location.required' => 'The :attribute field is required.',
            'location.string' => 'The :attribute must be string type.',
            'location.max' => 'The :attribute must be less than :value characters.',

        ];
        return $validator = Validator::make($data, [
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'date_event' => 'required|date_format:Y-m-d',
            'location' => 'required|string|max:50',

        ], $message);
    }


    public function validateDate($from, $to, $fixedDate)
    {
        $errors = [];
        if (isset($from) && !$this->checkDateFormat($from)) {
            $errors[] = "This 'from' field is invalid";
        }
        if (isset($to) && !$this->checkDateFormat($to)) {
            $errors[] = "This 'to' field is invalid";
        }
        if (isset($fixedDate) && !$this->checkDateFormat($fixedDate)) {
            $errors[] = "This 'fixedDate' field is invalid";
        }
        return $errors;
    }

    public function validateSort($sort)
    {
        $error = [];
        $descendent = 'desc';
        $ascendent = 'asc';
        if (isset($sort) && (strtolower($sort) !== $ascendent && strtolower($sort) !== $descendent)) {
            $error[] .= "The 'sort' field supports only 'asc' and 'desc' ";
        }
        return $error;
    }

    protected function checkDateFormat($date)
    {
        $format = 'Y-m-d';
        $formatedDate = DateTime::createFromFormat($format, $date);
        return $formatedDate && $formatedDate->format($format) === $date;
    }

    public function validateRegister($data)
    {
        $messages = [
            'email.required' => 'Email field is required.',
            'password.required' => 'Password field is required',
            'password.min' => 'Password must have minimum 6 characters',
            'password.confirmed' => "password_confirmation field doesn't match with password",
            'password_confirmation.required' => 'Password Confirmation field is required.',
        ];
        return $validator = Validator::make($data, [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|string|confirmed',
        ], $messages);
    }

    public function validateLogin($data)
    {
        $message = [
            'email.required' => 'Email field is required.',
            'password.required' => 'Password field is required.',
        ];
        return $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required',
        ], $message);
    }
}

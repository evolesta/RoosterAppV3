<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use App\Helpers\Helper;

class RegistrationController extends Controller
{
    // ++ DEFAULT CRUD ACTIONS
    // Get all registrations
    public function index(Request $request)
    {
        return response()->json(Registration::all());
    }

    public function show(Request $request, string $id)
    {
        $registration = Registration::find($id)->first();
        return response()->json($registration);
    }

    // Submit a new start time
    public function Start(Request $request)
    {
        // validate input
        $request->validate([
            'user_id' => 'required|integer',
            'date' => 'required|date',
            'start' => 'required'
        ]);

        // validate time
        if (!$this->validateTime($request->start)) {
            return response()->json(['error' => 'No correct time format!'], 400);
        }

       $registration = new Registration();
       $registration->user_id = $request->user_id; 
       $registration->date = $request->date; 
       $registration->start = $request->start; 
       $registration->save();

       return response()->json($registration);
    }

    // Returns a registration of today if made, otherwise return 204 no content
    public function Today(Request $request)
    {
        $registration = $this->GetTodaysRegistration($request->header('Authorization'));

        // if no registration has been made, $registration contains null
        if ($registration == null) {
            return response()->json([], 204);
        }

        return response()->json($registration);
    }

    // Submits the end time and calculate the differences
    public function End(Request $request)
    {
        // validate user input
        $request->validate([
            'end' => 'required'
        ]);

        if (!$this->validateTime($request->end)) {
            return response()->json(['error' => 'No correct time format!'], 400);
        }
        
        // submit end time and calculate the differences
        $registration = $this->GetTodaysRegistration($request->header('Authorization'));
        
        // check for null, if no registration has been made return a error
        if ($registration == null) {
            return response()->json(['error' => 'No registration made today!'], 400);
        }

        // update registration and calculate worked hours and difference
        $registration->end = $request->end;

        // Calculate worked hours from start and end times
        $start = date_create($registration->date . 'T' . $registration->start);
        $end = date_create($registration->date . 'T' . $registration->end);
        $workedHours = date_diff($start, $end);
        
        // Calculate the difference hours of the worked hours and the hours to work
        $user = Helper::GetUser($request->header('Authorization'));
        $hoursToWorkObj = date_create($user->workHours);
        $workedHoursObj = date_create($workedHours->format('%H:%I:%S'));
        $difference = date_diff($hoursToWorkObj, $workedHoursObj);

        // Update database row
        $registration->workedHours = $workedHours->format('%H:%I:%S');
        $registration->difference = $difference->format('%r%H:%I:%S');
        $registration->save();

        return response()->json($registration);
    }


    // helper function to validate time input from a payload
    protected function validateTime($input)
    {
        // Check if the time format contains a : as delimeter
        if (!str_contains($input, ':')) {
            return false;
        }

        // check if we can succesfully split the hours and minutes
        try {
            $arr = explode(':', $input);
        }
        catch (Exception $e) {
            return false;
        }

        // check if there aren't any empty values
        if (empty($arr[0]) || empty($arr[1])) {
            return false;
        }

        return true;
    }

    protected function GetTodaysRegistration($token)
    {
        $today = date('Y-m-d'); // get todays date
        $user = Helper::GetUser($token); // get user
        $registration = Registration::where('date', $today)
            ->where('user_id', $user->id)
            ->first();

        return $registration;
    }
}

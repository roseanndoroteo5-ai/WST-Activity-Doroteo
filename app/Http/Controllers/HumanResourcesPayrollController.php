<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HumanResourcesPayrollController extends Controller
{
    public function payroll(Request $request)
    {
        $age = $request->input('age');

        $message = "Your age is: " . $age;

        return response()->json([
            'age' => $age,
            'message' => $message
        ]);
    }
}
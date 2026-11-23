<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function calculate(Request $request)
    {
        
        $employmentType = $request->employmentType;
        $positionTier = $request->positionTier;
        $daysAbsent = $request->daysAbsent;
        $minutesLate = $request->minutesLate;
        $overtimeHours = $request->overtimeHours;

      
        $baseSalary = 0;
        $sss = 0;
        $philhealth = 0;
        $pagibig = 0;

       
        if ($employmentType === 'COS') {

            $baseSalary = 18000;
            $sss = 0;
            $philhealth = 0;
            $pagibig = 0;

        } elseif ($employmentType === 'Regular' && $positionTier === 'Junior') {

            $baseSalary = 25000;
            $sss = 1125;
            $philhealth = 625;
            $pagibig = 200;

        } elseif ($employmentType === 'Regular' && $positionTier === 'Senior') {

            $baseSalary = 40000;
            $sss = 1800;
            $philhealth = 1000;
            $pagibig = 200;

        } else {

            return response()->json([
                'message' => 'Invalid employment type or position tier.'
            ], 400);
        }

        
        $daysPresent = 22 - $daysAbsent;
        $dailyRate = $baseSalary / 22;
        $hourlyRate = $dailyRate / 8;
        $lateDeduction = ($hourlyRate / 60) * $minutesLate;
        $absentDeduction = $dailyRate * $daysAbsent;
        $overtimePay = $overtimeHours * ($hourlyRate * 1.25);
        $grossPay = $baseSalary
            - $lateDeduction
            - $absentDeduction
            + $overtimePay;

        $totalDeductions = $sss + $philhealth + $pagibig;
        $netPay = $grossPay - $totalDeductions;

        return response()->json([

            'daysAbsent' => $daysAbsent,
            'employmentType' => $employmentType,
            'positionTier' => $positionTier,
            'baseSalary' => round($baseSalary, 2), 
            'daysPresent' => $daysPresent,
            'dailyRate' => round($dailyRate, 2),
            'lateDeduction' => round($lateDeduction, 2),
            'absentDeduction' => round($absentDeduction, 2),
            'overtimePay' => round($overtimePay, 2),
            'grossPay' => round($grossPay, 2),
            'sss' => round($sss, 2),
            'philhealth' => round($philhealth, 2),
            'pagibig' => round($pagibig, 2),
            'totalDeductions' => round($totalDeductions, 2),
            'netPay' => round($netPay, 2),
        ]);
    }
}
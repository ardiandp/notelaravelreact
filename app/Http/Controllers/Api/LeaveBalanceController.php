<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;

class LeaveBalanceController extends Controller
{
    public function index(Request $request)
    {
        $balances = LeaveBalance::with('leaveType')
            ->where('user_id', $request->user()->id)
            ->where('year', now()->year)
            ->get();

        return response()->json($balances);
    }
}

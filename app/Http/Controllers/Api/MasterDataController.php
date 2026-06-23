<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Position;
use App\Models\Shift;
use App\Models\WorkLocation;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function divisions()
    {
        return response()->json(Division::with('children')->whereNull('parent_id')->get());
    }

    public function positions()
    {
        return response()->json(Position::with('division')->get());
    }

    public function shifts()
    {
        return response()->json(Shift::where('is_active', true)->get());
    }

    public function workLocations()
    {
        return response()->json(WorkLocation::where('is_active', true)->get());
    }

    public function leaveTypes()
    {
        return response()->json(LeaveType::where('is_active', true)->get());
    }

    public function employees()
    {
        $users = User::with('employeeDetail.division', 'employeeDetail.position')
            ->where('aktif', true)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nip' => $user->nip,
                    'division' => $user->employeeDetail?->division?->nama_bagian,
                    'position' => $user->employeeDetail?->position?->nama,
                ];
            });

        return response()->json($users);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AdminApprovalController extends Controller
{
    public function show($id)
    {
        $attendance = Attendance::with('break_times', 'user')->findOrFail($id);
        return view('admin_approval_of_amendment_application', compact('attendance'));
    }

    public function approve($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update(['approval_status' => '承認済み']);
        return back();
    }
}

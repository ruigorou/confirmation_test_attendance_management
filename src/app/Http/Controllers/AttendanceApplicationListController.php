<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;

class AttendanceApplicationListController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', '承認待ち');

        $attendances = Attendance::with('user')
            ->where('user_id', auth()->id())
            ->where('approval_status', $tab)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('attendance_application_list', compact('attendances', 'tab'));
    }
}

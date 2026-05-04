<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminStaffListController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin')->orderBy('id')->get();
        return view('admin_staff_list', compact('users'));
    }
}

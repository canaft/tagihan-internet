<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ODC;
use App\Models\ODP;

class SettingController extends Controller
{
    public function index()
    {
            $odcs = ODC::all(); // ambil semua data ODC
                $odps = ODP::all(); // <--- ini yang dibutuhkan

    return view('admin.setting', compact('odcs','odps'));

    }
}

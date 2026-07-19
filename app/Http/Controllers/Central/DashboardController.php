<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;

class DashboardController extends Controller
{
    public function index()
    {
        $tenantCount = Tenant::count();

        return view('central.dashboard', [
            'tenantCount' => $tenantCount,
        ]);
    }
}

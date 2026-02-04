<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Dashboard - Analytics
    public function dashboardAnalytics(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        $adminAuth=\Auth::guard('admin')->user();
        if($adminAuth->type=='7'){
            return Redirect('/admin/dc-report');
        }

        return view('/admin/dashboard-analytics', [
            'pageConfigs' => $pageConfigs
        ]);
    }

}


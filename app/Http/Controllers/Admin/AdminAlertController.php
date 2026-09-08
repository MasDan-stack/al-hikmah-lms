<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AlertService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAlertController extends Controller
{
    public function __construct(
        protected AlertService $alertService
    ) {}

    /**
     * Tampilkan Pusat Peringatan Operasional (Operational Alerts Center)
     */
    public function index(Request $request): View
    {
        $allAlerts = $this->alertService->getAllAlerts();
        $selectedTab = $request->query('tab', 'all');

        return view('admin.alerts.index', compact('allAlerts', 'selectedTab'));
    }
}

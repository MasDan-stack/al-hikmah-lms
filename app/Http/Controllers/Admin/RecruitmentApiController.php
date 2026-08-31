<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MentorApplication;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecruitmentApiController extends Controller
{
    /**
     * Data untuk ApexCharts: Status funnel rekrutmen.
     */
    public function funnel(Request $request): JsonResponse
    {
        $data = MentorApplication::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        $labels = [
            'submitted' => 'Pendaftar',
            'document_passed' => 'Lolos Dokumen',
            'test_passed' => 'Lolos Tes AI',
            'interview_passed' => 'Lolos Wawancara',
            'accepted' => 'Diterima',
            'probation' => 'Masa Percobaan',
            'rejected' => 'Ditolak',
        ];

        $chartData = [];
        $chartLabels = [];

        foreach ($labels as $key => $label) {
            $chartLabels[] = $label;
            $chartData[] = $data->get($key, 0);
        }

        return response()->json([
            'labels' => $chartLabels,
            'series' => $chartData,
        ]);
    }

    /**
     * Data untuk ApexCharts: Pendaftar harian (7 hari terakhir).
     */
    public function dailyTrend(Request $request): JsonResponse
    {
        $dates = collect(range(6, 0))->map(function ($days) {
            return now()->subDays($days)->format('Y-m-d');
        });

        $registrations = MentorApplication::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total')
        )
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date');

        $chartData = [];
        $chartLabels = [];

        foreach ($dates as $date) {
            $chartLabels[] = Carbon::parse($date)->format('d M');
            $chartData[] = $registrations->get($date, 0);
        }

        return response()->json([
            'labels' => $chartLabels,
            'series' => [
                [
                    'name' => 'Pendaftar',
                    'data' => $chartData,
                ],
            ],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RiskReportExport;
use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\PredictiveAnalyticsAuditLog;
use App\Models\Program;
use App\Models\RevenueForecast;
use App\Models\StudentDropoutPrediction;
use App\Models\StudentLearningVelocity;
use App\Services\PredictiveAnalytics\DropoutPredictionService;
use App\Services\PredictiveAnalytics\LearningVelocityService;
use App\Services\PredictiveAnalytics\RevenueForecastService;
use App\Services\PredictiveAnalytics\TeacherPredictionService;
use App\Services\PredictiveAnalytics\WhatsAppInterventionService;
use Illuminate\Http\Request;

class PredictiveAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $programId = $request->filled('program_id') ? (int) $request->program_id : null;
        $programs = Program::all();
        $today = today();

        // 1. Dropout Risk Predictions
        $dropoutQuery = StudentDropoutPrediction::with([
            'student.enrollments.program',
            'student.parent.user',
        ])
            ->whereDate('prediction_date', $today);

        if ($programId) {
            $dropoutQuery->whereHas('student.enrollments', function ($q) use ($programId) {
                $q->where('program_id', $programId)->where('status', 'active');
            });
        }

        $dropoutRisks = $dropoutQuery->orderByDesc('risk_score')->paginate(10, ['*'], 'dropout_page')->withQueryString();

        // 2. Learning Velocity Tracker
        $velocityQuery = StudentLearningVelocity::with([
            'student.enrollments.program',
            'student.progress',
        ])
            ->whereDate('calculation_date', $today);

        if ($programId) {
            $velocityQuery->whereHas('student.enrollments', function ($q) use ($programId) {
                $q->where('program_id', $programId)->where('status', 'active');
            });
        }

        $velocities = $velocityQuery->orderByDesc('velocity_ayat_per_day')->paginate(10, ['*'], 'velocity_page')->withQueryString();

        // 3. Revenue Forecasts (6 Bulan)
        $revenueForecasts = RevenueForecast::where('forecast_date', function ($q) {
            $q->selectRaw('MAX(forecast_date)')->from('revenue_forecasts');
        })
            ->orderBy('forecast_month')
            ->get();

        if ($revenueForecasts->isEmpty()) {
            $revenueForecasts = RevenueForecast::orderBy('forecast_month')->take(6)->get();
        }

        // 4. Teacher Coaching Alerts
        $teacherPredictions = Mentor::with(['user', 'students'])
            ->where('status', 'active')
            ->orderByDesc('coaching_needed')
            ->orderByRaw("FIELD(coaching_urgency, 'critical', 'high', 'medium', 'low')")
            ->paginate(10, ['*'], 'teacher_page')
            ->withQueryString();

        // 4 Executive KPI Cards
        $criticalCount = StudentDropoutPrediction::whereDate('prediction_date', $today)
            ->where('risk_level', 'critical')
            ->count();

        $slowVelocityCount = StudentLearningVelocity::whereDate('calculation_date', $today)
            ->where('velocity_status', 'slow')
            ->count();

        $nextMonthForecast = $revenueForecasts->first();
        $nextMonthRevenue = (float) ($nextMonthForecast->predicted_amount ?? 0);

        $coachingCount = Mentor::where('status', 'active')
            ->where('coaching_needed', true)
            ->count();

        return view('admin.analytics.predictive.index', [
            'programs' => $programs,
            'selectedProgramId' => $programId,
            'dropoutRisks' => $dropoutRisks,
            'velocities' => $velocities,
            'revenueForecasts' => $revenueForecasts,
            'teacherPredictions' => $teacherPredictions,
            'criticalCount' => $criticalCount,
            'slowVelocityCount' => $slowVelocityCount,
            'nextMonthRevenue' => $nextMonthRevenue,
            'coachingCount' => $coachingCount,
        ]);
    }

    public function recalculateAll(
        Request $request,
        DropoutPredictionService $dropoutService,
        LearningVelocityService $velocityService,
        RevenueForecastService $revenueService,
        TeacherPredictionService $teacherService
    ) {
        $dropoutService->invalidateCache();
        $dropoutService->snapshotDailyPredictions();
        $velocityService->snapshotDailyVelocities();
        $teacherService->snapshotDailyPredictions();
        $revenueService->snapshotMonthlyForecast();

        // Catat Audit Trail
        PredictiveAnalyticsAuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'action_type' => 'recalculate',
            'target_type' => 'student',
            'metadata' => [
                'triggered_by' => auth()->user()?->name ?? 'Admin',
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.analytics.predictive.index')
            ->with('success', '✨ Seluruh model Predictive Analytics berhasil dikalkulasi ulang secara real-time!');
    }

    public function sendWhatsAppIntervention(Request $request, WhatsAppInterventionService $waService)
    {
        $request->validate([
            'prediction_id' => 'required|exists:student_dropout_predictions,id',
        ]);

        $prediction = StudentDropoutPrediction::with('student.parent.user')->findOrFail($request->prediction_id);

        $result = $waService->sendInterventionByPrediction($prediction, auth()->id());

        if ($result['success']) {
            return back()->with('success', '✅ Pesan WhatsApp intervensi dini berhasil dikirim ke nomor wali santri.');
        }

        return back()->with('error', '❌ Gagal mengirim WhatsApp: '.($result['reason'] ?? 'Terjadi kesalahan.'));
    }

    public function exportRiskReport(Request $request)
    {
        $format = $request->query('format', 'csv');
        $filename = 'santri_berisiko_dropout_'.now()->format('Y-m-d');

        // Catat Audit Trail
        PredictiveAnalyticsAuditLog::create([
            'user_id' => auth()->id() ?? 1,
            'action_type' => 'export',
            'target_type' => 'student',
            'metadata' => ['format' => $format],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return (new RiskReportExport)->download($filename, $format);
    }
}

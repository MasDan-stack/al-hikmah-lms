<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialAuditLog;
use App\Models\Mentor;
use App\Models\Program;
use App\Services\BroadcastService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBroadcastController extends Controller
{
    public function __construct(
        protected BroadcastService $broadcastService
    ) {}

    /**
     * Tampilkan form WhatsApp Broadcast & Log Pengiriman
     */
    public function index(Request $request): View
    {
        $programs = Program::all();
        $mentors = Mentor::where('is_active', true)->get();
        $presets = $this->broadcastService->getPresetTemplates();

        // Riwayat Broadcast dari Audit Log
        $broadcastLogs = FinancialAuditLog::with('user')
            ->where('action', 'whatsapp_broadcast')
            ->latest('created_at')
            ->take(15)
            ->get();

        // Hitung total calon penerima default
        $defaultRecipientsCount = $this->broadcastService->resolveRecipients('all')->count();

        return view('admin.broadcast.index', compact(
            'programs',
            'mentors',
            'presets',
            'broadcastLogs',
            'defaultRecipientsCount'
        ));
    }

    /**
     * Eksekusi pengiriman pesan broadcast massal
     */
    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'target_type' => 'required|string|in:all,program,mentor',
            'target_id' => 'nullable|integer',
            'message_template' => 'required|string|min:10|max:2000',
        ]);

        $result = $this->broadcastService->dispatchBroadcast(
            $validated['message_template'],
            $validated['target_type'],
            ! empty($validated['target_id']) ? (int) $validated['target_id'] : null,
            $validated['title']
        );

        if ($result['total'] === 0) {
            return back()->with('warning', 'Tidak ditemukan nomor WhatsApp aktif untuk kriteria target penerima yang dipilih.');
        }

        return back()->with(
            'success',
            "Broadcast '{$validated['title']}' berhasil diproses! Sebanyak {$result['success_count']} dari {$result['total']} pesan terkirim."
        );
    }

    /**
     * Preview parsing variabel pesan via AJAX
     */
    public function preview(Request $request): JsonResponse
    {
        $template = $request->input('template', '');
        $targetType = $request->input('target_type', 'all');
        $targetId = $request->input('target_id') ? (int) $request->input('target_id') : null;

        $recipients = $this->broadcastService->resolveRecipients($targetType, $targetId);
        $sampleRecipient = $recipients->first() ?? [
            'parent_name' => 'Bapak Ahmad Fauzi',
            'children_names' => 'Fathur & Aisyah',
            'program_names' => 'Tahfidz Al-Qur\'an Juz 30',
            'phone' => '081234567890',
        ];

        $parsedMessage = $this->broadcastService->parseTemplate($template, $sampleRecipient);

        return response()->json([
            'status' => 'success',
            'total_recipients' => $recipients->count(),
            'sample_recipient' => $sampleRecipient,
            'parsed_message' => $parsedMessage,
        ]);
    }
}

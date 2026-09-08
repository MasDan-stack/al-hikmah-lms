<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MentorInterventionTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminInterventionTicketController extends Controller
{
    /**
     * Tampilkan Daftar Tiket Intervensi Komplain Wali Santri
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'open');

        $statusCounts = [
            'all' => MentorInterventionTicket::count(),
            'open' => MentorInterventionTicket::where('status', 'open')->count(),
            'in_progress' => MentorInterventionTicket::where('status', 'in_progress')->count(),
            'resolved' => MentorInterventionTicket::where('status', 'resolved')->count(),
            'escalated_to_mutation' => MentorInterventionTicket::where('status', 'escalated_to_mutation')->count(),
        ];

        $query = MentorInterventionTicket::with(['mentor.user', 'student.user', 'parent', 'feedback', 'handler'])
            ->latest();

        if ($statusFilter !== 'all' && array_key_exists($statusFilter, $statusCounts)) {
            $query->where('status', $statusFilter);
        }

        $tickets = $query->paginate(15)->withQueryString();

        return view('admin.tickets.index', compact('tickets', 'statusCounts', 'statusFilter'));
    }

    /**
     * Tampilkan Detail Tiket Intervensi Komplain
     */
    public function show(int|string $id): View
    {
        $ticket = MentorInterventionTicket::with([
            'mentor.user',
            'student.user',
            'student.parent.user',
            'parent',
            'feedback.ratings',
            'session',
            'handler',
        ])->findOrFail($id);

        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * Perbarui Status & Rencana Tindakan Tiket Intervensi
     */
    public function update(Request $request, int|string $id): RedirectResponse
    {
        $ticket = MentorInterventionTicket::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved',
            'action_plan' => 'nullable|string|max:1000',
            'resolution_notes' => 'nullable|string|max:1000',
        ]);

        $ticket->status = $validated['status'];
        $ticket->action_plan = $validated['action_plan'] ?? $ticket->action_plan;
        $ticket->resolution_notes = $validated['resolution_notes'] ?? $ticket->resolution_notes;
        $ticket->handled_by = auth()->id();

        if ($validated['status'] === 'resolved') {
            $ticket->resolved_at = now();
        }

        $ticket->save();

        return redirect()->route('admin.tickets.show', $ticket->id)
            ->with('success', "Tiket #{$ticket->ticket_number} berhasil diperbarui menjadi status [".strtoupper($ticket->status).'].');
    }

    /**
     * Eskalasi Tiket ke Mutasi Santri (Family Blacklist Engine) jika tidak dapat direkonsiliasi
     */
    public function escalate(Request $request, int|string $id): RedirectResponse
    {
        $ticket = MentorInterventionTicket::findOrFail($id);

        $validated = $request->validate([
            'escalation_notes' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($ticket, $validated) {
            $ticket->update([
                'status' => 'escalated_to_mutation',
                'resolution_notes' => 'Eskalasi ke Mutasi Santri: '.$validated['escalation_notes'],
                'handled_by' => auth()->id(),
                'resolved_at' => now(),
            ]);

            // Catat ke log mutasi santri (Family Blacklist)
            DB::table('student_mutation_logs')->insert([
                'parent_id' => $ticket->student?->parent_id ?? $ticket->parent_id,
                'student_id' => $ticket->student_id,
                'previous_mentor_id' => $ticket->mentor_id,
                'new_mentor_id' => null,
                'reason_category' => 'dissatisfaction',
                'notes' => "[Eskalasi Tiket #{$ticket->ticket_number}] ".$validated['escalation_notes'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('admin.tickets.show', $ticket->id)
            ->with('warning', "Tiket #{$ticket->ticket_number} telah dieskalasi ke Log Mutasi Santri. Rekomendasi mentor baru dapat diproses di menu pendaftaran.");
    }
}

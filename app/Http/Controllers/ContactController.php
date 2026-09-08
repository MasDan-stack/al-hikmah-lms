<?php

namespace App\Http\Controllers;

use App\Enums\NotificationType;
use App\Models\ContactMessage;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Tampilan Formulir Hubungi Kami
     */
    public function index(): View
    {
        return view('contact');
    }

    /**
     * Simpan pesan konsultasi calon wali santri ke database
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'min:8', 'max:30'],
            'address' => ['required', 'string', 'min:5', 'max:500'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'name.required' => 'Nama lengkap Orang Tua / Wali wajib diisi.',
            'email.required' => 'Alamat email aktif wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'phone.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'address.required' => 'Alamat lengkap / kota domisili wajib diisi.',
            'message.required' => 'Isi pesan / kebutuhan bimbingan wajib diisi.',
            'message.min' => 'Isi pesan minimal 10 karakter.',
        ]);

        $contactMsg = ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'message' => $validated['message'],
            'status' => 'unread',
        ]);

        // Notifikasi ke seluruh Admin via NotificationService
        NotificationService::notifyAdmins(
            'Pesan Kontak Baru Received',
            "Pesan baru dari {$validated['name']} ({$validated['phone']}): ".Str::limit($validated['message'], 80),
            NotificationType::INFO,
            route('admin.contacts.index'),
            'contact'
        );

        return back()->with('success', 'Alhamdulillah! Pesan Anda telah berhasil dikirim. Tim pengelola AL-HIKMAH akan segera menghubungi Anda melalui WhatsApp maksimal 1x24 jam.');
    }
}

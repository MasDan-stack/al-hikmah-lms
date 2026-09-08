<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    /**
     * Tampilkan daftar pesan masuk dari formulir kontak
     */
    public function index(Request $request): View
    {
        $query = ContactMessage::latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::unread()->count(),
            'contacted' => ContactMessage::contacted()->count(),
        ];

        return view('admin.contacts.index', compact('messages', 'stats'));
    }

    /**
     * Perbarui status pesan (read / contacted / unread)
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:unread,read,contacted'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $message = ContactMessage::findOrFail($id);

        $updateData = [
            'status' => $validated['status'],
        ];

        if (array_key_exists('admin_notes', $validated)) {
            $updateData['admin_notes'] = $validated['admin_notes'];
        }

        if ($validated['status'] === 'contacted' && ! $message->contacted_at) {
            $updateData['contacted_at'] = now();
        }

        $message->update($updateData);

        return back()->with('success', "Status pesan dari {$message->name} berhasil diperbarui menjadi '{$message->status_label}'.");
    }

    /**
     * Hapus pesan kontak
     */
    public function destroy(int $id): RedirectResponse
    {
        $message = ContactMessage::findOrFail($id);
        $name = $message->name;
        $message->delete();

        return back()->with('success', "Pesan kontak dari {$name} telah berhasil dihapus.");
    }
}

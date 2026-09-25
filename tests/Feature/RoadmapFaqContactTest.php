<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoadmapFaqContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_roadmap_page_can_be_rendered(): void
    {
        $response = $this->get(route('roadmap'));

        $response->assertStatus(200);
        $response->assertSee('Peta Perjalanan Belajar');
        $response->assertSee('Jalur Calon Orang Tua');
        $response->assertSee('Jalur Guru / Pendamping');
        $response->assertSee('Alur Pembayaran & SPP');
    }

    public function test_faq_page_can_be_rendered(): void
    {
        $response = $this->get(route('faq'));

        $response->assertStatus(200);
        $response->assertSee('Tanya Jawab (FAQ)');
        $response->assertSee('Apakah guru/pendamping datang langsung ke rumah?');
        $response->assertSee('Deal Dulu, Baru Bayar');
    }

    public function test_contact_page_can_be_rendered(): void
    {
        $response = $this->get(route('contact'));

        $response->assertStatus(200);
        $response->assertSee('Formulir Konsultasi & Pesan');
        $response->assertSee('Nama Orang Tua / Wali');
        $response->assertSee('Nomor WhatsApp');
        $response->assertSee('Alamat Lengkap / Kota Domisili');
    }

    public function test_parent_can_submit_contact_form(): void
    {
        $contactData = [
            'name' => 'Bunda Fatimah Az-Zahra',
            'email' => 'fatimah@example.com',
            'phone' => '081234567890',
            'address' => 'Jl. Dago Asri No. 12, Coblong, Kota Bandung',
            'message' => 'Ingin mendaftarkan 2 anak usia 11 dan 13 tahun untuk program Tahsin dan Tahfidz privat di rumah.',
        ];

        $response = $this->post(route('contact.store'), $contactData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Bunda Fatimah Az-Zahra',
            'email' => 'fatimah@example.com',
            'phone' => '081234567890',
            'status' => 'unread',
        ]);
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $response = $this->post(route('contact.store'), [
            'name' => '',
            'email' => 'not-an-email',
            'phone' => '',
            'address' => '',
            'message' => 'short',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'phone', 'address', 'message']);
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_admin_can_view_contact_messages_list(): void
    {
        $admin = User::factory()->admin()->create();

        ContactMessage::create([
            'name' => 'Ayah Hendra',
            'email' => 'hendra@example.com',
            'phone' => '085712345678',
            'address' => 'Jl. Sukajadi No. 50 Bandung',
            'message' => 'Mohon info ketersediaan guru hari Sabtu pagi.',
            'status' => 'unread',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.contacts.index'));

        $response->assertStatus(200);
        $response->assertSee('Ayah Hendra');
        $response->assertSee('hendra@example.com');
        $response->assertSee('Belum Dibaca');
    }

    public function test_admin_can_update_message_status_and_notes(): void
    {
        $admin = User::factory()->admin()->create();

        $message = ContactMessage::create([
            'name' => 'Bunda Sarah',
            'email' => 'sarah@example.com',
            'phone' => '081399887766',
            'address' => 'Jl. Riau No. 88 Bandung',
            'message' => 'Mau tanya jadwal bimbingan offline.',
            'status' => 'unread',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.contacts.update-status', $message->id), [
            'status' => 'contacted',
            'admin_notes' => 'Sudah dikonfirmasi via WA dan dijadwalkan wawancara.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('contact_messages', [
            'id' => $message->id,
            'status' => 'contacted',
            'admin_notes' => 'Sudah dikonfirmasi via WA dan dijadwalkan wawancara.',
        ]);
        $this->assertNotNull($message->fresh()->contacted_at);
    }

    public function test_admin_can_delete_contact_message(): void
    {
        $admin = User::factory()->admin()->create();

        $message = ContactMessage::create([
            'name' => 'Spam Sender',
            'email' => 'spam@example.com',
            'phone' => '0899999999',
            'address' => 'Unknown address test',
            'message' => 'Promo penawaran produk tidak penting.',
            'status' => 'unread',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.contacts.destroy', $message->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('contact_messages', [
            'id' => $message->id,
        ]);
    }
}

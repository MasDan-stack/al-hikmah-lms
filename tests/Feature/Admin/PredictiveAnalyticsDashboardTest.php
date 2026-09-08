<?php

namespace Tests\Feature\Admin;

use App\Models\ParentProfile;
use App\Models\Role;
use App\Models\Student;
use App\Models\StudentDropoutPrediction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PredictiveAnalyticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Administrator']);
        Role::firstOrCreate(['name' => 'student'], ['label' => 'Santri']);
        Role::firstOrCreate(['name' => 'parent'], ['label' => 'Wali']);
        Role::firstOrCreate(['name' => 'mentor'], ['label' => 'Mentor']);

        $this->adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);
    }

    public function test_guest_cannot_access_predictive_analytics_dashboard()
    {
        $response = $this->get(route('admin.analytics.predictive.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_predictive_analytics_dashboard_with_kpi_cards()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.analytics.predictive.index'));

        $response->assertStatus(200);
        $response->assertSee('Predictive Analytics & Early Warning System');
        $response->assertSee('Kritis Dropout');
        $response->assertSee('Velocity Lambat');
        $response->assertSee('Prediksi Revenue M+1');
        $response->assertSee('Guru Butuh Coaching');
    }

    public function test_admin_can_trigger_recalculate_all()
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.analytics.predictive.recalculate'));

        $response->assertRedirect(route('admin.analytics.predictive.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('predictive_analytics_audit_logs', [
            'action_type' => 'recalculate',
        ]);
    }

    public function test_admin_can_export_risk_report_csv()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.analytics.predictive.export', ['format' => 'csv']));

        $response->assertStatus(200);

        $this->assertDatabaseHas('predictive_analytics_audit_logs', [
            'action_type' => 'export',
        ]);
    }

    public function test_admin_can_send_whatsapp_intervention()
    {
        $parentRole = Role::where('name', 'parent')->first();
        $parentUser = User::factory()->create([
            'role_id' => $parentRole->id,
            'phone' => '081234567890',
        ]);
        $parentProfile = ParentProfile::create([
            'user_id' => $parentUser->id,
            'emergency_phone' => '081234567890',
        ]);

        $studentRole = Role::where('name', 'student')->first();
        $studentUser = User::factory()->create([
            'role_id' => $studentRole->id,
        ]);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'parent_id' => $parentProfile->id,
            'full_name' => 'Santri Target WA',
            'age' => 10,
            'gender' => 'L',
        ]);

        $prediction = StudentDropoutPrediction::create([
            'student_id' => $student->id,
            'prediction_date' => today()->toDateString(),
            'risk_score' => 85.0,
            'risk_level' => 'critical',
            'risk_factors' => ['Presensi rendah (< 70%)'],
            'is_alerted' => false,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.analytics.predictive.intervention.wa'), [
                'prediction_id' => $prediction->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $prediction->refresh();
        $this->assertTrue($prediction->is_alerted);
        $this->assertNotNull($prediction->alerted_at);

        $this->assertDatabaseHas('predictive_analytics_audit_logs', [
            'action_type' => 'intervention_wa',
            'target_id' => $student->id,
        ]);
    }
}

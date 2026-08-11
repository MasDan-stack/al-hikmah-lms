<?php

namespace App\Livewire;

use App\Models\Mentor;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class MentorManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $activeFilter = '';

    public ?int $mentorId = null;

    public ?int $user_id = null;

    public string $full_name = '';

    public ?string $specialization = '';

    public ?string $bio = '';

    public float $rating = 5.0;

    public bool $is_active = true;

    public bool $create_new_user = true;

    public string $user_email = '';

    public ?string $user_phone = '';

    public string $user_password = '';

    public bool $isModalOpen = false;

    public bool $isDeleteModalOpen = false;

    public ?int $deleteId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedActiveFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function openEditModal(int $id): void
    {
        $mentor = Mentor::with('user')->findOrFail($id);
        $this->mentorId = $mentor->id;
        $this->user_id = $mentor->user_id;
        $this->full_name = $mentor->full_name;
        $this->specialization = $mentor->specialization;
        $this->bio = $mentor->bio;
        $this->rating = (float) $mentor->rating;
        $this->is_active = (bool) $mentor->is_active;

        $this->create_new_user = false;
        $this->user_email = $mentor->user?->email ?? '';
        $this->user_phone = $mentor->user?->phone ?? '';

        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->isDeleteModalOpen = false;
        $this->resetForm();
    }

    public function saveMentor(): void
    {
        $mentorRole = Role::where('name', 'mentor')->first();
        $mentorRoleId = $mentorRole ? $mentorRole->id : 2;

        if ($this->mentorId) {
            // Editing existing mentor
            $this->validate([
                'full_name' => 'required|string|max:255',
                'specialization' => 'nullable|string|max:255',
                'bio' => 'nullable|string',
                'rating' => 'required|numeric|min:0|max:5',
                'is_active' => 'required|boolean',
            ]);

            $mentor = Mentor::findOrFail($this->mentorId);
            $mentor->update([
                'full_name' => $this->full_name,
                'specialization' => $this->specialization,
                'bio' => $this->bio,
                'rating' => $this->rating,
                'is_active' => $this->is_active,
            ]);

            if ($mentor->user) {
                $mentor->user->update([
                    'name' => $this->full_name,
                    'phone' => $this->user_phone,
                ]);
            }

            session()->flash('message', 'Data pendamping berhasil diperbarui.');
        } else {
            // Creating new mentor
            if ($this->create_new_user) {
                $this->validate([
                    'full_name' => 'required|string|max:255',
                    'user_email' => 'required|email|unique:users,email',
                    'user_password' => 'required|string|min:6',
                    'user_phone' => 'nullable|string|max:30',
                    'specialization' => 'nullable|string|max:255',
                    'bio' => 'nullable|string',
                    'rating' => 'required|numeric|min:0|max:5',
                    'is_active' => 'required|boolean',
                ]);

                $user = User::create([
                    'name' => $this->full_name,
                    'email' => $this->user_email,
                    'password' => Hash::make($this->user_password),
                    'role_id' => $mentorRoleId,
                    'phone' => $this->user_phone,
                ]);

                $userId = $user->id;
            } else {
                $this->validate([
                    'user_id' => ['required', 'exists:users,id', Rule::unique('mentors', 'user_id')],
                    'full_name' => 'required|string|max:255',
                    'specialization' => 'nullable|string|max:255',
                    'bio' => 'nullable|string',
                    'rating' => 'required|numeric|min:0|max:5',
                    'is_active' => 'required|boolean',
                ]);

                $userId = $this->user_id;

                User::where('id', $userId)->update(['role_id' => $mentorRoleId]);
            }

            Mentor::create([
                'user_id' => $userId,
                'full_name' => $this->full_name,
                'specialization' => $this->specialization,
                'bio' => $this->bio,
                'rating' => $this->rating,
                'is_active' => $this->is_active,
            ]);

            session()->flash('message', 'Pendamping baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function toggleActive(int $id): void
    {
        $mentor = Mentor::findOrFail($id);
        $mentor->update(['is_active' => ! $mentor->is_active]);

        session()->flash('message', 'Status aktif pendamping berhasil diubah.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function deleteMentor(): void
    {
        if ($this->deleteId) {
            $mentor = Mentor::findOrFail($this->deleteId);
            $mentor->delete();
            session()->flash('message', 'Data pendamping berhasil dihapus.');
        }

        $this->closeModal();
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->mentorId = null;
        $this->user_id = null;
        $this->full_name = '';
        $this->specialization = '';
        $this->bio = '';
        $this->rating = 5.0;
        $this->is_active = true;
        $this->create_new_user = true;
        $this->user_email = '';
        $this->user_phone = '';
        $this->user_password = '';
        $this->deleteId = null;
    }

    public function render()
    {
        $query = Mentor::with('user');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%'.$this->search.'%')
                    ->orWhere('specialization', 'like', '%'.$this->search.'%')
                    ->orWhereHas('user', function ($uq) {
                        $uq->where('email', 'like', '%'.$this->search.'%');
                    });
            });
        }

        if ($this->activeFilter !== '') {
            $query->where('is_active', (bool) $this->activeFilter);
        }

        $mentors = $query->latest()->paginate(10);
        $availableUsers = User::doesntHave('mentor')->latest()->get();

        return view('livewire.mentor-manager', [
            'mentors' => $mentors,
            'availableUsers' => $availableUsers,
        ]);
    }
}

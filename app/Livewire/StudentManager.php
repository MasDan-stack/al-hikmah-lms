<?php

namespace App\Livewire;

use App\Models\ParentProfile;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class StudentManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $genderFilter = '';

    public ?int $studentId = null;

    public ?int $user_id = null;

    public ?int $parent_id = null;

    public string $full_name = '';

    public int $age = 10;

    public string $gender = 'L';

    public ?string $location = '';

    public ?string $notes = '';

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

    public function updatedGenderFilter(): void
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
        $student = Student::with(['user', 'parent.user'])->findOrFail($id);
        $this->studentId = $student->id;
        $this->user_id = $student->user_id;
        $this->parent_id = $student->parent_id;
        $this->full_name = $student->full_name;
        $this->age = $student->age;
        $this->gender = $student->gender;
        $this->location = $student->location;
        $this->notes = $student->notes;

        $this->create_new_user = false;
        $this->user_email = $student->user?->email ?? '';
        $this->user_phone = $student->user?->phone ?? '';

        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->isDeleteModalOpen = false;
        $this->resetForm();
    }

    public function saveStudent(): void
    {
        $studentRole = Role::where('name', 'student')->first();
        $studentRoleId = $studentRole ? $studentRole->id : 4;

        if ($this->studentId) {
            // Editing existing student
            $this->validate([
                'full_name' => 'required|string|max:255',
                'age' => 'required|integer|min:3|max:30',
                'gender' => 'required|in:L,P',
                'parent_id' => 'nullable|exists:parents,id',
                'location' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
            ]);

            $student = Student::findOrFail($this->studentId);
            $student->update([
                'parent_id' => $this->parent_id ?: null,
                'full_name' => $this->full_name,
                'age' => $this->age,
                'gender' => $this->gender,
                'location' => $this->location,
                'notes' => $this->notes,
            ]);

            if ($student->user) {
                $student->user->update([
                    'name' => $this->full_name,
                    'phone' => $this->user_phone,
                ]);
            }

            session()->flash('message', 'Data santri berhasil diperbarui.');
        } else {
            // Creating new student
            if ($this->create_new_user) {
                $this->validate([
                    'full_name' => 'required|string|max:255',
                    'user_email' => 'required|email|unique:users,email',
                    'user_password' => 'required|string|min:6',
                    'user_phone' => 'nullable|string|max:30',
                    'age' => 'required|integer|min:3|max:30',
                    'gender' => 'required|in:L,P',
                    'parent_id' => 'nullable|exists:parents,id',
                    'location' => 'nullable|string|max:255',
                    'notes' => 'nullable|string',
                ]);

                $user = User::create([
                    'name' => $this->full_name,
                    'email' => $this->user_email,
                    'password' => Hash::make($this->user_password),
                    'role_id' => $studentRoleId,
                    'phone' => $this->user_phone,
                ]);

                $userId = $user->id;
            } else {
                $this->validate([
                    'user_id' => ['required', 'exists:users,id', Rule::unique('students', 'user_id')],
                    'full_name' => 'required|string|max:255',
                    'age' => 'required|integer|min:3|max:30',
                    'gender' => 'required|in:L,P',
                    'parent_id' => 'nullable|exists:parents,id',
                    'location' => 'nullable|string|max:255',
                    'notes' => 'nullable|string',
                ]);

                $userId = $this->user_id;

                User::where('id', $userId)->update(['role_id' => $studentRoleId]);
            }

            Student::create([
                'user_id' => $userId,
                'parent_id' => $this->parent_id ?: null,
                'full_name' => $this->full_name,
                'age' => $this->age,
                'gender' => $this->gender,
                'location' => $this->location,
                'notes' => $this->notes,
            ]);

            session()->flash('message', 'Data santri baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function deleteStudent(): void
    {
        if ($this->deleteId) {
            $student = Student::findOrFail($this->deleteId);
            $student->delete();
            session()->flash('message', 'Data santri berhasil dihapus.');
        }

        $this->closeModal();
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->studentId = null;
        $this->user_id = null;
        $this->parent_id = null;
        $this->full_name = '';
        $this->age = 10;
        $this->gender = 'L';
        $this->location = '';
        $this->notes = '';
        $this->create_new_user = true;
        $this->user_email = '';
        $this->user_phone = '';
        $this->user_password = '';
        $this->deleteId = null;
    }

    public function render()
    {
        $query = Student::with(['user', 'parent.user']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%'.$this->search.'%')
                    ->orWhere('location', 'like', '%'.$this->search.'%')
                    ->orWhereHas('user', function ($uq) {
                        $uq->where('email', 'like', '%'.$this->search.'%');
                    });
            });
        }

        if ($this->genderFilter) {
            $query->where('gender', $this->genderFilter);
        }

        $students = $query->latest()->paginate(10);
        $availableUsers = User::doesntHave('student')->latest()->get();
        $parents = ParentProfile::with('user')->get();

        return view('livewire.student-manager', [
            'students' => $students,
            'availableUsers' => $availableUsers,
            'parents' => $parents,
        ]);
    }
}

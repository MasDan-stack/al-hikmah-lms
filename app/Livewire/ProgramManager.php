<?php

namespace App\Livewire;

use App\Models\Program;
use Livewire\Component;
use Livewire\WithPagination;

class ProgramManager extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $levelFilter = '';

    public ?int $programId = null;

    public string $name = '';

    public ?string $description = '';

    public int $duration_weeks = 12;

    public float $price = 0;

    public string $level = 'Pemula';

    public bool $isModalOpen = false;

    public bool $isDeleteModalOpen = false;

    public ?int $deleteId = null;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_weeks' => 'required|integer|min:1|max:104',
            'price' => 'required|numeric|min:0',
            'level' => 'required|string|max:50',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLevelFilter(): void
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
        $program = Program::findOrFail($id);
        $this->programId = $program->id;
        $this->name = $program->name;
        $this->description = $program->description;
        $this->duration_weeks = $program->duration_weeks;
        $this->price = (float) $program->price;
        $this->level = $program->level;

        $this->isModalOpen = true;
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->isDeleteModalOpen = false;
        $this->resetForm();
    }

    public function saveProgram(): void
    {
        $validated = $this->validate();

        if ($this->programId) {
            $program = Program::findOrFail($this->programId);
            $program->update($validated);
            session()->flash('message', 'Program berhasil diperbarui.');
        } else {
            Program::create($validated);
            session()->flash('message', 'Program baru berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->isDeleteModalOpen = true;
    }

    public function deleteProgram(): void
    {
        if ($this->deleteId) {
            Program::findOrFail($this->deleteId)->delete();
            session()->flash('message', 'Program berhasil dihapus.');
        }

        $this->closeModal();
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->programId = null;
        $this->name = '';
        $this->description = '';
        $this->duration_weeks = 12;
        $this->price = 0;
        $this->level = 'Pemula';
        $this->deleteId = null;
    }

    public function render()
    {
        $query = Program::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->levelFilter) {
            $query->where('level', $this->levelFilter);
        }

        $programs = $query->latest()->paginate(10);

        return view('livewire.program-manager', [
            'programs' => $programs,
        ]);
    }
}

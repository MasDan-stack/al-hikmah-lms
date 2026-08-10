<?php

namespace App\Livewire;

use App\Models\Mentor;
use App\Models\Program;
use App\Models\Session;
use App\Models\Student;
use Livewire\Component;

class DashboardStats extends Component
{
    public int $totalStudents = 0;

    public int $totalMentors = 0;

    public int $todaySessions = 0;

    public int $totalPrograms = 0;

    public function mount(): void
    {
        $this->loadStats();
    }

    public function loadStats(): void
    {
        $this->totalStudents = Student::count();
        $this->totalMentors = Mentor::count();
        $this->todaySessions = Session::whereDate('date', today())->count();
        $this->totalPrograms = Program::count();
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}

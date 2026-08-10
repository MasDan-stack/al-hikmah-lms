<?php

namespace App\Livewire;

use App\Models\Progress;
use Livewire\Component;

class ProgressTracker extends Component
{
    public function render()
    {
        $progressList = Progress::with(['student.user', 'mentor.user'])
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.progress-tracker', [
            'progressList' => $progressList,
        ]);
    }
}

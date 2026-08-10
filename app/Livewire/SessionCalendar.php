<?php

namespace App\Livewire;

use App\Models\Session;
use Livewire\Component;

class SessionCalendar extends Component
{
    public string $filterStatus = 'all';

    public function render()
    {
        $query = Session::with(['student.user', 'mentor.user'])->latest('date');

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        $sessions = $query->take(10)->get();

        return view('livewire.session-calendar', [
            'sessions' => $sessions,
        ]);
    }
}

<?php

namespace Modules\Analytics\Livewire;

use Livewire\Component;
use Modules\Tasks\Models\Task;
use Illuminate\Support\Facades\Auth;

class AnalyticsDashboard extends Component
{
    public function render()
    {
        $userId = Auth::id();
        
        $stats = [
            'total' => Task::where('user_id', $userId)->count(),
            'done' => Task::where('user_id', $userId)->where('status', 'done')->count(),
            'todo' => Task::where('user_id', $userId)->where('status', 'todo')->count(),
            'doing' => Task::where('user_id', $userId)->where('status', 'doing')->count(),
            'flagged' => Task::where('user_id', $userId)->where('is_flagged', true)->count(),
        ];

        $stats['completion_rate'] = $stats['total'] > 0 
            ? round(($stats['done'] / $stats['total']) * 100) 
            : 0;

        $energyDistribution = Task::where('user_id', $userId)
            ->selectRaw('energy_level, count(*) as count')
            ->groupBy('energy_level')
            ->pluck('count', 'energy_level')
            ->toArray();

        // Fill missing energy levels
        $energyLabels = [1 => 'Easy', 2 => 'Low', 3 => 'Mid', 4 => 'High', 5 => 'Hard'];
        $energyData = [];
        foreach ($energyLabels as $level => $label) {
            $energyData[$label] = $energyDistribution[$level] ?? 0;
        }

        return view('analytics::livewire.dashboard', [
            'stats' => $stats,
            'energyData' => $energyData,
        ]);
    }
}

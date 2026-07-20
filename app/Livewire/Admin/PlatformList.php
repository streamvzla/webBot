<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Platform;
use App\Models\PlatformSubject;
use Illuminate\Support\Str;

class PlatformList extends Component
{
    use WithPagination;

    public string $search  = '';
    public string $status  = '';
    public string $view    = 'cards';   // 'cards' | 'table'
    public string $sortBy  = 'name';
    public string $sortDir = 'asc';

    protected $queryString = [
        'search'  => ['except' => ''],
        'status'  => ['except' => ''],
        'view'    => ['except' => 'cards'],
        'sortBy'  => ['except' => 'name'],
        'sortDir' => ['except' => 'asc'],
    ];

    public function updatedSearch()  { $this->resetPage(); }
    public function updatedStatus()  { $this->resetPage(); }

    public function sortBy(string $col): void
    {
        if ($this->sortBy === $col) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $col;
            $this->sortDir = 'asc';
        }
    }

    public function toggleSortDir(): void
    {
        $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
    }

    // â”€â”€ Computed: Plataformas paginadas â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function getPlatformsProperty()
    {
        $user = auth()->user();

        return Platform::withCount(['subjects', 'queries', 'clients'])
            ->when(\$user->id !== 1, fn(\$q) => \$q->where('user_id', \$user->id))
            ->when($this->search, fn($q) =>
                $q->where(fn($sq) =>
                    $sq->where('name', 'like', '%'.$this->search.'%')
                       ->orWhere('description', 'like', '%'.$this->search.'%')
                       ->orWhere('slug', 'like', '%'.$this->search.'%')
                )
            )
            ->when($this->status !== '', fn($q) =>
                $q->where('is_active', (bool) $this->status)
            )
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(12);
    }

    // â”€â”€ Computed: Stats globales â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function getStatsProperty(): array
    {
        $user = auth()->user();
        $base = Platform::when(\$user->id !== 1, fn(\$q) => \$q->where('user_id', \$user->id));

        return [
            'total'    => (clone $base)->count(),
            'active'   => (clone $base)->where('is_active', true)->count(),
            'inactive' => (clone $base)->where('is_active', false)->count(),
            'public'   => (clone $base)->where('is_public', true)->count(),
        ];
    }

    // â”€â”€ AcciÃ³n: Toggle activo/inactivo â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function toggleActive(int $id): void
    {
        $platform = Platform::findOrFail($id);
        $this->checkPlatformAccess($platform);
        $platform->update(['is_active' => !$platform->is_active]);
        $this->dispatch('notif', message: $platform->is_active
            ? "âœ… {$platform->name} activada."
            : "â¸ï¸ {$platform->name} desactivada."
        );
    }

    // â”€â”€ AcciÃ³n: Eliminar â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function deletePlatform(int $id): void
    {
        $platform = Platform::findOrFail($id);
        $this->checkPlatformAccess($platform);

        if ($platform->logo && file_exists(public_path($platform->logo))) {
            @unlink(public_path($platform->logo));
        }

        $name = $platform->name;
        $platform->delete();
        $this->dispatch('notif', message: "ðŸ—‘ï¸ Plataforma Â«{$name}Â» eliminada.");
    }

    // â”€â”€ AcciÃ³n: Duplicar plataforma â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function duplicatePlatform(int $id): void
    {
        $platform = Platform::with('subjects')->findOrFail($id);
        $this->checkPlatformAccess($platform);

        $newName = $platform->name . ' (Copia)';
        $newSlug = Str::slug($newName) . '-' . uniqid();

        $new = Platform::create([
            'name'        => $newName,
            'slug'        => $newSlug,
            'description' => $platform->description,
            'color'       => $platform->color,
            'is_active'   => false,
            'is_public'   => false,
            'user_id'     => auth()->id(),
        ]);

        foreach ($platform->subjects as $subject) {
            $new->subjects()->create([
                'subject'   => $subject->subject,
                'pattern'   => $subject->pattern,
                'is_active' => $subject->is_active,
            ]);
        }

        $this->dispatch('notif', message: "ðŸ“‹ Plataforma duplicada como Â«{$newName}Â».");
    }

    // â”€â”€ Helper: verificar permisos â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    private function checkPlatformAccess(Platform $platform): void
    {
        $user = auth()->user();
        if (\$user->id !== 1 && \$platform->user_id !== \$user->id) {
            abort(403, 'No autorizado');
        }
    }

    public function render()
    {
        return view('livewire.admin.platform-list');
    }
}



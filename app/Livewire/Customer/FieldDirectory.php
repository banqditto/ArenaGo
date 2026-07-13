<?php

namespace App\Livewire\Customer;

use App\Models\Field;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Fields Directory')]
class FieldDirectory extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type = '';

    /**
     * Reset pagination when search query is updated.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when field type is updated.
     */
    public function updatingType(): void
    {
        $this->resetPage();
    }

    /**
     * Render the fields directory view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        $query = Field::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->type)) {
            $query->where('type', $this->type);
        }

        $fields = $query->paginate(6);

        return view('livewire.customer.field-directory', [
            'fields' => $fields,
        ])->layout('layouts.app');
    }
}

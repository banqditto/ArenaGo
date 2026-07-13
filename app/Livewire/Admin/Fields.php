<?php

namespace App\Livewire\Admin;

use App\Models\Field;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Flux\Flux;
use Exception;

#[Title('Manage Fields')]
class Fields extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Form inputs
    public string $name = '';
    public string $type = 'Futsal';
    public string $description = '';
    public string $price_per_hour = '';
    /** @var mixed */
    public $image; // Uploaded file
    public ?string $currentImagePath = null; // Stored path

    // State management
    public ?int $editingFieldId = null;
    public bool $isFormOpen = false;
    public bool $isConfirmingDelete = false;
    public ?int $deletingFieldId = null;

    /**
     * Define validation rules.
     *
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:Futsal,Badminton,Basketball,Mini Soccer,Padel,Voli',
            'description' => 'nullable|string|max:1000',
            'price_per_hour' => 'required|numeric|min:0',
            'image' => $this->editingFieldId ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ];
    }

    /**
     * Reset pagination when searching or changing lists.
     */
    public function updated(string $propertyName): void
    {
        if (in_array($propertyName, ['isFormOpen', 'isConfirmingDelete'])) {
            $this->resetErrorBag();
        }
    }

    /**
     * Open form to create a new field.
     */
    public function createField(): void
    {
        $this->resetValidation();
        $this->reset(['name', 'type', 'description', 'price_per_hour', 'image', 'currentImagePath', 'editingFieldId']);
        $this->isFormOpen = true;
    }

    /**
     * Open form to edit an existing field.
     *
     * @param int $id
     */
    public function editField(int $id): void
    {
        $this->resetValidation();
        $field = Field::findOrFail($id);

        $this->editingFieldId = $field->id;
        $this->name = $field->name;
        $this->type = $field->type;
        $this->description = $field->description ?? '';
        $this->price_per_hour = (string) (int) $field->price_per_hour;
        $this->currentImagePath = $field->image_path;
        $this->image = null; // Reset file upload input

        $this->isFormOpen = true;
    }

    /**
     * Save the field (Create or Update).
     */
    public function saveField(): void
    {
        $this->validate();

        try {
            $imagePath = $this->currentImagePath;

            if ($this->image) {
                // Delete old image if exists
                if ($this->currentImagePath) {
                    Storage::disk('public')->delete($this->currentImagePath);
                }
                // Store new image
                $imagePath = $this->image->store('fields', 'public');
            }

            Field::updateOrCreate(
                ['id' => $this->editingFieldId],
                [
                    'name' => $this->name,
                    'type' => $this->type,
                    'description' => $this->description,
                    'price_per_hour' => (float) $this->price_per_hour,
                    'image_path' => $imagePath,
                ]
            );

            Flux::toast(
                variant: 'success',
                text: $this->editingFieldId ? __('Field updated successfully!') : __('Field created successfully!')
            );

            $this->isFormOpen = false;
            $this->reset(['name', 'type', 'description', 'price_per_hour', 'image', 'currentImagePath', 'editingFieldId']);

        } catch (Exception $e) {
            Flux::toast(variant: 'danger', text: __('Failed to save field: ') . $e->getMessage());
        }
    }

    /**
     * Trigger delete confirmation modal.
     *
     * @param int $id
     */
    public function confirmDelete(int $id): void
    {
        $this->deletingFieldId = $id;
        $this->isConfirmingDelete = true;
    }

    /**
     * Delete the field.
     */
    public function deleteField(): void
    {
        if (!$this->deletingFieldId) {
            return;
        }

        try {
            $field = Field::findOrFail($this->deletingFieldId);

            // Delete associated image file
            if ($field->image_path) {
                Storage::disk('public')->delete($field->image_path);
            }

            $field->delete();

            Flux::toast(variant: 'success', text: __('Field deleted successfully!'));

            $this->isConfirmingDelete = false;
            $this->deletingFieldId = null;

        } catch (Exception $e) {
            Flux::toast(variant: 'danger', text: __('Failed to delete field: ') . $e->getMessage());
        }
    }

    /**
     * Render the component view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        $fields = Field::orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin.fields', [
            'fields' => $fields,
        ])->layout('layouts.app');
    }
}

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading level="1" size="xl" class="font-bold">{{ __('Manage Fields') }}</flux:heading>
            <flux:subheading class="mt-1">{{ __('Add, update, or remove fields from the platform.') }}</flux:subheading>
        </div>
        <flux:button 
            type="button" 
            wire:click="createField" 
            variant="primary" 
            icon="plus"
        >
            {{ __('Add Field') }}
        </flux:button>
    </div>

    <!-- Fields Table Card -->
    <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900 overflow-hidden">
        @if($fields->isEmpty())
            <div class="flex flex-col items-center justify-center p-12 text-center text-zinc-400 dark:text-zinc-600">
                <flux:icon name="cube" class="size-16 mb-4" />
                <flux:heading level="3" class="font-bold">{{ __('No Fields Found') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Get started by adding your first field.') }}</flux:text>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-zinc-150 bg-zinc-50/50 text-xs font-semibold text-zinc-500 uppercase tracking-wider dark:border-zinc-800 dark:bg-zinc-900/50 dark:text-zinc-400">
                            <th class="px-6 py-4">{{ __('Image') }}</th>
                            <th class="px-6 py-4">{{ __('Field Name') }}</th>
                            <th class="px-6 py-4">{{ __('Category') }}</th>
                            <th class="px-6 py-4">{{ __('Price per Hour') }}</th>
                            <th class="px-6 py-4 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-150 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                        @foreach($fields as $item)
                            <tr class="hover:bg-zinc-50/30 dark:hover:bg-zinc-800/20 transition-colors duration-150">
                                <!-- Image Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="size-16 rounded-lg bg-zinc-100 dark:bg-zinc-800 overflow-hidden border border-zinc-200 dark:border-zinc-700">
                                        @if($item->image_path)
                                            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-zinc-400">
                                                <flux:icon name="photo" class="size-6" />
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <!-- Name Column -->
                                <td class="px-6 py-4 whitespace-nowrap font-semibold text-zinc-900 dark:text-zinc-100">
                                    {{ $item->name }}
                                </td>
                                <!-- Type Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                         @if($item->type === 'Futsal') bg-teal-50 text-teal-700 border border-teal-200 dark:bg-teal-950/30 dark:text-teal-400 dark:border-teal-900/50
                                         @elseif($item->type === 'Badminton') bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/30 dark:text-blue-400 dark:border-blue-900/50
                                         @elseif($item->type === 'Basketball') bg-orange-50 text-orange-700 border border-orange-200 dark:bg-orange-950/30 dark:text-orange-400 dark:border-orange-900/50
                                         @elseif($item->type === 'Mini Soccer') bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/50
                                         @elseif($item->type === 'Padel') bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-950/30 dark:text-indigo-400 dark:border-indigo-900/50
                                         @elseif($item->type === 'Voli') bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-950/30 dark:text-purple-400 dark:border-purple-900/50
                                         @else bg-zinc-50 text-zinc-700 border border-zinc-200 dark:bg-zinc-950/30 dark:text-zinc-400 dark:border-zinc-900/50
                                         @endif border">
                                        {{ $item->type }}
                                    </span>
                                </td>
                                <!-- Price Column -->
                                <td class="px-6 py-4 whitespace-nowrap text-zinc-600 dark:text-zinc-350">
                                    Rp {{ number_format($item->price_per_hour, 0, ',', '.') }} / hr
                                </td>
                                <!-- Actions Column -->
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button 
                                            type="button" 
                                            wire:click="editField({{ $item->id }})" 
                                            variant="ghost" 
                                            size="sm" 
                                            icon="pencil-square"
                                        />
                                        <flux:button 
                                            type="button" 
                                            wire:click="confirmDelete({{ $item->id }})" 
                                            variant="ghost" 
                                            size="sm" 
                                            icon="trash"
                                            class="text-rose-600 hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300"
                                        />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination Footer -->
            <div class="px-6 py-4 border-t border-zinc-150 dark:border-zinc-800 bg-zinc-50/20 dark:bg-zinc-900/20">
                {{ $fields->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal Form -->
    <flux:modal wire:model="isFormOpen" class="md:w-[32rem]">
        <form wire:submit.prevent="saveField" class="space-y-6">
            <div>
                <flux:heading level="2" size="lg" class="font-bold">
                    {{ $editingFieldId ? __('Edit Field Details') : __('Create New Field') }}
                </flux:heading>
                <flux:subheading class="mt-1">
                    {{ __('Fill in the information below to configure this field.') }}
                </flux:subheading>
            </div>

            <!-- Field Name Input -->
            <flux:input 
                type="text" 
                wire:model="name" 
                label="{{ __('Field Name') }}" 
                placeholder="{{ __('e.g., Skyline Badminton Court') }}"
                required
            />

            <!-- Field Type Select -->
            <flux:select wire:model="type" label="{{ __('Sports Category') }}">
                <option value="Futsal">{{ __('Futsal') }}</option>
                <option value="Badminton">{{ __('Badminton') }}</option>
                <option value="Basketball">{{ __('Basketball') }}</option>
                <option value="Mini Soccer">{{ __('Mini Soccer') }}</option>
                <option value="Padel">{{ __('Padel') }}</option>
                <option value="Voli">{{ __('Voli') }}</option>
            </flux:select>

            <!-- Field Description Input -->
            <flux:textarea 
                wire:model="description" 
                label="{{ __('Field Description') }}" 
                placeholder="{{ __('Enter field description and facility details...') }}"
                rows="3"
            />

            <!-- Price Input -->
            <flux:input 
                type="number" 
                wire:model="price_per_hour" 
                label="{{ __('Price per Hour (Rupiah)') }}" 
                placeholder="60000"
                required
            />

            <!-- Image File Upload Input -->
            <div class="space-y-2">
                <flux:label>{{ __('Field Image') }}</flux:label>
                
                @if($currentImagePath && !$image)
                    <div class="relative size-32 rounded-lg border overflow-hidden bg-zinc-50 dark:bg-zinc-800">
                        <img src="{{ asset('storage/' . $currentImagePath) }}" alt="Preview" class="h-full w-full object-cover">
                    </div>
                @endif

                @if($image)
                    <div class="relative size-32 rounded-lg border overflow-hidden bg-zinc-50 dark:bg-zinc-800">
                        <img src="{{ $image->temporaryUrl() }}" alt="Temporary Preview" class="h-full w-full object-cover">
                    </div>
                @endif

                <flux:input 
                    type="file" 
                    wire:model="image" 
                    accept="image/*"
                />
                
                @error('image')
                    <span class="text-xs text-rose-600 font-semibold">{{ $message }}</span>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 border-t border-zinc-100 pt-6 dark:border-zinc-800">
                <flux:button type="button" variant="ghost" wire:click="$set('isFormOpen', false)">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Save Details') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal wire:model="isConfirmingDelete" class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading level="2" size="lg" class="font-bold text-rose-600 dark:text-rose-400">
                    {{ __('Delete Field') }}
                </flux:heading>
                <flux:subheading class="mt-1">
                    {{ __('Are you sure you want to delete this field? This action is permanent and cannot be undone.') }}
                </flux:subheading>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 border-t border-zinc-100 pt-6 dark:border-zinc-800">
                <flux:button type="button" variant="ghost" wire:click="$set('isConfirmingDelete', false)">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="button" variant="danger" wire:click="deleteField">
                    {{ __('Delete Permanently') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

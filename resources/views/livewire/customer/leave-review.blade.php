<div>
    <flux:modal wire:model="isOpen" class="md:w-[28rem]">
        <form wire:submit.prevent="submitReview" class="space-y-6">
            <div>
                <flux:heading level="2" size="lg" class="font-bold">{{ __('Submit Review & Rating') }}</flux:heading>
                <flux:subheading class="mt-1">{{ __('Share your experience with others about this sports field.') }}</flux:subheading>
            </div>

            <!-- Star Selector -->
            <div class="space-y-2">
                <flux:label>{{ __('Rating') }}</flux:label>
                <div class="flex items-center gap-1.5">
                    @for($i = 1; $i <= 5; $i++)
                        <button 
                            type="button" 
                            wire:click="$set('rating', {{ $i }})" 
                            class="focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 rounded-full p-0.5"
                        >
                            <flux:icon 
                                name="star" 
                                variant="{{ $rating >= $i ? 'solid' : 'outline' }}" 
                                class="size-8 {{ $rating >= $i ? 'text-amber-400' : 'text-zinc-350 dark:text-zinc-650' }} hover:scale-110 transition-transform duration-100" 
                            />
                        </button>
                    @endfor
                </div>
            </div>

            <!-- Comment Input -->
            <flux:textarea 
                wire:model="comment" 
                label="{{ __('Review Comment') }}" 
                placeholder="{{ __('What did you like or dislike about the field condition, booking process, or staff?') }}"
                rows="4"
                required
            />
            @error('comment')
                <span class="text-xs font-semibold text-rose-600 dark:text-rose-400 mt-1 block">{{ $message }}</span>
            @enderror

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 border-t border-zinc-100 pt-6 dark:border-zinc-800">
                <flux:button type="button" variant="ghost" wire:click="$set('isOpen', false)">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Submit Review') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>

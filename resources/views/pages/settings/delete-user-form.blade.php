<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="w-full mt-8 p-6 border border-gray-300 dark:border-zinc-700 rounded-xl">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <flux:dialog id="confirm-user-deletion" variant="overlay">
        <flux:button slot="trigger" variant="danger" type="button" class="mt-5" data-test="delete-account-button">
            {{ __('Delete Account') }}
        </flux:button>

        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ __('Are you sure you want to delete your account?') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
        </p>

        <form wire:submit="deleteUser" class="mt-6">
            <flux:input
                wire:model="password"
                id="password"
                :label="__('Password')"
                type="password"
                required
                placeholder="{{ __('Password') }}"
                autocomplete="current-password"
            />

            <div class="mt-6 flex justify-end">
                <flux:button variant="ghost" type="button" x-on:click="$flux.closeDialog('confirm-user-deletion')">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button variant="danger" type="submit" class="ms-3" data-test="confirm-delete-account-button">
                    {{ __('Delete Account') }}
                </flux:button>
            </div>
        </form>
    </flux:dialog>
</section>
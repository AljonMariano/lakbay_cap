<div>
    <x-jet-action-section>
        <x-slot name="title">
            {{ __('Browser Sessions') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Manage and log out your active sessions on other browsers and devices.') }}
        </x-slot>

        <x-slot name="content">
            <div class="max-w-xl text-sm text-gray-600">
                {{ __('If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.') }}
            </div>

            <div class="mt-5">
                <x-jet-button wire:click="confirmLogout" wire:loading.attr="disabled">
                    {{ __('Log Out Other Browser Sessions') }}
                </x-jet-button>
            </div>

            <!-- Log Out Other Devices Confirmation Modal -->
            <x-jet-dialog-modal wire:model="confirmingLogout">
                <x-slot name="title">
                    {{ __('Log Out Other Browser Sessions') }}
                </x-slot>

                <x-slot name="content">
                    {{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}

                    <div class="mt-4" x-data="{}" x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
                        <x-jet-input type="password" class="mt-1 block w-3/4"
                                    placeholder="{{ __('Password') }}"
                                    x-ref="password"
                                    wire:model.defer="password"
                                    wire:keydown.enter="logoutOtherBrowserSessions" />

                        <x-jet-input-error for="password" class="mt-2" />
                    </div>
                </x-slot>

                <x-slot name="footer">
                    <
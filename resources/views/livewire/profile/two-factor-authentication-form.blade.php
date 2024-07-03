<div>
    <h3 class="text-lg font-medium text-gray-900">
        {{ __('Two-Factor Authentication') }}
    </h3>

    <div class="mt-3 max-w-xl text-sm text-gray-600">
        <p>
            {{ __('Add additional security to your account using two-factor authentication.') }}
        </p>
    </div>

    <div class="mt-5">
        @if ($this->enabled)
            <x-jet-confirms-password wire:then="disableTwoFactorAuthentication">
                <x-jet-danger-button type="button" wire:loading.attr="disabled">
                    {{ __('Disable') }}
                </x-jet-danger-button>
            </x-jet-confirms-password>
        @else
            <x-jet-confirms-password wire:then="enableTwoFactorAuthentication">
                <x-jet-button type="button" wire:loading.attr="disabled">
                    {{ __('Enable') }}
                </x-jet-button>
            </x-jet-confirms-password>
        @endif
    </div>

    @if ($this->enabled)
        <div class="mt-5">
            <x-jet-label value="{{ __('Two-Factor Authentication QR Code') }}" />

            <div class="mt-2">
                {!! $this->user->twoFactorQrCodeSvg() !!}
            </div>
        </div>

        <div class="mt-5">
            <x-jet-label value="{{ __('Recovery Codes') }}" />

            <div class="mt-2">
                @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                    <div>{{ $code }}</div>
                @endforeach
            </div>
        </div>
    @endif
</div>
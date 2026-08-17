<x-app-layout title="Profile">
    <div class="container grid px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 ">
            Profile
        </h2>

        <h4 class="mb-4 text-lg font-semibold text-gray-600 ">
            {{ __('Profile Information') }}
        </h4>

        @livewire('profile.update-profile-information-form')

        <x-section-border />

        <div class="mt-10 sm:mt-0">
            <h4 class="mb-4 text-lg font-semibold text-gray-600 ">
                {{ __('Update Password') }}
            </h4>

            @livewire('profile.update-password-form')
        </div>

        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
        <x-section-border />

        <div class="mt-10 sm:mt-0">
            <h4 class="mb-4 text-lg font-semibold text-gray-600 ">
                {{ __('Two Factor Authentication') }}
            </h4>

            @livewire('profile.two-factor-authentication-form')
        </div>
        @endif

        <x-section-border />

        <div class="mt-10 sm:mt-0">
            <h4 class="mb-4 text-lg font-semibold text-gray-600 ">
                {{ __('Browser Sessions') }}
            </h4>

            @livewire('profile.logout-other-browser-sessions-form')
        </div>

        <x-section-border />

        <div class="mt-10 sm:mt-0">
            <h4 class="mb-4 text-lg font-semibold text-gray-600 ">
                {{ __('Delete Account') }}
            </h4>

            @livewire('profile.delete-user-form')
        </div>

    </div>
</x-app-layout>
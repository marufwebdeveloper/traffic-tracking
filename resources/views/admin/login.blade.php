<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('admin.login') }}">
        @csrf

        @if(Session::has('error'))
        <p style="color:red">{{ Session::get('error') }}</p>
        @endif

        <div>
            <x-input-label for="vehicle_number" :value="__('User Name')" />
            <x-text-input id="vehicle_number" class="block mt-1 w-full" type="text" name="vehicle_number" :value="old('vehicle_number')" required autofocus autocomplete="vehicle_number" />
            <x-input-error :messages="$errors->get('vehicle_number')" class="mt-2" />
        </div>
        
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            

            <x-primary-button class="ml-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

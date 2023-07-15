<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div>
            <x-input-label for="vehicle_number" :value="__('Vehicle Number')" />
            <x-text-input id="vehicle_number" class="block mt-1 w-full" type="text" name="vehicle_number" :value="old('vehicle_number')" required autofocus autocomplete="vehicle_number" />
            <x-input-error :messages="$errors->get('vehicle_number')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="vehicle_type" :value="__('Vehicle Type')" />
            <select name="vehicle_type" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
                <option value="">Select Vehicle Type</option>
                @foreach([
                    'texi'=>'Texi',
                    'micro_bus'=>'Micro Bus',
                    'bus'=>'Bus',
                    'truck'=>'Truck',
                    'bike'=>'Bike'
                ] as $k=>$v)
                <option value="{{$k}}" {{old('vehicle_type')==$k?"selected":''}}>{{$v}}</option>
                @endforeach
            </select>

            <x-input-error :messages="$errors->get('vehicle_number')" class="mt-2" />
        </div>
        
        <div class="mt-4">
            <x-input-label for="mobile" :value="__('Mobile Number')" />
            <x-text-input id="mobile" class="block mt-1 w-full" type="text" name="mobile" :value="old('mobile')" required autofocus autocomplete="mobile" />
            <x-input-error :messages="$errors->get('mobile')" class="mt-2" />
        </div>
        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
            type="password"
            name="password"
            required autocomplete="new-password" 
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input 
            id="password_confirmation" 
            class="block mt-1 w-full"
            type="password"
            name="password_confirmation" 
            required autocomplete="new-password" 
            />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

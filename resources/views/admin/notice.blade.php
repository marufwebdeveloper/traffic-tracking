<x-app-layout>
     <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
    <form method="POST" action="{{ route('admin.notice') }}">
        @csrf

        @if(count((array)@$alert))
        <div class="alrt {{['alrferror','alrtsuccess'][$alert[0]]}} px-4 py-3 mb-2 rounded relative" role="alert">
          <span class="block sm:inline">{{$alert[1]}}</span>
          <span class="absolute top0 bottom-0 right-0 px-4 py-3 pointer" onclick="this.closest('.alrt').remove()">x   </span>
        </div>
        @endif  


        <div>
            <x-input-label for="notice" :value="__('Notice')" />
            <x-text-input id="notice" class="block mt-1 w-full" type="text" name="notice" :value="old('notice')" required autofocus autocomplete="off" />
            <x-input-error :messages="$errors->get('notice')" class="mt-2" />
        </div>
        <div class="mt-3">
            <x-input-label for="effective_date" :value="__('Effective Date')" />
            <x-text-input id="effective_date" class="block mt-1 w-full" type="datetime-local" name="effective_date" :value="old('effective_date')" required autofocus autocomplete="off" />
            <x-input-error :messages="$errors->get('effective_date')" class="mt-2" />
        </div>
        <div class="mt-3">
            <x-input-label for="active_till" :value="__('Active Till (Date)')" />
            <x-text-input id="active_till" class="block mt-1 w-full" type="datetime-local" name="active_till" :value="old('active_till')" required autofocus autocomplete="off" />
            <x-input-error :messages="$errors->get('active_till')" class="mt-2" />
        </div>
        
        
            <x-primary-button class="mt-3">
                {{ __('Save') }}
            </x-primary-button>
        </div>
    </form>
</div>
</div>
</div>
</div>
</x-app-layout>
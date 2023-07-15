<x-app-layout>
    <div class="flex justify-end">
        <a href="{{route('login')}}" class="bg-blue hover:bg-blue font-bold py-2 px-2 ">Login</a>
        <span class="px-2"></span>
        <a href="{{route('register')}}" class="bg-blue hover:bg-blue font-bold py-2 px-2 rounded">Register</a>
    </div>
    <div class="mx-auto sm:px-6 lg:px-8 pt-4">
        <div class="py-1 pnsp"></div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
            <table class="table">
                <thead>
                    <th>Route Name</th>                    
                    @foreach(
                    [
                        'texi'=>'Texi',
                        'micro_bus'=>'Micro Bus',
                        'bus'=>'Bus',
                        'truck'=>'Truck',
                        'bike'=>'Bike'
                    ] as $k=>$v)
                    <th>{{$v}}</th>
                    @endforeach
                </thead>
                <tbody class="rvsd">
                </tbody>
            </table>

        </div>
    </div>
</x-app-layout>


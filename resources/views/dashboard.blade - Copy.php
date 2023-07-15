<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>

    <button class="ddww">Click</button>
</x-app-layout>



<script type="text/javascript">
    $(document).ready(function(){
        //$(".ddww").click(function(){

            console.log(pusher);

            setTimeout(function(){
                $.ajax({
                  url: "{{route('pusher.broadcast')}}",
                  method:'POST',
                  headers:{
                    'X-Socket-Id':pusher.connection.socket_id
                  },
                  data:{
                    _token:"{{csrf_token()}}"
                  },
                  success: function(r){
                    console.log(r);
                    
                  }
                });
            },1000)
        //});        
    })
</script>

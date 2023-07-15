<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title??'Traffic Tracking' }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style type="text/css">
            .top0{
                top:0;
            }
            .arphover:hover{
                background: rgba(37, 99, 235, 0.3);
            }
            .bg100{
                background-color: rgb(3 105 161);
                color: white;
            }
            .bg100:hover{
                background-color: rgb(2 132 199);
            }
            .pointer{
                cursor: pointer;
            }
            .crpu:hover{
                font-weight: bold;
            }
            table.table{
                width: 100%;
            }
            table.table th,
            table.table td,
            table.table2 th,
            table.table2 td{
                border: 1px solid #ddd;
                padding: 2px 10px;
            }
            .alrtsuccess{
                border: 1px solid #47d1b1;
                background-color: rgb(142 237 219 / 50%);
            }
            .alrferror{
                border: 1px solid #ff8c8c;
                background-color: rgb(241 138 127 / 50%);
            }
            .crpu{
                position: absolute;
                top: 0;
                padding: 0 10px;
            }
            .crpu:active{
                color: blue;
            }
            .update-route-point{
                position: relative;
                display: inline-block;
                height: 20px;
                line-height: 18px;
            }
            .update-route-point .checkbox{
                box-shadow: none;
            }
            .update-route-point .checkbox:disabled{
                opacity: 0.6;
            }
        </style>
        <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script>
        Pusher.logToConsole = false;
        var pusher = new Pusher('1c0836e17c175e130139', {
          cluster: 'ap2'
        });
        var channel = pusher.subscribe('ttchannel');
        channel.bind('ttevent', function(data) {
            //console.clear();
            console.log(data.message);
            if(data.message=='noticepublish'){
                NoticeLoad();
            }else if(data.message=='travel'){
                RunningVehicleSummary();
            }            
        });
      </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @if(Auth::check() && request()->route()->getName()!='root')
            @include('layouts.navigation')
            @endif
            <main>
                {{ $slot }}
            </main>
        </div>
        <script type="text/javascript">
            if ( window.history.replaceState ) {
              window.history.replaceState( null, null, window.location.href );
            }
            RunningVehicleSummary();
            NoticeLoad();
            function RunningVehicleSummary(){
                if(!document.querySelector('.rvsd')) return;

                var vt = <?php echo json_encode([
                    'texi'=>'Texi',
                    'micro_bus'=>'Micro Bus',
                    'bus'=>'Bus',
                    'truck'=>'Truck',
                    'bike'=>'Bike'
                ]); ?>

                fetch("{{route('travel-info.running_vehicle_summary')}}", {
                    method: 'GET', 
                    mode: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        "X-CSRF-Token": "{{csrf_token()}}"
                    },
                    
                }).then(function(response){
                    return response.text();
                }).then(function(result){
                    try{
                        var data = JSON.parse(result);
                        var tr = '';
                        Object.keys(data).forEach(function(k){
                            var td = '';
                            Object.keys(vt).forEach(function(t){
                                td += `<td>${data[k][t]||''}</td>`;
                            });
                            tr += `
                                <tr>
                                    <td>${k.replace('___',' -- ')}</td>${td}
                                </tr>
                            `;
                        });
                        document.querySelector("table .rvsd").innerHTML = tr;
                    }catch(e){}
                });
            }

            function NoticeLoad(){
                if(!document.querySelector('.pnsp')) return;

                fetch("{{route('admin.get_notices')}}", {
                    method: 'GET', 
                    mode: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        "X-CSRF-Token": "{{csrf_token()}}"
                    },
                    
                }).then(function(response){
                    return response.text();
                }).then(function(result){
                    try{
                        var data = JSON.parse(result);
                        if(Array.isArray(data.data)){
                            document.querySelector('.pnsp').innerHTML = `<marquee class=''>${data.data.join(' | ')}</marquee>`;
                        } 
                    }catch(e){}
                });

            }

        </script>
    </body>
</html>

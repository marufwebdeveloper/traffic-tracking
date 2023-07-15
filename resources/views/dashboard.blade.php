<?php
    $rti = DB::select(
        "SELECT t1.id,t1.name,passed FROM `route_points` AS `t1` 
        LEFT JOIN `travel_history` AS `t2` 
        ON `t2`.`id` = `t1`.`travel_history_id` 
        WHERE t1.travel_history_id=(
            SELECT travel_history_id FROM route_points 
            WHERE user_id=".Auth::id()." 
            AND (passed IS NULL OR passed='')
            ORDER BY id DESC limit 1
        )
        "
    );
?>

<x-app-layout>
    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($alert=Session::get('alert'))
                    <div class="alrt {{['alrferror','alrtsuccess'][$alert[0]]}} px-4 py-3 mb-2 rounded relative" role="alert">
                      <span class="block sm:inline">{{$alert[1]}}</span>
                      <span class="absolute top0 bottom-0 right-0 px-4 py-3 pointer" onclick="this.closest('.alrt').remove()">x   </span>
                    </div>
                    @endif  


                    @if($rti)
<div>
    <table class="table2 border-collapse border border-slate-400">
        <thead>
            <tr>
                <th class="border border-slate-400">Route Point</th>
                <th class="border border-slate-400">Passed</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rti as $index=>$row)
            <tr>
                <td class="table-auto border border-slate-400">{{$row->name}}</td>
                <td class="border border-slate-400 text-center" style="width:90px">
                    <span class="update-route-point">
                        <input 
                            type="checkbox" 
                            value="{{$row->id}}" 
                            class="checkbox" 
                            data-id='{{$index+1}}'
                            sl='{{$index+1}}'
                            {{$row->passed?"checked disabled":""}}
                        />
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

                    @else

<datalist id='routepoints'>
    @foreach(DB::select("select distinct name from route_points") as $rp)
    <option value="{{$rp->name}}">
    @endforeach
</datalist>

<form method="post" action="{{route('travel-info.store')}}">
    @csrf
    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="from">
        From
      </label>
      <input 
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
          id="from" 
          type="text" 
          placeholder="Enter Starting Place Name"
          required 
          name='from'
          list="routepoints"
          autocomplete="off"
      >
        @error('from')
        <p class="text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="to">
        To
      </label>
      <input 
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
          id="to" 
          type="text" 
          placeholder="Enter Destination Place Name"
          required 
          name="to" 
          list="routepoints"
          autocomplete="off" 
      >
      
        @error('to')
        <p class="text-red-500">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="price" class="block text-sm font-medium leading-6 text-gray-700">Route Points</label>
        <div class="rpap"></div>
    </div>
                              
    <div class="relative mt-2 rounded-md shadow-sm arpz">    
        <input 
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline new-route-point" 
            type="text" 
            placeholder="Enter Via Route Point Name"
            list="routepoints"
            autocomplete="off"
        >
        <div class="absolute right-0 flex items-center top0">
          <p class="shadow rounded py-2 px-3 leading-tight cursor-default add-route-point arphover">+</p>
        </div>
    </div> 
    @error('route_points')
        <p class="text-red-500">{{ $message }}</p>
    @enderror

    <button class="rounded mt-4 py-1 px-4 bg100">Save</button>                     
</form>
                    @endif
                </div>
            </div>
        </div>
    </div>


<script type="text/javascript">
  document.addEventListener("DOMContentLoaded", function(event) {
      document.querySelector(".add-route-point")?.addEventListener('click',function(){
        place_route_point();
      });
      document.querySelector(".new-route-point")?.addEventListener('keypress',function(e){        
        if(e.keyCode===13){
            e.preventDefault();
            place_route_point();
        }
      });
      document.querySelectorAll(".update-route-point").forEach(function(s){

            if(s.querySelector(".checkbox").hasAttribute('disabled')) return;

            s.addEventListener('click',function(e){
                var ub = this.querySelector('.crpu');
                var cb = this.querySelector(".checkbox");
                var cbId = this.querySelector(".checkbox").getAttribute('data-id');
                var data_id = this.querySelector(".checkbox").value;;
                var sl = this.querySelector(".checkbox").getAttribute('sl');
                var lp = Array.from(document.querySelectorAll('.update-route-point .checkbox:disabled')).pop();
                var lpsl = parseInt(lp?.getAttribute('sl')||0)+1;
                if(lpsl!=sl){
                    alert('You Can Not Skip Previous Route Point');
                    this.querySelector(".checkbox").checked=false;
                    return;
                }

                if(e.target.classList.contains('checkbox')){
                    document.querySelectorAll(".crpu").forEach(function(f){
                        f.remove();
                    });
                    document.querySelectorAll(".update-route-point .checkbox").forEach(function(f){
                        if(
                            f.getAttribute('data-id')==cbId
                            || f.hasAttribute('disabled')
                        ) return;
                        f.checked=false;
                    });
                    
                    if(this.querySelector('.checkbox').checked==true){
                        this.insertAdjacentHTML(
                            'beforeend',
                            `<span class="p-2 pointer crpu">&#10003;</span>`
                        );
                    }
                }else if(e.target.classList.contains('crpu')){
                    if(ub.hasAttribute('pause')) return;
                    ub.setAttribute('pause','');

                    var postData = new FormData();
                    postData.append('data_id', data_id);

                    fetch("{{route('travel-info.update_passed_route')}}", {
                        method: 'POST', 
                        mode: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            "X-CSRF-Token": "{{csrf_token()}}"
                        },
                        body:postData
                    }).then(function(response){
                        return response.text();
                    }).then(function(data){
                        ub.removeAttribute('pause');

                        try{
                            var data = JSON.parse(data);
                            if(data['success']==1){
                                if(lpsl==document.querySelectorAll(".update-route-point").length){
                                    alert('Completed Your Journey');
                                }else{
                                    alert("Recorded Successfully");
                                }                                
                                ub.remove();
                                cb.setAttribute('disabled','disabled');
                                
                                return;
                            }
                        }catch(e){}
                        alert("Something Wrong. Please Try Again.");
                    });

                }
            });
      });

      function place_route_point(){
        var rn = document.querySelector(".new-route-point");

        if(!rn.value.trim())return;

        document.querySelector(".rpap").insertAdjacentHTML('beforeend',
            `
            <div class="relative mt-2 rounded-md shadow-sm arpz">    
                <input 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline route-point" 
                    type="text" 
                    value='${rn.value.trim()}'
                    name='route_points[]'
                >
                <div class="absolute right-0 flex items-center top0">
                  <p 
                      class="shadow rounded py-2 px-3 leading-tight cursor-default del-route-point arphover" 
                      onclick='remove_route_name(this)'
                  >-</p>
                </div>
            </div>
            `
        );
        rn.value='';
      }
  });
  function remove_route_name($this){
    $this.closest('.arpz').remove();
  }
</script>


</x-app-layout>

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Events\PusherBroadcast;
use DB;
use Auth;
use Session;

class TravelController extends Controller
{
    public function store(Request $request){

        if(@auth()->user()->type!=2){
            return redirect()->route('root');
        }

        $validate = $request->validate([
            'from'          => 'required|max:250',
            'to'            => 'required|max:250',
            'route_points'  => 'required'
        ]);


        DB::beginTransaction();
        try {
            $travelId = DB::table('travel_history')->insertGetId([
                'user_id' => Auth::id(),
                'from' => $request->from,
                'to' => $request->to
            ]); 
            $data = [[
                'user_id' => Auth::id(),
                'travel_history_id' => $travelId,
                'name'=>$request->from
            ]];
            $data = array_merge(
                $data,
                array_reduce(
                    (array)$request->route_points,
                    function($data,$rn) use ($travelId){
                        $data[] = [
                            'user_id' => Auth::id(),
                            'travel_history_id' => $travelId,
                            'name'=>$rn
                        ];
                        return $data;
                    },[]                
                )
            );
            $data[] = [
                'user_id' => Auth::id(),
                'travel_history_id' => $travelId,
                'name'=>$request->to
            ];
            DB::table('route_points')->insert($data);
            DB::commit();

            Session::flash('alert', [1,'Recorded Your Travel Info. Now Update Each Route Point After Passing']); 

            broadcast(new PusherBroadcast('travel'))->toOthers();

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('alert', [0,'Something Wrong! Please Try Again.']); 
            return redirect()->back();
        }
    }

    public function update_passed_route(Request $request){
        //print_r($request->all());
        $success = DB::table('route_points')
        ->where('id',$request->data_id)
        ->update([
            'passed'=>1,
            'passed_time'=>date('Y-m-d H:i:s')
        ]);
        if($success){
            broadcast(new PusherBroadcast('travel'))->toOthers();
        }
        echo json_encode([
            'success' => $success
        ]);
    }

    public function running_vehicle_summary(){
        $data = DB::select("
            SELECT 
            t1.travel_history_id,
            (
                SELECT vehicle_type from users where id=(select user_id from route_points t7 where t7.travel_history_id=t1.travel_history_id limit 1)
            ) as vehicle_type,
            ( SELECT name FROM route_points t3 
                WHERE t3.travel_history_id=t1.travel_history_id 
                AND t3.passed=1 
                ORDER BY t3.id DESC LIMIT 1 
            ) as first,
            ( SELECT name FROM route_points t4 
                WHERE t4.travel_history_id=t1.travel_history_id 
                AND (t4.passed IS NULL OR t4.passed='')
                ORDER BY t4.id ASC LIMIT 1 
            ) as second,
            ( SELECT name FROM route_points t5 
                WHERE t5.travel_history_id=t1.travel_history_id 
                AND (t5.passed IS NULL OR t5.passed='')
                ORDER BY t5.id ASC LIMIT 1,1 
            ) as third

            FROM route_points t1        
            WHERE (t1.passed IS NULL OR t1.passed='')
            GROUP BY  t1.travel_history_id
        ");

        $data = array_reduce(
            $data,
            function($data,$row){
                $indx = ($row->first)?$row->first.'___'.$row->second:$row->second.'___'.$row->third;

                $data[$indx][$row->vehicle_type] = ((int)@$data[$indx][$row->vehicle_type])+1;

                return $data;
            },[]
        );

        echo json_encode($data);
    }
}

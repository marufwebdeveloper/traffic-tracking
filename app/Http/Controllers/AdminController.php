<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Events\PusherBroadcast;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Routing\Redirector;
use App\Http\Requests\Auth\LoginRequest;
use DB;
use Session;

class AdminController extends Controller {
   
    function __construct(Redirector $redirect){
        /*if(
            !in_array(
                request()->route()->getName(),
                ['admin.login','admin.register']
            ) && !(Auth::user())
        ){
            //return redirect()->route('root')->send();
        }*/
        
    }

    public function register(Request $request){
        if(@auth()->user()->type!=1){
            return redirect()->route('root');
        }
            
        if($request->isMethod('post')){
            $request->validate([
                'vehicle_number' => ['required', 'string', 'max:255', 'unique:'.User::class],
                'vehicle_type' => ['required','max:255'],            
                'mobile' => ['required','numeric','digits:11', 'unique:'.User::class],
                'password' => ['required', 'confirmed']#, Rules\Password::defaults()],
            ]);

            $user = User::create([
                'vehicle_number' => $request->vehicle_number,
                'vehicle_type' => $request->vehicle_type,
                'mobile' => $request->mobile,
                'password' => Hash::make($request->password),
                'type' => 1,
            ]);

            event(new Registered($user));

            Auth::login($user);

            return redirect()->route('admin.dashboard');
        }

        return view('admin.register');
    }

    public function login(Request $request){

        if(@auth()->user()->type==1){
            return redirect()->route('admin.dashboard');
        }

        if($request->isMethod('post')){

        $request->validate([
            'vehicle_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('vehicle_number',$request->vehicle_number)
        ->where('type',1)
        ->first();

            if($user && Hash::check($request->password, $user->password)){
                Auth::login($user);
                return redirect()->route('admin.dashboard');
            }else{
                return redirect()->back()->with('error',"Credential Does not Match");
            }
        }
        return view('admin.login');
    }

    public function dashboard(Request $request){
        if(auth()->user()->type!=1){
            return redirect()->route('root');
        }
        return view('admin.dashboard');
    }

    public function notice(Request $request){
        if(auth()->user()->type!=1){
            return redirect()->route('root');
        }

        $data = [];

        if($request->isMethod('post')){
            $success = DB::table("notices")->insert([
                'notice'=> $request->notice,
                'effective_date'=> date('Y-m-d H:i:s',strtotime($request->effective_date)),
                'active_till' => date('Y-m-d H:i:s',strtotime($request->active_till)),
                'created_by' => auth()->user()->id
            ]);
            if($success){
                $data['alert'] = [1,"Successfully Saved"];

                broadcast(new PusherBroadcast('noticepublish'))->toOthers();
            }else{
                $data['alert'] = [0,"Something Wrong. Please Try Again."];
            }

        }

        return view('admin.notice',$data);
    }

    public function get_notices(){
        echo json_encode([
            'data' => array_column(
                DB::select("SELECT notice FROM notices WHERE  '". date("Y-m-d H:i:s", strtotime('+6 hours'))."' BETWEEN effective_date AND active_till"),
                'notice'
            )
        ]);
    }

    

}

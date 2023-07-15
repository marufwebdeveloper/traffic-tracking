<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Events\PusherBroadcast;

class PusherController extends Controller{
	
    public function broadcast(Request $request){
    	$message = rand(1111,9999);
    	broadcast(new PusherBroadcast($message))->toOthers();
    	#broadcast(new PusherBroadcast('hello world'));
    	#event(new PusherBroadcast('hello world'));
    	echo $message;
    }
    public function receive(Request $request){

    }
    
}

<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Models\Speachesapi;
use App\Models\Speachesresult;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
class SpeachesapiController extends Controller
{
    public function __construct()
    { }
	
	public function useby ( Request $request ) 
	{
		$speachesapi = new Speachesapi ();
		$speachesapi->user_id = $request->user_id;
		$speachesapi->lesson_id = $request->lesson_id;
		$speachesapi->game_id = $request->game_id;
		$speachesapi->date = date('Y-m-d');
		$speachesapi->save();
		return response()->json(
			[
				'status'=>'true',
				'message' =>'Record has been saved'
			]
		);
	}

	public function saveResult(Request $request) 
	{
		$speachesapi = new Speachesresult ();
		$speachesapi->user_id = $request->user_id;
		$speachesapi->lesson_id = $request->lesson_id;
		$speachesapi->game_id = $request->game_id;
		$speachesapi->response_json = $request->speach_json;
		$speachesapi->date = date('Y-m-d');
		$speachesapi->save();
		return response()->json(
			[
				'status'=>'true',
				'message' =>'Record has been saved'
			]
		);
	}    
}

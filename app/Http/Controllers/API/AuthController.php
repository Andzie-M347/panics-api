<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Panic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function create_user(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => "failed.",
                'message' => $validator->messages()
            ];
            return response()->json($response, 400);
        }
        
        $user = new User();
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        $token =  $user->createToken('user')->plainTextToken;
        
        return response()->json([
            "status" => "success",
            "message" => "Action completed successfully",
            "data" => [
                "api_access_token" => $token
            ],
        ],200);
    }

    public function log_in(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => "failed.",
                'message' => $validator->messages()
            ];
            return response()->json($response, 400);
        }
    
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $authUser = User::where('id', Auth::user()->id)->first(); 
            $token =  $authUser->createToken('user')->plainTextToken; 
   
            return response()->json([
                "status" => "success",
                "message" => "Action completed successfully",
                "data" => [
                    "api_access_token" => $token
                ],
            ],200);
        } 
        else{ 
            return response()->json([
                "status" => "failed.",
                "message" => 'Unauthorised'
            ],401);
        } 
    }

    public function send_panic(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'longitude' => 'required|string',
            'latitude' => 'required|string',
            'panic_type' => 'required|string',
            'details' => 'required',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => "failed.",
                'message' => $validator->messages()
            ];
            return response()->json($response, 400);
        }

        $user = User::findOrFail($id);
        if(!empty($user)){
            $new = new Panic();
            $new->longitude = $request->longitude;
            $new->latitude = $request->latitue;
            $new->panic_type = $request->panic_type;
            $new->details = $request->details;
            $new->user_id = $user->id;
            $new->save();
    
            return response()->json([
                "status" => "success",
                "message" => "Panic raised successfully",
                "data" => [
                    "panic_id" => $new->id
                ]
            ],200);
        } else {
            return response()->json([
                "status" => "failed",
                "message" => "User not found.",
                "data" => []
            ],400);
        }
    }

    public function cancel_panic(Request $request,$id){
        $find = User::findOrFail($id);
        if(!empty($find)){
            $panicid = $request->panic_id;
            $panic = Panic::where('id', $panicid)->first();
            if(!empty($panic)){
                if($panic->user_id == $find->id){
                    $panic->is_cancel = 1;
                    $panic->save();
    
                    return response()->json([
                        "status" => "success",
                        "message" => "Panic cancelled successfully",
                        "data" => []
                    ],200);
                } else {
                    return response()->json([
                        "status" => "failed",
                        "message" => "This user not belong to this panic.",
                    ],400);
                }
            } else {
                return response()->json([
                    "status" => "failed",
                    "message" => "Panic id not found.",
                ],400);
            }
        } else {
            return response()->json([
                "status" => "failed",
                "message" => "User not found.",
            ],400);
        }
    }

    public function get_panic(){
        $data = [];
        $panic = Panic::orderBy('created_at', 'DESC')->get();
        foreach($panic as $datum){
            array_push($data,[
                "id" => $datum->id,
                "longitude" => $datum->longitude,
                "latitude" => $datum->latitude,
                "panic_type" => $datum->panic_type,
                "details" => $datum->details,
                "created_at" => $datum->created_at->format('Y-m-d'),
                "created_by" => User::where('id', $datum->user_id)->first()
            ]);
        }
        
        return response()->json([
            "status" => "success",
            "message" => "Action completed successfully",
            "data" => [
                "panics" => $data
            ]
        ],200);
    }
}

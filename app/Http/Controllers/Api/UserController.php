<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use App\Models\DeviceToken;
use URL;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $jwtAuth;
    public function __construct( JWTAuth $jwtAuth )
    {
        $this->jwtAuth = $jwtAuth;
       
    }

   /**
    * @OA\Post(
    * path="/api/upload_image",
    * summary="Upload Image",
    * description="Upload Image",
    * operationId="authUploadImage",
    * tags={"User"},
    * security={ {"bearer_token": {} }},
    * @OA\RequestBody(
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="multipart/form-data",
    *             @OA\Schema(
    *                 @OA\Property(
    *                     description="Image to upload",
    *                     property="image",
    *                     type="file",
    *                     format="file",
    *                 ),
    *                 required={"image"}
    *             )
    *         )
    * ),
 
    * @OA\Response(
    *    response=422,
    *    description="Wrong format response",
    *    @OA\JsonContent(
    *       @OA\Property(property="message", type="string", example="Sorry, wrong Format. Please try again")
    *        )
    *     )
    * )
    */
    public function upload_image(Request $request) {
  
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048'
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->messages()->first()
            ];
            return response()->json($response);
        }

        $file = $request->file('image');
        $name = auth()->user()->id .'-'.time() .  '.' . $file->getClientOriginalExtension();
        $path = public_path('/uploads/users');
        if(!\File::exists($path)) {
            \File::makeDirectory($path, 0777, true, true);
        }
        $file_r = $file->move($path, $name);
        $path = url('/uploads/users/'.$name);


        $user = User::find(auth()->user()->id);
        $user->image = $name;
        if($user->save()) {
            $response = ['success' => true, 'message' => 'Image uploaded successfully', 'file_name' => $path];
        }   else {
            $response = ['success' => true, 'message' => 'Something went wrong, Please try again.'];
        }
        return response()->json($response);
    }
    /**
     * @OA\Post(
     * path="/api/create_profile",
     * summary="Create Profile",
     * description="Create profile for user",
     * operationId="authCreateProfile",
     * tags={"User"},
     * security={ {"bearer_token": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user name and email",
     *    @OA\JsonContent(
     *       required={"name","email"},
     *       @OA\Property(property="name", type="string", format="name", example="john"),
     *       @OA\Property(property="email", type="string", format="email", example="abc@gmail.com"),
    *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong invalid email or name. Please try again")
     *        )
     *     )
     * )
     */
    public function create_profile(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|email'
        ]);
        if ($validator->fails()) {
            $res = [
                'success' => false,
                'message' => $validator->messages()->first()
            ];
            return response()->json($res);
        }
        $data = $request->all();
        if(empty($data)) {
            return response()->json(['success' => false, 'message' => 'Please add some values to create profile']);
        }
        $user = User::find(auth()->user()->id);
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->profile_status = 1;
        if($user->save()) {
            $res = ['success' => true, 'message' => 'Profile created successfully','user' => $user];
        }   else {
            $res =['success' => true, 'message' => 'Something went wrong, Please try again.'];
        }
        return response()->json($res);

    }


  

  

}

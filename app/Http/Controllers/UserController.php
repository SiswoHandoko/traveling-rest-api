<?php
namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Request;
use App\Model\User;
class UserController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $req)
    {
        $user = User::where('status','!=','deleted')->get();
        $result = $this->generate_response($user,200,'All Data.',false);
        return response()->json($result, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        /* Validation */
        $validator = Validator::make($req->all(), [
          'firstname' => 'required|max:255',
          'lastname' => 'required|max:255',
          'contact' => 'required|max:255',
          'address' => 'required|max:255',
          'birthdate' => 'required|max:255',
          'username' => 'required|max:255',
          'password' => 'required|max:255',
          'repassword' => 'required|max:255',
          'email' => 'required|max:255',
          'role_id' => 'required|max:255|in:1,2,3',
          'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if($validator->fails()) {
            $result = $this->generate_response($user,400,'Bad Request.',true);
            return response()->json($result, 400);
        }else{

            /* Unique Field Validation */
            $unique_validator = Validator::make($req->all(), [
              'username' => 'unique:users',
              'email' => 'unique:users',
            ]);

            if($unique_validator->errors()->first('username')!='') {
                $result = $this->generate_response($user,1,'This Username already taken by others.',true);
                return response()->json($result, 200);
            }elseif($unique_validator->errors()->first('email')!='') {
                $result = $this->generate_response($user,2,'This Email already taken by others.',true);
                return response()->json($result, 200);
            }else{
                /* bundling data */
                $hasher = app()->make('hash');
                $password = $hasher->make($req->input('password'));

                $user = new User();
                $user->firstname = $req->has('firstname') ? $req->firstname : '';
                $user->lastname = $req->has('lastname') ? $req->lastname : '';
                $user->contact = $req->has('contact') ? $req->contact : '';
                $user->address = $req->has('address') ? $req->address : '';
                $user->birthdate = $req->has('birthdate') ? $req->birthdate : '0000-00-00';
                $user->username = $req->has('username') ? $req->username : '';
                $user->password = $req->has('password') ? $req->password : '';
                $user->email = $req->has('email') ? $req->email : '';
                $user->role_id = $req->has('role_id') ? $req->role_id : '1';
                $user->photo = $req->has('photo') ? $req->photo : '1';
                $user->status = 'active';
                $user->save();
                $result = $this->generate_response($user,200,'Data Has Been Saved.',false);

                return response()->json($result, 200);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where('status','!=','deleted')->find($id);
        if(!$user){
            $result = $this->generate_response($user,404,'Data Not Found.',true);
            return response()->json($result, 404);
        }else{
            $result = $this->generate_response($user,200,'Detail Data.',false);
            return response()->json($result, 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */

    public function update(Request $req,$id)
    {
        /* Validation */
        $validator = Validator::make($req->all(), [
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'contact' => 'required|max:255',
            'address' => 'required|max:255',
            'birthdate' => 'required|max:255',
            'username' => 'required|max:255',
            'password' => 'required|max:255',
            'repassword' => 'required|max:255',
            'email' => 'required|max:255',
            'role_id' => 'required|max:255|in:1,2,3',
            'status' => 'required|max:255',
        ]);

        if($validator->fails()) {
            $result = $this->generate_response($user,400,'Bad Request.',true);
            return response()->json($result, 400);
        }else{
            /* Custom Unique Field Validation */
            $username = User::where([['username', '=', $req->username],['id', '!=', $id]])->first();
            $email = User::where([['email', '=', $req->email],['id', '!=', $id]])->first();

            if($username) {
                $result = $this->generate_response($user,1,'This Username already taken by others.',true);
                return response()->json($result, 200);
            }elseif($email) {
                $result = $this->generate_response($user,2,'This Email already taken by others.',true);
                return response()->json($result, 200);
            }else{
                $user = User::find($id);
                $user->firstname = $req->has('firstname') ? $user->firstname : '';
                $user->lastname = $req->has('lastname') ? $user->lastname : '';
                $user->contact = $req->has('contact') ? $user->contact : '';
                $user->address = $req->has('address') ? $user->address : '';
                $user->birthdate = $req->has('birthdate') ? $user->birthdate : '0000-00-00';
                $user->username = $req->has('username') ? $user->username : '';
                $user->password = $req->has('password') ? $user->password : '';
                $user->email = $req->has('email') ? $user->email : '';
                $user->role_id = $req->has('role_id') ? $user->role_id : '1';
                $user->status = $req->has('status') ? $user->status : 'active';
                $user->save();
                $result = $this->generate_response($user,200,'Data Has Been Updated.',false);
                return response()->json($result, 200);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->status = 'deleted';
        $user->save();
        $result = $this->generate_response($user,200,'Data Has Been Deleted.',false);
        return response()->json($result, 200);
    }

    /**
     * Index login controller
     *
     * When user success login will retrive callback as api_token
     */
    public function login(Request $request)
    {
        /* Validation */
        $validator = Validator::make($request->all(), [
          'username' => 'required|max:255',
          'password' => 'required|max:255',
        ]);

        if($validator->fails()) {
            $result = $this->generate_response($city,400,'Bad Request.',true);
            return response()->json($result, 400);
        }else{
            $result = $this->authentication($request);
        }
        return response()->json($result, 200);
    }

    public function authentication($request){
        /* bundling data */
        $hasher = app()->make('hash');
        $username = $request->input('username');
        $password = $request->input('password');

        /* get data */
        $login = User::where('username', $username)->where('status','!=','deleted')->first();

        /* result */
        if (!$login) {
            /* Username Not Found */
            $res = $this->generate_response($login,1,'Your username or password incorrect!',true);
            return $res;
        }else{
            if ($hasher->check($password, $login->password)) {
                $api_token = sha1(time());
                if($request->header('device-type')=='web'){
                    $create_token = User::where('id', $login->id)->update(['web_token' => $api_token]);
                    if ($create_token) {
                        /* Set Web Token As Result */
                        $login['token'] = $api_token;
                        $res = $this->generate_response($login,200,'Success Login on Web!',false);
                        return $res;
                    }
                }elseif($request->header('device-type')=='android'){
                    $create_token = User::where('id', $login->id)->update(['android_token' => $api_token]);
                    if ($create_token) {
                        /* Set Android Token As Result */
                        $login['token'] = $api_token;
                        $res = $this->generate_response($login,200,'Success Login on Android!',false);
                        return $res;
                    }
                }elseif($request->header('device-type')=='ios'){
                    $create_token = User::where('id', $login->id)->update(['ios_token' => $api_token]);
                    if ($create_token) {
                        /* Set IOS Token As Result */
                        $login['token'] = $api_token;
                        $res = $this->generate_response($login,200,'Success Login on IOS!',false);
                        return $res;
                    }
                }
            }else{
                /* Password Incorrect */
                $login = null;
                $res = $this->generate_response($login,1,'Your username or password incorrect!',true);
                return $res;
            }
        }
    }
}

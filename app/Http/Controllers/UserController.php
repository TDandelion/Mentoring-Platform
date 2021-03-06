<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\User;
use Auth;
use \Input as Input;


class UserController extends BaseController
{
    public function showRegisterForm($type = null)
    { 
        if($type){
            return view('register')->with('type', $type);
        } else {
            return view('register');
        }
        
    }

    public function showLoginForm()
    {
        return view('login');
    }
    public function addNewUser(Request $request)
    {
        $request->validate([
            'username' => 'required|max:255',
            'password' => 'required|max:255',
            'email' => ''
        ]);

        $user = new User;
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->email = $request->email;
        $user->programming_languages = json_encode($request->programming_languages);
        $user->speaking_languages = json_encode($request->speaking_languages);
        $request->registerType === 'mentor' ? $user->is_a_mentor = true: $user->is_a_mentor = false;
        $user->is_admin = false;
        $user->save();
        $this->loginUser($user);
        return response(200);
    }

    public function updateUser(Request $request)
    {
        $user = Auth::user();
        if(!$user){
            return response('User not found', 404);
        }else{
            $user->email = $request->email;
            $user->first_name = $request->firstname;
            $user->last_name = $request->lastname;
            $user->email = $request->email;
            $user->programming_languages = json_encode($request->programming_languages);
            $user->speaking_languages = json_encode($request->speaking_languages);
            $user->password = Hash::make($request->password);
            $user->save();
        }
        return response(200);

    }  

    public function doLogin(Request $request)
    {
              
        $email=$request->email;
        $password=$request->password;
        $user = User::where('email', '=', $email)->first();
        $this->loginUser($user);
        return response(200);
    }
    
    private function loginUser($user)
    {
        Auth::login($user);
    }

    public function loggingOut()
    {
        if(Auth::check()) {
            Auth::logout();
        }

        return view('welcome');
    }

    public function getMyUser()
    {
        if(Auth::user()){
            return response()->json(Auth::user());
        } 
    }

    public function getAllUser()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function showBackend()
    {
        return view('backend');
    }
    /*public function uploadAvatar()
    {
        if(Input::hasFile('file')){

			echo 'Uploaded';
			$file = Input::file('file');
			$file->move('uploadAvatar', $file->getClientOriginalName());
			echo '';
    }*/
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Session;


class CustomAuthController extends Controller
{
    public function login(){

        return view("auth.Login");

    }

    public function registration(){

        return view("auth.Registration");

    }

    public function registerUser(Request $reqesut ){

        $reqesut->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:4|max:8'
        ]);

        $user = new User();
        $user->name = $reqesut->name;
        $user->email = $reqesut->email;
        $user->password =Hash::make($reqesut->password);
        $res = $user->save();
        if($res){
            return back()->with('success','You have Registered successfully');
        }
        else{
            return back()->with('fail','Something is wrong');
        }

    }

    public function loginUser(Request $reqesut ){

        $reqesut->validate([
            'email'=>'required|email',
            'password'=>'required|min:4|max:8'
        ]);

        $user = User::where('email','=',$reqesut->email)->first();

        if($user){
           if(Hash::check($reqesut->password,$user->password)){
                 $reqesut->Session()->put('loginId',$user->id);
                 return redirect('home');
           }else{
               return back()->with('fail','Password does not match');
           }
        }
        else{
            return back()->with('fail','This email is not registered!');
        }

        
    }

    public function home(){
        $data = array();
        if(Session::has('loginId')){
            $data = User::where('id','=',Session::get('loginId'))->first();
        }
        return view('home',compact('data'));
    }
    
    public function logout(){
        if(Session::has('loginId')){
            Session::pull('loginId');
            return redirect('login');
        }
    }

    
}

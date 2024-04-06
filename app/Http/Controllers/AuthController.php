<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;


class AuthController extends Controller
{
    public function login(Request $request )
  	{
		
  		
       return view('auth.login');
	}

	public function Weblogin(Request $request)
	{
		// $current_time = Carbon::now()->toDateString();

		// dd($current_time);

		$email = $request->email;
		$providedPassword = $request->password;	

		$query = User::where('email', $email)->first();
// 		dd($query);


		if($query && $query->is_active == 1){
			$storedPassword = Crypt::decrypt($query->password);
		// $data =	$request->input();
		if ($providedPassword === $storedPassword) {
		$request->session()->put('name', $query['name']);
		$request->session()->put('id', $query['id']);
		$request->session()->put('is_customer', $query['is_customer']);

		// dd()
		return redirect('yarn')->with('message', 'Login Successfully !');
	}
	}elseif($query && $query->is_active == 0){
		return redirect('/')->with('error', 'Your 
			account is not active. Please contact the administrator.');
	}
	else{
		return redirect('/')->with('error','incoorect Username or password');
	}
	}


	public function logout(Request $request)
	{
		Auth::logout();
		return redirect(url('/'));
	}

	public function signin()
	{
		return view('auth.signin');
	}

	public function singin_create(Request $request)
	{
		$user = new User();
		$user->name = $request->name;
		$user->email = $request->email;
		$user->password = Crypt::encrypt($request->password);
		$user->save();

		$email = $request->email;
		$providedPassword = $request->password;	

		$query = User::where('email', $email)->first();
		// dd($query);


		if($query){
			$storedPassword = Crypt::decrypt($query->password);
		// $data =	$request->input();
		if ($providedPassword === $storedPassword) {
		$request->session()->put('name', $query['name']);
		$request->session()->put('id', $query['id']);
		// dd(session('id'));
		// die;

		$user_id = session('id');
		$current_time = Carbon::now();


		$current_time_90 = Carbon::now();
		$time_after_90_days = $current_time_90->addDays(90);
		
		$package = new Package();
		$package->user_id = $user_id;
		$package->package_id = 1;
		$package->amount = 0;
		$package->starting_date = $current_time->toDateString();
		$package->ending_date = $time_after_90_days->toDateString();
		$package->notes = "free package";
		$package->payment_method = "free package";
		$package->save();

		return redirect('yarn')->with('message', 'Login Successfully !');



	}
	}
	else{
				return redirect('/')->with('error','incoorect Username or password');

	}

	}
    
}

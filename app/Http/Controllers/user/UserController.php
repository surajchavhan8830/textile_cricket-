<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\{
	Package,
	User,
	Admin,
    Packagelist
};

class UserController extends Controller
{
	public function admin_login()
	{
		return view('auth.user.login');
	}

	public function admin_login_form(Request $request)
	{
		$email = $request->email;
		$providedPassword = $request->password;
		$query = Admin::where('email', $email)->where('password', $providedPassword)->first();

		if ($query) {
			// $storedPassword = Crypt::decrypt($query->password);

			// if ($providedPassword === $storedPassword) {
			$request->session()->put('name', $query['name']);
			$request->session()->put('id', $query['id']);

			$request->session()->put('is_customer', $query['is_customer']);

			return redirect('user_customer')->with('message', 'Login Successfully !');
			// }
			// } elseif ($query && $query->is_active == 0) {
			// 	return redirect('/')->with('error', 'Your account is not active. Please contact the administrator.');
		} else {
			return redirect('/')->with('error', 'incoorect Username or password');
		}
	}

	// customer get 
	public function user_customer(Request $request)
	{
		if($request->ajax())
		{
			$customers = User::where('is_customer', 1)->where('is_delete', 1)
			->where('mobile_number', 'LIKE', '%' . $request->search . '%')->get();
			// dd($customers);
			return response()->json(['customers' => $customers]);	
		}
		$customers = User::where('is_customer', 1)->where('is_delete', 1)->get();
		// dd($customers);

		return view('user.customer.index', compact('customers'));
	}

	public function inactive(Request $request)
	{
		if($request->ajax())
		{
			$customers = User::where('is_customer', 1)->where('is_delete', 0)
			->where('mobile_number', 'LIKE', '%' . $request->search . '%')->get();
			// dd($customers);
			return response()->json(['customers' => $customers]);	
		}
		$customers = User::where('is_customer', 1)->where('is_delete', 0)->orderBy('updated_at', 'asc')->get();
		// dd($customers);

		return view('user.customer.inactive', compact('customers'));

	}

	public function user_package(Request $request)
	{
		$today = Carbon::now();
		$isExpired = $today->isPast();
		

		// dd($request->status_id);
		$query = Package::leftJoin('users', function ($join) {
			$join->on('users.id', '=', 'packages.user_id');
		});


		if ($request->mobile) {
			$query->where('users.mobile_number', 'LIKE', '%' . $request->mobile . '%');
		}

		if ($request->status_id == 0) {
			$query->where('packages.starting_date', '<=', $today)
				->where('packages.ending_date', '>=', $today);
		} elseif ($request->status_id == 1) {
			$query->where('packages.ending_date', '<', $today);
		} 

		$packages = $query->select(
			'packages.*',
			'packages.id as package_id',
			'users.name as name',
			'users.is_delete',
			'users.id as user.id'
		)->get();
		// $packages = $packages->where('users.is_delete' '=', )
		

		return view('user.packages.index', compact('packages'));
	}

	public function user_pack($id)
	{
		
		$startDate = Carbon::now(); // Start of today
		$user = User::where('id', $id)->first();
		// $packages = Package::with('user', 'package')->where('user_id', $id)
		// 	->where('starting_date', '<=', $startDate)
		// 	->where('ending_date', '>', $startDate)->get();
		$packages = Package::with('user', 'package')->where('user_id', $id)->get();
		
		// ->whereBetween('ending_date',[$startDate, $endDate])->get();
		return view('user.packages.user_package', compact('packages', 'id','user'));
	}

	public function create_package(Request $request, $id)
	{
		
		
		$startDate = Carbon::now();
		$package = Package::where('user_id', $id)->latest()->first();
		$newpackage = Packagelist::where('id', $request->package)->first();
		$user = User::where('id', $package->user_id)->select('name', 'id')->first();

		if ($package->ending_date > $startDate) {
			$daysRemaining = $startDate->diffInDays($package->ending_date);
			$package_start_date = $startDate->addDays($daysRemaining + 2);
			// dd($package_end_date);

		} else {
			$package_start_date = Carbon::now();

		}

		return view('user.packages.create', compact('package', 'user', 'package_start_date', 'newpackage'))->with('success', "Package Save Successfully");
	}

	public function edit_package($id)
	{
		$startDate = Carbon::now();
		$package = Package::where('id', $id)->latest()->first();
		// $newpackage = Packagelist::where('id', $id)->first();
		$user = User::where('id', $package->user_id)->select('name', 'id')->first();

		if ($package->ending_date > $startDate) {
			$daysRemaining = $startDate->diffInDays($package->ending_date);
			$package_start_date = $startDate->addDays($daysRemaining + 2);
			// dd($package_end_date);

		} else {
			$package_start_date = Carbon::now();

		}

		return view('user.packages.edit', compact('package', 'user', 'package_start_date', ))->with('success', "Package Save Successfully");
	}

	public function store_package(Request $request)
	{
		if($request->package_id == 1)
		{
			return back()->with('error', "Please Select Package");
		}	
			$validator = Validator::make($request->all(), [
			'user_id'  => 'required|not_in:0',
			'email'  => 'required',
			'mobile_number'  => 'required',
		]);

		

		$user_id = $request->user_id;

		$startDate = Carbon::now();
		$package = Package::where('user_id', $user_id)->select('ending_date', 'id')->latest()->first();
		// dd($package);
		if ($package->ending_date > $startDate) {
			$daysRemaining = $startDate->diffInDays($package->ending_date);
			$package_start_date = $startDate->addDays($daysRemaining + 2);
			// dd($package_start_date);
		} else {
			$package_start_date = Carbon::now();
		}

		if (!$validator->failed()) {
			$packagelist = new Package();
			$packagelist->user_id = $request->user_id;
			$packagelist->package_id = $request->package_id;
			$packagelist->starting_date = $package_start_date;
			$packagelist->ending_date = $request->ending_date;
			$packagelist->payment_method = $request->payment_method;
			$packagelist->amount = $request->amount;
			$packagelist->notes = $request->notes;
			$packagelist->save();
		}
		return redirect()->back()->with('success', "Package Save Successfully");
	}

	public function update_package(Request $request, $id)
	{
		

			$validator = Validator::make($request->all(), [
				'starting_date' => 'required',
				'ending_date' => 'required'
			]);
			if($validator->failed())
			{
				return redirect()->back()->withErrors($validator)->withInput();
			}else{

			$packagelist = Package::find($id);
			$packagelist->starting_date = $request->starting_date;
			$packagelist->ending_date = $request->ending_date;	
			$packagelist->amount = $request->amount;
			$packagelist->notes = $request->notes;
			$packagelist->save();
		}
		    
		return redirect()->back()->with('success', "Package Upadated; Successfully");
	}
}
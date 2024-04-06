<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $packagelist = Package::with('user', 'package')->get();
        // dd($packagelist);
        return view('admin.packages.index', compact('packagelist'));
    }

    public function user_package($id)
    {
        $startDate = Carbon::now(); // Start of today

        
        $package = Package::with('user', 'package')->where('user_id', $id)
        ->where('starting_date', '<=', $startDate)
        ->where('ending_date', '>', $startDate)->first();
        // ->whereBetween('ending_date',[$startDate, $endDate])->get();
        // dd($packagelist);
        return view('admin.packages.user_package', compact('package'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.packages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'user_id'  => 'required|not_in:0',
            'email'  => 'required',
            'mobile_number'  => 'required',
        ]);
        

        if (!$validator->failed()) {

            // dd($request->all());
            // die;
            $packagelist = new Package();
            $packagelist->user_id = $request->user_id;
            $packagelist->package_id = $request->package_id;
            $packagelist->starting_date = $request->starting_date;
            $packagelist->ending_date = $request->ending_date;
            $packagelist->payment_method = $request->payment_method;
            $packagelist->amount = $request->amount;
            $packagelist->notes = $request->notes;
            $packagelist->save();
        }


        return redirect()->route('packagelist.index')->with('success', "Package Save Successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $package = Package::find($id);
        // dd($package);

        $package = Package::with('user', 'package')->where('id', $id)->first();



        return view('admin.packages.edit', compact('package'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $package = Package::find($id);
        $package->package_id = $request->package_id;
        $package->starting_date = $request->starting_date;
        $package->ending_date = $request->ending_date;
        $package->notes = $request->notes;
        $package->payment_method = $request->payment_method;
        $package->save();

        return redirect()->route('packagelist.index')->with('success', 'Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $package = Packagelist::find($id);

        dd($package);
    }
}

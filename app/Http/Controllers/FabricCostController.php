<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FabricCost;
use App\Models\User;
use App\Models\Yarn;
use App\Models\Warp;
class FabricCostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $warp;
    public function index()
    {
        $fabric = FabricCost::find(1);
        dd($fabric->getculation());
        return view('admin.fabric_cost.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        return view('admin.fabric_cost.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    

        $cost = new FabricCost();
        $cost->fabric_name = $request->fabric_name;
        $cost->warp_yarn = $request->warp_yarn;
        $cost->weft_yarn = $request->weft_yarn;
        $cost->width = $request->width;
        $cost->final_ppi = $request->final_ppi;
        $cost->warp_wastage = $request->warp_wastage;
        $cost->weft_wastage = $request->weft_wastage;
        $cost->butta_cutting_cost = $request->butta_cutting_cost;
        $cost->additional_cost = $request->additional_cost;
        $cost->fabric_category_id =  $request->fabric_category_id ;
        $cost->user_id = 1;
        $cost->save();

        
        $this->warp = $cost->id;

        return redirect()->route('fabricCost.warp_yarnCreate')->with('success', 'Saved Successfuly');
        
        // return view('admin.warp_weft.warpcreate', compact('wrp'));
        
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function warp_yarnCreate()
    {
     
        $yarns = Yarn::where('user_id', session('id'))->get();

        // $array = [1, 2];

        
        // $yarn = Yarn::whereIn('id', $array)->get();
       
       
        $warp_yarn = FabricCost::where('user_id', session('id'))->latest()->first();
       
        return view('admin.warp_weft.warpcreate', compact('yarns', 'warp_yarn'));  
    }

    public function AddWarp(Request $request)
    {
        // dd($request->all());
        // die;
        $arrays = $request->fabric_category_id;

        foreach($arrays as $array)
        {
            $warp = new Warp();
            $warp->fabric_cost_id = $array;
            $warp->yarn_name = $request->yarn_name;
            $warp->ends  = implode(' ', $request->ends);
            $warp->fabric_cost_id  = implode(' ', $request->fabric_cost_id);
            $warp->save();
        }

        print_r($arrays);
    }
}

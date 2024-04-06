<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Yarn;

class YarnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

       $user_id = session('id');


       $yarns = Yarn::leftjoin('yarn_categories', function($join){
            $join->on('yarns.category_id', '=', 'yarn_categories.id');
        })->select('yarns.*','yarn_category')->where('yarns.user_id', $user_id )->get();
        
        return view('admin.yarn.index', compact('yarns'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.yarn.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        
        $yarn = new Yarn();
        $yarn->yarn_name = $request->yarn_name;
        $yarn->yarn_denier = $request->yarn_denier;
        $yarn->yarn_rate = $request->yarn_rate;
        $yarn->category_id = $request->category_id;
        $yarn->user_id = 1;
        $yarn->save();
 

        return redirect('yarn')->with('message', 'New Yarn Added Successfully');
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

        $yarn = Yarn::leftjoin('yarn_categories', function($join){
            $join->on('yarns.category_id', '=', 'yarn_categories.id');
        })->select('yarns.*', 'yarn_category')->orderBy('id','desc')->first();

     

        // $yarn  = Yarn::where('id' , $id)->first();

        return view('admin.yarn.edit', compact('yarn'));
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
        $yarn = Yarn::find($id);
        $yarn->yarn_name = $request->yarn_name;
        $yarn->yarn_denier = $request->yarn_denier;
        $yarn->yarn_rate = $request->yarn_rate;
        $yarn->category_id = $request->category_id;
        $yarn->user_id = 1;
        $yarn->save();

        return redirect('yarn')->with('message', 'Yarn Details Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $yarn = Yarn::find($id);
        $yarn->delete();
        return redirect('yarn')->with('message', 'Yarn  Deleted Successfully');
    }


    
}

<?php

namespace App\Http\Controllers;

use App\Models\FabricCategory;
use Illuminate\Http\Request;

class FabricCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fabric_category = FabricCategory::all(); 
        return view('admin.fabric_category.index', compact('fabric_category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.fabric_category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $category = new FabricCategory();
        $category->fabric_category = $request->fabric_category;
        $category->user_id = 1;
        $category->save();

        return redirect()->route('fabric_category.index')->with('message', 'New Fabric Category Added Successfully !');
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
        $category = FabricCategory::where('id', $id)->first();
        return view('admin.fabric_category.edit', compact('category'));
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
        $category = FabricCategory::find($id);
        $category->fabric_category = $request->fabric_category;
        $category->user_id = 1;
        $category->save();

        return redirect()->route('fabric_category.index')->with('message', 'Fabric Category Updated Successfully !');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = FabricCategory::find($id);
        $category->delete();
        return redirect('fabric_category')->with('message', 'Fabric Category Deleted Successfully !');
    }
}

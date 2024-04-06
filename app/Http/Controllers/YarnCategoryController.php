<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YarnCategory;

class YarnCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categorys = YarnCategory::all();
    
        return view('admin.yarn_category.index', compact('categorys'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('admin.yarn_category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $category = new YarnCategory();
        $category->yarn_category = $request->yarn_category;
        $category->user_id    = 1;
        $category->save();

        return  redirect('yarn_category')->with('message', 'New Yarn Category Added Successfully');

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
        $category  = YarnCategory::where('id' , $id)->first();

        return view('admin.yarn_category.edit', compact('category'));
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
        $category = YarnCategory::find($id);
        $category->yarn_category = $request->yarn_category;
        $category->user_id    = 1;
        $category->save();

        return  redirect('yarn_category')->with('message', 'Yarn Category Updated Successfully');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = YarnCategory::find($id);
        $category->delete();
        return redirect('yarn_category')->with('message', 'Yarn Category Deleted Successfully !');
    }
}

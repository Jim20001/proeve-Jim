<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Categories\Category;
use App\Models\Tests\Question;
use App\Models\Domains\Domain;
use App\Models\Tests\Test;
use Illuminate\Support\Str;

class Categories extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $domains = Domain::all();
        return view('admin.categories.create', compact('domains'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $category = new Category();
        $category->name = $request->input('name');
        $category->slug = Str::slug($request->input('name'), '-');
        $category->image = "";
        $category->body = $request->input('body');
        $category->prijs = $request->input('prijs');
        $category->save();

        return redirect()->route('categories.index')->with('message', trans('common.category.create.success'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $storedQuestions = [];

        return view('admin.categories.edit', compact('category', 'storedQuestions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $category->name = $request->input('name');
        $category->body = $request->input('body'); 
        $category->prijs = $request->input('prijs');
        $category->save();

        return redirect()->route('categories.edit', $category)->with('message', trans('common.category.create.success'));
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Category $category)
    {
        $category->delete();
    	return redirect()->route('admin.categories.index', $category_id)->with('message', trans('common.category.destroy.success'));
    }
}
<?php

namespace App\Http\Controllers;

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
        $category->uuid = (string) Str::uuid();
        $category->domains_id = $request->input('domains_id');
        $category->name = $request->input('name');
        $category->slug = Str::slug($request->input('name'), '-');
        $category->description = $request->input('description');
        $category->price = $request->input('price');
        $category->active = $request->input('active');
        $category->save();

        return redirect()->route('admin.categories.index')->with('message', trans('common.category.create.success'));
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
    public function edit(Category $category)
    {
        $storedQuestions = [];
        $domains = Domain::all();
        $tests = Test::where('categories_id', $category->id)->get();
        // foreach($tests as $test) {
        //     $demo_questions = explode(",", $test->demo_questions);
        //     foreach($demo_questions as $question) {
        //         $q = Question::where('id', $question)->get();
        //         array_push($storedQuestions, $q);
        //     }
        // }

        foreach ($tests as $test) {
            foreach (Question::where('tests_id', $test->id)->get() as $question) {
                array_push($storedQuestions, $question);
            }
        }

        return view('admin.categories.edit', compact('category', 'domains', 'tests', 'storedQuestions'));
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
        // $category->uuid = $request->input('uuid'); 
        $category->domains_id = $request->input('domains_id');
        $category->name = $request->input('name');
        // $category->slug = $request->input('slug');
        $category->description = $request->input('description'); 
        if(empty($request->questions)) {
            $category->demo_tests = "";
        } else {
            $category->demo_tests = implode(",", $request->questions);
        }
        $category->price = $request->input('price');
        $category->active = $request->input('active');
        $category->save();

        return redirect()->route('admin.categories.edit', $category)->with('message', trans('common.category.create.success'));
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
    	return redirect()->route('admin.categories.index')->with('message', trans('common.category.destroy.success'));
    }
}
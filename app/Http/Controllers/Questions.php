<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tests\Test;
use App\Models\Tests\Question;

class Questions extends Controller
{
    public function index()
    {
    	$questions = Question::all();
    	return view('admin.questions.index', compact('questions'));
    }

    public function create(Test $test)
    {
    	return view('admin.questions.create', compact('test'));
    }

    public function store(Request $request, Test $test)
    {
    	$question = new Question;
    	$question->test()->associate($test->id);
    	$question->name = $request->input('name');
    	$question->description = $request->input('description');
    	$question->answer_description = $request->input('answer_description');
    	$question->save();

    	return redirect()->route('admin.questions.index', $test->id)->with('message', trans('common.questions.create.success'));
    }

    public function edit(Question $questions_id)
    {
    	return view('admin.questions.edit');
    }

    public function update(Request $request, Question $question)
    {	
    	// $question->test()->associate($test->id);
        $question->id = $request->input('id');
    	$question->name = $request->input('name');
    	$question->description = $request->input('description');
    	$question->answer_description = $request->input('answer_description');
    	$question->save();

    	return redirect()->route('admin.questions.edit', $question)->with('message', trans('common.questions.create.success'));
    }

    public function show(){
        
    }
    public function destroy(Test $test, Question $question)
    {
    	$question->delete();
    	return redirect()->route('admin.questions.index', $test->id)->with('message', trans('common.questions.destroy.success'));
    }
}
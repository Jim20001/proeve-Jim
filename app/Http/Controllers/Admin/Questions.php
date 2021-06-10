<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tests\Test;
use App\Models\Tests\Question;

class Questions extends Controller
{
    public function index(Request $request, $categories_id, Question $question, $lesson_id)
    {
    	
        $questions = Question::where('lesson_id', $lesson_id)->get();
        
    	return view('admin.questions.index', compact('categories_id','lesson_id', 'questions'));
    }

    public function create($categories_id, $lesson_id)
    {
    	return view('admin.questions.create', compact('categories_id','lesson_id'));
    }

    public function store(Request $request,$categories_id, $lesson_id)
    {
    	$question = new Question;
    	$question->lesson_id = $lesson_id;
    	$question->name = $request->input('name');
    	$question->description = $request->input('description');
    	$question->answer_description = $request->input('answer_description');
    	$question->save();

    	return redirect()->route('questions.index', [$categories_id, $lesson_id])->with('message', trans('common.questions.create.success'));
    }

    public function edit(Request $request, $categories_id, $lesson_id, Question $question)
    {
         return view('admin.questions.edit', compact('categories_id','lesson_id', 'question'));
    }
    public function show(Question $question)
    {
    	return view('admin.questions.edit', compact( 'question'));
    }


    public function update(Request $request, $categories_id, $lesson_id,  Question $question)
    {	
    	$question->name = $request->input('name');
    	$question->description = $request->input('description');
    	$question->answer_description = $request->input('answer_description');
    	$question->save();


    	return redirect()->route('questions.index', [$categories_id, $lesson_id, $question])->with('message', trans('common.questions.create.success'));
    }

    public function destroy($categories_id, $lesson_id, Question $question)
    {
    	$question->delete();
    	return redirect()->route('admin.questions.index', $question->id)->with('message', trans('common.questions.destroy.success'));
    }
}
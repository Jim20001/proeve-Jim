<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Lessons\Lesson as LessonModel;
use App\Models\Lessons\Question;
use App\Models\Domains\Domain;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class Lesson extends Controller
{
    public function index($categories_id)
    {
    	$lessons = LessonModel::where('category_id', $categories_id)->paginate(20);
    	return view('admin.lessons.index', compact('categories_id','lessons'));
    }

    public function create($categories_id)
    {
    	return view('admin.lessons.create', compact('categories_id'));
    }

    public function store(Request $request, $categories_id)
    {
    	$lesson = new LessonModel;
    	$lesson->category_id = $categories_id;
    	$lesson->name = $request->input('name');
        $lesson->slucky = Str::slug($request->input('name'), '-');
        $lesson->body = $request->input('body');
        $lesson->image = "";
    	$lesson->video_url = $request->input('video_url');
    	$lesson->save();

    	return redirect()->route('lesson.index', $categories_id)->with('message', trans('common.lessons.create.success'));
    }

    public function edit($categories_id,  LessonModel $lesson)
    {
        return view('admin.lessons.edit', compact('categories_id', 'lesson'));
    }

    public function update(Request $request, $categories_id, LessonModel $lesson)
    {	
    	$lesson->name = $request->input('name');
        $lesson->body = $request->input('body');
        $lesson->image = "";
        $lesson->video_url = $request->input('video_url');

    	$lesson->save();
    	return redirect()->route('lesson.index', $categories_id)->with('message', trans('common.lessons.create.success'));
    }

    public function show( LessonModel $lesson)
    {
    	return redirect()->route('admin.lessons.edit', compact('categories_id','lesson'));
    }

    public function destroy(Request $request, $categories_id, LessonModel $lesson)
    {

        $lesson->delete();

        return redirect()->route('admin.lessons.index', $categories_id, $lesson_id)->with('message', trans('common.lessons.destroy.success'));
    }
}
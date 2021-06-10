<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tests\Test;
use App\Models\Tests\Question;
use App\Models\Tests\Answer;
use DB;
use Illuminate\Support\Str;

class Answers extends Controller
{
    public function index($categories_id, $lesson_id, Question $question)
    {
    	$answers = Answer::where('question_id', $question->id)->paginate(20);
    	return view('admin.answers.index', compact('question', 'lesson_id', 'categories_id',  'answers'));
    }

    public function create(Request $request, $categories_id, $lesson_id, Question $question, Answer $answer)
    {
    	return view('admin.answers.create', compact( 'categories_id', 'lesson_id',  'question', 'answer'));
    }

    public function store(Request $request, $categories_id, $lesson_id, Question $question,  Answer $answer)
    {
    	$answer = new Answer;
        $answer->question_id = $request->input('question_id');
    	$answer->name = $request->input('name');
    	$answer->correct = $request->input('correct');
    	$answer->save();

    	return redirect()->route('answers.index', [$lesson_id,$lesson_id, $question, $answer])->with('message', trans('uw antwoord is opgeslagen'));
    }

    public function edit(Request $request, $categories_id, $lesson_id, Question $question, Answer $answer)
    {
    	return view('admin.answers.edit', compact( 'categories_id', 'lesson_id',  'question', 'answer'));
    }

    public function update(Request $request, $categories_id, $lesson_id, Question $question, Answer $answer)
    {	
    	$answer->name = $request->input('name');
        $answer->correct = $request->input('correct');
    	$answer->save();

    	return redirect()->route('answers.index', [$categories_id, $lesson_id, $question])->with('message', trans('common.answers.create.success'));
    }

    public function destroy(Request $request, $categories_id, $lesson_id, Question $question, Answer $answer)
    {
        $answer->delete();
    	return redirect()->route('admin.answers.index')->with('message', trans('common.category.destroy.success'));
    }
public function show(){
    return view('admin.answers.edit');
}
    public function import()
    {   

        // foreach (Test::where('domains_id', 2)->get() as $test) {
        //     Test::where('id', $test->id)->update(['uuid'=> (string) Str::uuid()]);
        // }exit;

        ini_set('max_input_time', 0);
        ini_set('max_execution_time', 0);
        set_time_limit(0);

        $arr            = [];
        $file_handle    = fopen(storage_path('app/importable.csv'), 'r');
        $row            = 0;
        while (($data = fgetcsv($file_handle, 0, ",")) !== FALSE) {
            if(isset($data[1])){
            $test       = Str::slug($data[1], '-');
            $question   = Str::slug($data[0], '-');
            $answer1     = Str::slug($data[4], '-');
            $answer2     = Str::slug($data[6], '-');
            $answer3     = Str::slug($data[8], '-');
            $answer4     = Str::slug($data[10], '-');

            if(!isset($arr[$test])){
                $arr[$test] = [
                    'name'              => $data[1],
                    'questions'         => []
                ];
            }

            $answerdesc = "";
            $expl_ad    = explode("http", $data[3]);
            $answerdesc = $expl_ad[0];
            if(isset($expl_ad[1])){
                $answerdesc.= "<a href='http".$expl_ad[1]."' target='_blank'><i class='fas fa-external-link-alt'></i></a>";
            }
            
            if(!isset($arr[$test]['questions'][$question])){
                $arr[$test]['questions'][$question] = [
                    'name'                  => $data[0],
                    'answer_description'    => $answerdesc,
                    'answers'               => []
                ];
            }

            $arr[$test]['questions'][$question]['answers'][$answer1] = [
                'name'      => $data[4],
                'correct'   => 1
            ];

            $arr[$test]['questions'][$question]['answers'][$answer2] = [
                'name'      => $data[6],
                'correct'   => 0
            ];

            $arr[$test]['questions'][$question]['answers'][$answer3] = [
                'name'      => $data[8],
                'correct'   => 0
            ];

            $arr[$test]['questions'][$question]['answers'][$answer4] = [
                'name'      => $data[10],
                'correct'   => 0
            ];
        }
        }

        foreach ($arr as $test_slug => $test_arr) { 
            // $test = new Test;
            // $test->domain()->associate(1);
            // $test->name = $test_arr['name'];
            // $test->description = '';
            // $test->price = 299;
            // $test->active = 1;
            // $test->uuid = (string) Str::uuid();
            // $test->sort_order = 0;
            // $test->save();
            foreach ($test_arr['questions'] as $question_slug => $question_arr) {
                $question = new Question;
                $question->test()->associate($test_arr['name']);
                $question->name = $question_arr['name'];
                $question->description = '';
                $question->answer_description = $question_arr['answer_description'];
                $question->sort_order = 0;
                $question->save();

                foreach ($question_arr['answers'] as $answer_slug => $answer_arr) {
                    $answer = new Answer;
                    $answer->question()->associate($question->id);
                    $answer->name = $answer_arr['name'];
                    $answer->correct = $answer_arr['correct'];
                    $answer->save();
                }
            }
        }

        exit;

        $arr            = [];
        $file_handle    = fopen(storage_path('app/importable.csv'), 'r');
        $row            = 0;
        while (($data = fgetcsv($file_handle, 0, ";")) !== FALSE) {
            if($row > 0){
                $test       = Str::slug($data[1], '-');
                $question   = Str::slug($data[2], '-');
                $answer     = Str::slug($data[3], '-');

                if(!isset($arr[$test])){
                    $arr[$test] = [
                        'name'              => $data[1],
                        'questions'         => []
                    ];
                }

                if(!isset($arr[$test]['questions'][$question])){
                    $arr[$test]['questions'][$question] = [
                        'name'                  => $data[2],
                        'answer_description'    => '',
                        'answers'               => []
                    ];
                }

                // if $data[5] is set it is the right answer
                $arr[$test]['questions'][$question]['answers'][$answer] = [
                    'name'      => $data[3],
                    'correct'   => 0
                ];

                if($data[5] != ""){
                    $arr[$test]['questions'][$question]['answers'][$answer]['correct'] = 1;
                    $arr[$test]['questions'][$question]['answer_description'] = $data[5] . "<a href='".$data[6]."' target='_blank'><i class='fas fa-external-link-alt'></i></a>";
                }

            }
            $row++;
        }
        fclose($file_handle);

        foreach ($arr as $test_slug => $test_arr) { 
            $test = null;
            $test = Test::where('name', $test_arr['name'])->where('domains_id', 1)->first();
            if($test === null){
                $test = Test::where('name', $test_arr['name'].' України')->where('domains_id', 1)->first();
            }else{
                echo test_arr['name']."<br>";
            }

            // $test = new Test;
            // $test->domain()->associate(1);
            // $test->name = $test_arr['name'];
            // $test->description = '';
            // $test->price = 299;
            // $test->active = 1;
            // $test->save();

            if($test !== null){
                foreach ($test_arr['questions'] as $question_slug => $question_arr) {
                    $question = new Question;
                    $question->test()->associate($test->id);
                    $question->name = $question_arr['name'];
                    $question->description = '';
                    $question->answer_description = $question_arr['answer_description'];
                    $question->save();

                    foreach ($question_arr['answers'] as $answer_slug => $answer_arr) {
                        $answer = new Answer;
                        $answer->question()->associate($question->id);
                        $answer->name = $answer_arr['name'];
                        $answer->correct = $answer_arr['correct'];
                        $answer->save();
                    }
                }
            }
        }
    }
}
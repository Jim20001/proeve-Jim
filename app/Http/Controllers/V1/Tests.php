<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tests\Test;
use App\Models\Categories\Category;
use App\Models\Tests\Question;
use App\Models\Tests\Answer;
use App\Models\Domains\Domain;
use App\Models\Orders\Order;
use App\Models\Orders\Test as OrderedTest;
use App\Models\Orders\Answer as OrderedAnswer;
use Illuminate\Support\Str;
use LiqPay;
use App\Services\Email;

class Tests extends Controller
{
    public function single($uuid)
    {
        $test = Test::where('uuid', $uuid)->first();

    	$seo = [
            'title' => $test->name." | тест | ".app('config')->get('options')['name'],
            'description' => ''
        ];

        app('config')->set('seo', $seo);

        $type = "test";

        return view('app.tests.show', compact('type', 'test'));
    }

    public function singleBuy($uuid)
    {
        $test = Test::where('uuid', $uuid)->first();
        // $order = Order::where('users_id', auth('user')->user()->id)->where('test', $test->id)->first();

        // if($order === null){
            $order = $this->doOrder($test->id, $test->price, 'open');
            echo LiqPay::pay(
                intval($test->price), 
                'UAH', 
                'Ваше замовлення на '.app('config')->get('options')['name'],
                app('config')->get('options')['invoiceprefix'].'-'.str_pad($order->id, 8, '0', STR_PAD_LEFT),
                request()->root().'/app/do-test/'.$uuid.'/return?order='.$order->uuid,
                request()->root().'/app/liqpay?order='.$order->uuid,
                $order->domains_id
            );exit;
        // }else{
        //     return redirect('/app/my-tests')->with('message', 'Ви вже купили цей тест!');
        // }
    }

    public function singleReturn($uuid)
    {
        $test   = Test::where('uuid', $uuid)->first();
        $order  = Order::where('uuid', request()->input('order'))->first();
        $liqpay = LiqPay::status(app('config')->get('options')['invoiceprefix'].'-'.str_pad($order->id, 8, '0', STR_PAD_LEFT), $order->domains_id);
        $liqpay = json_decode($liqpay, true);

        if ($liqpay['status'] == "success") {
            Order::where('uuid', request()->input('order'))->update([
                'payment_status' => 'paid'
            ]);

            $real_i = 1;
            $real_i2 = 50;

            for ($i=0;$i<intval($test->questions->count() / 50)+1;$i++) {
                if ($real_i2 > $real_i) {

                    $otest = new OrderedTest;
                    $otest->uuid = (string) Str::uuid();
                    $otest->test = $test->id;
                    $otest->order()->associate($order->id);
                    $otest->random = 0;
                    $otest->question_range = $real_i."-".$real_i2;
                    $otest->started_on = null;
                    $otest->time_limit = 0;
                    $otest->status = "open";
                    $otest->save();

                    $real_i = $real_i+50;
                    $real_i2 = $real_i2+50;
                    if($real_i2 > $test->questions->count()){
                        $real_i2 = $test->questions->count();
                    }
                }
            }

            $otest = new OrderedTest;
            $otest->uuid = (string) Str::uuid();
            $otest->test = $test->id;
            $otest->order()->associate($order->id);
            $otest->random = 1;
            $otest->question_range = "";
            $otest->started_on = null;
            $otest->time_limit = 50;
            $otest->status = "open";
            $otest->save();

            (new Email)->thanksForBuy(auth('user')->user(), $order);

            return redirect('/app/my-tests')->with('message', 'Оплата успішна!');

        } elseif ($liqpay['status'] == "failure") {
            Order::where('uuid', request()->input('order'))->delete();
        } else {
            Order::where('uuid', request()->input('order'))->delete();
            return redirect('/app/my-tests')->with('message', 'Оплата успішна!');
        }

        return redirect('/app/do-test/'.$uuid)->with('message', 'Оплата не вдалася!');
    }

    public function general()
    {
        $seo = [
            'title' => "Загальний тест | ".app('config')->get('options')['name'],
            'description' => ''
        ];

        app('config')->set('seo', $seo);

        $type = "general";
        $test = null;

        return view('app.tests.show', compact('type', 'test'));
    }

    public function generalBuy()
    {
        $order = Order::where('users_id', auth('user')->user()->id)->where('test', 'general')->first();

        if($order === null){
            $order = $this->doOrder('general', intval(app('config')->get('options')['general_cost']), 'open');
            echo LiqPay::pay(
                intval(app('config')->get('options')['general_cost']), 
                'UAH', 
                'Ваше замовлення на '.app('config')->get('options')['name'],
                app('config')->get('options')['invoiceprefix'].'-'.str_pad($order->id, 8, '0', STR_PAD_LEFT),
                request()->root().'/app/do-general/return?order='.$order->uuid,
                request()->root().'/app/liqpay?order='.$order->uuid,
                $order->domains_id
            );exit;
        }else{
            return redirect('/app/mytests')->with('message', 'Ви вже купили цей тест!');
        }
    }

    public function generalReturn()
    {
        $order  = Order::where('uuid', request()->input('order'))->first();
        $liqpay = LiqPay::status(app('config')->get('options')['invoiceprefix'].'-'.str_pad($order->id, 8, '0', STR_PAD_LEFT), $order->domains_id);
        $liqpay = json_decode($liqpay, true);

        if ($liqpay['status'] == "success") {
            Order::where('uuid', request()->input('order'))->update([
                'payment_status' => 'paid'
            ]);

            foreach (Test::where('domains_id', $order->domains_id)->get() as $test) {
                $real_i = 1;
                $real_i2 = 50;

                for ($i=0;$i<intval($test->questions->count() / 50)+1;$i++) {
                    if ($real_i2 > $real_i) {

                        $otest = new OrderedTest;
                        $otest->uuid = (string) Str::uuid();
                        $otest->test = $test->id;
                        $otest->order()->associate($order->id);
                        $otest->random = 0;
                        $otest->question_range = $real_i."-".$real_i2;
                        $otest->started_on = null;
                        $otest->time_limit = 0;
                        $otest->status = "open";
                        $otest->save();

                        $real_i = $real_i+50;
                        $real_i2 = $real_i2+50;
                        if($real_i2 > $test->questions->count()){
                            $real_i2 = $test->questions->count();
                        }
                    }
                }

                $otest = new OrderedTest;
                $otest->uuid = (string) Str::uuid();
                $otest->test = $test->id;
                $otest->order()->associate($order->id);
                $otest->random = 1;
                $otest->question_range = "";
                $otest->started_on = null;
                $otest->time_limit = 50;
                $otest->status = "open";
                $otest->save();
            }

            $otest = new OrderedTest;
            $otest->uuid = (string) Str::uuid();
            $otest->test = 'general';
            $otest->order()->associate($order->id);
            $otest->random = 1;
            $otest->question_range = "";
            $otest->started_on = null;
            $otest->time_limit = 100;
            $otest->status = "open";
            $otest->save();

            (new Email)->thanksForBuy(auth('user')->user(), $order);

            return redirect('/app/my-tests')->with('message', 'Оплата успішна!');

        } elseif ($liqpay['status'] == "failure") {
            Order::where('uuid', request()->input('order'))->delete();
        } else {
            Order::where('uuid', request()->input('order'))->delete();
            // return redirect('/app/my-tests')->with('message', 'Оплата успішна!');
        }

        return redirect('/app/do-general')->with('message', 'Оплата не вдалася!');
    }
    
    public function demo()
    {
    	$seo = [
            'title' => "Демо | ".app('config')->get('options')['name'],
            'description' => ''
        ];

        app('config')->set('seo', $seo);

        $type = "demo";
        $test = null;

        return view('app.tests.show', compact('type', 'test'));
    }

    public function demoReturn()
    {
    	// $order = Order::where('users_id', auth('user')->user()->id)->where('test','demo')->first();
    	// if($order === null){
    		$order = $this->doOrder('demo', 0, 'paid');

    		$test = new OrderedTest;
    		$test->uuid = (string) Str::uuid();
    		$test->test = "demo";
    		$test->order()->associate($order->id);
    		$test->random = 0;
    		$test->question_range = "";
    		$test->started_on = null;
    		$test->time_limit = 0;
    		$test->status = "open";
    		$test->save();

            (new Email)->thanksForBuy(auth('user')->user(), $order);

    		return redirect('/app/take-test/'.$test->uuid);
    	// }else{
    	// 	return redirect('/app/my-tests')->with('message', 'Ви вже купили цей тест!');
    	// }
    }

    public function testDemoReturn($uuid)
    {
            $xtest   = Test::where('uuid', $uuid)->first();
            // $order = Order::where('users_id', auth('user')->user()->id)->where('test','test_demo')->first();
            // if($order === null){
    		$order = $this->doOrder('test_demo', 0, 'paid');

    		$test = new OrderedTest;
    		$test->uuid = (string) Str::uuid();
    		$test->test = "test_demo";
    		$test->order()->associate($order->id);
    		$test->random = 0;
    		$test->question_range = $xtest->id;
    		$test->started_on = null;
    		$test->time_limit = 0;
    		$test->status = "open";
    		$test->save();

            // (new Email)->thanksForBuy(auth('user')->user(), $order);

    		return redirect('/app/take-test/'.$test->uuid);
    	// }else{
    	// 	return redirect('/app/my-tests')->with('message', 'Ви вже купили цей тест!');
    	// }
    }

    private function doOrder(
    	$test 		= "demo",
    	$price 		= 0,
    	$status 	= "open"
    ){
    	$order = new Order;
    	$order->user()->associate(auth('user')->user()->id);
        $order->domain()->associate(app('config')->get('domain')->id);
    	$order->test = $test;
    	$order->price = $price;
        $order->uuid = (string) Str::uuid();
    	$order->payment_status = $status;
    	$order->save();

    	return $order;
    }

    public function take($uuid)
    {
    	$ordered_test = OrderedTest::where('uuid', $uuid)->first();
    	$base_test = Test::where('id', $ordered_test->test)->first();

    	$seo = [
            'title' => "Тест | ".app('config')->get('options')['name'],
            'description' => ''
        ];

        app('config')->set('seo', $seo);

        $total_questions = 0;

        if($ordered_test->test == "demo"){
        	$total_questions = 10;
        }elseif($ordered_test->test == "test_demo"){
        	$total_questions = 3;
        }elseif($ordered_test->test == "category_demo"){
        	$total_questions = 5;
        }elseif($ordered_test->test == "general"){
        	$total_questions = 100;
        }else{
        	$explode = explode('-', $ordered_test->question_range);
        	$total_questions = isset($explode[1]) ? intval($explode[1])-intval($explode[0])+1 : 50;
        }

        // if(request()->server('REMOTE_ADDR') == "185.214.112.106"){
        //     return view('app.tests.take2', compact('ordered_test', 'base_test', 'uuid', 'total_questions'));
        // }

        return view('app.tests.take', compact('ordered_test', 'base_test', 'uuid', 'total_questions'));
    }

    public function getCurrentQuestion(Request $request)
    {
    	$current_test   = OrderedTest::where('uuid', $request->input('UUID'))->first();
        OrderedTest::where('uuid', $request->input('UUID'))->update(['status' => 'in_progress']);
    	$last_answer    = OrderedAnswer::where('ordered_tests_id', $current_test->id)->limit(1)->orderBy('id', 'desc')->first();
        $order          = Order::where('id', $current_test->orders_id)->first();

    	$return_array = ['finished' => 'n'];

        if($current_test->test == "demo"){

            $questions = [];

            if($order->domains_id == 1){
            	$questions = ['42587' => 42587, '43181' => 43181, '39259' => 39259, '39347' => 39347, '41104' => 41104, '43585' => 43585, '43898' => 43898, '45148' => 45148, '45044' => 45044, '44898' => 44898];
            }elseif($order->domains_id == 2){
            	$questions = ['7338' => 7338, '7380' => 7380, '7438' => 7438, '7595' => 7595, '7518' => 7518, '7511' => 7511, '7617' => 7617, '7649' => 7649, '7664' => 7664, '7678' => 7678];
            }
            

            $current_count = OrderedAnswer::where('ordered_tests_id', $current_test->id)->count();

            if($current_count > 9){
                $return_array['finished'] = 'y';
                OrderedTest::where('uuid', $request->input('UUID'))->update(['status' => 'done']);
                return json_encode($return_array);
            }

            if(!isset($last_answer->questions_id)){
                $current_demo = current($questions);
            }else{
                $found = false;
                $last_demo = current($questions);
                while(!$found){
                    if($last_answer->questions_id == $last_demo){
                        $found = true;
                    }
                    $last_demo = next($questions);
                }
                
                $current_demo = $last_demo;
            }

            $question = Question::where('id', $current_demo)->first();
            $return_array['question_name']  = $question->name;
            $return_array['question_id']    = $question->id;
            $return_array['question_nr']    = $current_count+1;

            $options_array = [];
            
            foreach ($question->answers as $option) {
                $options_array[] = [
                    'id' => $option->id,
                    'name' => $option->name,
                    'correct' => $option->correct,
                ];
            }

            shuffle($options_array);

            $return_array['question_options'] = $options_array;

        }elseif($current_test->test == "test_demo"){
            
            $test = Test::where('id', $current_test->question_range)->first();
            $questions = explode(',', $test->demo_questions);
            
            $current_count = OrderedAnswer::where('ordered_tests_id', $current_test->id)->count();

            if($current_count > 2){
                $return_array['finished'] = 'y';
                OrderedTest::where('uuid', $request->input('UUID'))->update(['status' => 'done']);
                return json_encode($return_array);
            }

            if(!isset($last_answer->questions_id)){
                $current_demo = current($questions);
            }else{
                $found = false;
                $last_demo = current($questions);
                while(!$found){
                    if($last_answer->questions_id == $last_demo){
                        $found = true;
                    }
                    $last_demo = next($questions);
                }
                
                $current_demo = $last_demo;
            }

            $question = Question::where('id', $current_demo)->first();
            $return_array['question_name']  = $question->name;
            $return_array['question_id']    = $question->id;
            $return_array['question_nr']    = $current_count+1;

            $options_array = [];
            
            foreach ($question->answers as $option) {
                $options_array[] = [
                    'id' => $option->id,
                    'name' => $option->name,
                    'correct' => $option->correct,
                ];
            }

            shuffle($options_array);

            $return_array['question_options'] = $options_array;

        }elseif($current_test->test == "category_demo"){
            
            $category = Category::where('id', $current_test->question_range)->first();
            $questions = explode(',', $category->demo_tests);
            
            $current_count = OrderedAnswer::where('ordered_tests_id', $current_test->id)->count();

            if($current_count > 4){
                $return_array['finished'] = 'y';
                OrderedTest::where('uuid', $request->input('UUID'))->update(['status' => 'done']);
                return json_encode($return_array);
            }

            if(!isset($last_answer->questions_id)){
                $current_demo = current($questions);
            }else{
                $found = false;
                $last_demo = current($questions);
                while(!$found){
                    if($last_answer->questions_id == $last_demo){
                        $found = true;
                    }
                    $last_demo = next($questions);
                }
                
                $current_demo = $last_demo;
            }

            $question = Question::where('id', $current_demo)->first();
            $return_array['question_name']  = $question->name;
            $return_array['question_id']    = $question->id;
            $return_array['question_nr']    = $current_count+1;

            $options_array = [];
            
            foreach ($question->answers as $option) {
                $options_array[] = [
                    'id' => $option->id,
                    'name' => $option->name,
                    'correct' => $option->correct,
                ];
            }

            shuffle($options_array);

            $return_array['question_options'] = $options_array;

        }elseif($current_test->test == "general"){
            $current_count = OrderedAnswer::where('ordered_tests_id', $current_test->id)->count();

            if($current_count > 99){
                $return_array['finished'] = 'y';
                OrderedTest::where('uuid', $request->input('UUID'))->update(['status' => 'done']);
                return json_encode($return_array);
            }

            $arr = [];
            foreach (OrderedAnswer::where('ordered_tests_id', $current_test->id)->get() as $a) {
                array_push($arr, $a->questions_id);
            }

            $arr2 = [];

            foreach (Test::where('domains_id', $order->domains_id)->get() as $t) {
                array_push($arr2, $t->id);
            }

            $question = Question::whereIn('tests_id', $arr2)->whereNotIn('id', $arr)->inRandomOrder()->first();
            $return_array['question_name']  = $question->name;
            $return_array['question_id']    = $question->id;
            $return_array['question_nr']    = $current_count+1;

            $options_array = [];
            
            foreach ($question->answers as $option) {
                $options_array[] = [
                    'id' => $option->id,
                    'name' => $option->name,
                ];
            }

            shuffle($options_array);

            $return_array['question_options'] = $options_array;

        }else{

            $current_count = OrderedAnswer::where('ordered_tests_id', $current_test->id)->count();

            $explode = explode("-", $current_test->question_range);
            $limit = 49;

            if($current_test->random == 0){
                $limit = $explode[1]-$explode[0];
            }
        
            if($current_count > $limit){
                $return_array['finished'] = 'y';
                OrderedTest::where('uuid', $request->input('UUID'))->update(['status' => 'done']);
                return json_encode($return_array);
            }

            if($current_test->random == 0){
                $offset = $explode[0]-1;
                $question = Question::where('tests_id', $current_test->test)->offset($current_count+$offset)->first();
                $return_array['question_name']  = $question->name;
                $return_array['question_id']    = $question->id;
                $return_array['question_nr']    = $current_count+1;

                $options_array = [];
                
                foreach ($question->answers as $option) {
                    $options_array[] = [
                        'id' => $option->id,
                        'name' => $option->name,
                        'correct' => $option->correct,
                    ];
                }

                shuffle($options_array);

                $return_array['question_options'] = $options_array;
            }else{
                $arr = [];
                foreach (OrderedAnswer::where('ordered_tests_id', $current_test->id)->get() as $a) {
                    array_push($arr, $a->questions_id);
                }

                info($arr);

                $question = Question::where('tests_id', $current_test->test)->whereNotIn('id', $arr)->inRandomOrder()->first();
                $return_array['question_name']  = $question->name;
                $return_array['question_id']    = $question->id;
                $return_array['question_nr']    = $current_count+1;

                $options_array = [];
                
                foreach ($question->answers as $option) {
                    $options_array[] = [
                        'id' => $option->id,
                        'name' => $option->name,
                        'correct' => $option->correct,
                    ];
                }

                shuffle($options_array);

                $return_array['question_options'] = $options_array;
            }
        }

    	return json_encode($return_array);
    }

    public function getNextQuestion(Request $request)
    {

        $current_test   = OrderedTest::where('uuid', $request->input('UUID'))->first();

        if(OrderedAnswer::where('ordered_tests_id', $current_test->id)->where('questions_id', $request->input('current_question'))->first() != null){
            OrderedAnswer::where('ordered_tests_id', $current_test->id)->where('questions_id', $request->input('current_question'))->update([
                'answers_id' => $request->input('current_answer')
            ]);
        }else{
            $answer = new OrderedAnswer;
            $answer->test()->associate($current_test->id);
            $answer->question()->associate($request->input('current_question'));
            $answer->answer()->associate($request->input('current_answer'));
            $answer->save();
        }
    }

    public function getChangedQuestion(Request $request) {
        $current_test   = OrderedTest::where('uuid', $request->input('UUID'))->first();
        $prevAnswers    = OrderedAnswer::where('ordered_tests_id', $current_test->id)->orderBy('id')->get(['questions_id']);
        $answersArr     = [];
        $question       = null;

        foreach ($prevAnswers as $prevAnswer) {
            array_push($answersArr, $prevAnswer->questions_id);
        }

        foreach ($answersArr as $key => $value) {
            if(($key+1) == $request->input('question_number')){
                $question = Question::where('id', $value)->first();
                $current_count = $request->input('question_number');
            }
        }

        $question_details['question_name']  = $question->name;
        $question_details['question_id']  = $question->id;
        $question_details['question_nr']    = $current_count;

        $options_array = [];

        foreach ($question->answers as $option) {
            $options_array[] = [
                'id' => $option->id,
                'name' => $option->name,
                'correct' => $option->correct,
            ];
        }

        shuffle($options_array);

        $question_details['question_options'] = $options_array;
        $question_details['aids'] = OrderedAnswer::where('ordered_tests_id', $current_test->id)->where('questions_id', $question->id)->first(['answers_id'])->answers_id;

        return json_encode($question_details);

    }

    public function getLastQuestion(Request $request) {
        $current_test   = OrderedTest::where('uuid', $request->input('UUID'))->first();
        $prevAnswers    = OrderedAnswer::where('ordered_tests_id', $current_test->id)->orderBy('id', 'desc')->get(['questions_id']);
        $answersArr     = [];
        $answersStr     = "";
        $question       = null;

        foreach ($prevAnswers as $prevAnswer) {
            array_push($answersArr, $prevAnswer->questions_id);
            $answersStr.= $prevAnswer->questions_id.",";
        }
        rtrim($answersStr,",");

        if(in_array($request->input('current_question'), $answersArr)){
            $expl = explode($request->input('current_question'), $answersStr);
            $dString = rtrim($expl[1], ",");
            $dArr = explode(",", $dString);
            $question = Question::where('id', $dArr[1])->first();
            $current_count = count($dArr)-1;  
        }else{
            $question = Question::where('id', $answersArr[0])->first();
            $current_count = OrderedAnswer::where('ordered_tests_id', $current_test->id)->count();  
        }          

        $question_details['question_name']  = $question->name;
        $question_details['question_id']  = $question->id;
        $question_details['question_nr']    = $current_count;

        $options_array = [];

        foreach ($question->answers as $option) {
            $options_array[] = [
                'id' => $option->id,
                'name' => $option->name,
                'correct' => $option->correct,
            ];
        }

        shuffle($options_array);

        $question_details['question_options'] = $options_array;
        $question_details['aids'] = OrderedAnswer::where('ordered_tests_id', $current_test->id)->where('questions_id', $question->id)->first(['answers_id'])->answers_id;

        // info($question_details['aids']);

        return json_encode($question_details);
    }

    public function qa()
    {
        $arr = [];

        foreach (Test::where('domains_id', 1)->get() as $test) {
            $questions = [];
            $xquestions = Question::where('tests_id', $test->id)->get();
            if($xquestions->count() > 0){
                foreach ($xquestions as $question) {
                    $answer = Answer::where('questions_id', $question->id)->where('correct', 1)->first();
                    $questions[] = [
                        'name' => $question->name,
                        'answer' => ($answer !== null ? $answer['name'] : ""),
                        'desc' => $question->answer_description
                    ];
                }   

                $arr [] = [
                    'name' => $test->marketing_name,
                    'questions' => $questions
                ];
            }
        }

        return view('app.tests.qa', compact('arr'));
    }

    public function results($uuid)
    {
        $current_test   = OrderedTest::where('uuid', $uuid)->first();
        $base_test = Test::where('id', $current_test->test)->first();

        $seo = [
            'title' => "Результати тест | ".app('config')->get('options')['name'],
            'description' => ''
        ];

        app('config')->set('seo', $seo);

        $type = $current_test->test;
        if(!in_array($type, ['demo', 'general', 'test_demo', 'category_demo'])){
            $type = "test";
        }

        $answers = [];

        foreach ($current_test->answers as $answer) {

            $q_answers = [];

            foreach ($answer->question->answers as $ans) {
                $q_answers[] = [
                    'name' => $ans->name,
                    'was_answer' => ($ans->id == $answer->answers_id ? 1 : 0),
                    'is_correct' => $ans->correct
                ];
            }

            $answers[] = [
                'question' => $answer->question->name,
                'answers'  => $q_answers,
                'explain' => $answer->question->answer_description
            ];
        }

        $total_questions = 0;

        if($current_test->test == "demo"){
            $total_questions = 10;
        }elseif($current_test->test == "test_demo"){
            $total_questions = 3;
        }elseif($current_test->test == "category_demo"){
            $total_questions = 5;
        }elseif($current_test->test == "general"){
            $total_questions = 100;
        }else{
            $explode = explode('-', $current_test->question_range);
            $total_questions = isset($explode[1]) ? intval($explode[1])-intval($explode[0])+1 : 50;
        }

        return view('app.tests.results', compact('current_test', 'base_test', 'type', 'answers', 'total_questions', 'uuid'));
    }

    public function retake($uuid)
    {
        $current_test = OrderedTest::where('uuid', $uuid)->first();
        OrderedAnswer::where('ordered_tests_id', $current_test->id)->delete();
        OrderedTest::where('uuid', $uuid)->update(['status' => 'in_progress']);

        return redirect('/app/take-test/'.$uuid, 302);
    }

    public function endTest($uuid)
    {
        OrderedTest::where('uuid', $uuid)->update(['status' => 'done']);
        return redirect('/app/test/'.$uuid.'/results', 302);
    }

    // MOBILE
    public function mobileTests() {
        return Test::all();
    }

    public function mobileExams() {
        return Test::all();
    }

    public function mobileMistakenTests() {
        return Test::all();
    }
}

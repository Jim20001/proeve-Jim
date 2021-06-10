<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Categories\Category;
use App\Models\Orders\Order;
use App\Models\Tests\Test;
use App\Models\Orders\Test as OrderedTest;
use Illuminate\Support\Str;
use App\Services\Email;
use LiqPay;

class Categories extends Controller
{
    public function tests($uuid)
    {
        $category = Category::where('uuid', $uuid)->first();

    	$seo = [
            'title' => $category->name." | тест | ".app('config')->get('options')['name'],
            'description' => ''
        ];

        app('config')->set('seo', $seo);

        $type = "category";

        return view('app/categories.show', compact('type', 'category'));
    }

    public function categorySingleBuy($uuid)
    {
        $category = Category::where('uuid', $uuid)->first();
         $order = Order::where('users_id', auth('user')->user()->id)->where('category', $category->id)->first();

         if($order === null){
            $order = $this->doOrder($category->id, $category->price, 'open');
            echo LiqPay::pay(
                intval($category->price), 
                'UAH', 
                'Ваше замовлення на '.app('config')->get('options')['name'],
                app('config')->get('options')['invoiceprefix'].'-'.str_pad($order->id, 8, '0', STR_PAD_LEFT),
                request()->root().'category/'.$uuid.'/return?order='.$order->uuid,
                request()->root().'/app/liqpay?order='.$order->uuid,
                $order->domains_id
            );exit;
        }else{
            return redirect('/app/categories/'.$uuid)->with('message', 'Ви вже купили цей тест!');
         }
    }

    public function categorySingleReturn($uuid)
    {
        $order  = Order::where('uuid', request()->input('order'))->first();

        if($order === null)
        {
            return redirect('/categories');
        }

        $liqpay = LiqPay::status(app('config')->get('options')['invoiceprefix'].'-'.str_pad($order->id, 8, '0', STR_PAD_LEFT), $order->domains_id);
        $liqpay = json_decode($liqpay, true);
        
        if ($liqpay['status'] == "success") {
            Order::where('uuid', request()->input('order'))->update([
                'payment_status' => 'paid'
            ]);

            foreach (Test::where('categories_id', $order->category)->get() as $test) {
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

            (new Email)->thanksForBuy(auth('user')->user(), $order);

            return redirect('my-tests')->with('message', 'Оплата успішна!');

        } elseif ($liqpay['status'] == "failure") {
            Order::where('uuid', request()->input('order'))->delete();
        } else {
            Order::where('uuid', request()->input('order'))->delete();
             return redirect('/app/my-tests')->with('message', 'Оплата успішна!');
        }

        return redirect('categories'.$uuid)->with('message', 'Оплата не вдалася!');
        return $uuid;
    }

    public function testCategoryReturn($uuid)
    {
        $xcategory   = Category::where('uuid', $uuid)->first();
     
        $order = $this->doOrder('category_demo', 0, 'paid');

        $category = new OrderedTest;
        $category->uuid = (string) Str::uuid();
        $category->test = "category_demo";
        $category->order()->associate($order->id);
        $category->random = 0;
        $category->question_range = $xcategory->id;
        $category->started_on = null;
        $category->time_limit = 0;
        $category->status = "open";
        $category->save();

        (new Email)->thanksForBuy(auth('user')->user(), $order);

        return redirect('take-test/'.$category->uuid);
    }

    private function doOrder(
    	$category 		= "demo",
    	$price 		= 0,
    	$status 	= "open"
    ){
    	$order = new Order;
    	$order->user()->associate(auth('user')->user()->id);
        $order->domain()->associate(app('config')->get('domain')->id);
    	$order->category = $category;
    	$order->test = 'category';
    	$order->price = $price;
        $order->uuid = (string) Str::uuid();
    	$order->payment_status = $status;
        $order->save();

    	return $order;
    }
}
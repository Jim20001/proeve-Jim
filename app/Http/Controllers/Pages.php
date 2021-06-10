<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Domains\Page;
use Illuminate\Support\Str;

class Pages extends Controller
{
	public function index()
	{
		return view('site.index');
	}

	public function content($slug)
	{
		$page = Page::where('slug', $slug)->first();

		$title = "";
		$description = "";

		if ($page != "") {
			$title = $page->seo_title;
		} else {
			$title = $page. " | " . app('config')->get('options')['name'];
		}

		if ($page != "") {
			$description = $page->seo_description;
		} else {
			$description = Str::words(strip_tags($page), 25) . "...";
		}

		$seo = [
			'title' => $title,
			'description' => $description,
		];

		if ($page != "") {
			$seo['rich_snippet'] = $page->seo_richsnippet;
		}

		app('config')->set('seo', $seo);

		return view('site.content', compact('page'));
	}
}
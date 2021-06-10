@extends('admin.layouts.app')

@section('title', trans('vak aanmaken'))
@section('button')
	<a href="{{route('categories.index')}}" class="btn btn-secondary"><i class="fa fa-chevron-left"></i> {{trans('common.btn.back')}}</a>
@endsection

@section('content')
	<form action="{{ route('categories.store') }}" method="post">
    	{{ csrf_field() }}

    	<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('name')}}</label>
			<input type="text" name="name" class="form-control" id="exampleFormControlInput1" placeholder="{{trans('name')}}...">
		</div>

    	{{-- <div class="form-group">
			<label for="exampleFormControlInput1">{{trans('slug')}}</label>
			<input type="text" name="slug" class="form-control" id="exampleFormControlInput1" placeholder="{{trans('slug')}}...">
		</div> --}}

    	<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('beschrijvingen')}}</label>
			<textarea name="body" class="summernote" id="exampleFormControlInput1"></textarea>
		</div>

    	<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('prijs')}}</label>
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text">&#x20B4;</span>
				</div>
				<input type="text" class="form-control" name="prijs" placeholder="{{trans('prijs')}}...">
			</div>
		</div>

    	<button type="submit" class="btn btn-success">
            <i class="fa fa-plus"></i>
            {{ trans('opslaan') }}
        </button>
    </form>
@endsection
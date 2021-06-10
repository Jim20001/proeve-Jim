@extends('admin.layouts.app')

@section('title', trans('vakken editen'))
@section('button')
	<a href="{{route('categories.index')}}" class="btn btn-secondary"><i class="fa fa-chevron-left"></i> {{trans('terug')}}</a>
@endsection

@section('content')
	<form action="{{ route('categories.update', $category) }}" method="post">
    	{{ csrf_field() }}
    	@method('put')

    	

    	<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('name')}}</label>
			<input type="text" name="name" class="form-control" id="exampleFormControlInput1" placeholder="{{trans('name')}}..." value="{{$category->name}}">
		</div>

    	

    	<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('beschrijving')}}</label>
			<textarea name="body" class="summernote" id="exampleFormControlInput1">{{$category->body}}</textarea>
		</div>


        <div class="form-group">
			<label for="exampleFormControlInput1">{{trans('prijs')}}</label>
            <input type="number" name="prijs" class="form-control" id="exampleFormControlInput1" placeholder="{{trans('prijs')}}..." value="{{$category->prijs}}">	
        </div>

    	<button type="submit" class="btn btn-success">
            <i class="fa fa-save"></i>
            {{ trans('opslaan') }}
        </button>
    </form>
@endsection
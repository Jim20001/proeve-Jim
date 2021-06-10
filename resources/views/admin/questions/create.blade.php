@extends('admin.layouts.app')

@section('title', trans('vraag maken'))
@section('button')
	<a href="{{url('questions.index')}}" class="btn btn-secondary"><i class="fa fa-chevron-left"></i> {{trans('terug')}}</a>
@endsection

@section('content')
	<form action="{{ route('questions.store', [$categories_id, $lesson_id]) }}" method="post">
    	{{ csrf_field() }}

       
		<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('naam')}}</label>
			<input type="text" name="name" class="form-control" id="exampleFormControlInput1" placeholder="{{trans('naam')}}...">
		</div>

    	<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('beschrijving')}}</label>
			<textarea name="description" class="form-control" id="exampleFormControlInput1" placeholder="{{trans('beschrijving')}}..." rows="3"></textarea>
		</div>

    	<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('antwoord beschrijving')}}</label>
			<textarea name="answer_description" class="form-control" id="exampleFormControlInput1" placeholder="{{trans('antwoord beschrijving')}}..." rows="3"></textarea>
		</div>



    	<button type="submit" class="btn btn-success">
            <i class="fa fa-plus"></i>
            {{ trans('toevoegen') }}
        </button>
    </form>
@endsection
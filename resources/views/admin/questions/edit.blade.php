@extends('admin.layouts.app')

@section('title', trans('vraag editen'))
@section('button')
	<a href="{{route('questions.index', [$categories_id, $lesson_id, $question])}}" class="btn btn-secondary"><i class="fa fa-chevron-left"></i> {{trans('terug')}}</a>
@endsection

@section('content')
	<form action="{{ route('questions.update', [$categories_id, $lesson_id, $question]) }}" method="POST">
    	{{ csrf_field() }}
    	@method('put')

		
        <div class="form-group">
			<label for="exampleFormControlInput1">{{trans('naam')}}</label>
			<input type="text" name="name" class="form-control" id="exampleFormControlInput1" placeholder="{{trans('naam')}}..." value="{{$question->name}}">
		</div>

		<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('beschrijving')}}</label>
			<textarea name="description" class="form-control" id="exampleFormControlInput1" placeholder="{{trans('beschrijving')}}..." value="{{$question->description}}" rows="3"></textarea>
		</div>

		<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('antwoord beschrijving')}}</label>
			<textarea name="answer_description" class="form-control" id="exampleFormControlInput1" placeholder="{{trans('antwoord beschrijving')}}..." value="{{$question->answer_description}}" rows="3"></textarea>
		</div>


    	<button type="submit" class="btn btn-success">
            <i class="fa fa-save"></i>
            {{ trans('opslaan') }}
        </button>
    </form>
@endsection
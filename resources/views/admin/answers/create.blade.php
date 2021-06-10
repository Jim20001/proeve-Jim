@extends('admin.layouts.app')

@section('title', trans('maak een antwoord'))
@section('button')
	<a href="{{url('answers.index', $lesson_id)}}" class="btn btn-secondary"><i class="fa fa-chevron-left"></i> {{trans('terug')}}</a>
@endsection

@section('content')
	<form action="{{  route('answers.store', [$categories_id, $lesson_id, $question])}}" method="post">
    	{{ csrf_field() }}

		<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('question_id')}}</label>
			<input type="text" name="question_id" class="form-control" id="exampleFormControlInput1" placeholder="{{trans('question_id')}}...">
		</div>

		<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('naam')}}</label>
			<input type="text" name="name" class="form-control" id="exampleFormControlInput1" placeholder="{{trans('naam')}}...">
		</div>

		<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('correct')}}</label>
			<select class="form-control" name="correct">
				<option>{{trans('maak uw keuze')}}</option>
				<option value="1">{{trans('ja')}}</option>
				<option value="0">{{trans('nee')}}</option>
			</select>
		</div>

    	<button type="submit" class="btn btn-success">
            <i class="fa fa-plus"></i>
            {{ trans('toevoegen') }}
        </button>
    </form>
@endsection
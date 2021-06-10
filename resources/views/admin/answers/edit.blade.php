@extends('admin.layouts.app')

@section('title', trans('antwoorden editen'))
@section('button')
	<a href="{{route('answers.index', [$categories_id, $lesson_id, $question, $answer])}}" class="btn btn-secondary"><i class="fa fa-chevron-left"></i> {{trans('terug')}}</a>
@endsection

@section('content')
<form action="{{ route('answers.update', [$categories_id, $lesson_id, $question, $answer]) }}" method="post">
    	{{ csrf_field() }}
    	@method('put')


    	<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('naam')}}</label>
			<input type="text" name="name" class="form-control" id="exampleFormControlInput1"   placeholder="{{trans('naam')}}..." value="{{$answer->name}}">
		</div>

		<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('correct')}}</label>
			<select class="form-control" name="correct" >
				<option>{{trans('maak uw keuze')}}</option>
				<option @if($answer->correct) selected="selected" @endif value="1">{{trans('ja')}}</option>
				<option @if(!$answer->correct) selected="selected" @endif value="0">{{trans('nee')}}</option>
			</select>
		</div>

    	<button type="submit" class="btn btn-success">
            <i class="fa fa-save"></i>
            {{ trans('opslaan') }}
        </button>
    </form>
@endsection
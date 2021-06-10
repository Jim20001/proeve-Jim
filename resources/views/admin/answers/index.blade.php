@extends('admin.layouts.app')

@section('title', trans('answers'))
@section('button')
	<a href="{{route('questions.index', [$categories_id, $lesson_id, $question])}}" class="btn btn-secondary"><i class="fa fa-chevron-left"></i> {{trans('common.btn.back')}}</a>
	<a href="{{route('answers.create',[$categories_id, $lesson_id, $question])}}" class="btn btn-success"><i class="fa fa-plus"></i> {{trans('common.btn.add')}}</a>
@endsection

@section('content')
	<table class="table table-striped">
		<thead class="thead-dark">
			<tr>
				<th>#</th>
				<th>{{trans('name')}}</th>
				<th>{{trans('correct')}}</th>
				<th><a href="{{ route('answers.create',[$categories_id, $lesson_id, $question])}}" class="btn btn-success"><i class="fa fa-plus"></i>
				        </a></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			@foreach($answers as $answer)
				<tr>
					<td>{{$answer->id}}</td>
					<td>{{$answer->name}}</td>
					<td>
						@if($answer->correct)
							<i class="fa fa-check text-success"></i>
						@else
							<i class="fa fa-times text-danger"></i>
						@endif
					</td>
					<td>
						<a href="{{ route('answers.edit',[$categories_id, $lesson_id, $question, $answer->id])}}" class="btn btn-sm btn-dark" data-event="tooltip" data-placement="left" title="{{ trans('edit') }}">
				            <i class="fa fa-pen"></i>
							</a>
							<a href="{{ route('answers.destroy', [$categories_id, $lesson_id, $question, $answer->id]) }}" class="btn btn-sm btn-danger" data-action="destroy" data-token="{{ csrf_token() }}" data-event="tooltip" data-placement="left" title="{{ trans('verwijderen') }}">
				            <i class="fa fa-times"></i>
				        </a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	<div class="text-center">{{ $answers->links() }}</div>
@endsection
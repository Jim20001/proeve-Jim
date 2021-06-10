@extends('admin.layouts.app')

@section('title', trans('questions'))
@section('button')

<a href="{{route('questions.index', [$categories_id, $lesson_id])}}" class="btn btn-success"><i class="fa fa-plus"></i> {{trans('toevoegen')}}</a>
	<a href="{{route('questions.create', [$categories_id, $lesson_id])}}" class="btn btn-success"><i class="fa fa-plus"></i> {{trans('common.btn.add')}}</a>
@endsection

@section('content')
	<table class="table table-striped">
		<thead class="thead-dark">
			<tr>
				<th>#</th>
				<th>{{trans('name')}}</th>
				<th><a href="{{route('questions.create', [$categories_id, $lesson_id])}}" class="btn btn-success"><i class="fa fa-plus"></i> {{trans('toevoegen')}}</a></th>
			</tr>
		</thead>
		<tbody>
			@foreach($questions as $question)
				<tr>
					<td>{{$question->id}}</td>
					<td>{{$question->name}}</td>
					<td>
						<a href="{{ route('answers.index', [$categories_id, $lesson_id, $question->id]) }}" class="btn btn-sm btn-info" data-event="tooltip" data-placement="left" title="{{ trans('antwoorden') }}">
				            <i class="fa fa-eye"></i>
				        </a>
						<a href="{{ route('questions.edit',[$categories_id, $lesson_id, $question]) }}" class="btn btn-sm btn-dark" data-event="tooltip" data-placement="left" title="{{ trans('edit') }}">
				            <i class="fa fa-pen"></i>
							<!-- url('admin/questions/edit') -->
				        </a>
				        <a href="{{ route('questions.destroy', [$categories_id, $lesson_id, $question->id]) }}" class="btn btn-sm btn-danger" data-action="destroy" data-token="{{ csrf_token() }}" data-event="tooltip" data-placement="left" title="{{ trans('verwijderen') }}">
				            <i class="fa fa-times"></i>
				        </a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	
@endsection
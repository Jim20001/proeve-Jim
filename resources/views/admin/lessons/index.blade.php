@extends('admin.layouts.app')
@section('content')
	<table class="table table-striped">
		<thead class="thead-dark">
			<tr>
				<th>#</th>
				<th>{{trans('naam')}}</th>
				<th><a href="{{route('lesson.create', $categories_id)}}" class="btn btn-success"><i class="fa fa-plus"></i> {{trans('toevoegen')}}</a></th>
			</tr>
		</thead>
		<tbody>
			@foreach($lessons as $lesson)
				<tr>
					<td>{{$lesson->id}}</td>
					<td>{{$lesson->name}}</td>
					<td>
						<a href="{{ route('questions.index', [$categories_id, $lesson->id]) }}" class="btn btn-sm btn-info" data-event="tooltip" data-placement="left" title="{{ trans('vragen') }}">
				            <i class="fa fa-eye"></i>
				        </a>
						<a href="{{ route('lesson.edit', [$categories_id, $lesson->id]) }}" class="btn btn-sm btn-dark" data-event="tooltip" data-placement="left" title="{{ trans('edit') }}">
				            <i class="fa fa-pen"></i>
				        </a>
				        <a href="{{ route('lesson.destroy', [$categories_id, $lesson->id]) }}" class="btn btn-sm btn-danger" data-action="destroy" data-token="{{ csrf_token() }}" data-event="tooltip" data-placement="left" title="{{ trans('verwijderen') }}">
				            <i class="fa fa-times"></i>
				        </a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@endsection
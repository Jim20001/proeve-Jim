@extends('admin.layouts.app')
@section('content')
	<table class="table table-striped">
		<thead class="thead-dark">
			<tr>
				<th>#</th>
				<th>{{trans('naam')}}</th>
				<th><a href="{{route('categories.create')}}" class="btn btn-success"><i class="fa fa-plus"></i> {{trans('toevoegen')}}</a></th>
			</tr>
		</thead>
		<tbody>
			@foreach($categories as $category)
				<tr>
					<td>{{$category->id}}</td>
					<td>{{$category->name}}</td>
					<td>
						<a href="{{ route('lesson.index', $category->id) }}" class="btn btn-sm btn-info" data-event="tooltip" data-placement="left" title="{{ trans('les') }}">
				            <i class="fa fa-eye"></i>
				        </a>
						<a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-dark" data-event="tooltip" data-placement="left" title="{{ trans('edit') }}">
				            <i class="fa fa-pen"></i>
				        </a>
				        <a href="{{ route('categories.destroy', $category->id) }}" class="btn btn-sm btn-danger" data-action="destroy" data-token="{{ csrf_token() }}" data-event="tooltip" data-placement="left" title="{{ trans('verwijderen') }}">
				            <i class="fa fa-times"></i>
				        </a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@endsection
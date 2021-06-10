@extends('admin.layouts.app')

@section('title', trans('common.tests.edit.title'))
@section('button')
	<a href="{{route('lesson.index',[$categories_id, $lesson])}}" class="btn btn-secondary"><i class="fa fa-chevron-left"></i> {{trans('terug')}}</a>
@endsection

@section('content')
	<form action="{{ route('lesson.update',  [$categories_id, $lesson]) }}" method="post" enctype="multipart/form-data">
    	{{ csrf_field() }}
    	@method('put')

    
              <div class="form-group">
			<label for="exampleFormControlInput1">{{trans('name')}}</label>
			<input type="text" name="name" class="form-control" id="exampleFormControlInput1" placeholder="{{trans('name')}}..." value="{{$lesson->name}}">
		</div>

    	<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('slucky')}}</label>
			<textarea name="slucky" class="summernote" id="exampleFormControlInput1"></textarea>
		</div>

		<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('body')}}</label>
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text">&#x20B4;</span>
				</div>
				<input type="text" class="form-control" name="body" placeholder="{{trans('body')}}...">
			</div>
		</div>

		<div class="form-group">
			<label for="exampleFormControlInput1">afbeelding (500x500 px)</label>
			<div class="custom-file">
				@if($lesson->image != "")
				@endif
				<input type="file" class="custom-file-input" id="customFile" name="image">
				<label class="custom-file-label" for="customFile">
					@if($lesson->image != "")
						<img src="/images/{{$lesson->image}}" height="20"> {{$lesson->image}}
					@else
						afbeelding.
					@endif
				</label>
			</div>
		</div>

		<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('video_url')}}</label>
			<input type="text" name="video_url" class="form-control" id="exampleFormControlInput1" placeholder="{{trans('video_url')}}...">
		</div>

		</div>

    	<button type="submit" class="btn btn-success">
            <i class="fa fa-save"></i>
            {{ trans('opslaan') }}
        </button>
    </form>
@endsection
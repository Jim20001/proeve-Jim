@extends('admin.layouts.app')

@section('title', trans('les aanmaken'))
@section('button')
	<a href="{{route('lesson.index', $categories_id)}}" class="btn btn-secondary"><i class="fa fa-chevron-left"></i> {{trans('terug')}}</a>
@endsection

@section('content')
	<form action="{{ route('lesson.store', $categories_id) }}" method="post" enctype="multipart/form-data">
    	{{ csrf_field() }}


    	<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('name')}}</label>
			<input type="text" name="name" class="form-control" id="exampleFormControlInput1" placeholder="{{trans('name')}}...">
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
				<input type="file" class="custom-file-input" id="customFile" name="image">
				<label class="custom-file-label" for="customFile">Afbeelding</label>
			</div>
		</div>
		
        
    	<div class="form-group">
			<label for="exampleFormControlInput1">{{trans('video_url')}}</label>
			<input type="text" name="video_url" class="form-control" id="exampleFormControlInput1" placeholder="{{trans('video_url')}}...">
		</div>

    	
    	<button type="submit" class="btn btn-success">
            <i class="fa fa-plus"></i>
            {{ trans('opslaan') }}
        </button>
    </form>
@endsection
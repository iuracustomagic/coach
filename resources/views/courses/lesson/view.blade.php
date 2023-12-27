@extends(backpack_view('blank'))

<style type="text/css">
	video{
		width: 100%;
		max-width: 920px;
		min-height: 450px;
		margin: 20px auto;
	}
	.gallery {
		display: flex;
		flex-wrap: wrap;
		margin: 15px;
	}
	.gallery img{
		height: 300px;
		margin: 15px;
		cursor: pointer;
	}
	#preview-box{
		display: none;
		position: fixed;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		z-index: 1;
		background: rgba(0,0,0,.6);
	}
	#preview-box img{
		position: absolute;
		left: 50%;
		top: 50%;
		transform: translate(-50%,-50%);
		-webkit-transform: translate(-50%,-50%);
		-moz-transform: translate(-50%,-50%);
		width: auto;
		max-width: 1200px;
		height: auto;
		max-height: 80vh;
		z-index: 2;
	}



    /*   Project: Popup Lightbox
 *   Author: Asif Mughal
 *   URL: www.codehim.com
 *   License: MIT License
 *   Copyright (c) 2019 - Asif Mughal
 */

    /* File: popup-lightbox.css */

    .lightbox {
        position: fixed;
        background: rgba(0, 0, 0, 0.90);
        display: none;
        z-index: 100;
        width: 700px;
    }

    .lightbox .img-show {
        position: absolute;
        height: 100%;
        width: 100%;
        box-sizing: border-box;
        text-align: center;
    }

    .img-caption {
        background: rgba(0, 0, 0, 0.3);
        padding: 10px;
        position: absolute;
        bottom: 0;
        display: block;
        z-index: 101;
        color: #fff;
        text-shadow: 1px 0.4px rgba(0, 0, 0, 0.5);
        width: 100%;
        box-sizing: border-box;
    }

    .lightbox .btn-close {
        position: absolute;
        display: flex;
        align-items: center;
        justify-content: center;
        top: 10px;
        right: 10px;
        width: 32px;
        height: 32px;
        border: 1px solid #fff;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        text-align: center;
        font-size: 18pt;
        z-index: 101;
        cursor: pointer;
    }

    .lightbox .btn-close:hover {
        background: #fff;
    }

    .lightbox .lightbox-status {
        position: absolute;
        top: 20px;
        left: 20px;
        color: #fff;
        font-size: 14px;
        z-index: 101;
    }

    .img-show img {
        width: 100%;
        height: auto;
        position: absolute;
        display: block;
        top: 0;
        bottom: 0;
        margin: auto;
    }


    /* Next and Previous Buttons */

    .btn-prev,
    .btn-next {
        width: 32px;
        height: 100px;
        background: rgba(0, 0, 0, 0.30);
        cursor: pointer;
        position: absolute;
        top: 50%;
        margin-top: -50px;
        z-index: 101;
        border: 0;
        font-family: FontAwesome;
        color: #fff;
        color: rgba(255, 255, 255, 0.6);
        font-size: 18pt;
        border-radius: 5px;
    }

    .btn-prev:hover,
    .btn-next:hover {
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        transition: .4s;
        -webkit-transition: .4s;
        -moz-transition: .4s;
        -ms-transition: 0.4s;
        -o-transition: .4s;
    }

    .btn-prev {
        left: 5px;
    }

    .btn-next {
        right: 5px;
    }

    .btn-prev:before {
        content: "<";

    }

    .btn-next:before {
        content: ">";
    }
    @media screen and ( max-width: 679px) {
        .lightbox {
            width: 100% !important;
            height: 100% !important;
            margin-left: 20px;
        }
    }
    @media only screen and ( min-width: 680px) {
        .lightbox {
            border-radius: 5px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.5);
            width: 700px !important;
            /*height: auto !important;*/
        }
    }




</style>
@php
    use Illuminate\Support\Facades\App;
    $locale=App::getLocale();
                if($locale == 'ru'){
                            $name=$lesson->name;
                            $description = $lesson->description;
                            if($lesson->video) {
                                 $video = $lesson->video;
                            } else $video =null;

                            if($lesson->content) {
                                $content = $lesson->content;
                            } else $content =null;
                            if($lesson->gallery) {
                                $gallery = $lesson->gallery;
                            } else $gallery = null;
                }   else if($locale == 'ro'){
                        if(isset($lesson->name_ro)){
                            $name=$lesson->name_ro;
                        } else  $name=$lesson->name;
                        if(isset($lesson->description_ro)){
                           $description = $lesson->description_ro;
                        } else  $description = $lesson->description;
                        if(isset($lesson->content_ro)){
                           $content = $lesson->content_ro;
                        } else $content ='';
                        if(isset($lesson->video_ro)){
                            $video=$lesson->video_ro;
                        } else  $video=$lesson->video;
                        if(isset($lesson->gallery_ro)){
                            $gallery=$lesson->gallery_ro;
                        } else  $gallery=null;

                }  else {
                    if(isset($lesson->name_en)){
                            $name=$lesson->name_en;
                        } else  $name=$lesson->name;
                        if(isset($lesson->description_en)){
                           $description = $lesson->description_en;
                        } else  $description = $lesson->description;
                         if(isset($lesson->content_en)){
                           $content = $lesson->content_en;
                        } else $content ='';
                             if(isset($lesson->video_en)){
                            $video=$lesson->video_en;
                        } else  $video=$lesson->video;
                        if(isset($lesson->gallery_en)){
                            $gallery=$lesson->gallery_en;
                        } else  $gallery=null;
                }

@endphp
@section('content')

	<div class="row">
		<div class="col-sm-12">
		<!-- Default box -->
			<div class="card">
				<div class="card-body row">
                    <a href="{{route('course', $course_id)}}" class="btn btn-primary float-right m-3">{{ trans('front.go_back') }}</a>
					@if($lesson)
						<div class="col-sm-12">
							<h3>{{$name}}</h3>
							<p>{{$description}}</p>
							<div class="pl-4">{!! $content !!}</div>

							<div class="card">
								@if(isset($video) && is_array($video))
									@foreach($video as $videoItem)
										<video controls>
										  	<source src='{{\Storage::url(str_replace("\\", "/", $videoItem))}}' type="video/mp4">
											{{ trans('front.not_support_video') }}
										</video>
									@endforeach
								@endif

								@if(isset($gallery) && is_array($gallery))
									<div class="gallery">


										@foreach($gallery as $image)
											<img src='{{\Storage::url(str_replace("\\", "/", $image))}}' class="popup" alt="{{$name}}">

										@endforeach


									</div>
									<div id="preview-box"></div>
								@endif

								@if($lesson->quiz)
									<div class="card-footer text-right">
										@if($lesson->quiz)
											<span class="btn btn-outline-info">
												{{ trans('front.total_questions') }}: {{$lesson->quiz->questionsList->count()}}
											</span>
											<span class="btn btn-outline-warning">
												{{ trans('front.total_attempts') }}: {{$lesson->quiz->userAttempts()}}
											</span>
											 @if(!$lesson->passedByUser())
												@if($lesson->prevPassedByUser())
													<a href="/quizzes/start-quiz/{{$lesson->quiz->id}}" class="btn btn-primary">{{ trans('front.start_quiz') }}</a>
												 @endif
											 @else
                                                <a href="javascript:void(0)" class="btn btn-success"><i class="nav-icon la la-check"></i> {{ trans('front.mark') }}{{$lesson->attemptMark()}}</a>
                                                <a href="/quizzes/start-quiz/{{$lesson->quiz->id}}" class="btn btn-primary">{{ trans('front.try_again') }}</a>
											 @endif
										@endif

									</div>
								@endif
							</div>
						</div>
					@else
						<div class="col-sm-12">
							<h1>{{ trans('front.lessont_not_found') }}</h1>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>

    <!--Popup Lightbox Js-->

{{--    <script src="{{ asset('js/jquery.popup.lightbox.js') }}"></script>--}}

    <!--Popup Lightbox CSS-->

{{--    <link href="{{ asset('css/popup-lightbox.css') }}" rel="stylesheet">--}}

@endsection

@push('after_scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="{{asset('js/jquery.popup.lightbox.js')}}"></script>
<script>



    jQuery(document).ready(function($) {
        $('.gallery').popupLightbox({
            width: 700,
            height: 580
        });



	$('#preview-box').click(function(){
        $('.gallery').parent().fadeOut();
        imgNum = 0;
		// $(this).hide();
	})
});
</script>
@endpush

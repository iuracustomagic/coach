@extends(backpack_view('blank'))
@php
    use Illuminate\Support\Facades\App;
    $locale=App::getLocale();
                if($locale == 'ru'){
                            $name=$course->name;
                            $description = $course->description;
                            }   else if($locale == 'ro'){
                                    if(isset($course->name_ro)){
                                        $name=$course->name_ro;
                                    } else  $name=$course->name;
                                    if(isset($course->description_ro)){
                                       $description = $course->description_ro;
                                    } else  $description = $course->description;


                            }  else {
                                if(isset($course->name_en)){
                                        $name=$course->name_en;
                                    } else  $name=$course->name;
                                    if(isset($course->description_en)){
                                       $description = $course->description_en;
                                    } else  $description = $course->description;
                            }
@endphp
<style type="text/css">
	.card-horizontal {
	  display: flex;
	  flex: 1 1 auto;
	}
</style>

@section('content')
	<div class="row">
		<div class="col-sm-12">
		<!-- Default box -->
			<div class="card">
				<div class="card-body row">
					@if($course)

						<div class="col-sm-12">
							<h3>{{$name}}</h3>
							<p>{{$description}}</p>

							@if($course->lessons)
									@foreach($course->lessons as $lesson)
                                    @php
                                        if($locale == 'ru'){
                                        $lessonName=$lesson->name;
                                        $lessonDescription = $lesson->description;
                                         $banner = $lesson->banner;
                                        }   else if($locale == 'ro'){
                                                if(isset($lesson->name_ro)){
                                                    $lessonName=$lesson->name_ro;
                                                } else  $lessonName=$lesson->name;
                                                if(isset($lesson->description_ro)){
                                                   $lessonDescription = $lesson->description_ro;
                                                } else  $lessonDescription = $lesson->description;
                                                if(isset($lesson->banner_ro)){
                                                    $banner=$lesson->banner_ro;
                                                } else  $banner=$lesson->banner;


                                        }  else {
                                            if(isset($lesson->name_en)){
                                                    $lessonName=$lesson->name_en;
                                                } else  $lessonName=$lesson->name;
                                                if(isset($lesson->description_en)){
                                                   $lessonDescription = $lesson->description_en;
                                                } else  $lessonDescription = $lesson->description;
                                                if(isset($lesson->banner_en)){
                                                    $banner=$lesson->banner_en;
                                                } else  $banner=$lesson->banner;
                                        }
                                    @endphp
											<div class="card">
												<div class="card-horizontal">
													<div class="img-square-wrapper" style="max-width: 300px;">
														@if($banner)
															<img src="{{\Storage::url($banner)}}" alt="{{$lessonName}}" style="max-width: 100%">
														@else
												  			<img src="https://place-hold.it/286x180?text=NO IMAGE" alt="{{$lessonName}}">
												  		@endif
												  	</div>
												  	<div class="card-body">
													    <h5 class="card-title">{{$loop->iteration}}. {{$lessonName}}</h5>
													    <p class="card-text">{{$lessonDescription}}</p>
												  	</div>
												</div>
												<div class="card-footer">
													@if($lesson->quiz)
													<div class="float-right">
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
															<a href="/quizzes/start-quiz/{{$lesson->quiz->id}}" class="btn btn-success"><i class="nav-icon la la-check"></i> {{ trans('front.try_again') }}</a>
														@endif
													</div>
													@endif
											        <a href="/my-courses/course/{{$course->id}}/lesson/{{$lesson->id}}" class="btn btn-primary">{{ trans('front.go_to_lesson') }}</a>
											    </div>
											</div>
									@endforeach
							@endif
						</div>
					@else
						<div class="col-sm-12">
							<h1>Course not found</h1>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
@endsection

@extends(backpack_view('blank'))
@php
    use Illuminate\Support\Facades\App;
    $locale=App::getLocale();

    @endphp

@section('content')
	<div class="row">
		<div class="col-sm-12">
		<!-- Default box -->
			<div class="card">
				<div class="card-body row">
					@forelse($courses as $course)
                        @php
                            if($locale == 'ru'){
                            $name=$course->name;
                            $description = $course->description;
                            $banner = $course->banner;
                            }   else if($locale == 'ro'){
                                    if(isset($course->name_ro)){
                                        $name=$course->name_ro;
                                    } else  $name=$course->name;
                                    if(isset($course->description_ro)){
                                       $description = $course->description_ro;
                                    } else  $description = $course->description;
                                    if(isset($course->banner_ro)){
                                        $banner=$course->banner_ro;
                                    } else  $banner=$course->banner;


                            }  else {
                                if(isset($course->name_en)){
                                        $name=$course->name_en;
                                    } else  $name=$course->name;
                                    if(isset($course->description_en)){
                                       $description = $course->description_en;
                                    } else  $description = $course->description;
                                     if(isset($course->banner_en)){
                                        $banner=$course->banner_en;
                                    } else  $banner=$course->banner;
                            }
                        @endphp

						<div class="col-md-4">
							<div class="card">
								@if($banner)
									<div class="img-wrapper" style="position: relative; background-color: #ccc; overflow: hidden; height: 400px">
										<img class="card-img-top" src="{{\Storage::url($banner)}}" alt="{{$name}}" style="position: absolute; top: 50%; transform: translateY(-50%);">
									</div>
								@else
									<img class="card-img-top" src="https://place-hold.it/286x180?text=NO_IMAGE" alt="{{$name}}">
								@endif
								<div class="card-body" style="min-height: 100px">
									<h5 class="card-title">{{$name}}</h5>
									<p class="card-text">{{$description}}</p>

								</div>
								<div class="card-footer">
									@if($course::finalQuiz($course->id))
										<a href="/quizzes/start-quiz/{{$course::finalQuiz($course->id)->id}}" class="btn btn-primary">{{ trans('front.start_final_quiz') }}</a>
									@endif
									<a href="my-courses/course/{{$course->id}}" class="btn btn-primary float-right">{{ trans('front.go_to_course') }}</a>
								</div>
							</div>
						</div>
					@empty
						<div class="col-md-12">
							<h1>{{ trans('front.no_courses_available') }}</h1>
						</div>
					@endforelse
				</div>
			</div>
		</div>
	</div>
@endsection

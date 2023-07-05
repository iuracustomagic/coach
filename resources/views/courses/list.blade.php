@extends(backpack_view('blank'))

@section('content')
	<div class="row">
		<div class="col-sm-12">
		<!-- Default box -->
			<div class="card">
				<div class="card-body row">
					@forelse($courses as $course)
						<div class="col-md-4">
							<div class="card">
								@if($course->banner)
									<div class="img-wrapper" style="position: relative; background-color: #ccc; overflow: hidden; height: 400px">
										<img class="card-img-top" src="{{\Storage::url($course->banner)}}" alt="{{$course->name}}" style="position: absolute; top: 50%; transform: translateY(-50%);">
									</div>
								@else
									<img class="card-img-top" src="https://place-hold.it/286x180?text=NO IMAGE" alt="{{$course->name}}">
								@endif
								<div class="card-body" style="min-height: 100px">
									<h5 class="card-title">{{$course->name}}</h5>
									<p class="card-text">{{$course->description}}</p>

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

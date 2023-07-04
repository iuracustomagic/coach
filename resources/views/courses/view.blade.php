@extends(backpack_view('blank'))

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
							<h3>{{$course->name}}</h3>
							<p>{{$course->description}}</p>

							@if($course->lessons)
									@foreach($course->lessons as $lesson)
											<div class="card">
												<div class="card-horizontal">
													<div class="img-square-wrapper" style="max-width: 300px;">
														@if($lesson->banner)
															<img src="{{\Storage::url($lesson->banner)}}" alt="{{$lesson->name}}" style="max-width: 100%">
														@else
												  			<img src="https://place-hold.it/286x180?text=NO IMAGE" alt="{{$course->name}}">
												  		@endif
												  	</div>
												  	<div class="card-body">
													    <h5 class="card-title">{{$loop->iteration}}. {{$lesson->name}}</h5>
													    <p class="card-text">{{$lesson->description}}</p>
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

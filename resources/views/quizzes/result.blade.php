@extends(backpack_view('blank'))
@php
    use Illuminate\Support\Facades\App;
    $locale=App::getLocale();
                if($locale == 'ru'){
                            $courseName=$attempt->quiz->course->name;
                            $lessonName = $attempt->quiz->lesson->name;
                }   else if($locale == 'ro'){
                        if(isset($attempt->quiz->course->name_ro)){
                            $courseName=$attempt->quiz->course->name_ro;
                        } else  $courseName=$attempt->quiz->course->name;
                        if(isset($attempt->quiz->lesson->name_ro)){
                           $lessonName = $attempt->quiz->lesson->name_ro;
                        } else  $lessonName = $attempt->quiz->lesson->name;


                }  else {
                    if(isset($attempt->quiz->course->name_en)){
                            $courseName=$attempt->quiz->course->name_en;
                        } else  $courseName=$attempt->quiz->course->name;
                        if(isset($attempt->quiz->lesson->name_en)){
                           $lessonName = $attempt->quiz->lesson->name_en;
                        } else  $lessonName = $attempt->quiz->lesson->name;
                }
@endphp

@section('content')
	<div class="row">
		<div class="col-sm-12">
		<!-- Default box -->
			<div class="card">
				<div class="card-body">
					<h3>{{ trans('labels.course') }}: {{$courseName}}</h3>
					<h4>{{ trans('labels.lesson') }}: {{$lessonName}}</h4>
                    @if($attempt->status == 'PASSED')
                        <div class="alert alert-success" role="alert">
                            <p>{{ trans('front.success_quiz') }}</p>
                            <p> {{ trans('front.your_mark') }}: {{$attempt->mark}}</p>
                        </div>
                    @elseif($attempt->status == 'FAILED')
                        <div class="alert alert-danger" role="alert">
                            <p>{{trans('front.failed_quiz') }}</p>
                            <p>{{ trans('front.your_mark') }}: {{$attempt->mark}}</p>
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            <p>{{trans('front.not_finish_quiz') }}</p>
                        </div>
                    @endif
				</div>
                <div class="card-footer">
                    <a href="/my-courses/course/{{$attempt->quiz->course->id}}" class="btn btn-primary">
                        <i class="las la-arrow-circle-left"></i>
                        {{ trans('front.lessons_list') }}
                    </a>
                    <div class="float-right">
                        @if($attempt->status == 'PASSED')
                            @if(null != $attempt->quiz->lesson->nextLesson())
                                <a href="/my-courses/course/{{$attempt->quiz->course->id}}/lesson/{{$attempt->quiz->lesson->nextLesson()}}" class="btn btn-success">
                                    <i class="las la-arrow-circle-right"></i>
                                    {{ trans('front.next_lesson') }}
                                </a>
                            @else
                                <a href="javascript:void(0)" class="btn btn-success">
                                    <i class="las la-check-circle"></i>
                                    {{ trans('front.course_finished') }}
                                </a>
                            @endif
                        @else
                            <a href="/quizzes/start-quiz/{{$attempt->quiz->id}}" class="btn btn-warning">
                            <i class="las la-undo-alt"></i>
                                {{ trans('front.try_again') }}
                            </a>
                        @endif
                    </div>
                </div>
			</div>
		</div>
	</div>
@endsection

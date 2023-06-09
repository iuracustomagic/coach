@extends(backpack_view('blank'))

@section('content')
	<div class="row">
		<div class="col-sm-12">
		<!-- Default box -->	
			<div class="card">
				<div class="card-body">
					<h3>Курс: {{$attempt->quiz->course->name}}</h3>
					<h4>Урок: {{$attempt->quiz->lesson->name}}</h4>
                    @if($attempt->status == 'PASSED')
                        <div class="alert alert-success" role="alert">
                            <p>Вы успешно прошли тестирование!</p>
                            <p>Ваша оценка: {{$attempt->mark}}</p>
                        </div>
                    @elseif($attempt->status == 'FAILED')
                        <div class="alert alert-danger" role="alert">
                            <p>Вы не прошли тестирование!</p>
                            <p>Ваша оценка: {{$attempt->mark}}</p>
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            <p>Тестирование не было завершено!</p>
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
@php
    use App\Models\UserAvailableCourses;
    use App\Models\UserResults;
    use App\Models\Lesson;
    use App\Models\Attempt;

       $courses = UserAvailableCourses::where(['user_id' => backpack_user()->id])->get();

       $courseCount = $courses->count();
       $lessonsCount = 0;
       $lessonsPassed =0;
       $avgMarkSum = 0;
       if($courseCount > 0) {
           foreach ($courses as $course) {
           $lessons = Lesson::where('course_id', $course->course_id)->count();
           $lessonsCount += $lessons;
       }
       }

       $lessonsResult = UserResults::where(['user_id' => backpack_user()->id])->get();
       $lessonsResultCount = $lessonsResult->count();
         if($lessonsResultCount > 0) {
         foreach ($lessonsResult as $lesson) {
           $lessonsPassed += $lesson->lessons_passed;
           $avgMarkSum += $lesson->avg_mark;
       }
         }
       $coursePassed = UserResults::where(['user_id' => backpack_user()->id, 'course_is_passed' => 1])->count();

       $quizPassed = Attempt::where(['user_id' => backpack_user()->id, 'status' =>  'PASSED'])->count();
       $quizFailed = Attempt::where(['user_id' => backpack_user()->id, 'status' =>  'FAILED'])->count();
       $quizStarted = Attempt::where(['user_id' => backpack_user()->id, 'status' =>  'STARTED'])->count();
       $quizAll = $quizPassed + $quizFailed + $quizStarted;

       if($courseCount > 0) {
           $coursePercent = intval(round($coursePassed / $courseCount * 100));
       } else $coursePercent = 0;


       if($lessonsCount > 0) {
            $lessonPercent =  intval(round($lessonsPassed / $lessonsCount * 100));
       } else $lessonPercent = 0;


        if($quizAll > 0) {
           $quizPercent =  intval(round($quizPassed / $quizAll * 100));
        } else $quizPercent = 0;

        if($lessonsResultCount > 0) {
            $avgMark = round($avgMarkSum / $lessonsResultCount, 2);
        } else $avgMark = 0;


@endphp

<div class="row ml-4 mt-2">
        <!-- /.col-->
        <div class="col-sm-6 col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-1"><i class="la la-book"></i></div>
                    <div class="text-value">{{$courseCount}}</div><span class="text-muted text-uppercase font-weight-bold">Курсов</span>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: {{$coursePercent}}%" aria-valuenow="{{$coursePercent}}" aria-valuemin="0" aria-valuemax="100"></div>

                    </div>
                    <div class="text-muted mt-2">Курсов пройдено: {{$coursePassed}}</div>
        </div>
        </div>
        </div>
        <!-- /.col-->
        <div class="col-sm-6 col-md-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-1"><i class="la la-book-open"></i></div>
                    <div class="text-value">{{$lessonsCount}}</div><span class="text-muted text-uppercase font-weight-bold">Уроков</span>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: {{$lessonPercent}}%" aria-valuenow="{{$lessonPercent}}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="text-muted mt-2">Уроков пройдено: {{$lessonsPassed}}</div>
                </div>
            </div>
        </div>
        <!-- /.col-->
        <div class="col-sm-6 col-md-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="h1 text-muted text-right mb-1"><i class="la la-question"></i></div>
                    <div class="text-value">{{$quizPassed}}</div><span class="text-muted text-uppercase font-weight-bold">Тестов пройдено</span>
                    <div class="progress progress-white progress-xs mt-3">
                        <div class="progress-bar" role="progressbar" style="width: {{$quizPercent}}%" aria-valuenow="{{$quizPercent}}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="text-muted mt-2">Средняя оценка: {{$avgMark}}</div>
                </div>
            </div>
        </div>
        <!-- /.col-->


</div>
<!-- /.row-->

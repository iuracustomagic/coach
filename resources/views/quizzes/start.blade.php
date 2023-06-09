@extends(backpack_view('blank'))

<style type="text/css">
	/* Hide all steps by default: */
	.tab {
	  	display: none;
	}
    /* Customize the label (the container) */
    .checkbox-wrapper {
        padding: 20px 20px 20px 50px;
        position: relative;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    /* Hide the browser's default checkbox */
    .checkbox-wrapper input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    /* Create a custom checkbox */
    .checkmark {
        position: absolute;
        top: 5px;
        left: 5px;
        height: 25px;
        width: 25px;
        background-color: #eee;
    }

    /* On mouse-over, add a grey background color */
    .checkbox-wrapper:hover input ~ .checkmark {
        background-color: #ccc;
    }

    /* When the checkbox is checked, add a blue background */
    .checkbox-wrapper input:checked ~ .checkmark {
        background-color: #389e7f;
    }

    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the checkmark when checked */
    .checkbox-wrapper input:checked ~ .checkmark:after {
        display: block;
    }

    /* Style the checkmark/indicator */
    .checkbox-wrapper .checkmark:after {
        left: 10px;
        top: 7px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    .card-horizontal {
        display: flex;
        flex: 1 1 auto;
        padding-bottom: 1.25rem;
    }

    .card-horizontal img {
        width: 100%;
    }
</style>

@section('content')
	<div class="row">
		<div class="col-sm-12">
		<!-- Default box -->	
			<div class="card">
				<div class="card-body">
					<h3>{{ trans('front.course') }}: {{$quiz->course->name}}</h3>
					<h4>{{ trans('front.lesson') }}: {{$quiz->lesson->name ?? trans('front.final_quiz')}}</h4>

					@if($questions)
						<form id="quizForm" method="POST" action="/quizzes/verify-quiz/{{$attempt->id}}">
                            {!! csrf_field() !!}
                            <input type="hidden" name="attempt_id" value="{{$attempt->id}}">
							<div class="card col-sm-12">
								<div class="card-body">
									@foreach($questions as $question)
									<div    class="tab" 
                                            data-question-id="{{$question->id}}"
                                            data-question-nr="{{$loop->iteration}}"
                                    >   
                                    @if(!empty($question->image))
                                        <div class="card-horizontal row"> 
                                            <div class="img-square-wrapper col-lg-4 col-md-6">
                                                <img src="{{\Storage::url(str_replace("\\", "/", $question->image))}}" alt="{{$question->question}}">
                                            </div>
                                            <div class="card-body col-lg-8 col-md-6">
                                                <h5 class="card-title">
                                                    {{$loop->iteration}}. {{$question->question}}
                                                </h5>
                                            </div>
                                        </div>
                                    @else
                                        <h5 class="card-title">
                                            {{$loop->iteration}}. {{$question->question}}
                                        </h5>
                                    @endif

                                        <input id="duration_{{$question['id']}}" type="hidden" name="duration[{{$question['id']}}]" value="0">

                                        <div class="row">
    										@forelse($question->answers as $answer)
    											<div class="col-lg-4 col-md-6 col-sm-12">
                                                    <label class="card checkbox-wrapper" for="answer_{{$answer['id']}}">
                                                        {{$answer['answer']}}
        											  	<input class="form-check-input" type="checkbox" name="questions[{{$question['id']}}][]" id="answer_{{$answer['id']}}" value="{{$answer['id']}}">
                                                        <span class="checkmark"></span>
    											  	</label>
    											</div>
    										@empty
                                                <div class="col-sm-12">
        											<h3>{{ trans('front.no_answers') }}</h3>
                                                </div>
    										@endforelse
                                        </div>
									</div>
									@endforeach
								</div>
								<div class="card-footer text-center">
									<button class="btn btn-success float-left" type="button" id="prevBtn" onclick="nextPrev(-1)"><i class="las la-arrow-alt-circle-left"></i> {{ trans('front.prev_question') }}</button>
                                    <span id="current_question" class="btn btn-outline-info">1/{{$quiz->questionsList->count()}}</span>
    								<button class="btn btn-success float-right" type="button" id="nextBtn" onclick="nextPrev(1)">{{ trans('front.next_question') }} <i class="las la-arrow-alt-circle-right"></i></button>
								</div>
							</div>
						</form>
					@endif
				</div>
			</div>
		</div>
	</div>
@endsection	

<script type="text/javascript">

var currentTab = 0,
    duration = []; // Current tab is set to be the first tab (0)


function showTab(n) {
    // This function will display the specified tab of the form ...
    var x = document.getElementsByClassName("tab"),
        currentQuestion = x[n].dataset.questionId;

        document.getElementById("current_question").innerHTML = x[n].dataset.questionNr + "/" + x.length;

        if(typeof duration[currentQuestion] === 'undefined'){
            markers = {
                'start'     : new Date(),
                'end'       : new Date(),
                'duration'  : 0
            }
            duration[currentQuestion] = markers;
        }
        
    x[n].style.display = "block";

    // ... and fix the Previous/Next buttons:
    if (n == 0) {
        document.getElementById("prevBtn").style.display = "none";
    } else {
        document.getElementById("prevBtn").style.display = "inline";
    }
    if (n == (x.length - 1)) {
        document.getElementById("nextBtn").innerHTML = "{{ trans('front.finish') }}";
    } else {
        document.getElementById("nextBtn").innerHTML = '{{ trans('front.next_question') }} <i class="las la-arrow-alt-circle-right"></i>';
    }
}

function nextPrev(n) {
    // This function will figure out which tab to display
    var x = document.getElementsByClassName("tab"),
        currentQuestion = x[currentTab].dataset.questionId;

    duration[currentQuestion].end = new Date();
    miliseconds = duration[currentQuestion].end - duration[currentQuestion].start;
    seconds = Math.round(miliseconds / 1000);
    duration[currentQuestion].duration = seconds;
    document.getElementById('duration_'+currentQuestion).value = duration[currentQuestion].duration;
    // Exit the function if any field in the current tab is invalid:
    if (n == 1 && !validateForm()) return false;
    // Hide the current tab:
    x[currentTab].style.display = "none";
    // Increase or decrease the current tab by 1:

    currentTab = currentTab + n;
    // if you have reached the end of the form... :
    if (currentTab >= x.length) {
        //...the form gets submitted:
        document.getElementById("quizForm").submit();
        return false;
    }
    // Otherwise, display the correct tab:
    showTab(currentTab);
}

function validateForm() {
    // This function deals with validation of the form fields
    var x, y, i, valid = true;
    x = document.getElementsByClassName("tab");
    y = x[currentTab].getElementsByTagName("input");
    // A loop that checks every input field in the current tab:
    for (i = 0; i < y.length; i++) {
        // If a field is empty...
        if (y[i].value == "") {
          // add an "invalid" class to the field:
          y[i].className += " invalid";
          // and set the current valid status to false:
          valid = false;
        }
    }
    // If the valid status is true, mark the step as finished and valid:
    /*if (valid) {
        document.getElementsByClassName("step")[currentTab].className += " finish";
    }*/
    return valid; // return the valid status
}

document.addEventListener('DOMContentLoaded', function(){
	showTab(currentTab); // Display the current tab
});
</script>
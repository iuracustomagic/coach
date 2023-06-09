<?php

namespace App\Observers;

use App\Models\Quiz;

class QuizObserver
{
    /**
     * Handle the Quiz "created" event.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return void
     */
    public function created(Quiz $quiz)
    {
        if(!empty($quiz->questions)){
            $questions = json_decode($quiz->questions, true);
            if(count($questions) < $quiz->questions_to_show){
                $quiz->questions_to_show = count($questions);
            }
            if(!empty($questions)){
                foreach($questions as $qkey => $question){
                    if(isset($question['question']) && !empty($question['question'])){
                        $questionModel = new \App\Models\Question();
                        $questionModel->quiz_id = $quiz->id;
                        $questionModel->image = isset($question['image']) ? $question['image'] : null;
                        $questionModel->question = $question['question'];
                        if($questionModel->save()){
                            $questions[$qkey]['id'] = $questionModel->id;

                            $answers = json_decode($question['answers'], true);
                            if(!empty($answers)){
                                foreach($answers as $akey => $answer){
                                    if(isset($answer['option']) && !empty($answer['option'])){
                                        $answerModel = new \App\Models\Answer();
                                        $answerModel->question_id = $questionModel->id;
                                        $answerModel->answer = $answer['option'];
                                        $answerModel->is_true = $answer['is_true'];
                                        if($answerModel->save()){
                                            $answers[$akey]['id'] = $answerModel->id;
                                        }
                                    }
                                }
                                $questions[$qkey]['answers'] = json_encode($answers, JSON_UNESCAPED_UNICODE);
                            }
                        }
                    }
                }

                $quiz->questions = json_encode($questions, JSON_UNESCAPED_UNICODE);
                //$quiz->questions = json_encode($questions);
                $quiz->saveQuietly();
            }
        }
    }

    /**
     * Handle the Quiz "updated" event.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return void
     */
    public function updated(Quiz $quiz)
    {
        // TODO: Handle deleted questions and answers
        if(!empty($quiz->questions)){
            $questions = json_decode($quiz->questions, true);
            if(count($questions) < $quiz->questions_to_show){
                $quiz->questions_to_show = count($questions);
            }
            if(!empty($questions)){
                foreach($questions as $qkey => $question){
                    if(isset($question['question']) && !empty($question['question'])){
                        if(!isset($question['id'])){
                            $questionModel = new \App\Models\Question();
                        } else {
                            $questionModel = \App\Models\Question::find($question['id']);
                            if(empty($questionModel)){
                                $questionModel = new \App\Models\Question();
                            }
                        }
                        
                        $questionModel->quiz_id = $quiz->id;
                        $questionModel->image = isset($question['image']) ? $question['image'] : null;
                        $questionModel->question = $question['question'];
                        if($questionModel->save()){
                            $questions[$qkey]['id'] = $questionModel->id;

                            $answers = json_decode($question['answers'], true);
                            if(!empty($answers)){
                                foreach($answers as $akey => $answer){
                                    if(isset($answer['option']) && !empty($answer['option'])){
                                        if(!isset($answer['id'])){
                                            $answerModel = new \App\Models\Answer();
                                        } else {
                                            $answerModel = \App\Models\Answer::find($answer['id']);
                                            if(empty($answerModel)){
                                                $answerModel = new \App\Models\Answer();
                                            }
                                        }
                                        
                                        $answerModel->question_id = $questionModel->id;
                                        $answerModel->answer = $answer['option'];
                                        $answerModel->is_true = $answer['is_true'];
                                        if($answerModel->save()){
                                            $answers[$akey]['id'] = $answerModel->id;
                                        }
                                    }
                                }
                                $questions[$qkey]['answers'] = json_encode($answers, JSON_UNESCAPED_UNICODE);
                            }
                        }
                    }
                }

                $quiz->questions = json_encode($questions, JSON_UNESCAPED_UNICODE);
                $quiz->saveQuietly();
            }
        }
    }

    /**
     * Handle the Quiz "deleted" event.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return void
     */
    public function deleted(Quiz $quiz)
    {
        if(!empty($quiz->questions)){
            $questions = json_decode($quiz->questions, true);
            if(!empty($questions)){
                foreach($questions as $qkey => $question){
                    $questionModel = \App\Models\Question::find($question['id']);
                    if(!empty($questionModel)){
                        if($questionModel->delete()){
                            $answers = json_decode($question['answers'], true);
                            if(!empty($answers)){
                                foreach($answers as $akey => $answer){
                                    $answerModel = \App\Models\Answer::find($answer['id']);
                                    if(!empty($answerModel)){
                                        $answerModel->delete();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Handle the Quiz "restored" event.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return void
     */
    public function restored(Quiz $quiz)
    {
        //
    }

    /**
     * Handle the Quiz "force deleted" event.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return void
     */
    public function forceDeleted(Quiz $quiz)
    {
        //
    }
}

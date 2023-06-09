<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        if(!empty($user->profession->courses)){
            foreach($user->profession->courses as $course){
                $pivot = \App\Models\UserAvailableCourses::where(['user_id' => $user->id, 'course_id' => $course->id])->first();
                if(empty($pivot)){
                    $pivot = new \App\Models\UserAvailableCourses();
                    $pivot->user_id = $user->id;
                    $pivot->course_id = $course->id;
                    $pivot->save();
                }
            }
        }
		
		if(backpack_user()->hasRole('SuperAdmin') || backpack_user()->hasRole('Admin') || backpack_user()->hasRole('Manager')){
            $user->assignRole('Employee');
        }
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
       //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}

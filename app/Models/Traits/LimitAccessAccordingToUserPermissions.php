<?php

namespace App\Models\Traits;

trait LimitAccessAccordingToUserPermissions {
    protected function denyAccessIfNoPermission() {
        /*$user = backpack_user();
        $permission = \Request::getRequestUri().'/'.$this->crud->getCurrentOperation();

        if (!$user->can($permission)) {
            $this->crud->denyAccess($this->crud->getCurrentOperation());
        }*/
    }
}
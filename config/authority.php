<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Initialize User Permissions Based On Roles
    |--------------------------------------------------------------------------
    |
    | This closure is called by the Authority\Ability class
    |
    */

    'initialize' => function($account)
    {
        Authority::action_alias('manage', array('create', 'read', 'update', 'delete'));
        Authority::action_alias('moderate', array('update', 'delete'));

        if(Auth::guest() || count($account->roles) === 0) return false;
    }

);
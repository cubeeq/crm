<?php

use Illuminate\Support\Facades\Auth;
use App\Notifications\EmailNotification;
use Illuminate\Support\Facades\Notification;

/**
 *  Send notification
 */
if (!function_exists('notify')) {
    function notify($message, $user = null) 
    {
    	$user = ($user) ? $user : Auth::user();
        $project = [
            'greeting' => 'Hi '.$user->name.',',
            'body' => $message,
        ];
        Notification::send($user, new EmailNotification($project));
    }
}

/**
 *  Send notification
 */
if (!function_exists('getParams')) {
    function getParams($params, $type) 
    {
        $arr = [];
        foreach ($params as $param) {
            if (strpos($param, $type) !== false)
                array_push($arr, explode('=', $param)[1]);
        }
        return $arr;
    }
}
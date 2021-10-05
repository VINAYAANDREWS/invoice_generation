<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function security_check(){
    
    $CI= & get_instance();
    
    $user_id = $CI->input->post('user_id');
    $user_token = $CI->input->post('user_token');
    
    $secret_key = config_item('secret_key');
    
    $key = $user_id.$secret_key;
        
    $secret_token = SHA1($key);
    
    if ($secret_token != $user_token) {
        exit(json_encode(array('error'=>'User token verification failed')));
    }
    
    
    $timezone = config_item('time_reference');
    
    if ( function_exists( 'date_default_timezone_set' ) ){
     date_default_timezone_set($timezone);
    }

    
}

function push_notification_android($device_id,$message,$title=false){
    
    if(!$title){
      $title='Campus7 Notification'; 
    }

    //API URL of FCM
    $url = 'https://fcm.googleapis.com/fcm/send';

    /*api_key available in:
    Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key*/   
    $api_key = config_item('push_notification_android_server_key');
  
    $fields = array (
        'registration_ids' => array (
                $device_id
        ),
        'notification' => array (
                "body" => $message, 
                'title'   => $title, 
                'click_action'=>'',
                'color' =>'#00ff00',
                'icon' => 'myicon',
                //'image' =>'https://demo.campus7.in/public/img/favicon.png'
        )
    );

    //header includes Content type and api key
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key='.$api_key
    );
                
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $result;
}


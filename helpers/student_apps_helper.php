<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function testpress_student_login(){
    
    $CI= & get_instance();
    $CI->load->model('Model_students');
    $student_id = $CI->session->userdata('student_id');
    $student=$CI->Model_students->get_student_basic_info($student_id);
    $CI->load->library('testpress');
    $testpress=new Testpress();
    if($student['student_email']){
    $testpress->sso($student['student_email']);
    }
    else{
    $testpress->sso($CI->session->userdata('username'),'username');
    }
}   
function testpress_create_student($user){
    $user['batches'][]=$user['batch'];
    $response['error']=FALSE;
    $CI= & get_instance();  
    $CI->load->library('testpress');
    $testpress=new Testpress();
    $token=$testpress->auth_token();
    if($token){
    $response=$testpress->create_user($token,$user);    
    }
    else {
    $response['error']="Testpress Authentication failed.";
    }
    return $response;
}
function testpress_update_student($user){
    $response['error']=FALSE;
    $CI= & get_instance();  
    $CI->load->library('testpress');
    $testpress=new Testpress();
    $token=$testpress->auth_token();
    if($token){
    $response=$testpress->update_user($token,$user);    
    }
    else {
    $response['error']="Testpress Authentication failed.";
    }
    return $response;
}
function testpress_delete_student($user){
    $response['error']=FALSE;
    $CI= & get_instance();  
    $CI->load->library('testpress');
    $testpress=new Testpress();
    $token=$testpress->auth_token();
    if($token){
    $response=$testpress->delete_user($token,$user);    
    }
    else {
    $response['error']="Testpress Authentication failed.";
    }
    return $response;
}
function testpress_add_batch_to_student($user){
    $user['batches'][]=$user['batch'];
    $response['error']=FALSE;
    $CI= & get_instance();  
    $CI->load->library('testpress');
    $testpress=new Testpress();
    $token=$testpress->auth_token();
    if($token){
    $response=$testpress->add_batches_to_user($token,$user);    
    }
    else {
    $response['error']="Testpress Authentication failed.";
    }
    return $response;
}
function testpress_remove_batch_from_student($user){
    $user['batches'][]=$user['batch'];
    $response['error']=FALSE;
    $CI= & get_instance();  
    $CI->load->library('testpress');
    $testpress=new Testpress();
    $token=$testpress->auth_token();
    if($token){
    $response=$testpress->remove_batches_from_user($token,$user);    
    }
    else {
    $response['error']="Testpress Authentication failed.";
    }
    return $response;
}
function testpress_create_batch($batch_name){

    $response['error']=FALSE;
    $CI= & get_instance();  
    $CI->load->library('testpress');
    $testpress=new Testpress();
    $token=$testpress->auth_token();
    if($token){
    $response=$testpress->create_batch($token,$batch_name);    
    }
    else {
    $response['error']="Testpress Authentication failed.";
    }
    return $response;
}
function testpress_create_mentor($mentor){
    $response['error']=FALSE;
    $CI= & get_instance();  
    $CI->load->library('testpress');
    $testpress=new Testpress();
    $token=$testpress->auth_token();
    if($token){
    $response=$testpress->create_mentor($token,$mentor);    
    }
    else {
    $response['error']="Testpress Authentication failed.";
    }
    return $response;
}




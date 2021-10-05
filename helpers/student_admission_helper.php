<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function post_admission_student_email_old($post_admission_student_email){
   
    $CI = & get_instance();
    
     if (isset($post_admission_student_email) && $post_admission_student_email) {
         
         if(isset($post_admission_student_email['student_email']) && $post_admission_student_email['student_email']){
             $student_email=$post_admission_student_email['student_email'];
         }
         if(isset($post_admission_student_email['student_name']) && $post_admission_student_email['student_name']){
             $student_name=$post_admission_student_email['student_name'];
         }else{
             $student_name='';
         }
        if(isset($post_admission_student_email['institute_name']) && $post_admission_student_email['institute_name']){
             $institute_name=$post_admission_student_email['institute_name'];
         }else{
             $institute_name='';
         }
         if(isset($post_admission_student_email['course_name']) && $post_admission_student_email['course_name']){
             $course_name=$post_admission_student_email['course_name'];
         }else{
             $course_name=''; 
         }
         if(isset($post_admission_student_email['admission_number']) && $post_admission_student_email['admission_number']){
             $admission_number=$post_admission_student_email['admission_number'];
         }else{
             $admission_number='';
         }
          if(isset($post_admission_student_email['username']) && $post_admission_student_email['username']){
             $username=$post_admission_student_email['username'];
         }
          if(isset($post_admission_student_email['password']) && $post_admission_student_email['password']){
             $password=$post_admission_student_email['password'];
         }
         
     }
     if($student_email && $username && $password){
        
                            $CI->load->library('mailer');
                            
                            $subject = "Admission Confirmed .Campus7 - Login Credentials";
                            $CI->load->helper('string');
                            
                             $message = '<div style="line-height:20px;">Dear ' .$student_name. ', <br style="margin-bottom:5px;">'
                                     . '<br> '
                                     . 'Welcome to '. $institute_name.'.'
                                     . '<br> '
                                     . '<br> '
                                     . 'Your admission is confirmed for '.$course_name.' with Admission Number : '.$admission_number.' .'
                                    . '<br> '
                                     . '<br> '
                                    . 'Software login details are as follows. '
                                    . '<br> '
                                    . 'URL : <a href="'.base_url().'">' . base_url().'</a>'
                                    . '<br> '
                                    . 'Username : ' . $username
                                    . '<br> '
                                    . 'Password : ' . $password
                                    . '<br> '
                                     . '<br> '
                                      . 'Note: Please change your password after first login '
                                     . '<br> '
                                     . '<br> '
                                     .'Regards,'
                                     .'<br>'
                                     .'Administration Team,'
                                     .'<br>'
                                     .$institute_name;
                            
                           $CI->mailer->send_mail($student_email,$subject,$message);  
                            
                        }
}
function post_admission_parent_email_old($post_admission_parent_email){
   
    $CI = & get_instance();
  
     if (isset($post_admission_parent_email) && $post_admission_parent_email) {
         
         if(isset($post_admission_parent_email['parent_email']) && $post_admission_parent_email['parent_email']){
             $parent_email=$post_admission_parent_email['parent_email'];
         }
         if(isset($post_admission_parent_email['student_name']) && $post_admission_parent_email['student_name']){
             $student_name=$post_admission_parent_email['student_name'];
         }else{
             $student_name="Student";
         }
         if(isset($post_admission_parent_email['parent_name']) && $post_admission_parent_email['parent_name']){
             $parent_name=$post_admission_parent_email['parent_name'];
         }else{
             $parent_name="Parent";
         }
        if(isset($post_admission_parent_email['institute_name']) && $post_admission_parent_email['institute_name']){
             $institute_name=$post_admission_parent_email['institute_name'];
         }else{
             $institute_name='';
         }
         if(isset($post_admission_parent_email['course_name']) && $post_admission_parent_email['course_name']){
             $course_name=$post_admission_parent_email['course_name'];
         }else{
             $course_name=''; 
         }
         if(isset($post_admission_parent_email['admission_number']) && $post_admission_parent_email['admission_number']){
             $admission_number=$post_admission_parent_email['admission_number'];
         }else{
             $admission_number='';
         }
          if(isset($post_admission_parent_email['username']) && $post_admission_parent_email['username']){
             $username=$post_admission_parent_email['username'];
         }
          if(isset($post_admission_parent_email['password']) && $post_admission_parent_email['password']){
             $password=$post_admission_parent_email['password'];
         }
         
     }
     if($parent_email && $username && $password){
        
                            $CI->load->library('mailer');
                            $subject = "Admission Confirmed . Campus7 - Login Credentials";
                            $CI->load->helper('string');
                            $message = '<div style="line-height:20px;">Dear '.$parent_name.',' 
                                    
                                    . ' <br style="margin-bottom:5px;">'
                                    . '<br> '
                                     . 'Welcome to '. $institute_name.'.'
                                     . '<br> '
                                    . '<br> '
                                    . 'Admission for '.$student_name.' is confirmed for '.$course_name.' with Admission Number : '.$admission_number
                                    . '<br> '
                                     . '<br> '
                                    . 'Software (Parent) login details are as follows. '
                                    . '<br> '
                                    . 'URL : <a href="'.base_url().'">' . base_url().'</a>'
                                    . '<br> '
                                    . 'Username : ' . $username
                                    . '<br> '
                                    . 'Password : ' . $password
                                    . '<br> '
                                     . '<br> '
                                      . 'Note: Please change your password after first login '
                                     . '<br> '
                                     . '<br> '
                                     .'Regards,'
                                     .'<br>'
                                     .'Administration Team,'
                                     .'<br>'
                                     .$institute_name;

                            
                            $CI->mailer->send_mail($parent_email,$subject,$message);
                            
                        }
}
function post_admission_student_sms($student_sms){
   
    $CI = & get_instance();
    $stud_admission_sms = get_option(array('option' => 'student_admission_sms'));
//             "student_admission_sms"
                    $sms_rules = json_decode($stud_admission_sms, TRUE);
//                    var_dump($sms_rules);
                    if (isset($sms_rules) && $sms_rules) {
                       $adm_sms = $sms_rules['student_admission_sms'];
                       $adm_message = $sms_rules['message'];
                       $adm_sms_template_id = $sms_rules['template_id'];
                    }
    if (isset($adm_sms) && $adm_sms == 1 ) { 
                            $student_phone_number=$student_sms['student_phone_number'];
                            $parent_mobile=$student_sms['parent_mobile'];
                            $phone_number[] = $parent_mobile;
//                            
                            if(isset($student_phone_number) && $student_phone_number){
                                 $phone_number[] = $student_phone_number;
                            }
//                            $phone_number[] = ($student_phone_number) ? $student_phone_number : '';
                            $student_name = $student_sms['student_name'];
                            $student_admission_no = $student_sms['student_admission_no'];
                            $student_username=$student_sms['student_username'];
                            $student_password=$student_sms['student_password'];
                            $course_name=$student_sms['course_name'];
                            $institute=$student_sms['institute'];
                            $institute_name=$student_sms['institute_name'];
                            $student_id=$student_sms['student_id'];
                            $parent_id=$student_sms['parent_id'];
                            $search_words = array("{{INSTITUTENAME}}","{{STUDENTNAME}}","{{STUDENTUSERNAME}}","{{STUDENTPASSWORD}}","{{ADMISSIONNUMBER}}","{{COURSENAME}}");
                          $check_search_words_in_msg= get_string_contain_words_in_array($adm_message,$search_words);
//                            $search_words = array("{{student_name}}","{{date}}","{{parent_name}}","{{subject_name}}","{{batch_name}}");
//                            $rule_contain = get_string_contain_words_in_array($att_message,$search_words);
                                  if(in_array("{{INSTITUTENAME}}", $check_search_words_in_msg)){
                                     $adm_message = str_replace('{{INSTITUTENAME}}',$institute_name, $adm_message);
                                 }
                                if(in_array("{{STUDENTNAME}}", $check_search_words_in_msg)){
                                     $adm_message = str_replace('{{STUDENTNAME}}',$student_name, $adm_message);
                                 }
                               
                                  if(in_array("{{ADMISSIONNUMBER}}", $check_search_words_in_msg)){
                                     $adm_message = str_replace('{{ADMISSIONNUMBER}}',$student_admission_no, $adm_message);
                                 }
                                 if(in_array("{{COURSENAME}}", $check_search_words_in_msg)){
                                     $adm_message = str_replace('{{COURSENAME}}',$course_name, $adm_message);
                                 }
                                if(in_array("{{STUDENTUSERNAME}}", $check_search_words_in_msg)){
                                     $adm_message = str_replace('{{STUDENTUSERNAME}}',$student_username, $adm_message);
                                 }
                                 if(in_array("{{STUDENTPASSWORD}}", $check_search_words_in_msg)){
                                     $adm_message = str_replace('{{STUDENTPASSWORD}}',$student_password, $adm_message);
                                 }
                               
//                            
//                            $message = $att_message;
                    
                    $message = $adm_message;
                    
                    // provider details
                    $sms_provider = get_option(array('option' => 'sms_provider','institute_id' => $institute));
                    $sms_provider_details = json_decode($sms_provider,TRUE);
                                
                                
                    $sms_options = array();
                    if (isset($sms_provider_details['provider'])) {
                        $sms_options['provider'] = $sms_provider_details['provider'];
                    }
                    if (isset($sms_provider_details['username'])) {
                        $sms_options['username'] = $sms_provider_details['username'];
                    }
                    if (isset($sms_provider_details['password'])) {
                        $sms_options['password'] = $sms_provider_details['password'];
                    }
                    if (isset($message_options['type'])) {
                        $sms_options['type'] = $message_options['type'];
                    } else {
                        $sms_options['type'] = $sms_provider_details['type'];
                    }
                    if (isset($message_options['senderid'])) {
                        $sms_options['senderid'] = $message_options['senderid'];
                    } else {
                        $sms_options['senderid'] = $sms_provider_details['senderid'];
                    }
//                    echo $message;
                    $sms_result = send_sms($message,$phone_number,$sms_options,$adm_sms_template_id);
                    if ($sms_result) {
//                        var_dump($sms_result);
//                        die();
                        $admission_sms['sender_user_id'] = get_logged_userid();
                        $admission_sms['sender_institute_id'] = get_logged_user_institute_id();
                        $admission_sms['body'] = $message;
                        $admission_sms['sms'] = 1;
                        $admission_sms['date_time'] = current_date_time();
                        if (isset($sms_result['transaction_id'])) {
                            $admission_sms['sms_transaction_id'] = $sms_result['transaction_id'];
                        }
                        $admission_sms['provider'] = $sms_result['provider'];
                        $admission_sms['system'] = 0;
                        
                        //receiver code
                        $receiver_code_array = array();
                        $receiver_code = "ind_student_".$student_admission_no;
                        $receiver_code_array[] = $receiver_code;
                        if (isset($parent_mobile) && $parent_mobile) {
                            $receiver_code = "ind_parent_".$student_admission_no;
                            $receiver_code_array[] = $receiver_code;
                        }
                        $admission_sms['receiver'] = json_encode($receiver_code_array);
                        
                        $CI->load->model('model_message');
                        $message_id = $CI->model_message->create_message($admission_sms);
                        if (isset($sms_result['ind_wise_transaction_id'])) {
                            $transaction_id = $sms_result['ind_wise_transaction_id'];
                            $transaction_ids = explode(",", $transaction_id);
                        }
                        
                        $sms_receivers = array();
                        //receiver student 
                        $sms_receivers[0]['message_id'] = $message_id;
                        $sms_receivers[0]['receiver_id'] = $student_id;
                        $sms_receivers[0]['receiver_type'] = 'student';
                        $sms_receivers[0]['phone_number'] = $student_phone_number;
                        if (isset($sms_result['ind_wise_transaction_id'])) {
                            $sms_receivers[0]['sms_transaction_id'] = $transaction_ids[0];
                        }
                        //receiver parent 
                        if (isset($parent_mobile) && $parent_mobile) {
                            $sms_receivers[1]['message_id'] = $message_id;
                            $sms_receivers[1]['receiver_id'] = $parent_id;
                            $sms_receivers[1]['receiver_type'] = 'parent';
                            $sms_receivers[1]['phone_number'] = $parent_mobile;
                            if (isset($sms_result['ind_wise_transaction_id'])) {
                                $sms_receivers[1]['sms_transaction_id'] = $transaction_ids[1];
                            }
                        }
                        
                        $CI->model_message->create_message_receivers($sms_receivers);
                    }
                  
                }
}
function post_admission_parent_sms($parent_sms){
   
    $CI = & get_instance();
    $parent_admission_sms = get_option(array('option' => 'parent_admission_sms'));
                    $parent_sms_rules = json_decode($parent_admission_sms, TRUE);
                    
                    if (isset($parent_sms_rules) && $parent_sms_rules) {
                        $parent_adm_sms = $parent_sms_rules['parent_admission_sms'];
                        $parent_adm_message = $parent_sms_rules['message'];
                        $parent_adm_sms_template_id = $parent_sms_rules['template_id'];
                        
                    }
     if (isset($parent_adm_sms) && $parent_adm_sms == 1 ) { 
                            $parent_phone_number[] = $parent_sms['parent_mobile'] ;
                            $parent_mobile=$parent_sms['parent_mobile'] ;
                            $admission_no = $parent_sms['admission_no'];
                            $student_name = $parent_sms['student_name'];
                            $parent_id=$parent_sms['parent_id'];
                             $parent_name = isset($parent_sms['parent_name']) && $parent_sms['parent_name']? $parent_sms['parent_name']:"Parent";
                            $parent_username=$parent_sms['username'];
                            $parent_password=$parent_sms['password'];
                            $course_name=$parent_sms['course_name'];
                            $institute=$parent_sms['institute'];
                            $institute_name=$parent_sms['institute_name'];
                            $search_words = array("{{INSTITUTENAME}}","{{STUDENTNAME}}","{{ADMISSIONNUMBER}}","{{COURSENAME}}","{{PARENTNAME}}","{{PARENTUSERNAME}}","{{PARENTPASSWORD}}");
                          $check_search_words_in_msg= get_string_contain_words_in_array($parent_adm_message,$search_words);
                                 
                                if(in_array("{{INSTITUTENAME}}", $check_search_words_in_msg)){
                                     $parent_adm_message = str_replace('{{INSTITUTENAME}}',$institute_name, $parent_adm_message);
                                 }
                                if(in_array("{{STUDENTNAME}}", $check_search_words_in_msg)){
                                     $parent_adm_message = str_replace('{{STUDENTNAME}}',$student_name, $parent_adm_message);
                                 }
                                 if(in_array("{{PARENTNAME}}", $check_search_words_in_msg)){
                                     $parent_adm_message = str_replace('{{PARENTNAME}}',$parent_name, $parent_adm_message);
                                 }
                                if(in_array("{{PARENTUSERNAME}}", $check_search_words_in_msg)){
                                     $parent_adm_message = str_replace('{{PARENTUSERNAME}}',$parent_username, $parent_adm_message);
                                 }
                                 if(in_array("{{PARENTPASSWORD}}", $check_search_words_in_msg)){
                                     $parent_adm_message = str_replace('{{PARENTPASSWORD}}',$parent_password, $parent_adm_message);
                                 }
                                 if(in_array("{{ADMISSIONNUMBER}}", $check_search_words_in_msg)){
                                     $parent_adm_message = str_replace('{{ADMISSIONNUMBER}}',$admission_no, $parent_adm_message);
                                 }
                                 if(in_array("{{COURSENAME}}", $check_search_words_in_msg)){
                                     $parent_adm_message = str_replace('{{COURSENAME}}',$course_name, $parent_adm_message);
                                 }
                               
                   $parent_message = $parent_adm_message;
                    
                    // provider details
                    $sms_provider = get_option(array('option' => 'sms_provider','institute_id' => $institute));
                    $sms_provider_details = json_decode($sms_provider,TRUE);
                                
                                
                    $sms_options = array();
                    if (isset($sms_provider_details['provider'])) {
                        $sms_options['provider'] = $sms_provider_details['provider'];
                    }
                    if (isset($sms_provider_details['username'])) {
                        $sms_options['username'] = $sms_provider_details['username'];
                    }
                    if (isset($sms_provider_details['password'])) {
                        $sms_options['password'] = $sms_provider_details['password'];
                    }
                    if (isset($message_options['type'])) {
                        $sms_options['type'] = $message_options['type'];
                    } else {
                        $sms_options['type'] = $sms_provider_details['type'];
                    }
                    if (isset($message_options['senderid'])) {
                        $sms_options['senderid'] = $message_options['senderid'];
                    } else {
                        $sms_options['senderid'] = $sms_provider_details['senderid'];
                    }
                  
                    $sms_result = send_sms($parent_message,$parent_phone_number,$sms_options,$parent_adm_sms_template_id);
                    if ($sms_result) {
                        $admission_sms['sender_user_id'] = get_logged_userid();
                        $admission_sms['sender_institute_id'] = get_logged_user_institute_id();
                        $admission_sms['body'] = $parent_message;
                        $admission_sms['sms'] = 1;
                        $admission_sms['date_time'] = current_date_time();
                        if (isset($sms_result['transaction_id'])) {
                            $admission_sms['sms_transaction_id'] = $sms_result['transaction_id'];
                        }
                        $admission_sms['provider'] = $sms_result['provider'];
                        $admission_sms['system'] = 0;
                        
                        //receiver code
                        $receiver_code_array = array();
                        $receiver_code = "ind_student_".$admission_no;
                        $receiver_code_array[] = $receiver_code;
                        if (isset($parent_mobile) && $parent_mobile) {
                            $receiver_code = "ind_parent_".$admission_no;
                            $receiver_code_array[] = $receiver_code;
                        }
                        $admission_sms['receiver'] = json_encode($receiver_code_array);
                        
                        $CI->load->model('model_message');
                        $message_id = $CI->model_message->create_message($admission_sms);
                        if (isset($sms_result['ind_wise_transaction_id'])) {
                            $transaction_id = $sms_result['ind_wise_transaction_id'];
                            $transaction_ids = explode(",", $transaction_id);
                        }
                        
                        $sms_receivers = array();
                        
                        //receiver parent 
                        if (isset($parent_mobile) && $parent_mobile) {
                            $sms_receivers[0]['message_id'] = $message_id;
                            $sms_receivers[0]['receiver_id'] = $parent_id;
                            $sms_receivers[0]['receiver_type'] = 'parent';
                            $sms_receivers[0]['phone_number'] = $parent_mobile;
                            if (isset($sms_result['ind_wise_transaction_id'])) {
                                $sms_receivers[0]['sms_transaction_id'] = $transaction_ids[0];
                            }
                        }
                        
                        $CI->model_message->create_message_receivers($sms_receivers);
                    }
                  
                }
}
function post_admission_student_email($post_admission_student_email){
                        $CI = & get_instance();
     if (isset($post_admission_student_email) && $post_admission_student_email) {
         
         if(isset($post_admission_student_email['student_email']) && $post_admission_student_email['student_email']){
             $student_email=$post_admission_student_email['student_email'];
         }
         if(isset($post_admission_student_email['student_name']) && $post_admission_student_email['student_name']){
             $student_name=$post_admission_student_email['student_name'];
         }else{
             $student_name='';
         }
        if(isset($post_admission_student_email['institute_name']) && $post_admission_student_email['institute_name']){
             $institute_name=$post_admission_student_email['institute_name'];
         }else{
             $institute_name='';
         }
         if(isset($post_admission_student_email['institute_id']) && $post_admission_student_email['institute_id']){
             $institute_id=$post_admission_student_email['institute_id'];
         }
         if(isset($post_admission_student_email['course_name']) && $post_admission_student_email['course_name']){
             $course_name=$post_admission_student_email['course_name'];
         }else{
             $course_name=''; 
         }
         if(isset($post_admission_student_email['admission_number']) && $post_admission_student_email['admission_number']){
             $admission_number=$post_admission_student_email['admission_number'];
         }else{
             $admission_number='';
         }
          if(isset($post_admission_student_email['username']) && $post_admission_student_email['username']){
             $username=$post_admission_student_email['username'];
         }
          if(isset($post_admission_student_email['password']) && $post_admission_student_email['password']){
             $password=$post_admission_student_email['password'];
         }
         
         
     }
     
     if($student_email && $username && $password && $institute_id){
                            $CI->load->library('mailer');
                            $CI->load->helper('string');
                            $subject = "Admission Confirmed. Campus7 - Login Credentials";

                     $email_content = 
                      '<p>Dear <strong>'.$student_name.'</strong>,</p>
                      <p>
                      Welcome to '. $institute_name.'. </p>
                      <p>Your admission is confirmed for <strong>'.$course_name.'</strong> '
                      . 'with Admission Number : <strong>'.$admission_number.'</strong>.</p>
                      <p>Software login details are as follows. <br>
                      URL : <a href="'.base_url().'">' . base_url().'</a> <br>
                      Username : <strong>'. $username.'</strong>  <br>
                      Password : <strong>'. $password.'</strong>  </p>
                      <p>Note: Please change your password after first login.</p>
                      <p>Regards,<br>
                      Administration Team, '.$institute_name.'.</p>';
                      
                            
                            
                            $email_options['theme']=TRUE;
                            $email_options['institute_id']=$institute_id;
                            
                            $CI->mailer->send_mail($student_email,$subject,$email_content,$email_options);
                }
    
    
}
function post_admission_parent_email($post_admission_parent_email){
     $CI = & get_instance();

 if (isset($post_admission_parent_email) && $post_admission_parent_email) {
         
         if(isset($post_admission_parent_email['parent_email']) && $post_admission_parent_email['parent_email']){
             $parent_email=$post_admission_parent_email['parent_email'];
         }
         if(isset($post_admission_parent_email['student_name']) && $post_admission_parent_email['student_name']){
             $student_name=$post_admission_parent_email['student_name'];
         }else{
             $student_name="Student";
         }
         if(isset($post_admission_parent_email['parent_name']) && $post_admission_parent_email['parent_name']){
             $parent_name=$post_admission_parent_email['parent_name'];
         }else{
             $parent_name="Parent";
         }
        if(isset($post_admission_parent_email['institute_name']) && $post_admission_parent_email['institute_name']){
             $institute_name=$post_admission_parent_email['institute_name'];
         }else{
             $institute_name='';
         }
         if(isset($post_admission_parent_email['course_name']) && $post_admission_parent_email['course_name']){
             $course_name=$post_admission_parent_email['course_name'];
         }else{
             $course_name=''; 
         }
         if(isset($post_admission_parent_email['admission_number']) && $post_admission_parent_email['admission_number']){
             $admission_number=$post_admission_parent_email['admission_number'];
         }else{
             $admission_number='';
         }
          if(isset($post_admission_parent_email['username']) && $post_admission_parent_email['username']){
             $username=$post_admission_parent_email['username'];
         }
          if(isset($post_admission_parent_email['password']) && $post_admission_parent_email['password']){
             $password=$post_admission_parent_email['password'];
         }
         if(isset($post_admission_parent_email['institute_id']) && $post_admission_parent_email['institute_id']){
             $institute_id=$post_admission_parent_email['institute_id'];
         }
        
     }
     
    if($parent_email && $username && $password){
     
                            $CI->load->library('mailer');
                            $CI->load->helper('string');
                            $subject = "Admission Confirmed. Campus7 - Login Credentials";

                
                       $email_content = 
                      '<p>Dear <strong>'.$parent_name.'</strong>,</p>
                      <p>
                      Welcome to '. $institute_name.'. </p>
                      <p>Admission for <strong>'.$student_name.'</strong> is confirmed for <strong>'.$course_name.'</strong> '
                      . 'with Admission Number : <strong>'.$admission_number.'</strong>.</p>
                      <p>Software login details are as follows. <br>
                      URL : <a href="'.base_url().'">' . base_url().'</a> <br>
                      Username : <strong>'. $username.'</strong>  <br>
                      Password : <strong>'. $password.'</strong>  </p>
                      <p>Note: Please change your password after first login.</p>
                      <p>Regards,<br>
                      Administration Team, '.$institute_name.'.</p>';
                      
                            
                            
                            $email_options['theme']=TRUE;
                            $email_options['institute_id']=$institute_id;
                            
                            
                            $CI->mailer->send_mail($parent_email,$subject,$email_content,$email_options);
                           
                }
    
    
}

<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function get_walk_in_sms($message_var,$institute_id){
    
    $walk_in_sms_details = get_option(array( 'option' => 'walk_in_sms','institute_id' => $institute_id));
    $walk_in_sms = json_decode($walk_in_sms_details,TRUE);
    
    $message = $walk_in_sms['message'];
    $name = ucfirst($message_var['name']);
    $walk_in_sms['message'] = sprintf($message, $name);

    return $walk_in_sms;
    
}

function get_crm_lead_sms($message_var,$institute_id) {
    
    $crm_lead_sms_details = get_option(array( 'option' => 'crm_lead_sms','institute_id' => $institute_id));
    $crm_lead_sms = json_decode($crm_lead_sms_details,TRUE);

    $message = $crm_lead_sms['message'];
    $name = ucfirst($message_var['name']);
    $crm_lead_sms['message'] = sprintf($message, $name);
    $crm_lead_sms['message_template_id']=$crm_lead_sms['message_template_id'];
    return $crm_lead_sms;
    
}

function user_phone_verification_sms($message_var) {
    $verification_sms_sms_details = get_option(array( 'option' => 'user_phone_verification_sms'));
    $details = json_decode($verification_sms_sms_details,TRUE);
    $message = $details['message'];
    $code = $message_var['code'];
    $details['message'] = sprintf($message,$code);
    $details['template_id'] =  $details['template_id'];
    return $details;
}

function forgot_password_sms($message_var,$institute_id=false) {
    $forgot_password_sms_details = get_option(array( 'option' => 'forgot_password_sms','institute_id' => $institute_id));
    $details = json_decode($forgot_password_sms_details,TRUE);
    $message = $details['message'];
    //$name = ucfirst($message_var['name']);
    $code = $message_var['code'];
    //$date = $message_var['date'];
    $details['message'] = sprintf($message,$code);
    return $details;
}

function get_fee_receipt_sms($message_var,$institute_id) {
    
        $fee_receipt_sms = get_option(array( 'option' => 'fee_receipt_sms','institute_id' => $institute_id));
        $receipt_sms = json_decode($fee_receipt_sms,TRUE);
        
        $fee_message = $receipt_sms['message'];
        
        $CI = & get_instance();
        
//        //$name = ucfirst($message_var['name']);
//        $amount = $message_var['amount'];
//        //$balance = $message_var['balance_amount'];
//        $receipt_sms['message'] = sprintf($message,$amount);
        
        $CI->load->model('model_finance');
        
        $receipt_details = $CI->model_finance->get_payment_receipt_by_id($message_var['fee_payment_id']);
        
        $student_name = $receipt_details['full_name'];
        $fee_amount = $receipt_details['amount'] + $receipt_details['tax'] + $receipt_details['fine'];
        $institute_name = $receipt_details['institute_name'];
        
        $search_words = array("{{STUDENTNAME}}","{{FEEAMOUNT}}","{{BALANCEAMOUNT}}","{{INSTITUTE}}");
        
        $check_search_words_in_msg = get_string_contain_words_in_array($fee_message,$search_words);
        
        if(in_array("{{STUDENTNAME}}", $check_search_words_in_msg)){
            $fee_message = str_replace('{{STUDENTNAME}}',$student_name, $fee_message);
        }
        if(in_array("{{FEEAMOUNT}}", $check_search_words_in_msg)){
             $fee_message = str_replace('{{FEEAMOUNT}}',$fee_amount, $fee_message);
        }
        if(in_array("{{BALANCEAMOUNT}}", $check_search_words_in_msg)){
            
            $CI->load->model('model_student_fee');
            
            $student_fee_isset = $CI->model_student_fee->check_student_fee_isset($message_var['student_id'],$message_var['batch_id']);

            $fees_paid = $CI->model_finance->get_batch_fee_paid_by_student_id($message_var['student_id'],$message_var['batch_id']); 

            if($student_fee_isset) {
                
                    $student_total_fee = $CI->model_student_fee->get_total_student_fee($message_var['student_id'],$message_var['batch_id']);
                    $total_student_fee_amount = $student_total_fee['fee_amount'] - $student_total_fee['concession'];

            } else {
                
                $CI->load->model('model_fee_group');
                
                $student_fee_group = $CI->model_fee_group->get_student_fee_group_by_batch($message_var['student_id'],$message_var['batch_id']);
                if ($student_fee_group['fee_group_id']) {
                    
                    $batch_group_fee_isset = $CI->model_fee_group->check_batch_group_fee_isset($student_fee_group['fee_group_id'],$message_var['batch_id']);  

                    if ($batch_group_fee_isset) {
                        $student_total_fee = $CI->model_fee_group->get_total_group_fee($student_fee_group['fee_group_id'],$message_var['batch_id']);
                    
                        $total_student_fee_amount = $student_total_fee['fee_amount'] - $student_total_fee['concession'];
                    } else {
                        $CI->load->model('model_batches');
                        $student_total_fee = $CI->model_batches->get_total_batch_fee($message_var['batch_id']);
                    
                        $total_student_fee_amount = $student_total_fee['fee_amount'] - $student_total_fee['concession'];
                    }
                } else {
                    $CI->load->model('model_batches');
                    $student_total_fee = $CI->model_batches->get_total_batch_fee($message_var['batch_id']);
                
                    $total_student_fee_amount = $student_total_fee['fee_amount'] - $student_total_fee['concession'];
                }        
            }
                
            $total_fee_paid = 0;
            foreach($fees_paid as $fp){
                $total_fee_paid += $fp['amount']+$fp['tax']; 
            }  
            
            $balance_amount = $total_student_fee_amount - $total_fee_paid;
            
            $fee_message = str_replace('{{BALANCEAMOUNT}}',$balance_amount, $fee_message);
        }
        if(in_array("{{INSTITUTE}}", $check_search_words_in_msg)){
            $fee_message = str_replace('{{INSTITUTE}}',$institute_name, $fee_message);
        }
        
        $receipt_sms['message'] = $fee_message;

    return $receipt_sms;
}

function get_student_admission_sms($message_var) {
    
    $student_admission_sms = get_option(array( 'option' => 'student_admission_sms'));
    $admission_sms = json_decode($student_admission_sms,TRUE);
        
    $message = $admission_sms['message'];
        
    $student_name = $message_var['student_name'];
        
    $admission_sms['message'] = sprintf($message,$student_name);

    return $admission_sms;
}

function send_sms($message,$phone_numbers,$sms_options = FALSE,$template_id=FALSE,$provider_institute_id = FALSE){
    
    if ($sms_options) {
        $provider = $sms_options['provider'];
        $options['login_id'] = isset($sms_options['username']) ? urlencode($sms_options['username']) : '';
        $options['password'] = isset($sms_options['password']) ? urlencode($sms_options['password']) : '';
        $options['type'] = isset($sms_options['type']) ? $sms_options['type'] : '';
        $options['senderid'] = isset($sms_options['senderid']) ? $sms_options['senderid'] : '';
    } else {
        $sms_provider = get_option(array('option' => 'sms_provider','institute_id' => $provider_institute_id));
        $sms_provider_details = json_decode($sms_provider,TRUE);
        $provider = $sms_provider_details['provider'];
        
        $options['login_id'] = isset($sms_provider_details['username']) ? urlencode($sms_provider_details['username']) : '';
        $options['password'] = isset($sms_provider_details['password']) ? urlencode($sms_provider_details['password']) : '';
        $options['type'] = isset($sms_provider_details['type']) ? $sms_provider_details['type'] : '';
        $options['senderid'] = isset($sms_provider_details['senderid']) ? $sms_provider_details['senderid'] : '';
    }

//    $options['mobile'] = urlencode(implode(",", $phone_numbers));
    $options['mobile'] = (implode(",", $phone_numbers));
        
    $message = str_replace("\r\n"," ", $message);
    $message = str_replace("\n\r"," ", $message);
    $message = str_replace("\n"," ", $message);
    $message = str_replace("\r"," ", $message);
    $message = trim($message);
    $options['text'] = urlencode($message);
    $options['template_id'] =$template_id;

    include_once  APPPATH . 'third_party/sms/'.$provider.'.php';
    
    $sms_result = get_sms_api_url($options);    
    $sms_result['provider'] = $provider;
    return $sms_result;
}

function sms_report($transaction_id){
    
    $sms_provider = get_option(array('option' => 'sms_provider'));
    $sms_provider_details = json_decode($sms_provider,TRUE);
    $provider = $sms_provider_details['provider'];
 
    $options['login_id'] = isset($sms_provider_details['username']) ? urlencode($sms_provider_details['username']) : '';
    $options['password'] = isset($sms_provider_details['password']) ? urlencode($sms_provider_details['password']) : '';
    $options['transaction_id'] = $transaction_id;
    
    include_once  APPPATH . 'third_party/sms/'.$provider.'.php';
    $report = get_sms_report_api_url($options);
    return $report;
}
function courier_sms($message_var) {
    $courier_sms_details = get_option(array( 'option' => 'courier_sms'));
    $details = json_decode($courier_sms_details,TRUE);
    $message = $details['message'];
    $items = $message_var['items'];
    $tracking_number = $message_var['tracking_number'];
    $date = $message_var['date'];
    $details['message'] = sprintf($message,$items,$tracking_number,$date);
    return $details;
}
function courier_status_sms($message_var) {
    $courier_sms_details = get_option(array( 'option' => 'courier_tracking_status_sms'));
    $details = json_decode($courier_sms_details,TRUE);
    $message = $details['message'];
    $status= $message_var['status'];
    $items= $message_var['items'];
    $date= $message_var['date'];
    $details['message'] = sprintf($message,$items,$status,$date);
    return $details;
}

function get_sms_balance($institute_id) {
    
    // provider details
    $sms_provider = get_option(array('option' => 'sms_provider','institute_id' => $institute_id));
    $sms_provider_details = json_decode($sms_provider,TRUE);
    
    $user_id = isset($sms_provider_details['user_id']) && $sms_provider_details['user_id'] ? $sms_provider_details['user_id'] : '';
    $provider = $sms_provider_details['provider'];
    if ($user_id && $provider) { 
        include_once  APPPATH . 'third_party/sms/'.$provider.'.php';
    
        $result = sms_balance($user_id);
    } else {
        $result = FALSE;
    }
    return $result;
}



function get_fee_due_date_before_message($message_var,$institute_id) {
    
        $fee_due_sms = get_option(array( 'option' => 'fee_due_date_before_sms','institute_id' => $institute_id));
        $due_sms = json_decode($fee_due_sms,TRUE);
        
        $due_parent_message = $due_sms['parent'] ? $due_sms['parent'] : '';
        $due_parent_message_template_id=isset($due_sms['parent_fee_due_date_befor_sms_template_id']) ? $due_sms['parent_fee_due_date_befor_sms_template_id'] : "";
        $due_student_message = $due_sms['student'] ? $due_sms['student'] : '';
        $due_student_message_template_id= isset($due_sms['student_fee_due_date_befor_sms_template_id']) ? $due_sms['student_fee_due_date_befor_sms_template_id'] : "";
        $message_array = array();
        
        
        $student_name = $message_var['student_name'];
        $due_date = $message_var['fee_due_date'];
        $institute_name = $message_var['institute_name'];
        $fee_names_amount = $message_var['fee_name_amount'];
        
        $search_words = array("{{STUDENTNAME}}","{{FEENAMESAMOUNT}}","{{FEEDUEDATE}}","{{INSTITUTENAME}}");
        
        
        if ($due_parent_message && $message_var['send_parent'] == 1 && $due_parent_message_template_id){
            
            $check_search_words_in_parent_msg = get_string_contain_words_in_array($due_parent_message,$search_words);
            
            if(in_array("{{STUDENTNAME}}", $check_search_words_in_parent_msg)){
                $due_parent_message = str_replace('{{STUDENTNAME}}',$student_name, $due_parent_message);
            }
            if(in_array("{{FEENAMESAMOUNT}}", $check_search_words_in_parent_msg)){
                $due_parent_message = str_replace('{{FEENAMESAMOUNT}}',$fee_names_amount, $due_parent_message);
            }
            if(in_array("{{FEEDUEDATE}}", $check_search_words_in_parent_msg)){
                
                $date_format = to_print_date_format($due_date);
                
                $due_parent_message = str_replace('{{FEEDUEDATE}}',$date_format, $due_parent_message);
            }
            if(in_array("{{INSTITUTENAME}}", $check_search_words_in_parent_msg)){
                $due_parent_message = str_replace('{{INSTITUTENAME}}',$institute_name, $due_parent_message);
            }
            
            $message_array['parent_message'] = $due_parent_message;
            $message_array['parent_message_template_id']=$due_parent_message_template_id;
        }
        
        if ($due_student_message && $message_var['send_student'] == 1 && $due_student_message_template_id){
            
            $check_search_words_in_student_msg = get_string_contain_words_in_array($due_student_message,$search_words);
            
            if(in_array("{{STUDENTNAME}}", $check_search_words_in_student_msg)){
                $due_student_message = str_replace('{{STUDENTNAME}}',$student_name, $due_student_message);
            }
            if(in_array("{{FEENAMESAMOUNT}}", $check_search_words_in_student_msg)){
                $due_student_message = str_replace('{{FEENAMESAMOUNT}}',$fee_names_amount, $due_student_message);
            }
            if(in_array("{{FEEDUEDATE}}", $check_search_words_in_student_msg)){
                
                $date_format = to_print_date_format($due_date);
                
                $due_student_message = str_replace('{{FEEDUEDATE}}',$date_format, $due_student_message);
            }
            if(in_array("{{INSTITUTENAME}}", $check_search_words_in_student_msg)){
                $due_student_message = str_replace('{{INSTITUTENAME}}',$institute_name, $due_student_message);
            }
            $message_array['student_message'] = $due_student_message;
            $message_array['student_message_template_id']=$due_student_message_template_id;
        }
        

    return $message_array;
}


function get_fee_due_date_after_message($message_var,$institute_id) {

        $fee_due_sms = get_option(array( 'option' => 'fee_due_date_after_sms','institute_id' => $institute_id));
        $due_sms = json_decode($fee_due_sms,TRUE);
        
        $due_parent_message = $due_sms['parent'] ? $due_sms['parent'] : '';
        $due_parent_message_template_id=isset($due_sms['parent_sms_template_id']) ? $due_sms['parent_sms_template_id'] : "";
        $due_student_message = $due_sms['student'] ? $due_sms['student'] : '';
        $due_student_message_template_id= isset($due_sms['student_sms_template_id'])? $due_sms['student_sms_template_id'] : "";
        $message_array = array();
        
        
        $student_name = $message_var['student_name'];
        $total_due_amount = $message_var['total_due_amount'];
        $institute_name = $message_var['institute_name'];
        $fee_names = implode(', ', $message_var['fee_names']);
        
        $search_words = array("{{STUDENTNAME}}","{{TOTALDUEAMOUNT}}","{{FEENAMES}}","{{INSTITUTENAME}}");
        
        
        if ($due_parent_message && $due_parent_message_template_id &&  isset($message_var['send_parent']) && $message_var['send_parent'] == 1){
            
            $check_search_words_in_parent_msg = get_string_contain_words_in_array($due_parent_message,$search_words);
            
            if(in_array("{{STUDENTNAME}}", $check_search_words_in_parent_msg)){
                $due_parent_message = str_replace('{{STUDENTNAME}}',$student_name, $due_parent_message);
            }
            if(in_array("{{TOTALDUEAMOUNT}}", $check_search_words_in_parent_msg)){
                $due_parent_message = str_replace('{{TOTALDUEAMOUNT}}',$total_due_amount, $due_parent_message);
            }
            if(in_array("{{FEENAMES}}", $check_search_words_in_parent_msg)){
                
                $due_parent_message = str_replace('{{FEENAMES}}',$fee_names, $due_parent_message);
            }
            if(in_array("{{INSTITUTENAME}}", $check_search_words_in_parent_msg)){
                $due_parent_message = str_replace('{{INSTITUTENAME}}',$institute_name, $due_parent_message);
            }
            
            $message_array['parent_message'] = $due_parent_message;
            $message_array['parent_template_id'] = $due_parent_message_template_id;
        }
        
        if ($due_student_message && $due_student_message_template_id && isset($message_var['send_student']) && $message_var['send_student'] == 1){
            
            $check_search_words_in_student_msg = get_string_contain_words_in_array($due_student_message,$search_words);
            
            if(in_array("{{STUDENTNAME}}", $check_search_words_in_student_msg)){
                $due_student_message = str_replace('{{STUDENTNAME}}',$student_name, $due_student_message);
            }
            if(in_array("{{TOTALDUEAMOUNT}}", $check_search_words_in_student_msg)){
                $due_student_message = str_replace('{{TOTALDUEAMOUNT}}',$total_due_amount, $due_student_message);
            }
            if(in_array("{{FEENAMES}}", $check_search_words_in_student_msg)){
                
                $due_student_message = str_replace('{{FEENAMES}}',$fee_names, $due_student_message);
            }
            if(in_array("{{INSTITUTENAME}}", $check_search_words_in_student_msg)){
                $due_student_message = str_replace('{{INSTITUTENAME}}',$institute_name, $due_student_message);
            }
            $message_array['student_message'] = $due_student_message;
            $message_array['student_message_template_id'] = $due_student_message_template_id;
            
        }
        

    return $message_array;
}



<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


function get_payroll_option($array) {
    
    $CI = & get_instance();  
    $defaults = array(
        
        'option'           => '',
        'institute_id'     => false,
        'exact_institute'   => false
        
    );
    
    $option = array_merge($defaults, $array);
    $data = array();
    $CI->load->model('model_option');
    if ($option['option'] && $option['institute_id'] && $option['exact_institute']) {
        
        $data = $CI->model_option->get_payroll_options($option['option'],$option['institute_id']);
    }
    elseif ($option['option'] && $option['institute_id'] && !$option['exact_institute']) {
        
        $data = $CI->model_option->get_payroll_options($option['option'],$option['institute_id']);
        if (!$data) {
            
            $data = $CI->model_option->get_payroll_options($option['option']);
        }
    } elseif ($option['option'] && !$option['institute_id'] && !$option['exact_institute']) {
            
            $data = $CI->model_option->get_payroll_options($option['option']);
    }
    
    if ($data) {
        return $data['value'];
    } else {
        return NULL;
    }
  
}

function get_employee_pay_date_period($pay_month_year,$institute) {
    
    $CI= & get_instance();
    
    $CI->load->model('model_option');
    $pay_process_date = get_payroll_option(array('option' => 'pay_period_date','institute_id' => $institute));
    
    if ($pay_process_date == 'actual_month') {
        
        $process_start_date = to_date_format("01/".$pay_month_year);
        $process_end_date =  date('Y-m-t',strtotime($process_start_date));
        
    } else {
        
        $pay_month_year_start_date = to_date_format("01/".$pay_month_year);
        
        //for process start date
        $previous_month_end_date = date('Y-m-d', strtotime('-1 day', strtotime($pay_month_year_start_date)));
        $pre_month_end_date_details = explode("-", $previous_month_end_date);
        $pre_month_end_date = $pre_month_end_date_details[2];
        $pre_month_end_month = $pre_month_end_date_details[1];
        $pre_month_end_year = $pre_month_end_date_details[0];
        
        if ($pay_process_date <= $pre_month_end_date) {
            
            $process_start_date = $pre_month_end_year."-".$pre_month_end_month."-".$pay_process_date;
            
        } else {
            $process_start_date = $pay_month_year_start_date;
        }       
        
        //for process end date
        $pay_month_year_end_date =  date('Y-m-t',strtotime($pay_month_year_start_date));
        $pm_end_date_details = explode("-", $pay_month_year_end_date);
        $pm_end_date = $pm_end_date_details[2];
        
        if (($pay_process_date-1) <= $pm_end_date) {
            
            $process_end_date = to_date_format(($pay_process_date-1)."/".$pay_month_year);
            
        } else {
            $process_end_date = $pay_month_year_end_date;
        }
       
    }
    
    $process_period['process_from_date'] = $process_start_date;
    $process_period['process_to_date'] = $process_end_date;
    
    return $process_period;
}


function get_no_payable_dates($pay_month_year,$institute) {
    
    $CI= & get_instance();
    
    $CI->load->model('model_option');
    $payable_dates = get_payroll_option(array('option' => 'payable_dates','institute_id' => $institute));
    
    if ($payable_dates == 'month') {
        
        $pay_month_year_start_date = to_date_format("01/".$pay_month_year);
    
        $no_payable_dates =  date('t',strtotime($pay_month_year_start_date));
        
    } else {
        $no_payable_dates = $payable_dates;
    }
    
    return $no_payable_dates;
}


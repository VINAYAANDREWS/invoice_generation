<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');


function generate_tc_no_prefix($institute) {
    
    $CI= & get_instance();
    
    $tc_rules = get_option(array( 'option' => 'transfer_certificate_no_generation_rule'));
    $rules = json_decode($tc_rules,TRUE);
    
    $prefix = isset($rules['prefix']) ? $rules['prefix'] : "";
    if (isset($rules['institute_code_in_prefix']) && $rules['institute_code_in_prefix'] == 'yes'){
        $CI->load->model('model_institutes');
        $institute_details = $CI->model_institutes->get_institute_by_id($institute);
        $inst_code = $institute_details['student_adm_no_prefix'];
        $prefix .= $inst_code;
    }
    
    return $prefix;
    
}

//function generate_tc_number($institute) {
//    
//    $CI= & get_instance();
//    
//    $prefix = generate_tc_no_prefix($institute);
//    
//    $tc_rules = get_option(array( 'option' => 'transfer_certificate_no_generation_rule'));
//    $rules = json_decode($tc_rules,TRUE);
//    
//    $str_length = isset($rules['length']) && $rules['length'] ? $rules['length'] : 0 ;
//    $separation_character = isset($rules['separation_character']) && $rules['separation_character'] ? $rules['separation_character'] : "/" ;
//    
//    $CI->load->model('model_generate_certificate');
//    $row = $CI->model_generate_certificate->get_transfer_certificate_maxOf_max_id($institute,$prefix);
//    
//    if($row) {
//        $next_number = $row['max_id']+1;
//    } else {
//        $next_number = 1;   
//    }
//    
//    $s_number = str_pad($next_number,$str_length,"0",STR_PAD_LEFT);
//    $tc_number['tc_number']= $prefix.$separation_character.$s_number;
//    $tc_number['prefix_id'] = $prefix;
//    $tc_number['max_id'] = $next_number;
//    return $tc_number;
//}


function generate_tc_number($get_tc){
    
     $CI= & get_instance();
    
    $tc_rules = get_option(array( 'option' => 'transfer_certificate_no_generation_rule'));
    $rules = json_decode($tc_rules,TRUE);
    
    $rule = $rules['rule'];
    $search_words = array("{{ADMYEAR}}","{{INSCODE}}","{{COURSECODE}}","{{FINANCIALYEAR}}","{{YEAR}}","{{BATCHYEAR}}","{{ID}}");
       
    $rule_contain = get_string_contain_words_in_array($rule,$search_words);
    
    $prefix_group = $rules['prefix_group'];
    
    $prefix = '';
    
    //do not change the order
    
    
    if (in_array("{{INSCODE}}", $rule_contain)) {
        $CI->load->model('model_institutes');
        $institute_details = $CI->model_institutes->get_institute_by_id($get_tc['institute_id']);
        $inst_code = $institute_details['student_adm_no_prefix'];
        $rule = str_replace('{{INSCODE}}',$inst_code, $rule);
        $prefix .= $inst_code;
        
    }
    
    if (in_array("{{ADMYEAR}}", $rule_contain)) {
        $adm_date = to_date_format($get_tc['admission_date']);
        $date = strtotime($adm_date);
        $adm_year = date("Y",$date);
        
        if (isset($rules['adm_year_length']) && $rules['adm_year_length']){
            
            $adm_year_length = $rules['adm_year_length'];
            $adm_year = substr($adm_year, -$adm_year_length);
        }
        
        $rule = str_replace('{{ADMYEAR}}',$adm_year, $rule);
        $prefix .= $adm_year;
    }
    
    if (in_array("{{COURSECODE}}", $rule_contain)) {
        $CI->load->model('model_courses');
        $course_details = $CI->model_courses->get_course_details($get_tc['course_id']);
        $course_code = $course_details['code'];
        $rule = str_replace('{{COURSECODE}}',$course_code, $rule);
        $prefix .= $course_code;
    }
    
    if (in_array("{{FINANCIALYEAR}}", $rule_contain)) {
       
        $financial_year = financial_year();
        $year = str_replace("/","-",$financial_year);
        
        $rule = str_replace('{{FINANCIALYEAR}}',$year, $rule);
        $prefix .= $year;
    }
    if (in_array("{{YEAR}}", $rule_contain)) {
       
         $current_year = date("Y");
        $academ_year = $get_tc['year'];
        $next_year = substr(($academ_year+1), -2);
        $academic_year = $academ_year."-".$next_year;
        $rule = str_replace('{{YEAR}}',$current_year, $rule);
        $prefix .= $current_year;
    }
    if (in_array("{{BATCHYEAR}}", $rule_contain)) {
       
        $academ_year = $get_tc['year'];
        $academic_year = str_replace(' ', '', $academ_year); 
        $rule = str_replace('{{BATCHYEAR}}',$academic_year, $rule);
        $prefix .= $academic_year;
    }
    if (in_array("{{ID}}", $rule_contain)) {
        
        $str_length = isset($rules['id_length']) && $rules['id_length'] ? $rules['id_length'] : 0 ;
        
        $CI->load->model('Model_generate_certificate');
        
        if(isset($prefix_group) && $prefix_group==false){
            $tc_prefix = false;
        }else{
            $tc_prefix = $prefix;
        }
        
        $row = $CI->Model_generate_certificate->get_transfer_certificate_maxOf_max_id($get_tc['institute_id'],$tc_prefix);
        if($row) {
            $next_number = $row['max_id']+1;
        } else {
             $next_number = 1;   
        }
        $s_number = str_pad($next_number,$str_length,"0",STR_PAD_LEFT);
        
        $rule = str_replace('{{ID}}',$s_number, $rule);
    }
    
    
    $tc_number['tc_number']= $rule;
    $tc_number['prefix_id'] = $prefix;
    $tc_number['max_id'] = $next_number;
    return $tc_number;
}

function generate_cc_number($get_cc){
    
     $CI= & get_instance();
    
    $tc_rules = get_option(array( 'option' => 'cc_no_generation_rule'));
    $rules = json_decode($tc_rules,TRUE);
    
    $rule = $rules['rule'];
    $search_words = array("{{YEAR}}","{{ID}}");
       
    $rule_contain = get_string_contain_words_in_array($rule,$search_words);
    
    $prefix_group = $rules['prefix_group'];
    
    $prefix = '';
    
    //do not change the order
    
   
    if (in_array("{{YEAR}}", $rule_contain)) {
       
         $current_year = date("Y");
//        $academ_year = $get_tc['year'];
//        $next_year = substr(($academ_year+1), -2);
//        $academic_year = $academ_year."-".$next_year;
        $rule = str_replace('{{YEAR}}',$current_year, $rule);
        $prefix .= $current_year;
    }
    
    if (in_array("{{ID}}", $rule_contain)) {
        
        $str_length = isset($rules['id_length']) && $rules['id_length'] ? $rules['id_length'] : 0 ;
        
        $CI->load->model('Model_generate_certificate');
        
        if(isset($prefix_group) && $prefix_group==false){
            $cc_prefix = false;
        }else{
            $cc_prefix = $prefix;
        }
        
        $row = $CI->Model_generate_certificate->get_conduct_certificate_maxOf_max_id($get_cc['institute_id'],$cc_prefix);
        if($row) {
            $next_number = $row['max_id']+1;
        } else {
             $next_number = 1;   
        }
        $s_number = str_pad($next_number,$str_length,"0",STR_PAD_LEFT);
        
        $rule = str_replace('{{ID}}',$s_number, $rule);
    }
    
    
    $cc_number['cc_number']= $rule;
    $cc_number['prefix_id'] = $prefix;
    $cc_number['max_id'] = $next_number;
    return $cc_number;
}
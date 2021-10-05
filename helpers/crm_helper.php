<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function check_counselor_assigned_lead($lead_id) {

    $CI = & get_instance();
    $CI->load->model('model_crm_lead');
    $user_id = $CI->session->userdata('user_id');
    $lead = $CI->model_crm_lead->get_lead_details($lead_id,$user_id);

    if($lead){
        return TRUE;
    } else {
        return FALSE;
    }

}
     
//get crm walk in and admission stage id's
function get_crm_stage($stage,$institute) {

    $CI = & get_instance();
    $CI->load->model('model_crm_settings');
    $crm_stage = $CI->model_crm_settings->get_crm_stage_by_category($institute,$stage);
    return $crm_stage['id'];

}


//get crm walk in and admission stage id's
function get_crm_data_source($data_source,$institute) {

    $CI = & get_instance();
    $CI->load->model('model_crm_settings');
    $crm_data_source = $CI->model_crm_settings->get_crm_data_source_by_category($institute,$data_source);
    return $crm_data_source['id'];

}

function echo_crm_data_source($institute,$select = false, $print = true){
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_crm_settings');
    
    if ($select) {
       $select = is_array($select) ? $select : explode(' ', $select);
    }
    
    $data_sources = $CI->model_crm_settings->get_crm_data_sources($institute);
    
    foreach ($data_sources as $data_source) {
        if (in_array($data_source['id'], $select)) {
            $echo .= "<option selected value='$data_source[id]'>$data_source[source_name]</option>";
        } else {
            $echo .= "<option value='$data_source[id]'>$data_source[source_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }

}
    
function echo_crm_stages($institute,$select = false,$not_in = false, $print = true){
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_crm_settings');
    
    if ($select) {
       $select = is_array($select) ? $select : explode(' ', $select);
    }
    
    $stages = $CI->model_crm_settings->get_crm_stages($institute,$not_in);
    foreach ($stages as $stage) {
        if (in_array($stage['id'], $select)) {
            $echo .= "<option selected value='$stage[id]'>$stage[stage]</option>";
        } else {
            $echo .= "<option value='$stage[id]'>$stage[stage]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }

}

function echo_crm_stages_select_first($institute,$select = false,$not_in = false, $print = true){
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_crm_settings');
    $stages = $CI->model_crm_settings->get_crm_stages($institute,$not_in);
    foreach ($stages as $stage) {
        if ($select == $stage['id'] || (!$select && $stage['stage_order'] == 1)) {
            $echo .= "<option selected value='$stage[id]'>$stage[stage]</option>";
        } else {
            $echo .= "<option value='$stage[id]'>$stage[stage]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }

}

function get_crm_first_stage($institute) {
    
    $CI = & get_instance();
    $CI->load->model('model_crm_settings');
    $stages = $CI->model_crm_settings->get_crm_first_stage($institute);
    return $stages['id'];
    
}

function echo_life_cycle($institute,$select = false,$print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_crm_settings');
    $life_cycles = $CI->model_crm_settings->get_crm_life_cycles($institute);
    foreach ($life_cycles as $cycle) {
        if ($select == $cycle['id']) {
            $echo .= "<option selected value='$cycle[id]'>$cycle[life_cycle]</option>";
        } else {
            $echo .= "<option value='$cycle[id]'>$cycle[life_cycle]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}


function echo_follow_up_responsetype_response($institute,$response_type,$select=false,$print = true){
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_crm_lead');
    $responses = $CI->model_crm_lead->get_response_by_response_type($institute,$response_type);
    foreach ($responses as $response) {
      if($response['id']==$select){
            $echo .= "<option selected value='$response[id]' >$response[response]</option>";
        }  else {
            $echo .= "<option  value='$response[id]'>$response[response]</option>";
        }
       
       
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    
}


function generate_lead_number($lead_gen_array) {
    
    $CI= & get_instance();
    
    $lead_rules = get_option(array( 'option' => 'crm_lead_number_generation'));
    $rules = json_decode($lead_rules,TRUE);
    
    $rule = $rules['rule'];
    $search_words = array("{{ADMYEAR}}","{{INSCODE}}","{{COURSECODE}}","{{ID}}");
       
    $rule_contain = get_string_contain_words_in_array($rule,$search_words);
    
    $prefix = '';
    
    //do not change the order
    if (in_array("{{INSCODE}}", $rule_contain)) {
        $CI->load->model('model_institutes');
        $institute_details = $CI->model_institutes->get_institute_by_id($lead_gen_array['institute_id']);
        $inst_code = $institute_details['code'];
        $rule = str_replace('{{INSCODE}}',$inst_code, $rule);
        $prefix .= $inst_code;
    }
    
    if (in_array("{{ADMYEAR}}", $rule_contain)) {
       
        $lead_year = $lead_gen_array['adm_year'];
        
        if (isset($rules['adm_year_length']) && $rules['adm_year_length']){
            
            $lead_year_length = $rules['adm_year_length'];
            $lead_year = substr($lead_year, -$lead_year_length);
        }
        
        $rule = str_replace('{{ADMYEAR}}',$lead_year, $rule);
        $prefix .= $lead_year;
    }
    
    if (in_array("{{COURSECODE}}", $rule_contain) && isset($lead_gen_array['course_id'])) {
        $CI->load->model('model_courses');
        $course_details = $CI->model_courses->get_course_details($lead_gen_array['course_id']);
        $course_code = $course_details['code'];
        $rule = str_replace('{{COURSECODE}}',$course_code, $rule);
        $prefix .= $course_code;
    } elseif (in_array("{{COURSECODE}}", $rule_contain) && !isset($lead_gen_array['course_id'])) {
        $rule = str_replace('{{COURSECODE}}','', $rule);
    }
    
    if (in_array("{{ID}}", $rule_contain)) {
        
        $str_length = isset($rules['id_length']) && $rules['id_length'] ? $rules['id_length'] : 0 ;
        
        $CI->load->model('model_crm_lead');
        $row = $CI->model_crm_lead->get_student_lead_maxOf_max_id($lead_gen_array['institute_id'],$prefix);
        if($row) {
            $next_number = $row['max_id']+1;
        } else {
             $next_number = 1;   
        }
        $s_number = str_pad($next_number,$str_length,"0",STR_PAD_LEFT);
        
        $rule = str_replace('{{ID}}',$s_number, $rule);
    }
    
    $lead_number['lead_number']= $rule;
    $lead_number['prefix_id'] = $prefix;
    $lead_number['max_id'] = $next_number;
    return $lead_number;
}

function echo_student_entrance_exams($institute,$select = false, $print = true){
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_crm_settings');
    $exams = $CI->model_crm_settings->get_all_student_entrance_exams($institute);
    foreach ($exams as $ex) {
        if ($select == $ex['id']) {
            $echo .= "<option selected value='$ex[id]'>$ex[exam_name]</option>";
        } else {
            $echo .= "<option value='$ex[id]'>$ex[exam_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }

}

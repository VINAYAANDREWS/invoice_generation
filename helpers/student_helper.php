<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function check_adm_no($adm_no) {

    $CI = & get_instance();
    $CI->load->model('model_students');
    $result = $CI->model_students->check_adm_no($adm_no);
    if ($result) {
        return $result;
    } else {
        return FALSE;
    }
}

function generate_adm_no_prefix($institute,$adm_date) {
    
    $CI= & get_instance();
    
    $admission_rules = get_option(array( 'option' => 'student_adm_no_generation_rule'));
    $rules = json_decode($admission_rules,TRUE);
    
    $prefix = $rules['prefix'];
    if (isset($rules['institute_code_in_prefix']) && $rules['institute_code_in_prefix'] == 'yes'){
        $CI->load->model('model_institutes');
        $institute_details = $CI->model_institutes->get_institute_by_id($institute);
        $inst_code = $institute_details['student_adm_no_prefix'];
        $prefix .= $inst_code;
    }
    if (isset($rules['year_in_prefix']) && $rules['year_in_prefix'] == 'yes'){
        $admission_date = to_date_format($adm_date);
        $adm_year_length = $rules['adm_year_length'];
        $date=strtotime($admission_date);
        $year=date("Y",$date);           
        $end_year = substr("$year", -$adm_year_length);
        $prefix .= $end_year;
    }
    
    return $prefix;
    
}

function generate_admission_number_original($adm_gen_array) {
    
    //// {{ADMPREFIXCODE}}{{BATCHYEARCODE}}{{COURSECODE}}{{ID}}
    
    $CI= & get_instance();
    
    $admission_rules = get_option(array( 'option' => 'student_adm_no_generation_rule'));
    $rules = json_decode($admission_rules,TRUE);
    
    $rule = $rules['rule'];
    $search_words = array("{{ADMPREFIXCODE}}","{{ADMYEAR}}","{{BATCHYEARCODE}}","{{COURSECODE}}","{{ID}}");
       
    $rule_contain = get_string_contain_words_in_array($rule,$search_words);
    
    $prefix = '';
    
    //do not change the order
    if (in_array("{{ADMPREFIXCODE}}", $rule_contain)) {
        $CI->load->model('model_institutes');
        $institute_details = $CI->model_institutes->get_institute_by_id($adm_gen_array['institute_id']);
        $adm_prefix_code = $institute_details['student_adm_no_prefix'];
        $rule = str_replace('{{ADMPREFIXCODE}}',$adm_prefix_code, $rule);
        
    }
    
    if (in_array("{{ADMYEAR}}", $rule_contain)) {
        if(isset($adm_gen_array['admission_date']) && $adm_gen_array['admission_date']) {
            $adm_date = to_date_format($adm_gen_array['admission_date']);
            $date = strtotime($adm_date);
            $adm_year = date("Y",$date);
        } else if (isset($adm_gen_array['admission_year']) && $adm_gen_array['admission_year']) {
            $adm_year = $adm_gen_array['admission_year'];
        } else {
            $adm_year = '';
        }
        
        if (isset($rules['adm_year_length']) && $rules['adm_year_length'] && $adm_year){
            
            $adm_year_length = $rules['adm_year_length'];
            $adm_year = substr($adm_year, -$adm_year_length);
        }
        
        $rule = str_replace('{{ADMYEAR}}',$adm_year, $rule);
       
    }
    
    if (in_array("{{BATCHYEARCODE}}", $rule_contain)) {
        $CI->load->model('model_batch_years');
        $batch_year_details = $CI->model_batch_years->get_batch_year($adm_gen_array['batch_year_id']);
        $batch_year_code = $batch_year_details['batch_year_code'];
        $rule = str_replace('{{BATCHYEARCODE}}',$batch_year_code, $rule);
       
    }
    
    if (in_array("{{COURSECODE}}", $rule_contain)) {
        $CI->load->model('model_courses');
        $course_details = $CI->model_courses->get_course_details($adm_gen_array['course_id']);
        $course_code = $course_details['code'];
        $rule = str_replace('{{COURSECODE}}',$course_code, $rule);
    
    }
    
    if (in_array("{{ID}}", $rule_contain)) {
        
        $str_length = isset($rules['id_length']) && $rules['id_length'] ? $rules['id_length'] : 0 ;
        
        $prefix = str_replace('{{ID}}','', $rule); 
        
        $CI->load->model('model_students');
        $row = $CI->model_students->get_student_maxOf_max_id($adm_gen_array['institute_id'],$prefix);
        if($row) {
            $next_number = $row['max_id']+1;
        } else {
             $next_number = 1;   
        }
        $s_number = str_pad($next_number,$str_length,"0",STR_PAD_LEFT);
        
        $rule = str_replace('{{ID}}',$s_number, $rule);
    }
    
    $adm_number['adm_number']= $rule;
    $adm_number['prefix_id'] = $prefix;
    $adm_number['max_id'] = $next_number;
    return $adm_number;
}

function generate_admission_number($adm_gen_array) {

    $CI= & get_instance();
    
    $admission_rules = get_option(array( 'option' => 'student_adm_no_generation_rule'));
    $rules = json_decode($admission_rules,TRUE);
    
    $rule = $rules['rule'];
    $search_words = array("{{ADMPREFIXCODE}}","{{ADMYEAR}}","{{BATCHYEARCODE}}","{{COURSECODE}}","{{ID}}");
       
    $rule_contain = get_string_contain_words_in_array($rule,$search_words);
    
    $prefix = '';
    
    //do not change the order
    if (in_array("{{ADMPREFIXCODE}}", $rule_contain)) {
        $CI->load->model('model_institutes');
        $institute_details = $CI->model_institutes->get_institute_by_id($adm_gen_array['institute_id']);
        $adm_prefix_code = $institute_details['student_adm_no_prefix'];
        $rule = str_replace('{{ADMPREFIXCODE}}',$adm_prefix_code, $rule);
        
    }
    
    if (in_array("{{ADMYEAR}}", $rule_contain)) {
        if(isset($adm_gen_array['admission_date']) && $adm_gen_array['admission_date']) {
            $adm_date = to_date_format($adm_gen_array['admission_date']);
            $date = strtotime($adm_date);
            $adm_year = date("Y",$date);
        } else if (isset($adm_gen_array['admission_year']) && $adm_gen_array['admission_year']) {
            $adm_year = $adm_gen_array['admission_year'];
        } else {
            $adm_year = '';
        }
        
        if (isset($rules['adm_year_length']) && $rules['adm_year_length'] && $adm_year){
            
            $adm_year_length = $rules['adm_year_length'];
            $adm_year = substr($adm_year, -$adm_year_length);
        }
        
        $rule = str_replace('{{ADMYEAR}}',$adm_year, $rule);
       
    }
    
    if (in_array("{{BATCHYEARCODE}}", $rule_contain)) {
        $CI->load->model('model_batch_years');
        $batch_year_details = $CI->model_batch_years->get_batch_year($adm_gen_array['batch_year_id']);
        $batch_year_code = $batch_year_details['batch_year_code'];
        $rule = str_replace('{{BATCHYEARCODE}}',$batch_year_code, $rule);
       
    }
    
    if (in_array("{{COURSECODE}}", $rule_contain)) {
        $CI->load->model('model_courses');
        $course_details = $CI->model_courses->get_course_details($adm_gen_array['course_id']);
        $course_code = $course_details['code'];
        $rule = str_replace('{{COURSECODE}}',$course_code, $rule);
    
    }
    
    if (in_array("{{ID}}", $rule_contain)) {
        
        $str_length = isset($rules['id_length']) && $rules['id_length'] ? $rules['id_length'] : 0 ;
        
        $prefix = str_replace('{{ID}}','', $rule); 
        
        $CI->load->model('model_students');
        $row = $CI->model_students->get_student_maxOf_max_id($adm_gen_array['institute_id'],$prefix);
        
        if(isset($adm_gen_array['max_id']) && $adm_gen_array['max_id']){
           $next_number = $adm_gen_array['max_id']+1; 
        }else if($row) {
            $next_number = $row['max_id']+1;
        } else {
             $next_number = 1;   
        }
        $s_number = str_pad($next_number,$str_length,"0",STR_PAD_LEFT);
        
        $rule = str_replace('{{ID}}',$s_number, $rule);
    }
    
    $adm_number['adm_number']= $rule;
    $adm_number['prefix_id'] = $prefix;
    $adm_number['max_id'] = $next_number;
    return $adm_number;
}

function get_string_contain_words_in_array($string,$word_array){
    
    $result = array();
    if(is_string($string)){ 
        foreach($word_array as $word) {
            if(is_string($word) && stripos($string,$word) !== FALSE) { 
                $result[] = $word; 
            }
        }
    }

    
    return $result;
}

function echo_religions($institute,$select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_religion');
    $religions = $CI->model_religion->get_religions_by_institute($institute);
    foreach ($religions as $rel) {
        if($select == $rel['id']){
            $echo .= "<option selected value='$rel[id]'>$rel[name]</option>";
        } else {
            $echo .= "<option value='$rel[id]'>$rel[name]</option>"; 
        }
    }  
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_communities($institute,$select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_religion');
    $communities = $CI->model_religion->get_communities($institute);
    
    if ($select) {
       $select = is_array($select) ? $select : explode(' ', $select);
    }
    
    foreach ($communities as $com) { 
        if(in_array($com['id'], $select)){
            $echo .= "<option selected value='$com[id]'>$com[name]</option>";
        } else {
            $echo .= "<option value='$com[id]'>$com[name]</option>"; 
        }
    }  
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    
}

function echo_quota($select = false,$print = true) {
    
    $echo = "";
    
    $CI = & get_instance();
    $CI->load->model('model_quota');
    $quotas = $CI->model_quota->get_quota();
    
    foreach ($quotas as $quota) {
        if($select == $quota['id']){
            $echo .= "<option selected value='$quota[id]'>$quota[quota]</option>";
        } else {
            $echo .= "<option value='$quota[id]'>$quota[quota]</option>"; 
        }
    }  
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    
}

function echo_enrollment_type($select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_quota');
    $enrollment_types = $CI->model_quota->get_enrollment_types();
    
    foreach ($enrollment_types as $type) {
        if($select == $type['id']){
            $echo .= "<option selected value='$type[id]'>$type[type]</option>";
        } else {
            $echo .= "<option value='$type[id]'>$type[type]</option>"; 
        }
    }  
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    
}

function echo_student_caste($religion,$select=false,$print=true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_religion');
    $castes = $CI->model_religion->get_caste_by_religion($religion);
    
    foreach($castes as $caste) {
        if($select == $caste['caste_name']) {
            $echo .= "<option selected value='$caste[caste_name]'>$caste[caste_name]</option>";
        } else {
            $echo .= "<option value='$caste[caste_name]'>$caste[caste_name]</option>";
        }
    }
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_languages($select=false,$print=true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_students');
    $languages = $CI->model_students->get_languages();
    
    foreach ($languages as $language) {
        if($select == $language['language']){
            $echo .= "<option selected value='$language[language]'>$language[language]</option>";
        } else {
            $echo .= "<option value='$language[language]'>$language[language]</option>";
        }
    }
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_student_status($select = FALSE,$print = TRUE) {
    
    $student_status = config_item('student_status');
    
    $echo = "";
    
    foreach ($student_status as $key=>$value) {
        
        if ($select == $key) {
            $echo .= "<option selected value='$key'>$value</option>";
        } else {
            $echo .= "<option value='$key'>$value</option>";
        }
        
    }
    
    if ($print == TRUE) {
        echo $echo;
    } else {
        return $echo;
    }
    
}

function echo_student_multi_status($select = FALSE,$print = TRUE) {
    
    $student_status = config_item('student_status');
    
    $echo = "";
    
    foreach ($student_status as $key=>$value) {
        
        if (in_array($key, $select)) {
            $echo .= "<option selected value='$key'>$value</option>";
        } else {
            $echo .= "<option value='$key'>$value</option>";
        }
        
    }
    
    if ($print == TRUE) {
        echo $echo;
    } else {
        return $echo;
    }
    
}

function echo_student_transfer_status($select = FALSE,$print = TRUE) {
    
    $student_status = config_item('student_transfer_status');
    
    $echo = "";
    
    foreach ($student_status as $key=>$value) {
        
        if ($select == $key) {
            $echo .= "<option selected value='$key'>$value</option>";
        } else {
            $echo .= "<option value='$key'>$value</option>";
        }
        
    }
    
    if ($print == TRUE) {
        echo $echo;
    } else {
        return $echo;
    }
    
}

function echo_student_transfer_current_status($select = FALSE,$print = TRUE) {
    
    $student_status = config_item('student_transfer_current_status');
    
    $echo = "";
    
    foreach ($student_status as $key=>$value) {
        
        if ($select == $key) {
            $echo .= "<option selected value='$key'>$value</option>";
        } else {
            $echo .= "<option value='$key'>$value</option>";
        }
        
    }
    
    if ($print == TRUE) {
        echo $echo;
    } else {
        return $echo;
    }
    
}


function echo_institute_student_houses($institute,$select = FALSE,$print = TRUE) {
    
    $echo = "";
    $CI = & get_instance();
    
    $CI->load->model('model_student_houses');
    $student_houses = $CI->model_student_houses->get_student_houses($institute);
    foreach ($student_houses as $s_h) {
        if($select == $s_h['id']){
            $echo .= "<option selected value='$s_h[id]'>$s_h[house_name]</option>";
        } else {
            $echo .= "<option value='$s_h[id]'>$s_h[house_name]</option>"; 
        }
    }  
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

///$select = array();
function echo_institute_student_club($institute,$select = FALSE,$print = TRUE) {
    
    $echo = "";
    $CI = & get_instance();
    
    
    if ($select) {
       $select = is_array($select) ? $select : explode(' ', $select);
    }
    
    $CI->load->model('model_clubs');
    $student_clubs = $CI->model_clubs->get_clubs($institute);
    
    foreach ($student_clubs as $s_c) {
        
        if(in_array($s_c['id'], $select)){
            $echo .= "<option selected value='$s_c[id]'>$s_c[club_name]</option>";
        } else {
            $echo .= "<option value='$s_c[id]'>$s_c[club_name]</option>"; 
        }
  
    }  
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_student_academic_subjects($student_id,$batch,$sem,$select = FALSE,$print = TRUE){
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_subjects');
    $student_subjects = $CI->model_subjects->get_student_subjects($student_id,$batch,$sem);
    
    foreach($student_subjects as $stud_sub){
        
        if($stud_sub['elective_subject']==0  && !$stud_sub['specialization_id']){
            if($select==$stud_sub['subject_id']){
                $echo .= "<option value='$stud_sub[subject_id]' selected >$stud_sub[subject_name]</option>"; 
            }else{
                $echo .= "<option value='$stud_sub[subject_id]'>$stud_sub[subject_name]</option>"; 
            }
        }
        
        elseif($stud_sub['specialization_id']){
             if($select==$stud_sub['subject_id']){
                 $echo .= "<option value='$stud_sub[subject_id]' selected >$stud_sub[subject_name]</option>";  
             }else{
           $echo .= "<option value='$stud_sub[subject_id]'>$stud_sub[subject_name]</option>";  
             }
        }
 
    }
    
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}
function generate_student_leave_application_id($institute_id) {
    
    $CI= & get_instance();
    $CI->load->model('model_student_leave');
    
    $prefix = 'L';
   
    $row = $CI->model_student_leave->get_stud_leave_application_maxOf_max_id($institute_id,$prefix);
    if($row) {
        $next_number = $row['max_id']+1;
    } else {
         $next_number = 1;   
    }
    
    $leave_app_id['application_id']= $prefix.$next_number;
    $leave_app_id['prefix_id'] = $prefix;
    $leave_app_id['max_id'] = $next_number;
    return $leave_app_id;
}

function generate_student_out_pass_id($institute_id) {
    
    $CI= & get_instance();
    $CI->load->model('model_student_leave');
    
    $prefix = 'OP';
   
    $row = $CI->model_student_leave->get_stud_leave_application_maxOf_max_id($institute_id,$prefix);
    if($row) {
        $next_number = $row['max_id']+1;
    } else {
         $next_number = 1;   
    }
    
    $out_pass['out_pass_id']= $prefix.$next_number;
    $out_pass['out_pass_prefix'] = $prefix;
    $out_pass['out_pass_max_id'] = $next_number;
    return $out_pass;
}

function generate_provisional_admission_number($adm_gen_array) {
 
    $CI= & get_instance();
    $prefix=0;
    if(isset($adm_gen_array['exist_student_id']) && $adm_gen_array['exist_student_id']){
        $prefix=+1;
        $rule=$prefix."S".$adm_gen_array['student_id'];
    }else if (isset($adm_gen_array['student_id']) && $adm_gen_array['student_id']) {
         $rule="S".$adm_gen_array['student_id'];
       
    }
    
    $adm_number['adm_number']= $rule;
   
    return $adm_number;
}

function echo_disciplinary_action_types($institute,$select = false, $print = true) {
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_disciplinary_action');
    $get_array=array();
    $get_array['institute_id']=$institute;
    $action_types = $CI->model_disciplinary_action->get_action_type($get_array);
    
    foreach ($action_types as $type){
        if ($select == $type['id']){
            $echo .= "<option selected value='$type[id]'>$type[action_type]</option>";
        } else {
            $echo .= "<option value='$type[id]'>$type[action_type]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function show_active_mode_student_status($print = TRUE) {
    
    $student_status = config_item('active_mode_student_status');
    
    $echo = "";
    
    foreach ($student_status as $key=>$value) {
        
            $echo .= " ". ucwords(str_replace('_',' ',$value)).",";
    }
    
    if ($print == TRUE) {
        echo $echo;
    } else {
        return $echo;
    }
    
}
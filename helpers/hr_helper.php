<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function generate_employee_number_prefix($institute) {
    
    $CI= & get_instance();
    
    $employee_code_rules = get_option(array( 'option' => 'employee_code_generation'));
    $rules = json_decode($employee_code_rules,TRUE);
    
    $prefix = $rules['prefix'];
    if (isset($rules['institute_code_in_prefix']) && $rules['institute_code_in_prefix'] == 'yes'){
        $CI->load->model('model_institutes');
        $institute_details = $CI->model_institutes->get_institute_by_id($institute);
        $inst_code = $institute_details['employee_no_prefix'];
        $prefix .= $inst_code;
    }
    return $prefix;   
}

function generate_employee_number($emp_no_gen_array) {
//    {"rule_name" : "auto","rule" : "{{EMPNOPREFIX}}{{EMPDEPARTMENT}}{{ID}}", "join_year_length" : 2,"id_length" :3,"check_prefix":"TRUE"}
    
    
    
    $CI = & get_instance();
    
    $employee_code_rules = get_option(array('option' => 'employee_code_generation'));
    $rules = json_decode($employee_code_rules,TRUE);
    
    $rule = $rules['rule'];
    $search_words = array("{{EMPNOPREFIX}}","{{YEAROFJOINING}}","{{EMPCATEGORY}}","{{EMPDEPARTMENT}}","{{ID}}");
    
    $rule_contain = get_string_contain_words_in_array($rule,$search_words);
    
    $prefix = '';
    
    //do not change the order
    if (in_array("{{EMPNOPREFIX}}", $rule_contain)) {
        $CI->load->model('model_institutes');
        $institute_details = $CI->model_institutes->get_institute_by_id($emp_no_gen_array['institute_id']);
        $emp_prefix_code = $institute_details['employee_no_prefix'];
        $rule = str_replace('{{EMPNOPREFIX}}',$emp_prefix_code, $rule);
     
    }
    
    if (in_array("{{YEAROFJOINING}}", $rule_contain)) {
        if(isset($emp_no_gen_array['joining_date']) && $emp_no_gen_array['joining_date']) {
            $join_date = to_date_format($emp_no_gen_array['joining_date']);
            $date = strtotime($join_date);
            $join_year = date("Y",$date);
        } else if (isset($emp_no_gen_array['joining_year']) && $emp_no_gen_array['joining_year']) {
            $join_year = $emp_no_gen_array['joining_year'];
        } else {
            $join_year = '';
        }
        
        if (isset($rules['join_year_length']) && $rules['join_year_length'] && $join_year){
            
            $join_year_length = $rules['join_year_length'];
            $join_year = substr($join_year, -$join_year_length);
        }
        
        $rule = str_replace('{{YEAROFJOINING}}',$join_year, $rule);
   
    }
       
    if (in_array("{{EMPCATEGORY}}", $rule_contain)) {
        $CI->load->model('model_employee');
        $category_details = $CI->model_employee->get_employee_categories_by_id($emp_no_gen_array['category_id']);
        $category_code = $category_details['category_code'];
        $rule = str_replace('{{EMPCATEGORY}}',$category_code, $rule);
    
    }
    if (in_array("{{EMPDEPARTMENT}}", $rule_contain)) {
        $CI->load->model('model_department');
        $department_details = $CI->model_department->get_department_by_id($emp_no_gen_array['department_id']);
        $department = $department_details['department'];
        
        $rule = str_replace('{{EMPDEPARTMENT}}',$department, $rule);
    
    }
    
    if (in_array("{{ID}}", $rule_contain)) {
        $str_length = isset($rules['id_length']) && $rules['id_length'] ? $rules['id_length'] : 0 ;
        
        $check_prefix = isset($rules['check_prefix']) && $rules['check_prefix'] ? $rules['check_prefix'] : "TRUE" ;
        
        $CI->load->model('model_employee');
        
        $prefix = str_replace('{{ID}}','', $rule);
        
        if ($check_prefix == "TRUE"){
            
            $row = $CI->model_employee->get_employee_maxOf_max_id($emp_no_gen_array['institute_id'],$prefix);
        } else {
          
            $row = $CI->model_employee->get_employee_maxOf_max_id();
        }
        
        if($row) {
            $next_number = $row['max_id']+1;
        } else {
             $next_number = 1;   
        }
        
        $e_number = str_pad($next_number,$str_length,"0",STR_PAD_LEFT);
        
        $rule = str_replace('{{ID}}',$e_number, $rule);
    }
    
    $emp_number['emp_number'] = $rule;
    $emp_number['prefix_id'] = $prefix;
    $emp_number['max_id'] = $next_number;
    
    return $emp_number;
}


function generate_employee_number11($institute) {
    
    $CI = & get_instance();
    
    $prefix = generate_employee_number_prefix($institute);
    
    $employee_code_rules = get_option(array('option' => 'employee_code_generation'));
    $rules = json_decode($employee_code_rules,TRUE);
    
    $str_length = isset($rules['length']) && $rules['length'] ? $rules['length'] : 0 ;
    
    $CI->load->model('model_employee');
    $row = $CI->model_employee->get_employee_maxOf_max_id($institute,$prefix);
    
    if ($row) {
        $next_number = $row['max_id'] + 1;
    } else {
        $next_number = 1;
    }
    $e_number = str_pad($next_number, $str_length , "0", STR_PAD_LEFT);
    
    $emp_number['emp_number'] = $prefix.$e_number;
    $emp_number['prefix_id'] = $prefix;
    $emp_number['max_id'] = $next_number;
    return $emp_number;
}


function echo_employee_categories($institute,$select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_employee');
    $categories = $CI->model_employee->get_employee_categories($institute);
    
    if ($select) {
       $select = is_array($select) ? $select : explode(' ', $select);
    }
    
    
    foreach ($categories as $row) {
        if ($select && in_array($row['id'], $select)) {
            $echo .= "<option selected value='$row[id]'>$row[category_name]</option>";
        } else {
            $echo .= "<option  value='$row[id]'>$row[category_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_employee_category_employees($institute,$type,$select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_employee');   
    $employees = $CI->model_employee->get_employee_by_category_type($institute,$type);
    foreach ($employees as $row){
        if ($select == $row['id']) {
            $echo .= "<option selected value='$row[id]'>$row[first_name]</option>";
        } else {
            $echo .= "<option  value='$row[id]'>$row[first_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_user_institutes_employee_category_employees($institutes,$employee_category,$select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_employee');   
    $institute_ids = array_column($institutes, 'id');
    $employees = $CI->model_employee->get_employee_by_category_type($institute_ids,$employee_category);
    
    $last_loaded_institute = '';
    $group_count = 0;
    $count = 0;
    foreach ($employees as $emp) {
        
        if ($last_loaded_institute != $emp['institute_id']){
            $group_count++;
              if($count != 0){ 
                  $echo .= "</optgroup>";
              }
            $echo .= "<optgroup label='$emp[institute_name]'>";  
        }
       
        if ($select == $emp['id']) {
            $echo .= "<option selected value='$emp[id]'>$emp[first_name]</option>";
        } else {
            $echo .= "<option value='$emp[id]'>$emp[first_name]</option>";
        }
        
        $last_loaded_institute=$emp['institute_id'];
        $count++;
    }
    
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_employee_grade($institute = false, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_employee_grade');
    $employee_grades = $CI->model_employee_grade->get_employee_grades_by_institute_id($institute);
    foreach ($employee_grades as $employee_grade) {
        if ($select == $employee_grade['id']) {
            $echo .= "<option selected value='$employee_grade[id]'>$employee_grade[grade_name]</option>";
        } else {
            $echo .= "<option value='$employee_grade[id]'>$employee_grade[grade_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_employee_salary_category($institute = false, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_employee_salary_category');
    $salary_categories = $CI->model_employee_salary_category->get_employee_salary_category_by_institute_id($institute);
    foreach ($salary_categories as $category) {
        if ($select == $category['id']) {
            $echo .= "<option selected value='$category[id]'>$category[category_name]</option>";
        } else {
            $echo .= "<option value='$category[id]'>$category[category_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_employee_job_title($institute,$select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_employee_job_title');
    $job_titles = $CI->model_employee_job_title->get_employee_job_title_by_institute_id($institute);
    foreach ($job_titles as $job_title) {
        if ($select == $job_title['job_title']) {
            $echo .= "<option selected value='$job_title[job_title]'>$job_title[job_title]</option>";
        } else {
            $echo .= "<option value='$job_title[job_title]'>$job_title[job_title]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_institute_departments($institute, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_department');
    $departments = $CI->model_department->get_department_by_institute_id($institute);
    
    if ($select) {
       $select = is_array($select) ? $select : explode(' ', $select);
    }
    
    foreach ($departments as $dept) {
        if ($select && (in_array($dept['id'], $select))) {
            $echo .= "<option selected value='$dept[id]'>$dept[department]</option>";
        } else {
            $echo .= "<option value='$dept[id]'>$dept[department]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_employee_shifts($institute, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_shifts');
    $shifts = $CI->model_shifts->get_shifts_by_institute_id($institute);
    foreach ($shifts as $shift) {
        if ($select == $shift['id']) {
            $echo .= "<option selected value='$shift[id]'>$shift[shift_name]</option>";
        } else {
            $echo .= "<option value='$shift[id]'>$shift[shift_name]</option>";
        }
    }
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_payroll_due_categories($institute, $select = false,$print = true){
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_payroll');
    $categories = $CI->model_payroll->get_payroll_categories_by_due_category($institute,$due_category = TRUE);
    foreach ($categories as $category) {
        if ($select == $category['id']) {
            $echo .= "<option selected value='$category[id]'>$category[payroll_category_name]</option>";
        } else {
            $echo .= "<option value='$category[id]'>$category[payroll_category_name]</option>";
        }
    }
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_publications($select = false,$print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_employee_publications');
    $publications = $CI->model_employee_publications->get_publications();
    foreach ($publications as $publication) {
        if ($select == $publication['id']) {
            $echo .= "<option selected value='$publication[id]' rel='$publication[publication_type]'>$publication[publication_type]</option>";
        } else {
            $echo .= "<option value='$publication[id]' rel='$publication[publication_type]'>$publication[publication_type]</option>";
        }
    }
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_employee_courses($institute=false,$employee,$select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_employee');
    $emp_courses = $CI->model_employee->get_employee_courses_by_institute_employee($institute,$employee);
    foreach ($emp_courses as $emp_cou) {
        if ($select == $emp_cou['course_id']) {
            $echo .= "<option selected value='$emp_cou[course_id]'>$emp_cou[course_name]</option>";
        } else {
            $echo .= "<option value='$emp_cou[course_id]'>$emp_cou[course_name]</option>";
        }
        
    }
    
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}


function echo_institute_course_employees($course_id,$select = FALSE,$print = TRUE) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_employee');
    
    if ($select) {
       $select = is_array($select) ? $select : explode(' ', $select);
    }
    
    $employees = $CI->model_employee->get_employees_by_institute_course($course_id);
    foreach ($employees as $emp) {
        if (in_array($emp['id'], $select)) {
            $echo .= "<option selected value='$emp[id]'>$emp[full_name]</option>";
        } else {
            $echo .= "<option value='$emp[id]'>$emp[full_name]</option>";
        }
        
    }
    
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    
    
}
function echo_institute_biometric_machines($institute_id=false,$select = FALSE,$print = TRUE) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_biometric_machine');
       
    $machines = $CI->model_biometric_machine->get_machines_by_institute($institute_id);
   
    foreach ($machines as $machine) {
        if ($select == $machine['machine_id']) {
            $echo .= "<option selected value='$machine[machine_id]'>$machine[machine_name]-$machine[machine_code]</option>";
        } else {
            $echo .= "<option value='$machine[machine_id]'>$machine[machine_name]-$machine[machine_code]</option>";
        }
        
    }
    
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    
    
}

function echo_institute_employees($institute_id,$select = FALSE,$print = TRUE){
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_employee');
    if ($select) {
       $select = is_array($select) ? $select : explode(' ', $select);
    }

    $employees=$CI->model_employee->get_employees_by_institute($institute_id);
    
    foreach($employees as $employee){
        if ($select!=FALSE && (in_array($employee['employee_id'], $select))) {
       
            $echo .= "<option selected value='$employee[employee_id]'>$employee[full_name]-$employee[employee_number]</option>";
        } else {
            $echo .= "<option value='$employee[employee_id]'>$employee[full_name]-$employee[employee_number]</option>";
        }
    }
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_emp_attendance_time_ranges($institute_id,$select = FALSE,$print = TRUE) {
    $CI = & get_instance();
    $CI->load->model('model_employee_attendance');
    $time_ranges = $CI->model_employee_attendance->get_employee_attendance_time_ranges($institute_id);
    
    $echo = '';
    
    foreach($time_ranges as $t_range){
        
        $start_time = date( 'g:i A', strtotime($t_range['start_time']));
        $end_time = date( 'g:i A', strtotime($t_range['end_time']));
        
        if ($t_range['id'] == $select) {
       
            $echo .= "<option selected value='$t_range[id]'>$start_time - $end_time</option>";
        } else {
            $echo .= "<option value='$t_range[id]'>$start_time - $end_time</option>";
        }
    }
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_employee_leave_ranges($institute,$year,$select = FALSE,$print = TRUE){
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_employee_leave_types');
//    

    $leave_ranges=$CI->model_employee_leave_types->get_employee_leave_ranges($institute,$year);
    
    foreach($leave_ranges as $range){
        $pay_range = $range['pay_period_from']." - ".$range['pay_period_to'];
        if ($pay_range == $select) {
       
            $echo .= "<option selected value='$pay_range'>$pay_range</option>";
        } else {
            $echo .= "<option value='$pay_range'>$pay_range</option>";
        }
    }
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_employee_leave_groups($institute,$select = FALSE,$print = TRUE){
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_leave_hierarchy');
//    

    $leave_groups = $CI->model_leave_hierarchy->get_employee_leave_groups($institute);
    
    foreach($leave_groups as $leave_group){
        if ($leave_group['id'] == $select) {
            $echo .= "<option selected value='$leave_group[id]'>$leave_group[name]</option>";
        } else {
            $echo .= "<option value='$leave_group[id]'>$leave_group[name]</option>";
        }
    }
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function get_filtered_dates_by_holiday($from_date,$to_date,$institute,$leave_type_id,$leave_group_id=false) {
    $CI = & get_instance();
    $CI->load->model('model_holidays_vacations');
    $CI->load->model('model_leave_entitlement');
    $rules = $CI->model_leave_entitlement->get_employee_leave_entitlement($leave_type_id);
    $enable_holiday_leave = $rules['enable_holiday_leave'];
    
    $filtered_dates = array();
    while (strtotime($from_date) <= strtotime($to_date)) {
        
            if(isset($enable_holiday_leave) && $enable_holiday_leave==1) {
                $filtered_dates[] = $from_date;
            } else {
                
                ///check date is holiday 
            $leave_group_holiday_isset = $CI->model_holidays_vacations->check_holiday_set_for_leave_group($leave_group_id);
            if(isset($leave_group_holiday_isset)) {
                $is_not_holiday = $CI->model_holidays_vacations->check_holiday_set($institute,$from_date,'employee',$leave_group_id);
            } else {
                $is_not_holiday = $CI->model_holidays_vacations->check_holiday_set($institute,$from_date,'employee');
            }

                if ($is_not_holiday == TRUE) {
                    $filtered_dates[] = $from_date;
                }
            }
        
        $from_date = date ("Y-m-d", strtotime("+1 day", strtotime($from_date)));
    }
    return $filtered_dates;
}

function get_leave_application_period($fil_date,$period,$leave_type_id) {
    
    $CI = & get_instance();
    $CI->load->model('model_leave_entitlement');
    $leave_period = $CI->model_leave_entitlement->get_leave_processed_date_range($leave_type_id,$period,$fil_date);
    if(isset($leave_period) && $leave_period) {
        $start_date = $leave_period['date'];
    } else {
        $start_date = "";
    }
    return $start_date;
}

function get_previous_leave_application_period($fil_date,$period) {
    
    $f_month = date('m', strtotime($fil_date));
    $f_day = date('d', strtotime($fil_date));
    $f_year = date('Y', strtotime($fil_date));
    $date = "01-".$f_month."-".$f_year;
    
    if($period=='monthly') {
        
        $month = date('m', strtotime($date.'+-1 months'));
        $year = isset($month) && $month=='12'?$f_year-1:$f_year;
        $start_date = $year."-".$month."-01";
        
    } else if($period=='annual') {
        
        $year = $f_year-1;
        $start_date = $year."-".$f_month."-01";
        
    } else if($period=='half_yearly') {
          
        $pre_date = strtotime(date("Y-m-d", strtotime($fil_date)) . " -6 month");
        $pre_date_month = date("m",$pre_date);

        if($f_month>="07" && $f_month<="12") {
            $month = $pre_date_month;
            $year = $f_year;
        } else if($f_month>="01" && $f_month<="06") {
            $month = $pre_date_month;
            $year = $f_year-1;
        }

        $start_date = $year."-".$month."-01";
        
    } else if($period=='quarterly') {
        
        $pre_date = strtotime(date("Y-m-d", strtotime($fil_date)) . " -3 month");
        $pre_date_month = date("m",$pre_date);
        
        if($f_month>="04" && $f_month<="12") {
            $month = $pre_date_month;
            $year = $f_year;
        } else if($f_month>="01" && $f_month>="03") {
            $month = $pre_date_month;
            $year = $f_year-1;
        }
        
        $start_date = $year."-".$month."-01";
    }
    return $start_date;
}

function echo_institute_leave_types($institute,$select = FALSE,$print = TRUE) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_employee_leave_types');

    $leave_types = $CI->model_employee_leave_types->get_employee_leave_types($institute);
    
    foreach($leave_types as $leave_type){
        if ($leave_type['id'] == $select) {
            $echo .= "<option selected value='$leave_type[id]'>$leave_type[leave_name]</option>";
        } else {
            $echo .= "<option value='$leave_type[id]'>$leave_type[leave_name]</option>";
        }
    }
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}


function get_balance($leave_type_id,$employee_id,$from_date,$duration=false) { 
    
    $CI = & get_instance();
    $CI->load->model('model_leave_entitlement');
    $CI->load->model('model_employee_leave_types');
    
    $leave_type_info = $CI->model_leave_entitlement->get_employee_leave_entitlement($leave_type_id);
    $period = $leave_type_info['period'];
    $entitle_id = $leave_type_info['id'];
    
    
    // check override deduct created
    $override_leaves = $CI->model_leave_entitlement->get_employee_override_leaves($entitle_id);
    $balance_count = array();
    $duration = isset($duration) && $duration=='half_day'?".5":"1";
    //get balance count from override leave type
    if(isset($override_leaves) && $override_leaves) {
        foreach ($override_leaves as $leave) {
            $override_leave_info = $CI->model_leave_entitlement->get_employee_leave_entitlement($leave['leave_type_id']);
            $override_period = $override_leave_info['period'];
            $f_date = get_leave_application_period($from_date,$override_period,$leave['leave_type_id']);
            $leave_period = $CI->model_leave_entitlement->get_leave_processed_date_range($leave['leave_type_id'],$override_period,$from_date);
            $leave_count = $CI->model_leave_entitlement->check_employee_leave_process_balance_set($leave['leave_type_id'],$employee_id,$override_period,$leave_period['leave_month']);
            
            if(isset($leave_count['balance']) && $leave_count['balance'] >= $duration) {
                $balance_count['balance'] = $balance = $leave_count['balance'];
                $balance_count['id'] = $id = $leave_count['id'];
                $balance_count['process_set'] = $process_set = true;
                $balance_count['leave_type'] = $leave_count['type']; 
                $balance_count['leave_type_id'] = $leave_count['leave_type_id']; 
                $balance_count['lop_rule'] = $override_leave_info['lop_rule']; 
                $balance_count['apply_leave_on_no_balance'] = $leave_type_info['apply_leave_on_no_balance'];
                break;
            } else {
                $leave_period = $CI->model_leave_entitlement->get_leave_processed_date_range($leave['leave_type_id'],$override_period,$from_date);
                $leave_count = $CI->model_leave_entitlement->check_employee_leave_balance_set($leave['leave_type_id'],$employee_id,$override_period,$leave_period['leave_month']);
                if(isset($leave_count)  && $leave_count['balance'] >= $duration) {
                    $balance_count['balance'] = $balance = $leave_count['balance'];
                    $balance_count['id'] = $id = $leave_count['id'];
                    $balance_count['process_not_set'] = $process_not_set = true;
                    $balance_count['leave_type'] = $leave_count['type']; 
                    $balance_count['leave_type_id'] = $leave_count['leave_type_id']; 
                    $balance_count['lop_rule'] = $override_leave_info['lop_rule']; 
                    $balance_count['apply_leave_on_no_balance'] = $leave_type_info['apply_leave_on_no_balance'];
                    break;
                }
                
            }
           
        }
    } 
    
    //get balance count if override leave not set
    if(!isset($balance)) {
        $f_date = get_leave_application_period($from_date,$period,$leave_type_id);
        $c_leave_period = $CI->model_leave_entitlement->get_leave_processed_date_range($leave_type_id,$period,$from_date);
        $check_from_balance = $CI->model_leave_entitlement->check_employee_leave_process_balance_set($leave_type_id,$employee_id,$period,$c_leave_period['leave_month']);
       
        if(isset($check_from_balance)) {
            $balance_count['balance'] = $balance = $check_from_balance['balance'];
            $balance_count['id'] = $id = $check_from_balance['id'];
            $balance_count['process_set'] = $process_set = true;
            $balance_count['leave_type'] = $check_from_balance['type']; 
            $balance_count['leave_type_id'] = $check_from_balance['leave_type_id']; 
            $balance_count['lop_rule'] = $leave_type_info['lop_rule']; 
            $balance_count['apply_leave_on_no_balance'] = $leave_type_info['apply_leave_on_no_balance'];
        } else {
            $check_leave_period = $CI->model_leave_entitlement->get_leave_processed_date_range($leave_type_id,$period,$from_date);
            $check_balance_date_set = $CI->model_leave_entitlement->check_employee_leave_balance_set($leave_type_id,$employee_id,$period,$check_leave_period['leave_month']);
            if(isset($check_balance_date_set)) { 
                $balance_count['balance'] = $balance = $check_balance_date_set['balance'];
                $balance_count['id'] = $id = $check_balance_date_set['id'];
                $balance_count['process_not_set'] = $process_not_set = true;
                $balance_count['leave_type'] = $check_balance_date_set['type'];
                $balance_count['leave_type_id'] = $check_balance_date_set['leave_type_id']; 
                $balance_count['lop_rule'] = $leave_type_info['lop_rule']; 
                $balance_count['apply_leave_on_no_balance'] = $leave_type_info['apply_leave_on_no_balance'];
            } else {
                $leave_info = $CI->model_employee_leave_types->get_employee_leave_types_by_id($leave_type_id);
                $balance_count['apply_leave_on_no_balance'] = $leave_type_info['apply_leave_on_no_balance'];
                $balance_count['leave_type'] = $leave_info['type'];
                $balance_count['leave_type_id'] = $leave_info['id'];
                $balance_count['balance'] = 0;
            }
        }
        
    }
    
    return $balance_count;
}


function generate_employee_application_id($institute_id) {
    
    $CI= & get_instance();
    $CI->load->model('model_employee_leave_applications');
    
    $prefix = 'L';
    
    
    $row = $CI->model_employee_leave_applications->get_emp_leave_application_maxOf_max_id($institute_id,$prefix);
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

function get_period_last_date($date,$period,$institute_id){
    
//    $month = date('m', strtotime($date));
//    $year = date('Y', strtotime($date));
    $CI= & get_instance();
    $CI->load->model('model_leave_entitlement');
    
//    $leave_settings = $CI->model_leave_entitlement->get_leave_settings($institute_id);
//    if(isset($leave_settings) && $leave_settings) {
//        $array = explode("/",$leave_settings['start_day_and_month']);
//        $l_day = $array[0];
//        $l_month = $array[1];
//    } else {
//        $l_day = "01";
//        $l_month = "01";
//    }
    
//    $start_date = $year."-".$l_month."-".$l_day;
    $last_date = get_end_date_from_period_and_startdate($date,$period);
    
    return $last_date;
}


function add_leave_balance($leave_type_id,$employee_id,$date,$duration=false) {
    $CI = & get_instance();
    $CI->load->model('model_leave_entitlement');
    $CI->load->model('model_employee_leave_types');
    
    $leave_type_info = $CI->model_leave_entitlement->get_employee_leave_entitlement($leave_type_id);
    $period = $leave_type_info['period'];
    $duration = isset($duration) && $duration=='half_day'?".5":"1";
    $f_date = get_leave_application_period($date,$period,$leave_type_id);
    //get leave month    
    $leave_period = $CI->model_leave_entitlement->get_leave_processed_date_range($leave_type_id,$period,$date); 
                  
    $check_balance_set = $CI->model_leave_entitlement->check_employee_leave_process_balance_set($leave_type_id,$employee_id,$period,$leave_period['leave_month']);
    if(isset($check_balance_set) && $check_balance_set) {
        $balance = isset($check_balance_set['balance'])?$check_balance_set['balance']:0;
        $id = $check_balance_set['id'];
        $update['balance'] = $balance+$duration;
        $CI->model_leave_entitlement->update_process_history($update,$id);
    } else {
        $check_balance_date_set = $CI->model_leave_entitlement->check_employee_leave_balance_set($leave_type_id,$employee_id);
        if(isset($check_balance_date_set) && $check_balance_date_set) {
            $balance = isset($check_balance_date_set['balance'])?$check_balance_date_set['balance']:0;
            $id = $check_balance_date_set['id'];
            $update['balance'] = $balance+$duration;
            $CI->model_leave_entitlement->update_initial_balance($update,$id);
        }
    }
}

function decrement_leave_balance($leave_type_id,$employee_id,$date,$duration=false) {
    $CI = & get_instance();
    $CI->load->model('model_leave_entitlement');
    $CI->load->model('model_employee_leave_types');
    
    $leave_type_info = $CI->model_leave_entitlement->get_employee_leave_entitlement($leave_type_id);
    $period = $leave_type_info['period'];
    $duration = isset($duration) && $duration=='half_day'?".5":"1";
    $f_date = get_leave_application_period($date,$period,$leave_type_id);
    $leave_period = $CI->model_leave_entitlement->get_leave_processed_date_range($leave_type_id,$period,$date);
    $check_balance_set = $CI->model_leave_entitlement->check_employee_leave_process_balance_set($leave_type_id,$employee_id,$period,$leave_period['leave_month']);
    if(isset($check_balance_set) && $check_balance_set) {
        $update['balance'] = isset($check_balance_set['balance']) && ($check_balance_set['balance']-$duration)>0?($check_balance_set['balance']-$duration):0;
        $id = $check_balance_set['id'];
        $CI->model_leave_entitlement->update_process_history($update,$id);
    } else {
        $check_balance_date_set = $CI->model_leave_entitlement->check_employee_leave_balance_set($leave_type_id,$employee_id);
        if(isset($check_balance_date_set) && $check_balance_date_set) {
            $update['balance'] = isset($check_balance_date_set['balance']) && ($check_balance_date_set['balance']-$duration)>0?($check_balance_date_set['balance']-$duration):0;
            $id = $check_balance_date_set['id'];
            $CI->model_leave_entitlement->update_initial_balance($update,$id);
        }
    }
}

function check_required_leave_balance_isset($leave_id) {
    $CI = & get_instance();
    $CI->load->model('model_leave_entitlement');
    $CI->load->model('model_employee_leave_types');
    $CI->load->model('model_employee_leave_applications');
    $CI->load->model('model_holidays_vacations');
    $CI->load->model('model_employee');
    $application_info = $CI->model_employee_leave_applications->get_leave_application_by_id($leave_id);
    $status = FALSE;
    if($application_info) {
        $from_date = $application_info['from_date'];
        $to_date = $application_info['to_date'];
        $half_day = $application_info['from_half_day'];
        $leave_type_id = $application_info['leave_type_id'];
        $employee_id = $application_info['employee_id'];
        $employee_info = $CI->model_employee->get_employee($employee_id);
        $leave_type_info = $CI->model_leave_entitlement->get_employee_leave_entitlement($leave_type_id);
        $leave_group = $employee_info['leave_group_id'];
        $institute = $employee_info['institute_id'];
        $period = $leave_type_info['period'];
        
        //leave balance
        
        $f_date = get_leave_application_period($from_date,$period,$leave_type_id);
        $leave_period = $CI->model_leave_entitlement->get_leave_processed_date_range($leave_type_id,$period,$from_date);
        $leave_count = $CI->model_leave_entitlement->check_employee_leave_process_balance_set($leave_type_id,$employee_id,$period,$leave_period['leave_month']);
 

        if(isset($leave_count) && $leave_count) {
            $balance = isset($leave_count['balance'])?$leave_count['balance']:0;
            $leave_isset = TRUE;
        } else {
            $leave_count = $CI->model_leave_entitlement->check_employee_leave_balance_set($leave_type_id,$employee_id);
            if(isset($leave_count) && $leave_count) {
                $balance = isset($leave_count['balance'])?$leave_count['balance']:0;
                $leave_isset = TRUE;
            } 

        }

        if($leave_type_info['apply_leave_on_no_balance']==1) {
            $status = isset($leave_isset) && $leave_isset==TRUE?TRUE:FALSE;
        } else {
            if(isset($to_date) && $to_date!="0000-00-00") {
                $filtered_dates = get_filtered_dates_by_holiday($from_date,$to_date,$institute,$leave_type_id,$leave_group);
                $leaves_applied = count($filtered_dates);
            } else {
//                if(isset($enable_holiday_leave) && $enable_holiday_leave==0) {
//                    $leave_group_holiday_isset = $this->model_holidays_vacations->check_holiday_set_for_leave_group($leave_group);
//                    if(isset($leave_group_holiday_isset)) {
//                        $is_not_holiday = $this->model_holidays_vacations->check_holiday_set($institute,$from_date,'employee',$leave_group);
//                    } else {
//                        $is_not_holiday = $this->model_holidays_vacations->check_holiday_set($institute,$from_date,'employee');
//                    }
//                }
                $leaves_applied = isset($half_day) && $half_day==1?".5":"1";
            }
            
            //check balance required isset
            $status = isset($balance) && $balance>=$leaves_applied?TRUE:FALSE;
            
        }
        
    }
    return $status;
}

function get_applied_leave_count($application_id) {
    
    $CI = & get_instance();
    $CI->load->model('model_employee_leave_applications');
    $CI->load->model('model_employee');
    $application_info = $CI->model_employee_leave_applications->get_leave_application_by_id($application_id);
    
    if($application_info) {
        
        $from_date = $application_info['from_date'];
        $to_date = $application_info['to_date'];
        $half_day = $application_info['from_half_day'];
        $employee_id = $application_info['employee_id'];
        $leave_type_id = $application_info['leave_type_id'];
        $employee_info = $CI->model_employee->get_employee($employee_id);
        $leave_group = $employee_info['leave_group_id'];
        $institute = $employee_info['institute_id'];

        if(isset($half_day) && $half_day==1) {
            $count = .5;
        } else if(isset ($to_date) && $to_date=='0000-00-00') {
            $count = 1;
        } else {
            $filtered_dates = get_filtered_dates_by_holiday($from_date,$to_date,$institute,$leave_type_id,$leave_group);
            $count = count($filtered_dates);
        }

    }
    return $count;
}

function leave_notification($apply_id,$leave_status=false,$leave_date_id=false) {
    
    $CI = & get_instance();
    $CI->load->model('model_employee_leave_applications');
    $CI->load->model('model_employee');
    $CI->load->model('model_users');
    $application_info = $CI->model_employee_leave_applications->get_leave_application_details($apply_id);
    $status = $application_info['status'];
    $employee_id = $application_info['employee_id'];
    $application_history = $CI->model_employee_leave_applications->get_leave_application_history($apply_id);
    $notify_users = $CI->model_employee_leave_applications->get_leave_application_notification_receivers($apply_id);
    $leave_dates = $CI->model_employee_leave_applications->get_employee_leave_dates($apply_id);
    
    $employee_info = $CI->model_employee->get_employee_with_employee_id($employee_id);
    $from_date = to_print_date_format($application_info['from_date']);
    $to_date = isset($application_info['to_date']) && $application_info['to_date'] != "0000-00-00"?" - ".to_print_date_format($application_info['to_date']):"";
    $users = array();
    if($status=='applied') {
        
       if(isset($leave_status) && $leave_status==false) {
            //for next approver
            if($application_info['approve_user_type']=='user') {
                $users[] = $application_info['approve_id'];
            } else {
                $CI->load->model('model_department');
                $hods = $CI->model_department->get_department_hod($application_info['approve_id']);
                if(isset($hods)) {
                    foreach ($hods as $hod) {
                        $users[] = $hod['user_id'];
                    }
                }
            }
       }    
        //if status is applied and no user approved/denied leave, get notify users
        if((isset($application_history) && !$application_history) ||  (isset($leave_status) && $leave_status=='deleted')) {
            
            if(isset($leave_status) && $leave_status=='deleted') {
                $action_performed_by = $employee_info['user_id'];
            }
            
            $notify_users = $CI->model_employee_leave_applications->get_leave_application_notification_receivers($apply_id);
            
            if(isset($notify_users) && $notify_users) {
                foreach ($notify_users as $user) {
                    $users[] = $user['user_id'];
                }
            }
        }
        
        
        
    } else  {
        if($status=='approved' || $status=='denied' || $status=='cancelled') {
            
            $users[] = $employee_info['user_id'];
            
            if(isset($leave_status) && $leave_status=='cancelled') {
                foreach ($leave_dates as $l_date) {
                    if($l_date['id']==$leave_date_id) {
                        $cancelled_date = to_print_date_format($l_date['leave_date']);
                        $action_performed_by = $l_date['denied_by'];
                    }
                }
            } else if($status=='cancelled') {
                $action_performed_by = $leave_dates[0]['denied_by'];
            } else {
                $action_performed_by = $application_info['approved_by'];
            }
            $action_user_info = $CI->model_users->get_user_details_by_user_id($action_performed_by);
        } 
        $notify_users = $CI->model_employee_leave_applications->get_leave_application_notification_receivers($apply_id);

        if(isset($notify_users) && $notify_users) {
            foreach ($notify_users as $user) {
                $users[] = $user['user_id'];
            }

        }
        
    } 
    
    $users = array_unique($users);
    foreach ($users as $user) {
        $user_info = $CI->model_users->get_user_details_by_user_id($user);
        
        $email = $user_info['email'];
        if($status=='applied') {
            //if leave is deleted by applied employee
            if(isset($leave_status) && $leave_status=='deleted') {
                $employee_name = $user_info['id']==$employee_info['user_id']?"your":$employee_info['full_name']."'s";
                
                $subject = "Leave Request Status ".$application_info['application_id']." ".$employee_info['full_name'];
                $message = '<p>Dear '.$user_info['full_name'].',</p>'
                . '<p>'.$employee_info['full_name']." has deleted his/her leave request, find the leave details below,"
                . '<br> '
                . 'Employee name : '.$employee_info['full_name'].'<br>'
                . 'Employee code : '.$employee_info['employee_number'].'<br>'
                . 'Department    : '.$employee_info['department'].'<br>'
                . 'Leave Type    : '.$application_info['leave_name'].'<br>'        
                . 'Leave Date    : '.$from_date.$to_date.'</p>';
            } else {
                //for leave approval
                $subject = "Leave Request ".$application_info['application_id']." ".$employee_info['full_name'];
                $message = '<p>Dear '.$user_info["full_name"].',</p>'
                . '<p>'.$employee_info['full_name'].' has  applied for leave, find the leave details below,'
                . '<br> '
                . 'Employee name : '.$employee_info['full_name'].'<br>'
                . 'Employee code : '.$employee_info['employee_number'].'<br>'
                . 'Department    : '.$employee_info['department'].'<br>'
                . 'Leave Type    : '.$application_info['leave_name'].'<br>'        
                . 'Leave Date    : '.$from_date.$to_date.'</p>';
            }
        } else {
            if(isset($leave_status) && $leave_status=='cancelled') {
                
                $employee_name = $user_info['id']==$employee_info['user_id']?"your":$employee_info['full_name']."'s";
                $subject = "Leave Request Status ".$application_info['application_id']." ".$employee_info['full_name'];
                $message = '<p>Dear '.$user_info['full_name'].',</p>'
                . '<p>'.$action_user_info['full_name']." has cancelled ".$employee_name." leave for the date ".$cancelled_date.", find the leave details below,"
                . '<br> '
                . 'Employee name : '.$employee_info['full_name'].'<br>'
                . 'Employee code : '.$employee_info['employee_number'].'<br>'
                . 'Department    : '.$employee_info['department'].'<br>'
                . 'Leave Type    : '.$application_info['leave_name'].'<br>'        
                . 'Leave Date    : '.$from_date.$to_date.'</p>';
                
            } else {
                
                $employee_name = $user_info['id']==$employee_info['user_id']?"your":$employee_info['full_name']."'s";
                $subject = "Leave Request Status ".$application_info['application_id']." ".$employee_info['full_name'];
                $message = '<p>Dear '.$user_info['full_name'].',</p>'
                . '<p>'.$action_user_info['full_name']." has ". $application_info['status']." ".$employee_name." leave request, find the leave details below,"
                . '<br> '
                . 'Employee name : '.$employee_info['full_name'].'<br>'
                . 'Employee code : '.$employee_info['employee_number'].'<br>'
                . 'Department    : '.$employee_info['department'].'<br>'
                . 'Leave Type    : '.$application_info['leave_name'].'<br>'        
                . 'Leave Date    : '.$from_date.$to_date.'</p>';
                
            }
        }
        if(isset($email) && $email) {
            $send_email['email'] = $email;
            $send_email['subject'] = $subject;
            $send_email['message'] = $message;
            if(isset($rec_institute_id)){
            $send_email['institute_id'] = $rec_institute_id;
            }
            send_leave_notification($send_email);
        }
    }
  
}

function send_leave_notification($send_email) {
    $CI = & get_instance();
    $CI->load->library('mailer');
    $email_options['theme']=TRUE;
    if(isset($send_email['institute_id'])){
    $email_options['institute_id']=$send_email['institute_id'];
    }
    $CI->mailer->send_mail($send_email['email'],$send_email['subject'],$send_email['message'],$email_options);
}
function echo_employee_qualification_type($institute = false, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_employee_qualification_type');
    $qualification_types = $CI->model_employee_qualification_type->get_employee_qualification_type_by_institute_id($institute);
    foreach ($qualification_types as $type) {
        if ($select == $type['id']) {
            $echo .= "<option selected value='$type[id]'>$type[qualification_type]</option>";
        } else {
            $echo .= "<option value='$type[id]'>$type[qualification_type]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}
function echo_employee_experience_type($institute = false, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_employee_experience_type');
    $experience_types = $CI->model_employee_experience_type->get_employee_experience_type_by_institute_id($institute);
    foreach ($experience_types as $type) {
        if ($select == $type['id']) {
            $echo .= "<option selected value='$type[id]'>$type[experience_type]</option>";
        } else {
            $echo .= "<option value='$type[id]'>$type[experience_type]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_employee_batches_by_year($employee,$year=false, $course=false, $select = false, $all_batches = FALSE,$print = true) {
    
   
     $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_batches');
   // $show_adm_year_in_batch = show_adm_year_in_batch_name($institute);
//     if($category){
//            if ($all_batches == TRUE) {
//                         $batches = $CI->model_batches->get_batch_by_course_category($institute,$year,$course,'',$category);
//                } else {
//                    $batches = $CI->model_batches->get_batch_by_course_category($institute,$year,$course,'active',$category);
//                }
//    }else{

          if ($all_batches == TRUE) {
            $batches = $CI->model_batches->get_employee_batch_by_admission_year($employee,$year,$course);
        } else {
            $batches = $CI->model_batches->get_employee_batch_by_admission_year($employee,$year,$course,'active');
        }
//    }
    
    if ($course == FALSE) {
        $last_loaded_group='';
        $count ="";
        foreach ($batches as $batch) {
           // $batch_name = $show_adm_year_in_batch == TRUE ? $batch['admission_year']."-".$batch['batch_name'] : $batch['batch_name'];
             $batch_name =  $batch['admission_year']."-".$batch['batch_name'];
            if ($last_loaded_group != $batch['course_id']){
//                $group_count++;
                  if($count != 0){ 
                      $echo .= "</optgroup>";
                  }
                $echo .= "<optgroup label='$batch[course_name]'>";  
            }

            if ($select == $batch['id']) {
                $echo .= "<option selected value='$batch[id]'>$batch_name</option>";
            } else {
                $echo .= "<option value='$batch[id]'>$batch_name</option>";
            }

            $last_loaded_group=$batch['course_id'];
            $count++;
        }

        $echo .= "</optgroup>";
    
    } else {

        foreach ($batches as $batch) {
            $batch_name = $batch['admission_year']."-".$batch['batch_name'];
             //$batch_name = $show_adm_year_in_batch == TRUE ? $batch['admission_year']."-".$batch['batch_name'] : $batch['batch_name'];
                $batch['admission_year']."-".$batch['batch_name'];
            if ($select == $batch['id']) {
                $echo .= "<option selected value='$batch[id]'>$batch_name</option>";
            } else {
                $echo .= "<option value='$batch[id]'>$batch_name</option>";
            }
        }
    }

    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
   
}

function get_end_date_from_period_and_startdate($start_date,$period) {
    
    switch ($period) {
        case "annual":
            $end = strtotime(date("Y-m-d", strtotime($start_date)) . " +12 month");
            $date = date("Y-m-d",$end);
            $year_end = strtotime(date("Y-m-d", strtotime($date)) . " -1 day");
            $end_date = date("d/m/Y",$year_end);
            break;
        case "half_yearly":
            $end = strtotime(date("Y-m-d", strtotime($start_date)) . " +6 month");
            $date = date("Y-m-d",$end);
            $year_end = strtotime(date("Y-m-d", strtotime($date)) . " -1 day");
            $end_date = date("d/m/Y",$year_end);
            break;
        case "quarterly":
            $end = strtotime(date("Y-m-d", strtotime($start_date)) . " +3 month");
            $date = date("Y-m-d",$end);
            $year_end = strtotime(date("Y-m-d", strtotime($date)) . " -1 day");
            $end_date = date("d/m/Y",$year_end);
            break;
        case "monthly":
            $end = strtotime(date("Y-m-d", strtotime($start_date)) . " +1 month");
            $date = date("Y-m-d",$end);
            $year_end = strtotime(date("Y-m-d", strtotime($date)) . " -1 day");
            $end_date = date("d/m/Y",$year_end);
            break;
    }
    return $end_date;
}


function get_previous_period($date,$period) {
    
    $f_month = date('m', strtotime($date));
    $f_day = date('d', strtotime($date));
    $f_year = date('Y', strtotime($date));
    $date = $f_day."-".$f_month."-".$f_year;
    
    //if($period == 'monthly') {
        
    $month = date('m', strtotime($date.'+-1 months'));
    $year = isset($month) && $month=='12'?$f_year-1:$f_year;
    $start_date = $year."-".$month."-".$f_day;
        
//    } else if($period == 'annual') {
//        
//        $year = $f_year-1;
//        $start_date = $year."-".$f_month."-".$f_day;
//        
//    } else if($period == 'half_yearly') {
//          
//        $pre_date = strtotime(date("Y-m-d", strtotime($f_month)) . " -6 month");
//        $pre_date_month = date("m",$pre_date);
//
//        if($f_month>="07" && $f_month<="12") {
//            $month = $pre_date_month;
//            $year = $f_year;
//        } else if($f_month>="01" && $f_month<="06") {
//            $month = $pre_date_month;
//            $year = $f_year-1;
//        }
//
//        $start_date = $year."-".$month."-".$f_day;
//        
//    } else if($period == 'quarterly') {
//        
//        $pre_date = strtotime(date("Y-m-d", strtotime($f_month)) . " -3 month");
//        $pre_date_month = date("m",$pre_date);
//        
//        if($f_month>="04" && $f_month<="12") {
//            $month = $pre_date_month;
//            $year = $f_year;
//        } else if($f_month>="01" && $f_month>="03") {
//            $month = $pre_date_month;
//            $year = $f_year-1;
//        }
//        
//        $start_date = $year."-".$month."-".$f_day;
//    }
    return $start_date;
}

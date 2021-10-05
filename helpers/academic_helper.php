<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function echo_subjects($batch = false, $batch_sem = false, $select = false, $print = true) {
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_subjects');
    $sub = array();
    $sub['batch'] = $batch;
    $sub['batch_sem'] = $batch_sem;
    $subjects = $CI->model_subjects->get_all_subjects($sub);
    foreach ($subjects as $subject){
        if ($select == $subject['id']){
            $echo .= "<option selected value='$subject[id]'>$subject[subject_name]</option>";
        } else {
            $echo .= "<option value='$subject[id]'>$subject[subject_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_subjects_with_exam($batch = false, $batch_sem = false, $select = false, $print = true) {
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_subjects');
    $sub = array();
    $sub['batch']= $batch;
    $sub['batch_sem']= $batch_sem;
    $subjects = $CI->model_subjects->get_subjects_with_exam($sub);
    foreach ($subjects as $subject){
        if ($select == $subject['id']){
            $echo .= "<option selected value='$subject[id]'>$subject[subject_name]</option>";
        } else {
            $echo .= "<option value='$subject[id]'>$subject[subject_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_chapters($subject = false, $select = false, $print = true){
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_chapters');
    $chapters = $CI->model_chapters->get_chapters_with_subject_id($subject);
    foreach ($chapters as $chapter){
        if ($select == $chapter['id']){
            $echo .= "<option selected value='$chapter[id]'>$chapter[chapter_name]</option>";
        } else {
            $echo .= "<option value='$chapter[id]'>$chapter[chapter_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }

}

function echo_faculty($subject = false, $select = false, $print = true){
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_chapters');
    $faculty = $CI->model_chapters->get_faculty_with_subject_id($subject);
    foreach ($faculty as $fac){
        if ($select == $fac['employee_id']){
            $echo .= "<option selected value='$fac[employee_id]'>$fac[first_name]</option>";
        } else {
             $echo .= "<option value='$fac[employee_id]'>$fac[first_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_exams($subject,$select = false, $print = true){
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_exam');
    $exams = $CI->model_exam->get_exams($subject);
    
    foreach ($exams as $exam){
        if ($select == $exam['id']){
            $echo .= "<option selected value='$exam[id]'>$exam[group_name].$exam[category_name]</option>";
        } else {
             $echo .= "<option value='$exam[id]'>$exam[group_name].$exam[category_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_batch_semester($batch_id,$select = false,$current=false,$print= true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_batches');
    $batch_semester = $CI->model_batches->get_total_batch_semester($batch_id);
    $batch_details = $CI->model_batches->get_batch_details_by_id($batch_id);
    $current_sem = $batch_details['current_sem'];
    $semester = $batch_semester['semester'];
    
    for($i=1;$i<=$semester;$i++){
        if ($select == $i) {
            $echo .= "<option selected value='$i'>$i</option>";
        }
        elseif ($current && $current_sem == $i) {
           $echo .= "<option selected value='$i'>$i</option>";
       }
        
        
        else {
            $echo .= "<option value='$i'>$i</option>";
        }
    }
    
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_batch_no_of_period($batch_id,$select = false,$print= true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_batches');
    $batch_semester = $CI->model_batches->get_batch_details_by_id($batch_id);
    $period = $batch_semester['max_period'];
    
    for($i=1;$i<=$period;$i++){
        if ($select == $i) {
            $echo .= "<option selected value='$i'>$i</option>";
        } else {
            $echo .= "<option value='$i'>$i</option>";
        }
    }
    
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_employee_subjects($batch = false, $batch_sem = false, $employee_id = false, $select = false, $print = true) {
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_subjects');
    
    $subjects = $CI->model_subjects->get_subjects_with_employee_id($batch,$batch_sem,$employee_id);
    
    foreach ($subjects as $subject){
        if ($select == $subject['id']){
            $echo .= "<option selected value='$subject[id]'>$subject[subject_name]</option>";
        } else {
            $echo .= "<option value='$subject[id]'>$subject[subject_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_employee_subjects_with_exam($batch = false, $batch_sem = false, $employee_id = false, $select = false, $print = true){
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_subjects');
    $permission = current_user_can('create_exam_for_assigned_employees');
    if($permission == TRUE) {
        $get_subject['batch'] = $batch;
        $get_subject['batch_sem'] = $batch_sem;
        $subjects = $CI->model_subjects->get_subjects_with_exam($get_subject);
    }   
       else {
        $subjects = $CI->model_subjects->get_subjects_with_exam_by_employee_id($batch,$batch_sem,$employee_id);
     }
    foreach ($subjects as $subject){
        if ($select == $subject['id']){
            $echo .= "<option selected value='$subject[id]'>$subject[subject_name]</option>";
        } else {
            $echo .= "<option value='$subject[id]'>$subject[subject_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_student_subjects($student_adm,$select = false, $print = true){
     $CI  = & get_instance();
     $echo = '';
     $CI->load->model('model_students');
     $CI->load->model('model_subjects');
     if($student_adm){
    $student = $CI->model_students->get_student($student_adm,$type='adm_no');
     }
     if ($student['batch_id']){
         $get_subject['batch'] = $student['batch_id'];
         $subjects = $CI->model_subjects->get_all_subjects($get_subject);
     }
     foreach ($subjects as $subject){
        if ($select == $subject['id']){
            $echo .= "<option selected value='$subject[id]'>$subject[subject_name]</option>";
        } else {
            $echo .= "<option value='$subject[id]'>$subject[subject_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_student_batch($student_adm,$institute_id,$select = false,$print = true,$year=false){
    $CI  = & get_instance();
     $echo = '';
     $CI->load->model('model_students');
     $CI->load->model('model_subjects');
     if($student_adm && isset($year) && $year){
        $batches = $CI->model_students->get_student_batch($student_adm,$institute_id,$year);
        $pre_batches = $CI->model_students->get_student_previous_batches($student_adm,'adm',$institute_id,$year);
     }else if($student_adm){
        $batches = $CI->model_students->get_student_batch($student_adm,$institute_id);
        $pre_batches = $CI->model_students->get_student_previous_batches($student_adm,'adm',$institute_id);
     }
   if(isset($batches) && $batches){
    //foreach ($batches as $batch){
        if ($select == $batches['id']){
            $echo .= "<option selected value='$batches[id]'>$batches[batch_name] - $batches[course_name]</option>";
        } else if($select == false) {
            $echo .= "<option selected value='$batches[id]'>$batches[batch_name] - $batches[course_name]</option>";
        } else {
            $echo .= "<option value='$batches[id]'>$batches[batch_name] - $batches[course_name]</option>";
        }
    //}
   }
   if(isset($pre_batches) && $pre_batches){
    foreach ($pre_batches as $pre_batch){
        if ($select == $pre_batch['id']){
            $echo .= "<option selected value='$pre_batch[id]'>$pre_batch[batch_name] - $pre_batch[course_name]</option>";
        } else {
            $echo .= "<option value='$pre_batch[id]'>$pre_batch[batch_name] - $pre_batch[course_name]</option>";
        }
    }
   }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
   
    
}

function get_total_hours($chapter_id,$employee_id){
  $CI  = & get_instance();
     $echo = '';
     $CI->load->model('model_topics');
     if($chapter_id && $employee_id){
        $hours = $CI->model_topics->get_total_hours($chapter_id,$employee_id);
     }  
     return $hours;
}

function echo_elective_group($batch, $batch_sem, $select = false, $print = true){
    $echo = "";
    $CI = get_instance();
    $CI->load->model('model_subjects');
    $elective_groups = $CI->model_subjects->get_elective_groups($batch,$batch_sem);
    foreach ($elective_groups as $elective_group) {
        if ($select == $elective_group['id']){
            $echo .= "<option selected value='$elective_group[id]'>$elective_group[subject_name]</option>";
        } else {
            $echo .= "<option value='$elective_group[id]'>$elective_group[subject_name]</option>";
        }
    }
    if($print == true) {
        echo $echo;
    }
    else {
        return $echo;
    }
}

function echo_elective_group_subject($elective_group, $select = false, $print = true) {
    $echo = "";
    $CI = get_instance();
    $CI->load->model('model_subjects');
    $elective_subjects = $CI->model_subjects->get_elective_group_subjects($elective_group);
    foreach ($elective_subjects as $elective_subject) {
        if ($select == $elective_subject['id']){
            $echo .= "<option selected value='$elective_subject[id]'>$elective_subject[subject_name]</option>";
        } else {
            $echo .= "<option value='$elective_subject[id]'>$elective_subject[subject_name]</option>";
        }
    }
    if($print == true) {
        echo $echo;
    }
    else {
        return $echo;
    }
}

function echo_academic_event_category($institute = false, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_academic_calendar');
    $categories = $CI->model_academic_calendar->get_event_category_by_institute_id($institute);
    foreach ($categories as $category) {
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
function echo_academic_event_sub_category($category_id = false, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_academic_calendar');
    $sub_categories = $CI->model_academic_calendar->get_event_sub_category_by_category($category_id);
//    var_dump($sub_categories);
    foreach ($sub_categories as $category) {
        if ($select == $category['id']) {
            $echo .= "<option selected value='$category[id]'>$category[sub_category]</option>";
        } else {
            $echo .= "<option value='$category[id]'>$category[sub_category]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}
    
    function echo_exam_category1($institute = false, $batch = false,$sem =false, $grading_system=false, $category=false, $select = false, $print = true) {
        $echo = "";
        $CI = & get_instance();
        $CI->load->model('model_exam');
        $categories = $CI->model_exam->get_exam_category($institute,$batch,$sem,$grading_system,$category);
//        var_dump($categories); die();
        foreach ($categories as $category) {
            if ($select == $category['id']) {
                $echo .= "<option selected value='$category[id]'>$category[group_name][$category[category_group_name]]</option>";
            } else {
                $echo .= "<option value='$category[id]'>$category[group_name][$category[category_group_name]]</option>";
            }
        }
        if ($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
    }
function echo_exam_category($institute = false, $grading_system=false, $category=false, $term=false, $select = false, $print = true) {
        $echo = "";
        $CI = & get_instance();
        $CI->load->model('model_exam');
        $categories = $CI->model_exam->get_exam_category($institute,$grading_system,$category,$term);
       
        foreach ($categories as $category) {
            
            if ($select == $category['id']) {
                $echo .= "<option selected value='$category[id]'>$category[group_name][$category[category_group_name]]";
                if($category['term_name']){
                    $echo.= " - ".$category['term_name'];
                }
                $echo.="</option>";
            } else {
                $echo .= "<option value='$category[id]'>$category[group_name][$category[category_group_name]]";
                if($category['term_name']){
                    $echo.= " - ".$category['term_name'];
                }
                $echo.="</option>";
            }
        }
        if ($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
    }
    
    
function echo_rating_time($institute = false,$course = false,$batch = false,$select = false, $print = true){
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_staff_rating');
    $rating_time = $CI->model_staff_rating->get_rating_period($institute,$course,$batch);
    $date_period = array();
    foreach ($rating_time as $time) {
        $date_period[] = to_print_date_format($time['from_date'])."-".to_print_date_format($time['to_date']);
    }
    $date_period = array_unique($date_period);
    foreach ($date_period as $key=>$value) {
    if ($select == $value) {
            $echo .= "<option selected>".$value."</option>";
        } else {
            $echo .= "<option>".$value."</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}



function echo_staff_rating_time($institute,$adm_year = false,$course = false,$batch = false,
        $batch_sem = false,$select = false, $print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_staff_rating');
    
//    $get_rate_time['institute'] = $institute;
//    $get_rate_time['adm_year'] = $adm_year;
//    $get_rate_time['course'] = $course;
//    $get_rate_time['batch'] = $batch;
//    $get_rate_time['sem'] = $batch_sem;
    
    $rating_time = $CI->model_staff_rating->get_staff_rating_period($institute,$adm_year,$course,
                                                                $batch,$batch_sem);
    $date_period = array();
    foreach ($rating_time as $time) {
        $date_period[] = to_print_date_format($time['from_date'])."-".to_print_date_format($time['to_date']);
    }
    $date_period = array_unique($date_period);
    foreach ($date_period as $key=>$value) {
    if ($select == $value) {
            $echo .= "<option selected>".$value."</option>";
        } else {
            $echo .= "<option>".$value."</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    
}



function echo_time_table_start_date($batch_id, $sem, $select = false, $print = true) {
        $echo = "";
        $CI = & get_instance();
        $CI->load->model('model_time_table');
        $time_table['batch_id'] = $batch_id;
        $time_table['sem'] = $sem;
        $from_date = $CI->model_time_table->get_timetable_from_date($time_table);
       
        foreach ($from_date as $unq_from) {
            if ($select == $unq_from['from_date']) {
                $echo .= "<option selected value='$unq_from[from_date]'>$unq_from[from_date]</option>";
            } else {
                if($unq_from['from_date'] != '0000-00-00') {
                    $echo .= "<option value='$unq_from[from_date]'>$unq_from[from_date]</option>";
                }
            }
        }
        if ($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
    }
    
    function echo_time_table_end_date($batch_id, $sem, $select = false, $print = true) {
        $echo = "";
        $CI = & get_instance();
        $CI->load->model('model_time_table');
        $time_table['batch_id'] = $batch_id;
        $time_table['sem'] = $sem;
        $end_date = $CI->model_time_table->get_timetable_to_date($time_table);
        
        foreach ($end_date as $end) {
            if ($select == $end['to_date']) {
                $echo .= "<option selected value='$end[to_date]'>$end[to_date]</option>";
            } else {
                if($end['to_date'] != '0000-00-00') {
                    $echo .= "<option value='$end[to_date]'>$end[to_date]</option>";
                }
            }
        }
        if ($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
    }
    
    function echo_time_table_date_range($batch_id,$sem,$child_batch=false, $select = false, $print = true) {
        $echo = "";
        $CI = & get_instance();
        $CI->load->model('model_time_table');
        
        $time_table['sem'] = $sem;
        if(isset($child_batch) && $child_batch){
            $time_table['batch_id'] = $child_batch;
        }else{
          $time_table['batch_id'] = $batch_id;  
        }
        
        $end_date = $CI->model_time_table->get_timetable_date_ranges($time_table);
        
        foreach ($end_date as $end) {
            $date = $end['from_date']." - ".$end['to_date'];
            if ($select == $date) {
                $echo .= "<option selected value='$date'>$date</option>";
            } else {
                $echo .= "<option value='$date'>$date</option>";
            }
        }
        if ($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
    }
    
    function echo_employee_time_table_date_range($employee, $select = false, $print = true) {
        $echo = "";
        $CI = & get_instance();
        $CI->load->model('model_time_table');
        $date_range = $CI->model_time_table->get_employee_timetable_date_range($employee);
        
        foreach ($date_range as $range) {
            $date = $range['from_date']." - ".$range['to_date'];
            if ($select == $date) {
                $echo .= "<option selected value='$date'>$date</option>";
            } else {
                $echo .= "<option value='$date'>$date</option>";
            }
        }
        if ($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
    }
    
    function echo_exam_category_group($batch = false,$select = false, $print = true) {
        $echo = "";
        $CI = & get_instance();
        $CI->load->model('model_exam');
        $categories = $CI->model_exam->get_exam_category_group($batch);
        foreach ($categories as $category) {
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
    
    function echo_batch_of_timetable_based_attendance($institute,$date,$employee_id,$select = false, $print = true){
        $echo = "";
        $CI = & get_instance();
        $CI->load->model('model_time_table');
        $employee_subjects = array();
        $ta_date = to_date_format($date);
    
        $day_name = get_day_name($ta_date);
 
            $subjects = $CI->model_time_table->get_subjects_by_date_and_employee_id($institute,$ta_date,false);
            
            $period_wise_subjects = $CI->model_time_table->get_subjects_by_period_and_employee_id($institute,$ta_date,false,$day_name);

            $att_date = '0000-00-00';
            $default_subjects = $CI->model_time_table->get_subjects_by_period_and_employee_id($institute,$att_date,false,$day_name); 

        $academic_sem = $CI->session->userdata('academic_sem');

        $emp_subject= array();
        if(isset($subjects) && $subjects){
            
            foreach ($subjects as $sub){
                $emp_subject[$sub['batch_id']][$sub['period']]= $sub['subject_id'];
//                
              if($sub['employee_id'] == $employee_id){  
                $emp_sub['period']=$sub['period'];
                $emp_sub['sem']=$sub['sem'];
                $emp_sub['batch_id']=$sub['batch_id'];
                $emp_sub['course_code']=$sub['course_code'];
                $emp_sub['subject_code']=$sub['subject_code'];
                $emp_sub['batch_code']=$sub['batch_code'];
                $emp_sub['subject_id']=$sub['subject_id'];
                $employee_subject[]=$emp_sub;
              }
                
                
            }
        }
        $period_emp_subject = array();
        if(isset($period_wise_subjects) && $period_wise_subjects){
            
            foreach ($period_wise_subjects as $sub){
                if(!isset($emp_subject[$sub['batch_id']][$sub['period']])){
                $period_emp_subject[$sub['batch_id']][$sub['period']]['subject_id']= $sub['subject_id'];
                if($sub['employee_id'] == $employee_id){ 
                $emp_sub['period']=$sub['period'];
                $emp_sub['sem']=$sub['sem'];
                $emp_sub['batch_id']=$sub['batch_id'];
                $emp_sub['course_code']=$sub['course_code'];
                $emp_sub['subject_code']=$sub['subject_code'];
                $emp_sub['batch_code']=$sub['batch_code'];
                 $emp_sub['subject_id']=$sub['subject_id'];
                $employee_subject[]=$emp_sub;
                }
                
                }
            }
        }
        if(isset($default_subjects) && $default_subjects){
           
            foreach ($default_subjects as $sub){
               
                if(!isset($emp_subject[$sub['batch_id']][$sub['period']]) &&
                        !isset($period_emp_subject[$sub['batch_id']][$sub['period']])){
                    
                $emp_subject[$sub['batch_id']][$sub['period']]= $sub['subject_id'];
                if($sub['employee_id'] == $employee_id){ 
                $emp_sub['period']=$sub['period'];
                $emp_sub['sem']=$sub['sem'];
                $emp_sub['batch_id']=$sub['batch_id'];
                $emp_sub['course_code']=$sub['course_code'];
                $emp_sub['batch_code']=$sub['batch_code'];
                $emp_sub['subject_code']=$sub['subject_code'];
                $emp_sub['subject_id']=$sub['subject_id'];
                $employee_subject[]=$emp_sub;
                }
                }
            }
        }

        
        if($employee_subject) {
            foreach ($employee_subject as $emp_sub) {
                if($academic_sem == 'yes') {
                    $sem = "Sem ".$emp_sub['sem']." - ";
                    $sem_id = $emp_sub['sem']."-";
                } else {
                    $sem = '';
                }
                if(isset($emp_sub['subject_code']) && $emp_sub['subject_code']) {
                    $subject = $emp_sub['subject_code'];
                } else {
                    $subject = $emp_sub['subject_name'];
                }
                $option = $emp_sub['course_code']." - ".$emp_sub['batch_code']." - ".$sem."Period ".$emp_sub['period']." - ".$subject;
                $value = $emp_sub['batch_id']."-".$sem_id.$emp_sub['period']."-".$emp_sub['subject_id'];
                
                if ($select == $value) {

                $echo .= "<option selected value='$value'>$option</option>";
                } else {
                    $echo .= "<option value='$value'>$option</option>";
                } 
            }
        }
        if ($print == true) {
            echo $echo;
        } else {
            return $echo;
        
        }

    }
    
    function echo_employee_course_subjects($course,$employee_id,$select = false,$print = true) {
        
        $echo = '';
        $CI = & get_instance();
        $CI->load->model('model_subjects');
        $subjects = $CI->model_subjects->get_employee_subjects_by_course_employee_id($course,$employee_id);
        $academic_sem = $CI->session->userdata('academic_sem');
        foreach ($subjects as $sub) {
            
            $name = ($academic_sem == 'yes') ? $sub['batch_name']." - ".$sub['sem']." - ".$sub['subject_name'] :
                                             $sub['batch_name']." - ".$sub['subject_name'];    
            
            if ($select == $sub['subject_id']) {
                $echo .= "<option selected value='$sub[subject_id]'>$name</option>";
            } else {
                $echo .= "<option value='$sub[subject_id]'>$name</option>";
            }

        }

        if($print == true) {
            echo $echo;
        } else {
            return $echo;
        }

    }
    
    function echo_attendance_sessions($institute, $select = false, $print = true) {
        $echo = '';
        $CI = & get_instance();
        $CI->load->model('model_institutes');
        $institute_info = $CI->model_institutes->get_institute_by_id($institute);
        $no_of_sessions  = $institute_info['no_of_sessions'];
        if($no_of_sessions>1) {
            for ($i=1;$i<=$no_of_sessions;$i++) {

                if ($select == $i) {
                    $echo .= "<option selected value='$i'>Session $i</option>";
                } else {
                    $echo .= "<option value='$i'>Session $i</option>";
                }
            }
        }

        if($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
    }
    
    function echo_subject_period($institute,$date,$employee_id,$subject_id,$child_batch,$select = false, $print = true){
       
       
        $echo = "";
        $CI = & get_instance();
        $CI->load->model('model_time_table');
        $employee_subject = array();
        $ta_date = to_date_format($date);
    
        $day_name = get_day_name($ta_date);
            
//            $subject_period = $CI->model_time_table->get_subject_period_by_subject_id($institute,$ta_date,$employee_id,$subject_id)
            $subjects = $CI->model_time_table->get_subjects_by_date_and_employee_id($institute,$ta_date,false,false,false,false,$child_batch);
            
            $period_wise_subjects = $CI->model_time_table->get_subjects_by_period_and_employee_id($institute,$ta_date,false,$day_name,false,false,false,$child_batch);
//            var_dump($period_wise_subjects);
            $att_date = '0000-00-00';
            $default_subjects = $CI->model_time_table->get_subjects_by_period_and_employee_id($institute,$att_date,false,$day_name,false,false,false,$child_batch); 
           
        $academic_sem = $CI->session->userdata('academic_sem');

        $emp_subject= array();
        if(isset($subjects) && $subjects){
            
            foreach ($subjects as $sub){
                
                $emp_subject[$sub['batch_id']][$sub['period']]['hour']= $sub['period'];
                
//                $emp_subject[$sub['batch_id']][$sub['period']]= $sub['subject_id'];
//                
              if($sub['subject_id']==$subject_id && $sub['employee_id']==$employee_id){
                $emp_sub['period']=$sub['period'];

                $employee_subject[]=$emp_sub;
              }
                
            }
            
            
        }
       $period_emp_subject = array();
        if(isset($period_wise_subjects) && $period_wise_subjects){
            
            foreach ($period_wise_subjects as $sub){
               
                if(!isset($emp_subject[$sub['batch_id']][$sub['period']]['hour'])){
                    
                $period_emp_subject[$sub['batch_id']][$sub['period']]['hour']= $sub['period'];
//                if(!isset($emp_subject[$sub['batch_id']][$sub['period']])){
//                $emp_subject[$sub['batch_id']][$sub['period']]['subject_id']= $sub['subject_id'];
                 if($sub['subject_id']==$subject_id && $sub['employee_id']==$employee_id){ 
                    $emp_sub['period']=$sub['period'];
 
                    $employee_subject[]=$emp_sub;
                 }
                
                }
            }

            
        }
        
        
        if(isset($default_subjects) && $default_subjects){
            
            foreach ($default_subjects as $sub){
                if(!isset($emp_subject[$sub['batch_id']][$sub['period']]['hour']) && !isset($period_emp_subject[$sub['batch_id']][$sub['period']]['hour'])){
                    
//                $emp_subject1[$sub['batch_id']][$sub['period']]['hour']= $sub['period'];
               if($sub['subject_id']==$subject_id && $sub['employee_id']==$employee_id){ 
                $emp_sub['period']=$sub['period'];

                $employee_subject[]=$emp_sub;
               }
                }
            }
        }
        
        if($employee_subject) {
            foreach ($employee_subject as $emp_sub) {
                if($academic_sem == 'yes') {
                    $sem = "Sem ".$emp_sub['sem']." - ";
                    $sem_id = $emp_sub['sem']."-";
                } else {
                    $sem = '';
                }

                $option = $emp_sub['period'];
                $value =  $emp_sub['period'];
                
                if ($select == $value) {

                $echo .= "<option selected value='$value'>$option</option>";
                } else {
                    $echo .= "<option value='$value'>$option</option>";
                } 
            }
        }
        if ($print == true) {
            echo $echo;
        } else {
            return $echo;
        
        }

    }
    

     function echo_batch_timetable($institute,$date,$batch,$sem){
        $echo = "";
        $CI = & get_instance();
        $CI->load->model('model_time_table');
        $employee_subjects = array();
        $ta_date = to_date_format($date);
    
        $day_name = get_day_name($ta_date);
 
            $subjects = $CI->model_time_table->get_subjects_by_date_and_employee_id($institute,$ta_date,'','',$batch,$sem);
//            
//           
            $period_wise_subjects = $CI->model_time_table->get_subjects_by_period_and_employee_id($institute,$ta_date,'',$day_name,'',$batch,$sem);
//            
            $att_date = '0000-00-00';
            $default_subjects = $CI->model_time_table->get_subjects_by_period_and_employee_id($institute,$att_date,'',$day_name,'',$batch,$sem); 
//            var_dump($default_subjects);
        $emp_subject= array();
        $subject_employee = array();
        if(isset($subjects) && $subjects){
           
            foreach ($subjects as $sub){
                $emp_subject[$sub['batch_id']][$sub['sem']][$sub['period']]['hour']= $sub['period'];

                $emp[$sub['batch_id']][$sub['sem']][$sub['period']]["subject"][$sub['subject_id']]['name']=$sub['subject_name'];
                $emp[$sub['batch_id']][$sub['sem']][$sub['period']]["subject"][$sub['subject_id']]['sub_id']=$sub['subject_id'];
                $emp[$sub['batch_id']][$sub['sem']][$sub['period']]["employee"][$sub['subject_id']][]=$sub['full_name'];
               
               
                
            }
            
        }
        $period_emp_subject = array();
        if(isset($period_wise_subjects) && $period_wise_subjects){
            
            foreach ($period_wise_subjects as $sub){
                if(!isset($emp_subject[$sub['batch_id']][$sub['sem']][$sub['period']]['hour'])){
                    
                $period_emp_subject[$sub['batch_id']][$sub['sem']][$sub['period']]['hour']= $sub['period'];

                
                $emp[$sub['batch_id']][$sub['sem']][$sub['period']]["subject"][$sub['subject_id']]['name']=$sub['subject_name'];
                $emp[$sub['batch_id']][$sub['sem']][$sub['period']]["subject"][$sub['subject_id']]['sub_id']=$sub['subject_id'];
                $emp[$sub['batch_id']][$sub['sem']][$sub['period']]["employee"][$sub['subject_id']][]=$sub['full_name'];
 
                }
            }
        }
        if(isset($default_subjects) && $default_subjects){
            
            foreach ($default_subjects as $sub){
                if(!isset($emp_subject[$sub['batch_id']][$sub['sem']][$sub['period']]['hour']) && !isset($period_emp_subject[$sub['batch_id']][$sub['sem']][$sub['period']]['hour'])){

                $emp[$sub['batch_id']][$sub['sem']][$sub['period']]["subject"][$sub['subject_id']]['name']=$sub['subject_name'];
                $emp[$sub['batch_id']][$sub['sem']][$sub['period']]["subject"][$sub['subject_id']]['sub_id']=$sub['subject_id'];
                $emp[$sub['batch_id']][$sub['sem']][$sub['period']]["employee"][$sub['subject_id']][]=$sub['full_name'];
 
                }
            }
           
        }
        if(isset($emp) ) {
 
        return $emp;
 
        }
     }
     
     
     
     function echo_employee_time_table($institute,$date,$employee_id,$batch){
      //   var_dump($date);         die();
        $echo = "";
        $CI = & get_instance();
        $CI->load->model('model_time_table');
        $employee_subject = array();
        $ta_date = to_date_format($date);
    
        $day_name = get_day_name($ta_date);
        
        $subjects = $CI->model_time_table->get_subjects_by_date_and_employee_id($institute,$ta_date,false,false,$batch);
                      
        $period_wise_subjects = $CI->model_time_table->get_subjects_by_period_and_employee_id($institute,$ta_date,false,$day_name,false,$batch);
        $att_date = "0000-00-00";
        $default_subjects = $CI->model_time_table->get_subjects_by_period_and_employee_id($institute,$att_date,false,$day_name,'',$batch);
            
        $academic_sem = $CI->session->userdata('academic_sem');

        $emp_subject= array();
        
        if(isset($subjects) && $subjects){
            
            foreach ($subjects as $sub){
                $emp_subject[$sub['batch_id']][$sub['period']]= $sub['subject_id'];
                if(isset($sub['employee_id']) && $sub['employee_id'] == $employee_id){
                $emp_sub[$sub['batch_id']][$sub['period']]['subject_name']=$sub['subject_name'];
                $emp_sub[$sub['batch_id']][$sub['period']]['course']=$sub['course_code'];
                $emp_sub[$sub['batch_id']][$sub['period']]['batch']=$sub['child_batch_name']?$sub['child_batch_name']:$sub['batch_code'];
                $emp_sub[$sub['batch_id']][$sub['period']]['sem']=$sub['sem'];
                $emp_sub[$sub['batch_id']][$sub['period']]['subject_id']=$sub['subject_id'];
                }
            }
            
            
        }
        $period_emp_subject = array();
        if(isset($period_wise_subjects) && $period_wise_subjects){
            
            foreach ($period_wise_subjects as $sub){
                if(!isset($emp_subject[$sub['batch_id']][$sub['period']])){
                $period_emp_subject[$sub['batch_id']][$sub['period']]= $sub['subject_id'];
                 if(isset($sub['employee_id']) && $sub['employee_id'] == $employee_id){

                $emp_sub[$sub['batch_id']][$sub['period']]['subject_name']=$sub['subject_name'];
                $emp_sub[$sub['batch_id']][$sub['period']]['course']=$sub['course_code'];
                $emp_sub[$sub['batch_id']][$sub['period']]['batch']=$sub['child_batch_name']?$sub['child_batch_name']:$sub['batch_code'];
                $emp_sub[$sub['batch_id']][$sub['period']]['sem']=$sub['sem'];
                $emp_sub[$sub['batch_id']][$sub['period']]['subject_id']=$sub['subject_id'];
                 }
                }
            }
        }
        if(isset($default_subjects) && $default_subjects){
            
            foreach ($default_subjects as $sub){
                if(!isset($emp_subject[$sub['batch_id']][$sub['period']]) && !isset($period_emp_subject[$sub['batch_id']][$sub['period']])){
                   if(isset($sub['employee_id']) && $sub['employee_id'] == $employee_id){ 
//                $emp_subject[$sub['period']]= $sub['subject_id'];
//                
//                $emp_sub[$sub['batch_id']][$sub['sem']][$sub['period']]['subject']['subject_id']=$sub['subject_id'];
                $emp_sub[$sub['batch_id']][$sub['period']]['subject_name']=$sub['subject_name'];
                $emp_sub[$sub['batch_id']][$sub['period']]['course']=$sub['course_code'];
                $emp_sub[$sub['batch_id']][$sub['period']]['batch']=$sub['child_batch_name']?$sub['child_batch_name']:$sub['batch_code'];
                $emp_sub[$sub['batch_id']][$sub['period']]['sem']=$sub['sem'];
                $emp_sub[$sub['batch_id']][$sub['period']]['subject_id']=$sub['subject_id'];
                 
//                $employee_subject[]=$emp_sub;
                   }
                }
            }
        }
         
        if(isset($emp_sub) ) {
          
        return $emp_sub;
 
        }
     }
     
     
     function echo_academic_terms($institute,$select = false,$print = true){
         
        $echo = '';
        $CI = & get_instance();
         $CI->load->model('model_internals');
         
         if ($select) {
       $select = is_array($select) ? $select : explode(' ', $select);
        }
         
         
         
         $terms = $CI->model_internals->get_terms($institute);
         if(isset($terms) && $terms){
            foreach($terms as $term){
              
                if (in_array($term['id'], $select)) {
            
                $echo .= "<option selected value='$term[id]'>$term[term_name]</option>";
            } else {
                $echo .= "<option value='$term[id]'>$term[term_name]</option>";
            }
                
            }
                
             
         }
         
         if($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
     }
     
     
//   exam groups of batch  
      function echo_batch_exam_groups($batch,$select = false,$print = true){
         
        $echo = '';
        $CI = & get_instance();
        $CI->load->model('model_exam');
         
         if ($select) {
       $select = is_array($select) ? $select : explode(' ', $select);
        }
         
         
         $exams = $CI->model_exam->get_exam_groups($batch);
         
         if(isset($exams) && $exams){
            foreach($exams as $exam){
               if (in_array($exam['id'], $select)) {
               
                $echo .= "<option selected value='$exam[id]'>$exam[group_name]</option>";
                } else 
                    {
                    $echo .= "<option value='$exam[id]'>$exam[group_name]</option>";
                }
                
            }
                
             
         }
         
         if($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
     }
     
     
//  exam groups by term
     

       function echo_term_exam_groups($term,$institute,$batch=false,$select = false,$print = true){
  
        $echo = '';
        $CI = & get_instance();
        $CI->load->model('model_exam');
         
         if ($select) {
       $select = is_array($select) ? $select : explode(' ', $select);
        }
         
         
           $exams = $CI->model_exam->get_exam_groups_by_term($term,$institute,$batch);
        
         if(isset($exams) && $exams){
            foreach($exams as $exam){
               if (isset($select) && $select && (in_array($exam['id'], $select))) {
               
                $echo .= "<option selected value='$exam[id]'>$exam[group_name]</option>";
                } else 
                    {
                    $echo .= "<option value='$exam[id]'>$exam[group_name]</option>";
                }
                
            }
                
             
         }
         
         if($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
     }
     
     //  exam groups by term
     
      function echo_institute_exam_groups($institute,$sem=false,$batch=false,$select = false,$print = true,$template_number=false){
        $echo = '';
        $CI = & get_instance();
        $CI->load->model('model_exam');
         
         if ($select) {
       $select = is_array($select) ? $select : explode(' ', $select);
        }
        if(isset($template_number) && $template_number==6){
//          echo $institute."-batch:".$batch."-tem:".$template_number;
//          var_dump($sem);
           $exams = $CI->model_exam->get_final_exam_groups_by_institute($institute,$sem,$batch); 
           
        }else{
            
            $exams = $CI->model_exam->get_exam_groups_by_institute($institute,$sem,$batch);
            
        }
//         var_dump($exams);
         if(isset($exams) && $exams){
            foreach($exams as $exam){
               if (isset($select) && $select && in_array($exam['id'],$select)) {
                $echo .= "<option selected value='$exam[id]'>$exam[group_name]</option>";
                } else 
                    {
                    $echo .= "<option value='$exam[id]'>$exam[group_name]</option>";
                }
            }     
         }
         
         if($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
     }
     
     
     function echo_exam_groups_by_term($term,$select = false,$print = true){
         
        $echo = '';
        $CI = & get_instance();
        $CI->load->model('model_exam');
         
//         if ($select) {
//       $select = is_array($select) ? $select : explode(' ', $select);
//        }
         
         
         $exams = $CI->model_exam->get_exam_groups_by_single_term($term);
       
         if(isset($exams) && $exams){
            foreach($exams as $exam){
               if (isset($exam) && $exam['id']==$select) {
               
                $echo .= "<option selected value='$exam[id]'>$exam[group_name]</option>";
                } else 
                    {
                    $echo .= "<option value='$exam[id]'>$exam[group_name]</option>";
                }
                
            }
                
             
         }
         
         if($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
     }
     
     
     function echo_course_academic_sem($course,$year,$select = false,$print = true){
        
        $echo = '';
        $CI = & get_instance();
        $CI->load->model('model_courses');
 
        $semester = $CI->model_courses->get_max_semester_by_course($course,$year);

         if(isset($semester) && $semester){
           for($i=1;$i<=$semester['total_sem'];$i++){
               
               if ($select == $i) {

                $echo .= "<option selected value='$i'>$i</option>";
                } else {
                $echo .= "<option value='$i'>$i</option>";
                }
           }
                
             
         }
         
         if($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
 
     }
     
     function echo_subject_specialization($batch, $batch_sem, $select = false, $print = true){
        $echo = "";
        $CI = get_instance();
        $CI->load->model('model_subjects');
        $specialize_groups = $CI->model_subjects->get_specialization_groups($batch,$batch_sem);
        foreach ($specialize_groups as $specialize_group) {
            if ($select == $specialize_group['id']){
                $echo .= "<option selected value='$specialize_group[id]'>$specialize_group[subject_name]</option>";
            } else {
                $echo .= "<option value='$specialize_group[id]'>$specialize_group[subject_name]</option>";
            }
        }
        if($print == true) {
            echo $echo;
        }
        else {
            return $echo;
        }
    }
    
    function echo_my_time_table($from_date,$to_date,$employee_id){
        
        $echo = "";
        $CI = get_instance();
        
        $employee['from_date'] = $from_date;
        $employee['to_date'] = $to_date;
         $CI->load->model('model_time_table');
         $date_period_time_table = $CI->model_time_table->get_time_table_by_date_range($from_date,$to_date);  
         
         $date_time_table = $CI->model_time_table->get_time_table_by_dates($employee);
         
         $default_time_table = $CI->model_time_table->get_all_time_table($employee);
 
         $academic_sem = $CI->session->userdata('academic_sem');

        $emp_subject= array();
        $emp_sub1 = array();
        $date_batches = array();
        $range_batches = array();
        
        if(isset($date_time_table) && $date_time_table){
            foreach ($date_time_table as $sub){
                $emp_subject[$sub['batch_id']][$sub['from_date']][$sub['period']]= $sub['subject_id'];
                if(isset($sub['employee_id']) && $sub['employee_id'] == $employee_id){

                $emp_sub1['subject_name']=$sub['subject_name'];
                $emp_sub1['course']=$sub['course_code'];
                $emp_sub1['batch']=$sub['batch_code'];
                $emp_sub1['child_batch']=$sub['child_batch_name'];
                $emp_sub1['sem']=$sub['sem'];
                $emp_sub1['subject_id']=$sub['subject_id'];
                $emp_sub1['parent_batch_id']=$sub['parent_batch_id'];
                $emp_sub1['batch_id']=$sub['batch_id'];
                $date_batches[] = $sub['batch_id'];
                $emp_sub[$sub['from_date']][$sub['batch_id']][$sub['period']][]=$emp_sub1;
                }
            }
        }
       
        $period_emp_subject = array();
        if(isset($date_period_time_table) && $date_period_time_table){
            
            
            $ranges = array();
            
            foreach ($date_period_time_table as $sub){
  
                $emp_sub1['subject_name']=$sub['subject_name'];
                $emp_sub1['course']=$sub['course_code'];
                $emp_sub1['batch']=$sub['batch_code'];
                $emp_sub1['child_batch']=$sub['child_batch_name'];
                $emp_sub1['sem']=$sub['sem'];
                $emp_sub1['subject_id']=$sub['subject_id'];
                $emp_sub1['parent_batch_id']=$sub['parent_batch_id'];
                $emp_sub1['batch_id']=$sub['batch_id'];
                $emp_sub1['week_id']=$sub['week_id'];
                $emp_sub1['employee_id']=$sub['employee_id'];
                $emp_sub1['from_date'] = $sub['from_date'];
                $emp_sub1['to_date'] = $sub['to_date'];
                $range_batches[]= $sub['batch_id'];
                $date_range = $sub['from_date']."_".$sub['to_date'];
                $sub_period[$date_range][$sub['batch_id']][$sub['week_id']][]= $sub['period'];
                
                $ranges[$date_range][$sub['batch_id']][$sub['week_id']][$sub['period']][] = $emp_sub1;
                
            }
           
            $time_table_batch = array_unique(array_merge($date_batches,$range_batches));
         
            if(isset($ranges)){
//             var_dump($ranges)
                
                foreach($ranges as $key=>$range){
                 
                    $date_range = explode('_',$key);
                    $per_from_date = strtotime($date_range[0]);
                    $per_to_date = strtotime($date_range[1]);
//                  
                    for ($i=$per_from_date; $i<=$per_to_date; $i+=86400) {
                          
                        $date = date("Y-m-d",$i);
                        $week_id= date('N',  $i);
                        
                        foreach($time_table_batch as $batches){
                        
                            if(isset($sub_period[$key][$batches][$week_id])){

                                foreach ($sub_period[$key][$batches][$week_id] as $period){

                                   foreach($range[$batches][$week_id][$period] as $value){

                                        if(!isset($emp_subject[$value['batch_id']][$date][$period]) && !isset($subjects[$date][$value['batch_id']][$period][$value['subject_id']])){

                                           $period_emp_subject[$value['batch_id']][$date][$period]= $value['subject_id'];
                                          
                                           if(isset($value['employee_id']) && $value['employee_id'] == $employee_id){ 

                                                      $emp_sub[$date][$value['batch_id']][$period][]=$value;
                                                      $subjects[$date][$value['batch_id']][$period][$value['subject_id']] = $value['subject_id'];
//                                                
                                          }

                                        }

                                    }

                                }
                            }
                        }
                    }
                    
                }
                    
            }
       
        }
        
       // var_dump($emp_subject);
        
         if(isset($default_time_table) && $default_time_table){
       
            
            
            foreach ($default_time_table as $sub){
            $emp_sub1 = array();
//                $date = date("Y-m-d",$i);
//                $week_id= date('N',  $i);
//                
                if(
                   isset($sub['employee_id']) && $sub['employee_id'] == $employee_id &&
                   !isset($def_subjects[$sub['batch_id']][$sub['week_id']][$sub['period']][$sub['subject_id']])){ 

                    $emp_sub1['subject_name']=$sub['subject_name'];
                    $emp_sub1['course']=$sub['course_code'];
                    $emp_sub1['batch']=$sub['batch_code'];
                    $emp_sub1['child_batch']=$sub['child_batch_name'];
                    $emp_sub1['sem']=$sub['sem'];
                    $emp_sub1['subject_id']=$sub['subject_id'];
                    $emp_sub1['parent_batch_id']=$sub['parent_batch_id'];
                    $emp_sub1['batch_id']=$sub['batch_id']; 
                    $def_batches[] = $sub['batch_id'];
                    $def_period[] = $sub['period'];
                  // $def[$sub['batch_id']][$week_id][$sub['period']][]= $emp_sub1;
//                    $emp_sub[$date][$sub['batch_id']][$sub['period']][] =  $emp_sub1;
//                    $subjects[$date][$value['batch_id']][$sub['period']][$sub['subject_id']] = $sub['subject_id'];
                   //if($sub['week_id'] == $week_id) { 
                    // $emp_sub[$date][$sub['batch_id']][$period][]=$emp_sub1;
                      $def_emp_sub[$sub['batch_id']][$sub['week_id']][$sub['period']][] =  $emp_sub1;
                    //}
                    $def_subjects[$emp_sub1['batch_id']][$sub['week_id']][$sub['period']][$sub['subject_id']] = $sub['subject_id'];
                }
                        
            }
       if(isset($def_subjects) && $def_subjects){
            $d_batches = array_unique($def_batches);
            $d_period = array_unique($def_period);
            
             $def_from_date = strtotime($from_date);
             $def_to_date = strtotime($to_date);
             for ($i=$def_from_date; $i<=$def_to_date; $i+=86400) { 
                 
                $date = date("Y-m-d",$i);
                $week_id= date('N',  $i);
                
                foreach($d_batches as $d_batch){
                    
                    foreach ($d_period as $d_p){
                        
                    if(!isset($emp_subject[$d_batch][$date][$d_p]) && 
                   !isset($period_emp_subject[$d_batch][$date][$d_p])&& 
                     isset($def_emp_sub[$d_batch][$week_id][$d_p])){
                        
                            $emp_sub[$date][$d_batch][$d_p]=$def_emp_sub[$d_batch][$week_id][$d_p];
                    }
                    }
                }
                
             }
            
       }
            
      

        }
        
        
        
//        if(isset($default_time_table) && $default_time_table){
//       
//            $def_from_date = strtotime($from_date);
//            $def_to_date = strtotime($to_date);
////                  
//            for ($i=$def_from_date; $i<=$def_to_date; $i+=86400) {       
//            foreach ($default_time_table as $sub){
//            $emp_sub1 = array();
//                $date = date("Y-m-d",$i);
//                $week_id= date('N',  $i);
//                
//                if(
//                   isset($sub['employee_id']) && $sub['employee_id'] == $employee_id &&
//                   !isset($def_subjects[$sub['batch_id']][$sub['week_id']][$sub['period']][$sub['subject_id']])){ 
//
//                    $emp_sub1['subject_name']=$sub['subject_name'];
//                    $emp_sub1['course']=$sub['course_code'];
//                    $emp_sub1['batch']=$sub['batch_code'];
//                    $emp_sub1['child_batch']=$sub['child_batch_name'];
//                    $emp_sub1['sem']=$sub['sem'];
//                    $emp_sub1['subject_id']=$sub['subject_id'];
//                    $emp_sub1['parent_batch_id']=$sub['parent_batch_id'];
//                    $emp_sub1['batch_id']=$sub['batch_id']; 
//                    
//                    $def[$sub['batch_id']][$sub['week_id']][$sub['period']]= $emp_sub1;
//                    if(isset($def[$sub['batch_id']][$week_id][$sub['period']]) && !isset($emp_subject[$sub['batch_id']][$date][$sub['period']]) && 
//                   !isset($period_emp_subject[$sub['batch_id']][$date][$sub['period']])){
//                        $emp_sub[$date][$sub['batch_id']][$sub['period']][]= $emp_sub1;
//                       //  $emp_sub[$sub['batch_id']][$sub['week_id']][$sub['period']][] =  $emp_sub1;
//                    }
////                    $emp_sub[$date][$sub['batch_id']][$sub['period']][] =  $emp_sub1;
////                    $subjects[$date][$value['batch_id']][$sub['period']][$sub['subject_id']] = $sub['subject_id'];
//                   //if($sub['week_id'] == $week_id) { 
//                    // $emp_sub[$date][$sub['batch_id']][$period][]=$emp_sub1;
//                     // $emp_sub[$sub['batch_id']][$sub['week_id']][$sub['period']][] =  $emp_sub1;
//                    //}
//                    $def_subjects[$emp_sub1['batch_id']][$sub['week_id']][$sub['period']][$sub['subject_id']] = $sub['subject_id'];
//                }
//                        
//            }
//       
//        }
//
//        }
//      var_dump($emp_sub5);
        
        
        if(isset($emp_sub) ) {
          
        return $emp_sub;
 
        }
         
         
        
    }
    
   
    
    function echo_specialization_subject_employees($specialization,$select = false,$print = true) {
        $echo = "";
        $CI = get_instance();
        $CI->load->model('model_subjects');
        $employees = $CI->model_subjects->get_specialization_subject_employees($specialization);
        foreach ($employees as $employee) {
            if ($select == $employee['id']){
                $echo .= "<option selected value='$employee[id]'>$employee[full_name]</option>";
            } else {
                $echo .= "<option value='$employee[id]'>$employee[full_name]</option>";
            }
        }
        if($print == true) {
            echo $echo;
        }
        else {
            return $echo;
        }
    }
    
    function check_specialization_subject($subject_id){
        
       
        $CI = get_instance();
        $CI->load->model('model_subjects');
       
        $specialization_students = $CI->model_subjects->get_specialization_subject_assigned_employee($subject_id);
        return $specialization_students;
    }
    
//    function check_employee_assigned($check_emp_hour){
//         $CI = get_instance();
//         $result ="";
//         $emp_time_table_default = $CI->model_time_table->check_employee_hour_default($check_emp_hour); 
//         $emp_time_table_date_range = $CI->model_time_table->check_employee_hour_date_range($check_emp_hour); 
//         $emp_time_table_date = $CI->model_time_table->check_employee_hour_date($check_emp_hour); 
//         
//         var_dump($emp_time_table_default);
//         
//        if(isset($emp_time_table_default) && $emp_time_table_default){
//                 
//                 $result= "true";
//        }
//        
//        if(isset($emp_time_table_date_range) && $emp_time_table_date_range){
//            
//                $result= "true";
//        }
//        
//        if(isset($emp_time_table_date) && $emp_time_table_date){
//            
//               $result= "true";
//        }
//        
//        return $result;
//    }
    
    function get_institute_type_by_subject($subject_id){
        
        $CI = get_instance();
        $CI->load->model('model_institutes');
        $get_institute = $CI->model_institutes->get_institute_by_subject($subject_id);
       
        return $get_institute;
    }
    
    function echo_module_topic($module_id,$select = false,$print = true){
        
        $echo ="";
        $CI = get_instance();
        $CI->load->model('model_topics');
        $employee_id = $CI->session->userdata("employee_id");
        $topics = $CI->model_topics->get_topics_with_chapter_id($module_id,$employee_id);
        if($topics){
            foreach($topics as $topic){
             if ($select == $topic['id']){
                $echo .= "<option selected value='$topic[id]'>$topic[topic_name]</option>";
            } else {
                $echo .= "<option value='$topic[id]'>$topic[topic_name]</option>";
            }
        }
        if($print == true) {
            echo $echo;
        }
        else {
            return $echo;
        }
        }
    }

 function echo_subjects_by_type($batch = false, $batch_sem = false,$type=false, $select = false, $print = true) {
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_subjects');
    $sub = array();
    $sub['batch'] = $batch;
    $sub['batch_sem'] = $batch_sem;
    if($type){
        if($type=="theory"){
            $sub['subject_type'] = 0;
        }
        else if($type=="practical"){
            $sub['subject_type'] = 1;
        }
    }
    
    $subjects = $CI->model_subjects->get_all_subjects($sub);
    foreach ($subjects as $subject){
        if ($select == $subject['id']){
            $echo .= "<option selected value='$subject[id]'>$subject[subject_name]</option>";
        } else {
            $echo .= "<option value='$subject[id]'>$subject[subject_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function get_final_internal_max_mark($rule_id){
    
    //assignment
    $CI = & get_instance();
    
    $CI->load->model('model_internals');
    $echo = '';
    $internal_rule = $CI->model_internals->get_internal_rule_by_id($rule_id);
    
    
    $total_score = 0;
    $attendance_score =0;
    $internal_total_score = 0;
    if($internal_rule){
            foreach($internal_rule as $rule){
               
                if($rule['component_name']!="attendance"){
                if($rule['convert_to']){
                    
                    $total_score+=$rule['best_of']?$rule['convert_to']*$rule['best_of']:$rule['convert_to'];
                    
                }else{
                   $total_score+=$rule['best_of']?$rule['max_score']*$rule['best_of']:$rule['max_score']; 
                  
                }
                }
                if($rule['component_name']=="attendance"){
                    
                    $attendance_score = $rule['attendance_max_score'];
                }
               
            }
    }
    
  $internal_total_score = $total_score+$attendance_score;
 
  return $internal_total_score;
}

function echo_review_subjects($batch = false, $batch_sem = false, $select = false, $print = true) {
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_subjects');
    $sub = array();
    $sub['batch']= $batch;
    $sub['batch_sem']= $batch_sem;
    $subjects = $CI->model_subjects->get_review_subjects($sub);
    
    foreach ($subjects as $subject){
        if ($select == $subject['id']){
            $echo .= "<option selected value='$subject[id]'>$subject[subject_name]</option>";
        } else {
            $echo .= "<option value='$subject[id]'>$subject[subject_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

 function get_subject_period($institute,$date,$employee_id,$subject_id,$child_batch){
       
       
       
        $CI = & get_instance();
        $CI->load->model('model_time_table');
        $employee_subject = array();
        $ta_date = to_date_format($date);
    
        $day_name = get_day_name($ta_date);
            
//            $subject_period = $CI->model_time_table->get_subject_period_by_subject_id($institute,$ta_date,$employee_id,$subject_id)
            $subjects = $CI->model_time_table->get_subjects_by_date_and_employee_id($institute,$ta_date,false,false,false,false,$child_batch);
            
            $period_wise_subjects = $CI->model_time_table->get_subjects_by_period_and_employee_id($institute,$ta_date,false,$day_name,false,false,false,$child_batch);
//            var_dump($period_wise_subjects);
            $att_date = '0000-00-00';
            $default_subjects = $CI->model_time_table->get_subjects_by_period_and_employee_id($institute,$att_date,false,$day_name,false,false,false,$child_batch); 
           
        $academic_sem = $CI->session->userdata('academic_sem');

        $emp_subject= array();
        if(isset($subjects) && $subjects){
            
            foreach ($subjects as $sub){
                
                $emp_subject[$sub['batch_id']][$sub['period']]['hour']= $sub['period'];
                
//                $emp_subject[$sub['batch_id']][$sub['period']]= $sub['subject_id'];
//                
              if($sub['subject_id']==$subject_id && $sub['employee_id']==$employee_id){
                $emp_sub['period']=$sub['period'];

                $employee_subject[]=$emp_sub;
              }
                
            }
            
            
        }
       $period_emp_subject = array();
        if(isset($period_wise_subjects) && $period_wise_subjects){
            
            foreach ($period_wise_subjects as $sub){
               
                if(!isset($emp_subject[$sub['batch_id']][$sub['period']]['hour'])){
                    
                $period_emp_subject[$sub['batch_id']][$sub['period']]['hour']= $sub['period'];
//                if(!isset($emp_subject[$sub['batch_id']][$sub['period']])){
//                $emp_subject[$sub['batch_id']][$sub['period']]['subject_id']= $sub['subject_id'];
                 if($sub['subject_id']==$subject_id && $sub['employee_id']==$employee_id){ 
                    $emp_sub['period']=$sub['period'];
 
                    $employee_subject[]=$emp_sub;
                 }
                
                }
            }

            
        }
        
        
        if(isset($default_subjects) && $default_subjects){
            
            foreach ($default_subjects as $sub){
                if(!isset($emp_subject[$sub['batch_id']][$sub['period']]['hour']) && !isset($period_emp_subject[$sub['batch_id']][$sub['period']]['hour'])){
                    
//                $emp_subject1[$sub['batch_id']][$sub['period']]['hour']= $sub['period'];
               if($sub['subject_id']==$subject_id && $sub['employee_id']==$employee_id){ 
                $emp_sub['period']=$sub['period'];

                $employee_subject[]=$emp_sub;
               }
                }
            }
        }
        
        if($employee_subject) {
             return $employee_subject;
        }
        

    }
    
    function echo_question_bank_categories($institute_id, $select = false, $print = true){
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_question_bank');
    $categories = $CI->model_question_bank->get_question_bank_categories($institute_id);
    foreach ($categories as $category){
        if ($select == $category['id']){
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

function echo_subject_employee_time_table($from_date,$to_date,$employee_id,$subject_id,$batch_id){
        
        $echo = "";
        $CI = get_instance();
        
        $employee['from_date'] = $from_date;
        $employee['to_date'] = $to_date;
        $employee['batch'] = $batch_id;
        $subject = $subject_id;
        $CI->load->model('model_time_table');
         $date_period_time_table = $CI->model_time_table->get_time_table_by_date_range($from_date,$to_date,$batch_id);  
         
         $date_time_table = $CI->model_time_table->get_time_table_by_dates($employee);
         
         $default_time_table = $CI->model_time_table->get_all_time_table($employee);
 
         $academic_sem = $CI->session->userdata('academic_sem');

        $emp_subject= array();
        $emp_sub1 = array();
        $date_batches = array();
        $range_batches = array();
        
        if(isset($date_time_table) && $date_time_table){
            foreach ($date_time_table as $sub){
                $emp_subject[$sub['batch_id']][$sub['from_date']][$sub['period']]= $sub['subject_id'];
                if(isset($sub['employee_id']) && $sub['employee_id'] == $employee_id 
                        && $sub['subject_id']==$subject_id){

                $emp_sub1['subject_name']=$sub['subject_name'];
                $emp_sub1['course']=$sub['course_code'];
                $emp_sub1['batch']=$sub['batch_code'];
                $emp_sub1['child_batch']=$sub['child_batch_name'];
                $emp_sub1['sem']=$sub['sem'];
                $emp_sub1['subject_id']=$sub['subject_id'];
                $emp_sub1['parent_batch_id']=$sub['parent_batch_id'];
                $emp_sub1['batch_id']=$sub['batch_id'];
                $date_batches[] = $sub['batch_id'];
                $emp_sub[$sub['from_date']][$sub['batch_id']][$sub['period']][]=$emp_sub1;
                }
            }
        }
       
        $period_emp_subject = array();
        if(isset($date_period_time_table) && $date_period_time_table){
            
            
            $ranges = array();
            
            foreach ($date_period_time_table as $sub){
  
                $emp_sub1['subject_name']=$sub['subject_name'];
                $emp_sub1['course']=$sub['course_code'];
                $emp_sub1['batch']=$sub['batch_code'];
                $emp_sub1['child_batch']=$sub['child_batch_name'];
                $emp_sub1['sem']=$sub['sem'];
                $emp_sub1['subject_id']=$sub['subject_id'];
                $emp_sub1['parent_batch_id']=$sub['parent_batch_id'];
                $emp_sub1['batch_id']=$sub['batch_id'];
                $emp_sub1['week_id']=$sub['week_id'];
                $emp_sub1['employee_id']=$sub['employee_id'];
                $emp_sub1['from_date'] = $sub['from_date'];
                $emp_sub1['to_date'] = $sub['to_date'];
                $range_batches[]= $sub['batch_id'];
                $date_range = $sub['from_date']."_".$sub['to_date'];
                $sub_period[$date_range][$sub['batch_id']][$sub['week_id']][]= $sub['period'];
                
                $ranges[$date_range][$sub['batch_id']][$sub['week_id']][$sub['period']][] = $emp_sub1;
                
            }
           
            $time_table_batch = array_unique(array_merge($date_batches,$range_batches));
         
            if(isset($ranges)){
//             var_dump($ranges)
                
                foreach($ranges as $key=>$range){
                 
                    $date_range = explode('_',$key);
                    $per_from_date = strtotime($date_range[0]);
                    $per_to_date = strtotime($date_range[1]);
//                  
                    for ($i=$per_from_date; $i<=$per_to_date; $i+=86400) {
                          
                        $date = date("Y-m-d",$i);
                        $week_id= date('N',  $i);
                        
                        foreach($time_table_batch as $batches){
                        
                            if(isset($sub_period[$key][$batches][$week_id])){

                                foreach ($sub_period[$key][$batches][$week_id] as $period){

                                   foreach($range[$batches][$week_id][$period] as $value){

                                        if(!isset($emp_subject[$value['batch_id']][$date][$period]) && !isset($subjects[$date][$value['batch_id']][$period][$value['subject_id']])){

                                           $period_emp_subject[$value['batch_id']][$date][$period]= $value['subject_id'];
                                          
                                           if(isset($value['employee_id']) && $value['employee_id'] == $employee_id && $value['subject_id']==$subject_id){ 

                                                      $emp_sub[$date][$value['batch_id']][$period][]=$value;
                                                      $subjects[$date][$value['batch_id']][$period][$value['subject_id']] = $value['subject_id'];
//                                                
                                          }

                                        }

                                    }

                                }
                            }
                        }
                    }
                    
                }
                    
            }
       
        }
        
       // var_dump($emp_subject);
        
         if(isset($default_time_table) && $default_time_table){
       
            
            
            foreach ($default_time_table as $sub){
            $emp_sub1 = array();
//                $date = date("Y-m-d",$i);
//                $week_id= date('N',  $i);
//                
                if(
                   isset($sub['employee_id']) && $sub['employee_id'] == $employee_id && $sub['subject_id']==$subject_id && 
                   !isset($def_subjects[$sub['batch_id']][$sub['week_id']][$sub['period']][$sub['subject_id']])){ 

                    $emp_sub1['subject_name']=$sub['subject_name'];
                    $emp_sub1['course']=$sub['course_code'];
                    $emp_sub1['batch']=$sub['batch_code'];
                    $emp_sub1['child_batch']=$sub['child_batch_name'];
                    $emp_sub1['sem']=$sub['sem'];
                    $emp_sub1['subject_id']=$sub['subject_id'];
                    $emp_sub1['parent_batch_id']=$sub['parent_batch_id'];
                    $emp_sub1['batch_id']=$sub['batch_id']; 
                    $def_batches[] = $sub['batch_id'];
                    $def_period[] = $sub['period'];
                  // $def[$sub['batch_id']][$week_id][$sub['period']][]= $emp_sub1;
//                    $emp_sub[$date][$sub['batch_id']][$sub['period']][] =  $emp_sub1;
//                    $subjects[$date][$value['batch_id']][$sub['period']][$sub['subject_id']] = $sub['subject_id'];
                   //if($sub['week_id'] == $week_id) { 
                    // $emp_sub[$date][$sub['batch_id']][$period][]=$emp_sub1;
                      $def_emp_sub[$sub['batch_id']][$sub['week_id']][$sub['period']][] =  $emp_sub1;
                    //}
                    $def_subjects[$emp_sub1['batch_id']][$sub['week_id']][$sub['period']][$sub['subject_id']] = $sub['subject_id'];
                }
                        
            }
       if(isset($def_subjects) && $def_subjects){
            $d_batches = array_unique($def_batches);
            $d_period = array_unique($def_period);
            
             $def_from_date = strtotime($from_date);
             $def_to_date = strtotime($to_date);
             for ($i=$def_from_date; $i<=$def_to_date; $i+=86400) { 
                 
                $date = date("Y-m-d",$i);
                $week_id= date('N',  $i);
                
                foreach($d_batches as $d_batch){
                    
                    foreach ($d_period as $d_p){
                        
                    if(!isset($emp_subject[$d_batch][$date][$d_p]) && 
                   !isset($period_emp_subject[$d_batch][$date][$d_p])&& 
                     isset($def_emp_sub[$d_batch][$week_id][$d_p])){
                        
                            $emp_sub[$date][$d_batch][$d_p]=$def_emp_sub[$d_batch][$week_id][$d_p];
                    }
                    }
                }
                
             }
            
       }
            
      

        }
        
 
        
        if(isset($emp_sub) ) {
          
        return $emp_sub;
 
        }
         
         
        
    }
    
    function echo_module_topics($module,$subject,$employee= false, $select = false, $print = true){
        
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_topics');
    $sub = array();
    $sub['module_id']= $module;
    $sub['subject']= $subject;
    $sub['employee']= $employee;
    
    $topics = $CI->model_topics->get_topics_by_module_id($sub);
    
    foreach ($topics as $topic){
        if ($select == $topic['id']){
            $echo .= "<option selected value='$topic[id]'>$topic[topic_name]</option>";
        } else {
            $echo .= "<option value='$topic[id]'>$topic[topic_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
        
    }
    
    function echo_internal_session($batch,$select = false, $print = true){
        
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_exam_internal');
    
    
    $sessions = $CI->model_exam_internal->get_exam_internal_session($batch);
    
    foreach ($sessions as $session){
        if ($select == $session['id']){
            $echo .= "<option selected value='$session[id]'>$session[session_name]</option>";
        } else {
            $echo .= "<option value='$session[id]'>$session[session_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
        
    }
    
    function echo_exam_group_exams($batch=false,$sem=false,$exam_group = false,$select = false, $print = true) {
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_exam');
    
    $exams = $CI->model_exam->get_exam_by_exam_group($exam_group,$batch,$sem);
    
    foreach ($exams as $exam){
        if ($select == $exam['id']){
            $echo .= "<option selected value='$exam[id]'>$exam[subject_name]</option>";
        } else {
            $echo .= "<option value='$exam[id]'>$exam[subject_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}
    
    
function echo_batch_semesters($batch_id,$select = false,$current=false,$print= true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_batches');
    $batch_semester = $CI->model_batches->get_total_batch_semester($batch_id);
    $batch_details = $CI->model_batches->get_batch_details_by_id($batch_id);
    $current_sem = $batch_details['current_sem'];
    $semester = $batch_semester['semester'];
    
    for($i=1;$i<=$semester;$i++){
        if ($select == $i) {
            $echo .= "<option selected value='$i'>$i</option>";
        }
        elseif ($current && $current_sem == $i) {
           $echo .= "<option selected value='$i'>$i</option>";
       }
        
        
        else {
            $echo .= "<option value='$i'>$i</option>";
        }
    }
    
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

 //  exam groups by term
     
      function echo_batch_template_semesters($template,$batch,$select = false,$print = true){
         
        
//        echo  $batch;
        $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_batches');
    $batch_semester = $CI->model_batches->get_total_batch_semester($batch);
 
    $batch_details = $CI->model_batches->get_batch_details_by_id($batch);
    $current_sem = $batch_details['current_sem'];
    $semester = $batch_semester['semester'];
     if($template==6){
        
         if ($select) {
       $select = is_array($select) ? $select : explode(' ', $select);
        }
        for($i=1;$i<=$semester;$i++){
         if(isset($batch_semester) && $batch_semester){
               if (isset($select) && $select && in_array($i,$select)) {
                $echo .= "<option selected value='$i'>$i</option>";
                } else {
                    $echo .= "<option value='$i'>$i</option>";
                }
         }
        }
         if($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
      
     }
     }
     

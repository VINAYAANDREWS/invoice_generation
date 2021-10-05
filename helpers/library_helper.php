<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function generate_library_call_no($first_author,$book_name) {
    
    $author = ucwords(substr($first_author, 0, 3));
    $bn = ucwords(substr($book_name, 0 ,1));
    $call_no = $author."/".$bn;
    return $call_no;
}

function create_accession_no($library_id) {
    $CI = & get_instance();
    $CI->load->model('model_library_catalog');
    $max_accession_no = $CI->model_library_catalog->get_max_library_accession_no($library_id);
        if(isset($max_accession_no['max_accession_no']) && $max_accession_no['max_accession_no']) {
            $accession_no = $max_accession_no['max_accession_no'] +1;
        } else {
            $accession_no = 1;
        }
        return $accession_no;
}

function get_member_details($member_id,$member_type) {
    $CI = & get_instance();
    $CI->load->model('model_employee');
    $CI->load->model('model_students');
    if($member_type == 'employee') {
        $member_details = $CI->model_employee->get_employee($member_id,'id');
        $member['full_name'] = $member_details['full_name'];
        $member['code'] = $member_details['employee_number'];
    }
    else if($member_type == 'student') {
        $member_details = $CI->model_students->get_student($member_id,'id');
        $member['full_name'] = $member_details['full_name'];
        $member['code'] = $member_details['admission_no'];
    }
    return $member;
}

function echo_libraries($institute,$select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_library_catalog');
    $libraries = $CI->model_library_catalog->get_libraries_by_institute($institute);
    $library_count = count($libraries);
    $select = isset($select) && $select ? $select : $libraries[0]['id'];
    
    foreach ($libraries as $library) {
        if($select == $library['id']||$library_count==1) {
            $echo .= "<option selected value='$library[id]'>$library[library_name]</option>";
        } else {
            $echo .= "<option value='$library[id]'>$library[library_name]</option>";
        }
    }
    if ($print == true){
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_library_media_types($select = false,$print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_library_catalog');
    $media_types = $CI->model_library_catalog->get_media_types();
    foreach ($media_types as $media) {
        if($select == $media['id']) {
            $echo .= "<option selected value='$media[id]'>$media[name]</option>";
        } else {
            $echo .= "<option value='$media[id]'>$media[name]</option>";
        }
    }
    if ($print == true){
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_library_category($institute,$select = false,$print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_library');
    $categories = $CI->model_library->get_categories($institute);
    foreach ($categories as $category) {
        if($select == $category['id']) {
            $echo .= "<option selected value='$category[id]'>$category[category_name]</option>";
        } else {
            $echo .= "<option value='$category[id]'>$category[category_name]</option>";
        }
    }
    if ($print == true){
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_library_publishers($select = false,$print = true){
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_library');
    $publishers = $CI->model_library->get_publishers();
    foreach ($publishers as $publisher) {
        if($select == $publisher['id']) {
            $echo .= "<option selected value='$publisher[id]'>$publisher[name]</option>";
        } else {
            $echo .= "<option value='$publisher[id]'>$publisher[name]</option>";
        }
    }
    if ($print == true){
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_library_authors($select = false,$print = true){
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_library');
    $authors = $CI->model_library->get_authors();
    foreach ($authors as $author) {
        if($select == $author['id']) {
            $echo .= "<option selected value='$author[id]'>$author[name]</option>";
        } else {
            $echo .= "<option value='$author[id]'>$author[name]</option>";
        }
    }
    if ($print == true){
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_media_types($select = false,$print = true) {
   
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_library_catalog');
    $all_media_types = $CI->model_library_catalog->get_media_types();
//    foreach($all_media_types as $all){
//        $media_types[$all['head']][]=$all;
//        
//    }
//
//    foreach ($media_types as $head=>$type) {
//    $echo .= "<optgroup label='$head'>";
        foreach($all_media_types as $t){
            if($select && ($select == $t['id'])){
                $echo .= "<option rel='$t[head]' selected value='$t[id]'>$t[name]</option>";
            } else {
                $echo .= "<option rel='$t[head]' value='$t[id]'>$t[name]</option>"; 
            }
        }
//          $echo .= "</optgroup>";
//    }   
  
    
    
    
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_institute_authors($institute,$select = false,$print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_library');
    $authors = $CI->model_library->get_authors($institute);
    foreach ($authors as $author) {
        if($select == $author['id']){
            $echo .= "<option selected value='$author[id]'>$author[name]</option>";
        } else {
            $echo .= "<option value='$author[id]'>$author[name]</option>"; 
        }
    }   
    
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_institute_publishers($institute,$select = false,$print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_library');
    $publishers = $CI->model_library->get_publishers($institute);
    foreach ($publishers as $publisher) {
        if($select == $publisher['id']){
            $echo .= "<option selected value='$publisher[id]'>$publisher[name]</option>";
        } else {
            $echo .= "<option value='$publisher[id]'>$publisher[name]</option>"; 
        }
    }   
    
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_library_subjects($institute,$select = false,$print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_library');
    $subjects = $CI->model_library->get_library_subjects($institute);
    foreach ($subjects as $subject) {
        if($select == $subject['id']){
            $echo .= "<option selected value='$subject[id]' rel='$subject[classification_no]'>$subject[subject_name]</option>";
        } else {
            $echo .= "<option value='$subject[id]' rel='$subject[classification_no]'>$subject[subject_name]</option>"; 
        }
    }   
    
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}
function echo_library_periodical_title($institute,$library=false,$media_type=false,$select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_library_catalog');
    $libraries = $CI->model_library_catalog->get_libraries_by_institute($institute);
    $library_count = count($libraries);
    $library_id = isset($library) && $library?$library:$libraries[0]['id'];
    //var_dump($library_id);die;
    $CI->load->model('model_library_periodicals');
    if($media_type){
    $libraries = $CI->model_library_periodicals->get_periodical_title_by_library($library_id,$media_type);
    }else{
     $libraries = $CI->model_library_periodicals->get_periodical_title_by_library($library_id);
       
    }
    foreach ($libraries as $l) {
        if($select && ($select == $l['id'])) {
            $echo .= "<option selected value='$l[id]'>$l[title]</option>";
        } else {
            $echo .= "<option value='$l[id]'>$l[title]</option>";
        }
    }
    if ($print == true){
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_library_periodical_title_issue($title,$select = false,$print = true) {
    
   $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_library_periodicals');
    $issues = $CI->model_library_periodicals->get_periodical_title_issues($title);
    foreach ($issues as $issue) {
        $year = isset($issue['year']) && $issue['year'] != '0000' ? $issue['year']." - " : "";
        $volume = isset($issue['volume']) && $issue['volume'] ? "Vol ".$issue['volume']." - " : "";
       // $issue = isset($issue['issue']) && $issue['issue'] ? " - ".$issue['issue'] : "";
        
        if($select && ($select == $issue['id'])){
            $echo .= "<option selected value='$issue[id]'>$year$volume$issue[issue]</option>";
        } else {
            $echo .= "<option value='$issue[id]'>$year$volume$issue[issue]</option>"; 
        }
    }   
    
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function get_default_library_id($institute) {
    
    $CI = & get_instance();
    $CI->load->model('model_library');
    $libraries = $CI->model_library->get_libraries($institute);
    $library_id = $libraries[0]['id'];
    return $library_id;
}


function echo_periodical_media_types($select=false,$print=true){
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_library_periodicals');
    $media_types = $CI->model_library_periodicals->get_periodical_media_types();
   
    foreach ($media_types as $type) {
        if(isset($select) && $select && ($select == $type['id'])){
            $echo .= "<option selected value='$type[id]'>$type[name]</option>";
        } else {

            $echo .= "<option value='$type[id]'>$type[name]</option>"; 
        }
    }   
    
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_library_branch($institute,$select=false,$print=true){
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_library');
    $branches = $CI->model_library->get_institute_branches($institute);
   
    foreach ($branches as $branch) {
        if(isset($select) && $select && ($select == $branch['id'])){
            $echo .= "<option selected value='$branch[id]'>$branch[branch_name]</option>";
        } else {

            $echo .= "<option value='$branch[id]'>$branch[branch_name]</option>"; 
        }
    }   
    
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}


function check_reserve_is_possible($library_id,$accession_no) {
    if(isset($accession_no) && $accession_no) {
        $view_data = array();
        $CI = & get_instance();
        $CI->load->model('model_library_catalog');
        
        //check if resource is issued
        $issue_info = $CI->model_library_catalog->get_catalog_issue_by_acc_no($accession_no,$library_id);
        
        if(isset($issue_info) && $issue_info) {
            $status = TRUE;
        } else {
            $CI->load->model('model_library_reservation');
            $check_reservation = $CI->model_library_reservation->check_catalog_reserved($accession_no,$library_id);
           if(isset($check_reservation['reserved_count']) && $check_reservation['reserved_count'] != 0) {
               $status = TRUE;
           } else {
               $status = FALSE;
           }
            
        } 
        
        return $status;

    }
}

function get_catalog_copy_info($library_id,$acc_no) {
    
    if(isset($acc_no) && $acc_no) {
    $CI = & get_instance();
    $CI->load->model('model_library_catalog');
        //check if resource is available
        $check_catalog_available = $CI->model_library_catalog->check_catalog_available($acc_no,$library_id);

        //if available get details
        if(isset($check_catalog_available) && $check_catalog_available) {
            $catalog_copy = $CI->model_library_catalog->get_catalog_copy_details($check_catalog_available['id']);
            $data['catalog_copy'] = $catalog_copy;
            $data['status'] = TRUE;
        } else {
            $data['status'] = FALSE;
        }
        return $data;
    }
}

function check_if_catalog_copy_is_taken($catalog_id,$member_type,$member_id) {
    
    $CI = & get_instance();
    $CI->load->model('model_library_catalog');
    $catalog_taken_details = $CI->model_library_catalog->get_catalog_taken_by_members($member_type,$member_id);

    foreach ($catalog_taken_details as $details) {
        $catalog_acc_ids[] = $details['catalog_accession_id'];
    }

    if(isset($catalog_acc_ids) && $catalog_acc_ids) {
        $catalog_ids = array();
        foreach ($catalog_acc_ids as $ca_acc_id) {
            $issued_copies = $CI->model_library_catalog->get_catalog_copy_details($ca_acc_id);
            $catalog_ids[] = $issued_copies['catalog_id'];
        }

        //check if the copy of a resource is taken by the user
        if(in_array($catalog_id, $catalog_ids)) {
            return TRUE;
        } else {
            return FALSE;
        }
        
    }
}

function get_member_settings($institute_id,$member_type,$member_id,$catalog_category=false) {
    
    $CI = & get_instance();
    $CI->load->model('model_library_catalog');
    
    $lib_set['institute_id'] = $institute_id;
    $lib_set['member_id'] = $member_id;
    $lib_set['member_type'] = $member_type;

    //member wise settings
    $max_catalog = $CI->model_library_catalog->get_library_member_settings($lib_set);
    if(isset($max_catalog) && $max_catalog) {
        $view_data['max_catalog'] = $max_catalog;
        $return_days = $max_catalog['return_days'];
        $view_data['return_days'] = $return_days;
        if($max_catalog['ban']==1) {
            $view_data['member_banned'] = TRUE;
        }
        //$view_data['fine_per_day'] = $max_catalog['fine_per_day'];
    } else {
        $CI->load->model('model_library_member_group');
        $group_max_catalog = $CI->model_library_member_group->get_max_catalog_alloted_to_member($lib_set);
        if(isset($group_max_catalog) && $group_max_catalog) {
            $view_data['max_catalog'] = $group_max_catalog;
            $return_days = $group_max_catalog['return_days'];
            $view_data['return_days'] = $return_days;
        } else {
            $mem_max_catalog = $CI->model_library_catalog->get_max_catalog_alloted_to_members($member_type,$institute_id);
            $view_data['max_catalog'] = $mem_max_catalog;
            $return_days = $mem_max_catalog['return_days'];
            $view_data['return_days'] = $return_days;
           // $view_data['fine_per_day'] = $mem_max_catalog['fine_per_day'];
        }
    }
    
    //check if max catalog issue for a catagory isset
//    if(isset($catalog_category)) {
//        //check maximum catalog alloted in a category for induvidual 
//        if(isset($max_catalog) && $max_catalog) {
//            $check_max_resource_alloted_with_category = $CI->model_library_catalog->get_library_settings_category_for_member($max_catalog['id'],$catalog_category,"individual");
//            if(isset($check_max_resource_alloted_with_category) && $check_max_resource_alloted_with_category) {
//                $view_data['max_resource'] = $check_max_resource_alloted_with_category['max_resource'];
//            }
//        }
//        //check maximum catalog alloted in a category for group 
//        if(isset($mem_max_catalog) && $mem_max_catalog) {
//            $check_max_resource_alloted = $CI->model_library_catalog->get_library_settings_category_for_member($mem_max_catalog['id'],$catalog_category,"group"); 
//            if(isset($check_max_resource_alloted) && $check_max_resource_alloted) {
//                $view_data['max_resource'] = $check_max_resource_alloted['max_resource'];
//            }
//        }
//    }
    
    //return date
    if(isset($return_days) && $return_days) {
        $return_date = date('Y-m-d', strtotime("+".$return_days." days"));
        $view_data['resource_return_date'] = $return_date;
    }
    return $view_data;
}

function get_member_fine($member_type,$member_id) {
    $CI = & get_instance();
    $CI->load->model('model_library_catalog');
    $total_fine_generated = 0;
    $view_data = array();
    $issue_history = $CI->model_library_catalog->get_issue_history($member_type,$member_id);
    foreach ($issue_history as $history){
        $total_fine = $history['fine_paid'] + $history['fine_for_damage']; 
        $total_fine_generated += $total_fine;
    }
    if(isset($total_fine_generated) && $total_fine_generated) {
        $view_data['total_fine_generated'] = $total_fine_generated;
    }

    //get total fine paid
    $fine_paid = $CI->model_library_catalog->get_total_library_fine($member_type,$member_id);
    $view_data['fine_paid'] = isset($fine_paid) && $fine_paid['fine_paid']?$fine_paid['fine_paid']:0;
    return $view_data;
}

function get_reservation_member($accession_id) {
    if(isset($accession_id) && $accession_id) {
        $CI = & get_instance();
        $CI->load->model('model_library_reservation');
        $reservation_info = $CI->model_library_reservation->get_first_reserved_member($accession_id);
        return $reservation_info;
    }
}

//function echo_spine_label($subject_code,$author_ids,$volume,$edition,$title,$reference) {
//    $CI = & get_instance();
//    $CI->load->model('model_library');
//    $spine_label = "";
//    
//    if(isset($reference) && $reference) {
//        $reference_label = "R ";
//    } else {
//        $reference_label = "";
//    }
//    
//    if(isset($subject_code) && $subject_code) {
////        $subject_info = $CI->model_library->get_library_subject($subject_id);
////        $subject_label = isset($subject_info['classification_no'])?$subject_info['classification_no']." ":"";
//          $subject_label = $subject_code." ";
//    } else {
//        $subject_label = "";
//    }
//    if(isset($author_ids) && $author_ids) {
//        //$author_ids = explode(',', $author_ids);
//        $author_id = $author_ids[0];
//        $author_info = $CI->model_library->get_author_details($author_id);
//        $author_name = isset($author_info['name'])?substr($author_info['name'], 0, 3):"";
//        $author_label = $author_name;
//    } else {
//        $author_label = "";
//    }
//    if(isset($title) && $title) {
//        $title = isset($title)?substr($title, 0, 1):"";
//        $title_label = "-".$title;
//    } else {
//        $title_label = "";
//    }
//    if(isset($edition) && $edition) {
//        $edition_label = $edition;
//    } else {
//        $edition_label = "";
//    }
//    if(isset($volume) && $volume) {
//        $volume_label = ".".$volume;
//    } else {
//        $volume_label = "";
//    }
//    
//    $spine_label = $reference_label.$subject_label.$author_label.$title_label.$edition_label.$volume_label;
//    echo $spine_label;
//    
//}

function check_checkout_limit_exceeded($institute,$member_type,$member_id) {
    
    $CI = & get_instance();
    $CI->load->model('model_library_catalog');
    
    //member settings
    $member_settings = get_member_settings($institute,$member_type,$member_id);
    $max_catalog = $member_settings['max_catalog']['max_resources'];

    //get catalog issued to student
    $get_catlogs_not_returned = $CI->model_library_catalog->get_catalog_count_not_returned($member_type,$member_id);
    $not_returned = $get_catlogs_not_returned['catalog_count'];

    //get catalogs reserved by student
    $reserved_catalogs = $CI->model_library_reservation->get_catalogs_member_reserved($member_id,$member_type);
    $reserved_count = count($reserved_catalogs);
    
    $not_returned = isset($not_returned)?$not_returned:0;
    $reserved_count = isset($reserved_count)?$reserved_count:0;
    $total_catalogs = $not_returned+$reserved_count;
    
    if($max_catalog > $total_catalogs) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function get_library_fine($institute_id,$member_type,$member_id,$day_diff) {
                  
   
    $CI = & get_instance();
    $CI->load->model('model_library_catalog');
    $CI->load->model('model_library_settings');
    $CI->load->model('model_library_member_group');

    $lib_set['institute_id'] = $institute_id;
    $lib_set['member_type'] = $member_type;
    $lib_set['member_id'] = $member_id;
    
    $check_induvidual_fine = $CI->model_library_catalog->get_library_member_settings($lib_set);
    if(isset($check_induvidual_fine) && $check_induvidual_fine) {
        
        $fine_settings = $CI->model_library_settings->get_member_fine_settings($check_induvidual_fine['id']);
        
    } else {
        $group_fine = $CI->model_library_member_group->get_max_catalog_alloted_to_member($lib_set);
        if(isset($group_fine) && $group_fine) {
            $fine_settings = $CI->model_library_member_group->get_member_group_fine_settings($group_fine['id']);
        } else {
            $member_fine = $CI->model_library_catalog->get_max_catalog_alloted_to_members($member_type,$institute_id);
            if(isset($member_fine) && $member_fine) {
                $fine_settings = $CI->model_library_settings->get_member_type_fine_settings($member_fine['id']);
            }
        }
    }
    
    $balance_days = $day_diff;
    $fine = 0;
    
    $first_loop = TRUE;
    
    foreach ($fine_settings as $f_settings) {       
        
        if ($balance_days > 0) {
            
            if ($f_settings['day_to']) {
                $interval = $f_settings['day_to'] - $f_settings['day_from'] + 1;
            } else {
                $interval = $balance_days;

            }
            
            $first_loop = FALSE;
            
            if ($f_settings['per_day'] && $interval >= $balance_days) {
                
                $fine += ($balance_days*$f_settings['amount']);
                
            } else if($f_settings['per_day'] && $interval < $balance_days) {
                
                $fine += ($interval*$f_settings['amount']);
            } else {
                $fine += $f_settings['amount'];
            }
            
            $balance_days -= $interval;
            
        }
        
    }

    return $fine;

}

function echo_call_no($library) {
    $CI = & get_instance();
    $CI->load->model('model_library');
    $call_no = "";
    
    $call_no_generation_rules = get_option(array('option' => 'library_call_no_generation_rule','institute_id'=>$library['institute_id']));
    $rules = json_decode($call_no_generation_rules,TRUE);
    
    $rule = $rules['rule'];
    $search_words = array("{{REFERENCE}}","{{TITLENAME}}","{{CLASSIFICATIONNO}}","{{AUTHERNAME}}","{{SUBJECT}}","{{EDITION}}","{{VOLUME}}");
    
    $rule_contain = get_string_contain_words_in_array($rule,$search_words);
    
    if (in_array("{{REFERENCE}}", $rule_contain)) {
        if(isset($library['reference']) && $library['reference']) {
            $reference_label = "R ";
        } else {
            $reference_label = "";
        }
        $rule = str_replace('{{REFERENCE}}',$reference_label, $rule);
    }
    
    
    if (in_array("{{CLASSIFICATIONNO}}", $rule_contain)) {
        if(isset($library['subject_id']) && $library['subject_id']) {
            $subject_info = $CI->model_library->get_library_subject($library['subject_id']);
            $subject_code = isset($subject_info['classification_no'])?$subject_info['classification_no']." ":"";
            $subject_code = isset($rules['classification_no_separator']) && $rules['classification_no_separator'] ? $rules['classification_no_separator'].$subject_code : $subject_code;
        } else {
            $subject_code = "";
        }
        $rule =  str_replace('{{CLASSIFICATIONNO}}',$subject_code, $rule);
    }
    
    if (in_array("{{SUBJECT}}", $rule_contain)) {
        if(isset($library['subject_id']) && $library['subject_id']) {
            $subject_info = $CI->model_library->get_library_subject($library['subject_id']);
            $subject_name = isset($subject_info['subject_name'])?$subject_info['subject_name']." ":"";
            $subject_name = substr($subject_name,0,1) ;
            $subject_name = isset($rules['subject_separator']) && $rules['subject_separator'] ? $rules['subject_separator'].$subject_name : $subject_name ;
            
        } else {
            $subject_name = "";
        } 
        $rule = str_replace('{{SUBJECT}}',$subject_name, $rule);
    }
    
    if (in_array("{{AUTHERNAME}}", $rule_contain)) {
        if(isset($library['author_id']) && $library['author_id']) {
            //$author_ids = explode(',', $author_ids);
            $author_id = $library['author_id'][0];
            $author_info = $CI->model_library->get_author_details($author_id);
            $author_name = $author_info['name'];
            $author_name = isset($rules['auther_name_length']) && $rules['auther_name_length']? substr($author_name,0,$rules['auther_name_length']) : substr($author_name,0,$rules['auther_name_length']) ;
            $author_name = isset($rules['author_separator']) && $rules['author_separator'] ? $rules['author_separator'].strtoupper($author_name) : strtoupper($author_name) ;
            
        } else {
            $author_name = "";
        } 
        $rule = str_replace('{{AUTHERNAME}}',$author_name, $rule);
    }
    
    if (in_array("{{TITLENAME}}", $rule_contain)) {
        if(isset($library['title']) && $library['title']) {
            $title = $library['title'];
            $title = isset($rules['title_length']) && $rules['title_length']? substr($title,0,$rules['title_length']) : substr($title,0,1) ;
            $title = isset($rules['title_separator']) && $rules['title_separator'] ? $rules['title_separator'].$title : $title ;
        } else if(isset($library['title_id']) && $library['title_id']) {
            $CI->load->model('model_library_periodicals');
            $title_id = $library['title_id'];
            $title_info = $CI->model_library_periodicals->get_periodical_titile_by_id($title_id);
            $title = $title_info['title'];
            $title = isset($rules['title_length']) && $rules['title_length']? substr($title,0,$rules['title_length']) : substr($title,0,1) ;
            $title = isset($rules['title_separator']) && $rules['title_separator'] ? $rules['title_separator'].$title : $title ;
        } else {
            $title = "";
        } 
        $rule = str_replace('{{TITLENAME}}',$title, $rule);
    }
    
    if (in_array("{{EDITION}}", $rule_contain)) {
        if(isset($library['edition']) && $library['edition']) {
            $edition = $library['edition'];
            $edition = isset($rules['edition_separator']) && $rules['edition_separator'] ? $rules['edition_separator'].$edition : $edition ;
        } else {
            $edition = "";
        }
        $rule = str_replace('{{EDITION}}',$edition, $rule);
    }
    
    if (in_array("{{VOLUME}}", $rule_contain)) {
        if(isset($library['volume']) && $library['volume']) {
            $volume = $library['volume'];
            $volume = isset($rules['volume_separator']) && $rules['volume_separator'] ? $rules['volume_separator'].$volume : $volume ;
        } else {
            $volume = "";
        }
       $rule = str_replace('{{VOLUME}}',$volume, $rule);
    }
    
//   var_dump($rule);die; 
    //$spine_label = $rule;
    echo ltrim($rule);
    
}

function echo_spine_label($library) {
    
    $CI = & get_instance();
    $CI->load->model('model_library');
    $spine_label = "";
    
    $spine_label_generation_rules = get_option(array('option' => 'library_spine_label_generation_rule','institute_id'=>$library['institute_id']));
    $rules = json_decode($spine_label_generation_rules,TRUE);
    
    $rule = $rules['rule'];
    
    $search_words = array("{{INSTITUTENAME}}","{{REFERENCE}}","{{TITLENAME}}","{{CLASSIFICATIONNO}}","{{AUTHERNAME}}","{{SUBJECT}}",
        "{{EDITION}}","{{VOLUME}}","{{ACCESSIONNO}}");
    
    $rule_contain = get_string_contain_words_in_array($rule,$search_words);
    
    if (in_array("{{REFERENCE}}", $rule_contain)) {
        if(isset($library['reference']) && $library['reference']) {
            $reference_label = "R ";
        } else {
            $reference_label = "";
        }
        $rule = str_replace('{{REFERENCE}}',$reference_label, $rule);
    }
    
    if (in_array("{{INSTITUTENAME}}", $rule_contain)) {
        if(isset($library['institute_id']) && $library['institute_id']) {
            $CI->load->model('model_institutes');
            $institute_info = $CI->model_institutes->get_institute_by_id($library['institute_id']);
            $institute_code = isset($institute_info['code'])?$institute_info['code']." ":"";
            $institute_code = isset($rules['institute_code_separator']) && $rules['institute_code_separator'] ? $rules['institute_code_separator'].$institute_code : $institute_code;
        } else {
            $institute_code = "";
        }
        $rule =  str_replace('{{INSTITUTENAME}}',$institute_code, $rule);
    }
    
    if (in_array("{{CLASSIFICATIONNO}}", $rule_contain)) {
        if(isset($library['subject_id']) && $library['subject_id']) {
            $subject_info = $CI->model_library->get_library_subject($library['subject_id']);
            $subject_code = isset($subject_info['classification_no'])?$subject_info['classification_no']." ":"";
            $subject_code = isset($rules['classification_no_separator']) && $rules['classification_no_separator'] ? $rules['classification_no_separator'].$subject_code : $subject_code;
        } else {
            $subject_code = "";
        }
        $rule =  str_replace('{{CLASSIFICATIONNO}}',$subject_code, $rule);
    }
    
    if (in_array("{{SUBJECT}}", $rule_contain)) {
        if(isset($library['subject_id']) && $library['subject_id']) {
            $subject_info = $CI->model_library->get_library_subject($library['subject_id']);
            $subject_name = isset($subject_info['subject_name'])?$subject_info['subject_name']." ":"";
            $subject_name = substr($subject_name,0,1) ;
            $subject_name = isset($rules['subject_separator']) && $rules['subject_separator'] ? $rules['subject_separator'].$subject_name : $subject_name ;
            
        } else {
            $subject_name = "";
        } 
        $rule = str_replace('{{SUBJECT}}',$subject_name, $rule);
    }
    
    if (in_array("{{AUTHERNAME}}", $rule_contain)) {
        if(isset($library['author_id']) && $library['author_id']) {
            //$author_ids = explode(',', $author_ids);
            $author_id = $library['author_id'][0];
            $author_info = $CI->model_library->get_author_details($author_id);
            $author_name = $author_info['name'];
            $author_name = isset($rules['auther_name_length']) && $rules['auther_name_length']? substr($author_name,0,$rules['auther_name_length']) : substr($author_name,0,$rules['auther_name_length']) ;
            $author_name = isset($rules['author_separator']) && $rules['author_separator'] ? $rules['author_separator'].strtoupper($author_name) : strtoupper($author_name) ;
            
        } else {
            $author_name = "";
        } 
        $rule = str_replace('{{AUTHERNAME}}',$author_name, $rule);
    }
    
    if (in_array("{{TITLENAME}}", $rule_contain)) {
        if(isset($library['title']) && $library['title']) {
            $title = $library['title'];
            $title = isset($rules['title_length']) && $rules['title_length']? substr($title,0,$rules['title_length']) : substr($title,0,1) ;
            $title = isset($rules['title_separator']) && $rules['title_separator'] ? $rules['title_separator'].$title : $title ;
        } else if(isset($library['title_id']) && $library['title_id']) {
            $CI->load->model('model_library_periodicals');
            $title_id = $library['title_id'];
            $title_info = $CI->model_library_periodicals->get_periodical_titile_by_id($title_id);
            $title = $title_info['title'];
            $title = isset($rules['title_length']) && $rules['title_length']? substr($title,0,$rules['title_length']) : substr($title,0,1) ;
            $title = isset($rules['title_separator']) && $rules['title_separator'] ? $rules['title_separator'].$title : $title ;
        } else {
            $title = "";
        } 
        $rule = str_replace('{{TITLENAME}}',$title, $rule);
    }
    
    if (in_array("{{EDITION}}", $rule_contain)) {
        if(isset($library['edition']) && $library['edition']) {
            $edition = $library['edition'];
            $edition = isset($rules['edition_separator']) && $rules['edition_separator'] ? $rules['edition_separator'].$edition : $edition ;
        } else {
            $edition = "";
        }
        $rule = str_replace('{{EDITION}}',$edition, $rule);
    }
    
    if (in_array("{{VOLUME}}", $rule_contain)) {
        if(isset($library['volume']) && $library['volume']) {
            $volume = $library['volume'];
            $volume = isset($rules['volume_separator']) && $rules['volume_separator'] ? $rules['volume_separator'].$volume : $volume ;
        } else {
            $volume = "";
        }
       $rule = str_replace('{{VOLUME}}',$volume, $rule);
    }
    
    if (in_array("{{ACCESSIONNO}}", $rule_contain)) {
        if(isset($library['accession_no']) && $library['accession_no']) {
            $accession_no = $library['accession_no'];
            $accession_no = isset($rules['accession_no_separator']) && $rules['accession_no_separator'] ? $rules['accession_no_separator'].$accession_no : $accession_no ;
        } else {
            $accession_no = "";
        } 
        $rule = str_replace('{{ACCESSIONNO}}',$accession_no, $rule);
    }
    
//   var_dump($rule);die; 
    //$spine_label = $rule;
    return ltrim($rule);
    
}

function echo_member_groups($institute,$select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_library_member_group');
    $member_groups = $CI->model_library_member_group->get_member_groups($institute);
    
    foreach ($member_groups as $group) {
        if($select == $group['id']) {
            $echo .= "<option selected value='$group[id]'>$group[group_name]</option>";
        } else {
            $echo .= "<option value='$group[id]'>$group[group_name]</option>";
        }
    }
    if ($print == true){
        echo $echo;
    } else {
        return $echo;
    }
}

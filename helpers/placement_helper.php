<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function check_student_group_access($company_id,$student_group) {
    $CI = & get_instance();
    $CI->load->model('model_company');
    $result = $CI->model_company->check_student_group_access_by_company_id($company_id,$student_group);
    return $result;
}

function check_student_access_for_company($company_id,$student_id) {
    $CI = & get_instance();
    $CI->load->model('model_company');
    $result = $CI->model_company->check_student_access_by_company_id($company_id,$student_id);
    return $result;
}

function echo_interviews($year, $select = false,$print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_placements');
    $interviews = $CI->model_placements->get_year_wise_interviews($year);
    foreach ($interviews as $interview) {
        if ($select == $interview['id']) {
            $echo .= "<option selected value='$interview[id]'>$interview[name]</option>";
        } else {
            $echo .= "<option value='$interview[id]'>$interview[name]</option>";
        }
    }
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
} 

function echo_placement_student_groups($select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_placements');
    $student_groups = $CI->model_placements->get_placement_student_groups();
    foreach ($student_groups as $groups) {
        if ($select == $groups['id']) {
            $echo .= "<option selected value='$groups[id]'>$groups[group_name]</option>";
        } else {
            $echo .= "<option value='$groups[id]'>$groups[group_name]</option>";
        }
    }
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_interview_years($select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_placements');
    $years = $CI->model_placements->get_interview_years();
    foreach ($years as $year) {
        if ($select == $year['year']) {
            $echo .= "<option selected value='$year[year]'>$year[year]</option>";
        } else {
            $echo .= "<option value='$year[year]'>$year[year]</option>";
        }
    }
    if($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}


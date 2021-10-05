<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function echo_hostel_block($hostel, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_hostel');
    
    $blocks = $CI->model_hostel->get_hostel_block_details($hostel);
    foreach ($blocks as $block) {
        if ($select == $block['id']) {
            $echo .= "<option selected value='$block[id]'>$block[block_name]</option>";
        } else {
            $echo .= "<option value='$block[id]'>$block[block_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_hostel_floor($block, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_hostel');
    $floors = $CI->model_hostel->get_hostel_floor($block);
    
    foreach ($floors as $floor) {
        if ($select == $floor['id']) {
            $echo .= "<option selected value='$floor[id]'>$floor[floor_name]</option>";
        } else {
            $echo .= "<option value='$floor[id]'>$floor[floor_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_hostel_room($floor, $select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_hostel');
    $rooms = $CI->model_hostel->get_hostel_rooms($floor);
    foreach ($rooms as $room) {
        if ($select == $room['id']) {
            $echo .= "<option selected value='$room[id]'>$room[room_no]</option>";
        } else {
            $echo .= "<option value='$room[id]'>$room[room_no]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_institute_hostel($institute, $select = false, $print = true) {
     $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_hostel');
    $hostels = $CI->model_hostel->get_hostels($institute);
    foreach ($hostels as $hostel) {
        if ($select == $hostel['id']) {
            $echo .= "<option selected value='$hostel[id]'>$hostel[hostel_name]</option>";
        } else {
            $echo .= "<option value='$hostel[id]'>$hostel[hostel_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}
    
    function echo_vacant_hostel_room($floor, $select = false, $print = true) {
        $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_hostel');
    $rooms = $CI->model_hostel->get_hostel_rooms($floor);
    foreach ($rooms as $room) {
        $member_count = $CI->model_hostel->get_room_members_count($room['id']);
        
            if ($select == $room['id']) {
                $echo .= "<option selected value='$room[id]'>$room[room_no]</option>";
            } else {
                if($member_count < $room['capacity']) {
                $echo .= "<option value='$room[id]'>$room[room_no]</option>";
            }
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
    }



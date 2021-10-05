<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function echo_gallery_categories($institute,$select = false, $print = true) {
    $CI = & get_instance();
    $echo = '';
    $CI->load->model('model_gallery');
    $categories = $CI->model_gallery->get_gallery_categories($institute);
    foreach ($categories as $category){
        if ($select == $category['id']){
            $echo .= "<option selected value='$category[id]'>$category[album_name]</option>";
        } else {
            $echo .= "<option value='$category[id]'>$category[album_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}
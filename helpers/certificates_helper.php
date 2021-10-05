<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function get_parent_bins($bin_id){
    $CI = & get_instance();
    $CI->load->model('model_certificate');
    $parent_bins = $CI->model_certificate->get_parent_bins($bin_id);
    return $parent_bins;
}

function echo_bins($institute,$select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_certificate');
    $bins = $CI->model_certificate->get_certificate_bins($institute);
    foreach ($bins as $bin) {
        if ($select == $bin['id']) {
            $echo .= "<option selected value='$bin[id]'>$bin[name]</option>";
        } else {
            $echo .= "<option value='$bin[id]'>$bin[name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_secondary_bins($bin,$select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_certificate');
    $bins = $CI->model_certificate->get_secondary_bin_details($bin);
    if(isset($bins) && $bins) {
            $echo .= '
                <div class="col-sm-6">
                    <div class="form-group">
                        <div class="col-sm-10">
                        <select  class="form-control select-bin"  name="bin">
                            <option value="">Select One</option>';
            foreach ($bins as $bin) {
                if ($select == $bin['id']) {
                    $echo .= "<option selected value='$bin[id]'>$bin[name]</option>";
                } else {
                    $echo .= "<option value='$bin[id]'>$bin[name]</option>";
                }
            }
                            
            $echo .= '</select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary get_bin">Assign</button>
                    </div>
                </div>
           ';
        
        if ($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
    }    
}


function echo_service_provider($institute, $select = false, $print = true) {
        $echo = "";
        $CI = & get_instance();
        $CI->load->model('model_service_provider');
        $service_providers = $CI->model_service_provider->get_service_provider($institute);
        foreach ($service_providers  as $service_provider) {
            if (isset($select) && $select == $service_provider['id']) {
                $echo .= "<option selected value='$service_provider[id]'>$service_provider[service_provider]</option>";
            } else {
                $echo .= "<option value='$service_provider[id]'>$service_provider[service_provider]</option>";
            }
        }
        if ($print == true) {
            echo $echo;
        } else {
            return $echo;
        }
}


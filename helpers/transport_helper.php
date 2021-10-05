<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

function echo_vehicle_types($select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_transport');
    $vehicle_types = $CI->model_transport->get_vehicle_types();
    foreach ($vehicle_types as $vehicle_type) {
        if ($select == $vehicle_type['id']) {
            $echo .= "<option selected value='$vehicle_type[id]'>$vehicle_type[type]</option>";
        } else {
            $echo .= "<option value='$vehicle_type[id]'>$vehicle_type[type]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_vehicles($institute,$vehicle_type = false, $select = false, $print = true){
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_transport');        
    $vehicles = $CI->model_transport->get_transport_vehicles($institute,$vehicle_type,'','','active');          
    foreach ($vehicles as $vehicle) {
        if ($select == $vehicle['id']) {
            $echo .= "<option selected value='$vehicle[id]'>";
                        if($vehicle[vehicle_no]) {
                            $echo .=  "$vehicle[vehicle_no]</option>";
                        } else {
                            $echo .=  "$vehicle[reg_no]</option>"; 
                        }
        } else {
            $echo .= "<option value='$vehicle[id]'>";
                        if($vehicle[vehicle_no]) {
                            $echo .=  "$vehicle[vehicle_no]</option>";
                        } else {
                            $echo .=  "$vehicle[reg_no]</option>";
                        }
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_vehicle_expense_types($select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_transport');
    $expense_types = $CI->model_transport->get_vehicle_expense_types();
    foreach ($expense_types as $expense_type) {
        if ($select == $expense_type['id']) {
            $echo .= "<option selected value='$expense_type[id]'>$expense_type[type]</option>";
        } else {
            $echo .= "<option value='$expense_type[id]'>$expense_type[type]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_tansport_areas($institute,$select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_transport');
    $route_areas = $CI->model_transport->get_institute_assigned_areas($institute);
    foreach ($route_areas as $area) {
        if ($select == $area['id']) {
            $echo .= "<option selected value='$area[id]'>$area[route_area]</option>";
        } else {
            $echo .= "<option value='$area[id]'>$area[route_area]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_tansport_routes($institute,$select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_transport');
    $routes = $CI->model_transport->get_institute_assigned_area_routes($institute);
    foreach ($routes as $route) {
        if ($select == $route['id']) {
            $echo .= "<option selected value='$route[id]'>$route[route_name] - $route[route_no]</option>";
        } else {
            $echo .= "<option value='$route[id]'>$route[route_name] - $route[route_no]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_tansport_pickup_points($institute,$select = false, $print = true) {
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_transport');
    $pickup_points = $CI->model_transport->get_tansport_pickup_points($institute);
    foreach ($pickup_points as $pickup_point) {
        if ($select == $pickup_point['id']) {
            $echo .= "<option selected value='$pickup_point[id]'>$pickup_point[point_name]</option>";
        } else {
            $echo .= "<option value='$pickup_point[id]'>$pickup_point[point_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_route_pickup_points($institute,$route,$select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_transport');
    $pickup_points = $CI->model_transport->get_pickup_points_assigned_to_route($institute,$route);
    foreach ($pickup_points as $pickup_point) {
        if ($select == $pickup_point['id']) {
            $echo .= "<option selected value='$pickup_point[id]'>$pickup_point[point_name]</option>";
        } else {
            $echo .= "<option value='$pickup_point[id]'>$pickup_point[point_name]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_route_vehicles($institute,$route,$select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $CI->load->model('model_transport');
    $vehicles = $CI->model_transport->get_vehicles_assigned_to_route($institute,$route);
    foreach ($vehicles as $vehicle) {
         if ($select == $vehicle['id']) {
            $echo .= "<option selected value='$vehicle[id]'>$vehicle[vehicle_no]</option>";
        } else {
            $echo .= "<option value='$vehicle[id]'>$vehicle[vehicle_no]</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

function echo_vehicle_ownership($select = false,$print = true) {
    
    $echo = "";
    $CI = & get_instance();
    $vehicle_ownerships = config_item('vehicle_ownership');
    foreach ($vehicle_ownerships as $key=>$value) {
         if ($select == $key) {
            $echo .= "<option selected value='$key'>$value</option>";
        } else {
            $echo .= "<option value='$key'>$value</option>";
        }
    }
    if ($print == true) {
        echo $echo;
    } else {
        return $echo;
    }
}

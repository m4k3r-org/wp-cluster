<?php

namespace bjork\views\generic;

use bjork\shortcuts as s;

function direct_to_template($request, $template_name, array $kwargs=null) {
    return s::render($request, $template_name, $kwargs);
}

function redirect($request, $to, $permanent=false) {
    return s::redirect($request, $to, $permanent);
}

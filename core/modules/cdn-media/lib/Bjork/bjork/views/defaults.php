<?php

namespace bjork\views\defaults;

use bjork\http\HttpResponseNotFound,
    bjork\http\HttpResponseServerError,
    bjork\template\loader,
    bjork\template\context\RequestContext;

function page_not_found($request) {
    $t = loader::get_template('404.php');
    $c = new RequestContext($request, array(
        'request_path' => $request->getPath(),
    ));
    return new HttpResponseNotFound($t->render($c), 'text/html');
}

function server_error($request) {
    $t = loader::get_template('500.php');
    $c = new RequestContext($request);
    return new HttpResponseServerError($t->render($c), 'text/html');
}

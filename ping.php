<?php

$response = array(
  "ok" => true,
  "message" => "Service vailable.",
  "request" => $_REQUEST
);

die(json_encode( $response ));

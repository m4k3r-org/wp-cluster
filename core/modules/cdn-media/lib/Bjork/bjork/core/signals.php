<?php

namespace bjork\core\signals;

use bjork\dispatch;

dispatch::register_signal('bjork.request_started');
dispatch::register_signal('bjork.request_finished');
dispatch::register_signal('bjork.got_request_exception', 'request');

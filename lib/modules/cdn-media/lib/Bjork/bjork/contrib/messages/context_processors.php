<?php

namespace bjork\contrib\messages\context_processors;

use bjork\contrib\messages;

/**
* Returns a lazy 'messages' context variable.
*/
function messages($request) {
    return array(
        'messages' => messages::get_messages($request),
    );
}

<?php

namespace bjork\contrib\messages\middleware;

use bjork\conf\settings,
    bjork\contrib\messages\storage;

/**
* Middleware that handles temporary messages.
*/
class MessageMiddleware {
    
    function processRequest($request) {
        $request['_messages'] = storage::get_default_storage($request);
    }
    
    /**
    * Updates the storage backend (i.e., saves the messages).
    *
    * If not all messages could be stored and ``DEBUG`` is ``True``, an
    * ``OverflowException`` is raised.
    */
    function processResponse($request, $response) {
        // A higher middleware layer may return a request which does not
        // contain messages storage, so make no assumption that it will
        // be there.
        if ($request->hasKey('_messages')) {
            $unstored_messages = $request['_messages']->update($response);
            if (!empty($unstored_messages) && settings::get('DEBUG'))
                throw new \OverflowException(
                    'Not all temporary messages could be saved.');
        }
        return $response;
    }
}

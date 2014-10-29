<?php
/*********************************************************************************************

Content Validation

*********************************************************************************************/
function content_validation( $content ) {
    $content = str_replace( array( '<b>', '</b>' ), array( '<strong>', '</strong>' ), $content );
    $content = str_replace( '></param>', ' />', $content );
    $content = str_replace( '></embed>', ' />', $content );
    $content = str_replace( '<object', '<object type="video/flv"', $content );
    return $content;
}
?>
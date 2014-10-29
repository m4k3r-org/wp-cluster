<?php 
$portfolio_page = of_get_option('sc_portfoliodesc');
$template_name = get_post_meta( $portfolio_page, '_wp_page_template', true );

?>
<?php
$template_name = get_post_meta( $audio_page, '_wp_page_template', true );
$template_name = get_post_meta( $event_page, '_wp_page_template', true );
?>

<?php include ($template_name) ?>
<?php
if( !function_exists( 'site_has_feature_flag' ) ) {
  function site_has_feature_flag( $flag ) {
    return UsabilityDynamics\Feature\Flag::get( $flag, 'site' );
  }
}

if( !function_exists( 'network_has_feature_flag' ) ) {
  function network_has_feature_flag( $flag ) {
    return UsabilityDynamics\Feature\Flag::get( $flag, 'network' );
  }
}

if( !function_exists( 'user_has_feature_flag' ) ) {
  function user_has_feature_flag( $flag ) {
    return UsabilityDynamics\Feature\Flag::get( $flag, 'user' );
  }
}
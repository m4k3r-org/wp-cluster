## Features
* Conditional feature-tag handler.
* Module loader.

## Setting Flags

* HTTP_X_FEATURE_FLAGS - Header value, or setting in $_SERVER[HTTP_X_FEATURE_FLAGS]
* WP_FEATURE_FLAGS - Constant.
* WP_FEATURE_FLAGS - Environment.

## Usage

```php
    use UsabilityDynamics\Feature;

    Feature\Flag::set( array( 'one,two,three' ) );
    Feature\Flag::set( 'four,five,six' );
    Feature\Flag::set( 'user::name' );

    if( user_has_feature_flag( 'name' ) ) {
      die( 'user has name' );
    }
```

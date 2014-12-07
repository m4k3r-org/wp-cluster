<?php

namespace DiscoDonniePresents {

  if ( !class_exists( 'DiscoDonniePresents\Microdata' ) ) {

    class Microdata {

      protected static $URL = '';
      protected static $SCHEMA_JSON_DIR = '';
      protected static $ENTITY_NAMESPACE = '';

      public static function init() {
        self::$URL = 'http://schema.org/';
        self::$SCHEMA_JSON_DIR = get_stylesheet_directory() . '/vendor/usabilitydynamics/lib-model/static/schemas/';
        self::$ENTITY_NAMESPACE = __NAMESPACE__;
      }

      /**
       * Function to output microdata manually in a valid format
       * @param string $prop the itemprop (optional)
       * @param string $type the itemtype (optional)
       * @return string valid microdata string containing the parameters or empty string if nothing provided
       */
      public static function manual( $prop = '', $type = '' ) {
        if ( !empty( $prop ) ) {
          $prop = ' itemprop="' . $prop . '"';
        }

        if ( !empty( $type ) ) {
          $type = ucfirst( $type );
          $type = self::$URL . $type;
          $type = ' itemscope itemtype="' . $type . '"';
        }

        return $prop . $type;
      }

      /**
       * Function to output itemtype (and optionally itemprop) for an object.
       * Note: the output should be used INSIDE an HTML element as attributes.
       * @param object $object an entity object to display the type for
       * @param string $prop an optional itemprop to display along the itemtype
       * @param boolean $novalidate if true, the type is not validated (optional, default is false)
       * @return string the itemtype and itemprop, or nothing if type is invalid
       */
      public static function type( $object, $prop = '', $novalidate = false ) {
        $class = get_class( $object );
        $class = str_replace( self::$ENTITY_NAMESPACE . '\\', '', $class );
        $type = self::get_mapped_type( $class );
        $type = self::get_valid_type( $type, $novalidate );

        if ( $type != null ) {
          if ( !empty( $prop ) ) {
            $prop = ' itemprop="' . $prop . '"';
          }
          return $prop . ' itemtype="' . $type . '"';
        }
        return '';
      }

      /**
       * Function to output link and name for a group of entities
       * @param mixed $objects either an entity object or an array of entity objects
       * @param boolean $wrap the HTML element to wrap the link with (either div, p or span); if false, the link is not wrapped
       * @param string $wrapper_property itemprop to use in the wrapping element
       * @param string $separator how to separate the links
       * @param string $before any output to place before the link, but inside the wrapper (works only if one object is passed)
       * @param string $after any output to place after the link, but inside the wrapper (works only if one object is passed)
       * @param boolean $novalidate if true, the type is not validated (optional, default is false)
       * @return string the links for the entities with microdata applied
       */
      public static function link( $objects, $wrap = false, $wrapper_property = '', $separator = ', ', $before = '', $after = '', $novalidate = false ) {
        $links = array();

        if ( !is_array( $objects ) ) {
          $objects = array( $objects );
        }

        // do not allow before and after if multiple links
        if ( count( $objects ) > 1 ) {
          $before = '';
          $after = '';
        }

        if ( $wrap !== false ) {
          $wrap = !in_array( $wrap, array( 'div', 'p', 'span' ) ) ? 'span' : $wrap;

          if ( !empty( $wrapper_property ) ) {
            $wrapper_property = ' itemprop="' . $wrapper_property . '"';
          }
        }

        foreach ( $objects as $object ) {

          if ( is_a( $object, self::$ENTITY_NAMESPACE . '\\Entity' ) ) {
            $id = $object->post( 'ID' );

            $output = $before . '<a href="' . get_permalink( $id ) . '" itemprop="url"><span itemprop="name">' . get_the_title( $id ) . '</span></a>' . $after;
            
            if ( $wrap ) {

              $class = get_class( $object );
              $class = str_replace( self::$ENTITY_NAMESPACE . '\\', '', $class );
              $type = self::get_mapped_type( $class );
              $type = self::get_valid_type( $type, $novalidate );
              if ( $type != null ) {
                $type = ' itemtype="' . $type . '"';
              }

              $output = '<' . $wrap . $wrapper_property . $type . '>' . $output . '</' . $wrap . '>';

            }

            $links[] = $output;

          }

        }

        return implode( $separator, $links );
      }

      /**
       * Function to output microdata meta, invisible to the user, but visible for search engines.
       * This function should be used for properties which cannot actually be displayed anywhere on the page.
       * @param object $object an entity object to retrieve data from
       * @param array $fields the fields to include in the meta information
       * @return string HTML code for the meta information
       */
      public static function meta( $object, $fields = array() ) {
        $output = '';

        if ( is_a( $object, self::$ENTITY_NAMESPACE . '\\Entity' ) ) {

          $id = $object->post( 'ID' );
          $mappings = array(
            'name'        => array( 'get_the_title', $id ),
            'url'         => array( 'get_permalink', $id ),
            'startDate'   => array( array( $object, 'meta' ), 'dateStart' ),
            'endDate'     => array( array( $object, 'meta' ), 'dateEnd' ),
            'sameAs'      => array( array( $object, 'meta' ), 'officialLink' ),
          );

          foreach ( $fields as $prop ) {
            if ( isset( $mappings[ $prop ] ) ) {
              $value = call_user_func( $mappings[ $prop ][0], $mappings[ $prop ][1] );
            } else {
              $value = $object->meta( $prop );
            }
            if ( $value && is_string( $value ) ) {
              if ( strpos( $value, 'http://' ) === 0 || strpos( $value, 'http://' ) === 0 ) {
                $output .= '<link itemprop="' . $prop . '" href="' . $value . '">';
              } elseif ( preg_match( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?/', $value ) ) {
                $output .= '<time itemprop="' . $prop . '" datetime="' . $value . '"></time>';
              } else {
                $output .= '<meta itemprop="' . $prop . '" content="' . $value . '">';
              }
            }
          }

        }

        return $output;
      }

      /**
       * This handler function is attached to some entity functions to make applying microdata as automatic as possible.
       * @param  array $args an array of microdata arguments
       * @return string the HTML string with microdata included
       */
      public static function handler( $args = array() ) {
        if ( !is_array( $args ) ) {
          return '';
        }

        extract( wp_parse_args( $args, array(
          'build_mode'        => '',
          'fields'            => array(),
          'origin_function'   => '',
          'origin_class'      => '',
          'super_type'        => null,
          'super_prop'        => null,
          'super_super_type'  => null,
          'novalidate'        => false,
          'image_size'        => 'thumbnail',
        ) ) );

        $origin_class = str_replace( self::$ENTITY_NAMESPACE . '\\', '', $origin_class );

        $fields = self::map( $fields, $origin_class, $origin_function );

        if ( !is_array( $fields ) || count( $fields ) == 0 ) {
          return '';
        }

        if ( $build_mode == '' ) {
          $build_mode = 'text';
          if ( in_array( $origin_function, array( 'termsToString' ) ) ) {
            $build_mode = 'link';
          } elseif ( in_array( $origin_function, array( 'image' ) ) ) {
            $build_mode = 'image';
          } elseif ( isset( $fields['locationAddress'] ) ) {
            $build_mode = 'address';
            $super_type = 'PostalAddress';
          }
        }

        $output = '';

        $original_super_type = $super_type;

        $super_open = $super_close = '';
        if ( $super_type != null ) {
          $super_type = ucfirst( $super_type );
          if ( ( $super_type = self::get_valid_type( $super_type, $novalidate ) ) != null ) {
            $super_open = '<span';
            if ( $super_prop != null ) {
              if ( ( $super_prop = self::get_valid_prop( $super_prop, $super_super_type, $novalidate ) ) != null ) {
                $super_open .= ' itemprop="' . $super_prop . '"';
              }
            }
            $super_open .= ' itemscope';
            $super_open .= ' itemtype="' . $super_type . '"';
            $super_open .= '>';
            $super_close = '</span>';
          }
        }

        if ( $super_type == null && !empty( $origin_class ) ) {
          $super_type = self::get_mapped_type( $origin_class );
        }

        $disable_microdata = false;
        if( $original_super_type != null && $original_type != $super_type ) {
          $disable_microdata = true;
        }

        switch ( $build_mode ) {
          case 'address':
            $first_key = key( $fields );
            $parts = explode( ', ', $fields[ $first_key ] );
            foreach( $parts as $key => &$part ) {
              if ( $key == 0 ) {
                $part = '<span itemprop="streetAddress">' . $part . '</span>';
              } elseif ( preg_match( '/^[A-Z]{2} [0-9]{5}$/', $part ) ) {
                $p = explode( ' ', $part );
                $p[0] = '<span itemprop="addressRegion">' . $p[0] . '</span>';
                $p[1] = '<span itemprop="postalCode">' . $p[1] . '</span>';
                $part = implode( ' ', $p );
              } elseif ( $part == 'USA' ) {
                $part = '<span itemprop="addressCountry" itemscope itemtype="' . self::get_valid_type( 'Country' ) . '"><span itemprop="name">' . $part . '</span></span>';
              } elseif( preg_match( '/^[^0-9]*$/', $part ) )
              {
                $part = '<span itemprop="addressLocality">' . $part . '</span>';
              }
            }
            $output = implode( ', ', $parts );
            break;
          case 'image':
            $prop = '';
            foreach ( $fields as $key => $value ) {
              if ( is_int( $value ) ) {
                $prop = $key;
                break;
              }
            }
            $attr = array();
            if ( !$disable_microdata && ( $itemprop = self::get_valid_prop( $prop, $super_type, $novalidate ) ) != null ) {
              $attr['itemprop'] = $itemprop;
            }
            $output = wp_get_attachment_image( $fields[ $prop ], $image_size, false, $attr );
            break;
          case 'link':
            $text_prop = $url_prop = '';
            foreach ( $fields as $key => $value ) {
              if ( strpos( $value, 'http://' ) === 0 || strpos( $value, 'https://' ) === 0 ) {
                $url_prop = $key;
              } else {
                $text_prop = $key;
              }
              if ( $text_prop != '' && $url_prop != '' ) {
                break;
              }
            }
            $text_itemprop = '';
            if ( !$disable_microdata && ( $text_itemprop = self::get_valid_prop( $text_prop, $super_type, $novalidate ) ) != null ) {
              $text_itemprop = ' itemprop="' . $text_itemprop . '"';
            }
            $output = '<span' . $text_itemprop . '>' . $fields[ $text_prop ] . '</span>';
            if ( $url_prop != '') {
              $url_itemprop = '';
              if ( !$disable_microdata && ( $url_itemprop = self::get_valid_prop( $url_prop, $super_type, $novalidate ) ) != null ) {
                $url_itemprop = ' itemprop="' . $url_itemprop . '"';
              }
              $output = '<a' . $url_itemprop . ' href="' . $fields[ $url_prop ] . '">' . $output . '</a>';
            }
            break;
          case 'text':
          default:
            reset( $fields );
            $prop = key( $fields );
            $itemprop = '';
            if ( !$disable_microdata && ( $itemprop = self::get_valid_prop( $prop, $super_type, $novalidate ) ) != null ) {
              $itemprop = ' itemprop="' . $itemprop . '"';
            }
            $output = '<span' . $itemprop . '>' . $fields[ $prop ] . '</span>';
        }

        if( $super_open != '' )
        {
          $output = $super_open . $output . $super_close;
        }

        return $output;
      }

      /**
       * This function prepares arguments to be compatible with the handler() method.
       * @param mixed $args usually an array (with user-created arguments); if neither array nor boolean true, no arguments will be created
       * @param array $fields the fields for this microdata structure
       * @param string $origin_class the class where the fields were retrieved
       * @param string $origin_function the function where the fields were retrieved
       * @param array $more_args any additional arguments which are automatically created
       * @return mixed an array of microdata args or false, if no microdata should be used
       */
      public static function prepare_args( $args, $fields, $origin_class = '', $origin_function = '', $more_args = array() ) {
        if ( $args === true ) {
          $args = array();
        }

        foreach ( $fields as $field ) {
          if ( is_array( $field ) ) {
            return false;
          }
        }

        if ( is_array( $args ) ) {
          $special_args = array(
            'fields'            => $fields,
            'origin_class'      => $origin_class,
            'origin_function'   => $origin_function,
          );
          $special_args = array_merge( $special_args, $more_args );
          return array_merge( $special_args, $args );
        }

        return false;
      }

      /**
       * This function validates a type.
       * @param string $type the type to validate
       * @param boolean $novalidate if true, the type is not validated (optional, default is false)
       * @return mixed the validated type or null if invalid
       */
      protected static function get_valid_type( $type = null, $novalidate = false ) {
        if ( $type !== null ) {
          $type = (string) $type;
          $type = ucfirst( $type );

          if ( !$novalidate ) {

            $json = self::get_type_json( $type );
            if ( $json ) {
              $type = $json['type'];
            } else {
              return null;
            }

          }

          $type = self::$URL . $type;
        }
        return $type;
      }

      /**
       * This function validates a prop. However, it only does so if the superior type is provided.
       * @param string $prop the prop to validate
       * @param string $type the superior type of the prop
       * @param boolean $novalidate if true, the prop is not validated (optional, default is false)
       * @return mixed the validated prop or null if invalid
       */
      protected static function get_valid_prop( $prop = null, $type = null, $novalidate = false ) {
        if ( $prop !== null ) {
          $prop = (string) $prop;

          if ( $type !== null && !$novalidate ) {

            $json = self::get_type_json( $type );
            if ( $json ) {

              $found = false;
              foreach ( $json['bases'] as $base => $props ) {
                foreach ( $props as $p ) {
                  if ( isset( $p['name'] ) && $p['name'] == $prop ) {
                    $found = true;
                    break;
                  }
                }
                if ( $found ) {
                  break;
                }
              }

              if ( !$found ) {
                $prop = null;
              }

            } else {
              $prop = null;
            }
            
          }

        }
        return $prop;
      }

      /**
       * Gets the Schema JSON for a type.
       * @param string $type the type to retrieve the Schema JSON for
       * @return mixed an array of Schema information or false if nothing found
       */
      protected static function get_type_json( $type ) {
        $type = str_replace( self::$URL, '', $type );
        $type = strtolower( $type );
        if ( file_exists( self::$SCHEMA_JSON_DIR . $type . '.json' ) ) {
          $data = file_get_contents( self::$SCHEMA_JSON_DIR . $type . '.json' );
          $data = json_decode( $data, true );
          if ( is_array( $data ) ) {
            return $data;
          }
        }
        return false;
      }

      /**
       * Maps an array of keys to their actual properties if possible. If not, use the key as property.
       * Edit the get_mappings() function to adjust the key => prop mapping.
       * @param array $fields the array of prop keys and values
       * @param string $origin_class the class where the fields were retrieved
       * @param string $origin_function the function where the fields were retrieved
       * @return array the array of mapped prop keys and values
       */
      protected static function map( $fields, $origin_class = '', $origin_function = '' ) {
        $mapped_fields = array();

        $mappings = self::get_mappings( $origin_class, $origin_function, false );

        foreach ( $fields as $key => $value ) {

          if ( isset( $mappings[ $key ] ) ) {
            $mapped_fields[ $mappings[ $key ] ] = $value;
          } else {
            $mapped_fields[ $key ] = $value;
          }

        }
        return $mapped_fields;
      }

      /**
       * Retrieves mappings for a specific class and function. Edit the mappings array to adjust the key => prop mapping.
       * @param string $origin_class the class to retrieve mappings for
       * @param string $origin_function the function to retrieve mappings for
       * @param boolean $all_if_not_found if true, all mappings will be retrieved if class or function do not have any mappings (optional, default is false)
       * @return array an array of mappings, depending on the function parameters
       */
      protected static function get_mappings( $origin_class = '', $origin_function = '', $all_if_not_found = false ) {
        $mappings = array(
          'Event'           => array(
            'post'            => array(
              'post_title'      => 'name',
            ),
            'image'           => array(
              'posterImage'     => 'image',
            ),
          ),
          'Venue'           => array(
            'post'            => array(
              'post_title'      => 'name',
            ),
            'image'           => array(
              'imageLogo'       => 'logo',
            ),
          ),
          'Artist'          => array(
            'post'            => array(
              'post_title'      => 'name',
            ),
            'image'           => array(
              'logo'            => 'logo',
              'headshotImage'   => 'image',
            ),
          ),
        );

        if ( empty( $origin_function ) ) {
          $origin_function = '_default';
        }
        if ( empty( $origin_class ) ) {
          $origin_class = '_Default';
        }

        if ( isset( $mappings[ $origin_class ] ) && isset( $mappings[ $origin_class ][ $origin_function ] ) ) {
          return $mappings[ $origin_class ][ $origin_function ];
        }

        if ( $all_if_not_found ) {
          return $mappings;
        }

        return array();
      }

      /**
       * Gets a mapped type for an origin class (if possible). This function is only used if no super_type is provided in the microdata handler function.
       * @param string $origin_class class name to map to a type
       * @return mixed the mapped type or null if the class could not be mapped to a type
       */
      protected static function get_mapped_type( $origin_class ) {
        $mappings = array(
          'Event'         => 'MusicEvent',
          'Venue'         => 'MusicVenue',
          'Artist'        => 'MusicGroup',
        );

        $origin_class = ucfirst( $origin_class );

        if ( isset( $mappings[ $origin_class ] ) ) {
          return $mappings[ $origin_class ];
        }

        return null;
      }

    }

  }

}

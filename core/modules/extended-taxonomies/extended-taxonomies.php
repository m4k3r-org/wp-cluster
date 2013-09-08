<?php
/**
 * Name: Extended Taxonomies
 * Version: 1.0
 * Description: Adds meta attributes to terms.
 * Author: Usability Dynamics, Inc.
 * Theme Feature: extended-taxonomies
 *
 * @name Maintenence
 * @description Widgets for the Flawless theme.
 * @author Usability Dynamics, Inc.
 * @version 1.0
 * @namespace Flawless
 * @module Maintenence
 */
namespace Flawless {

  /**
   * Class Extended_Taxonomies
   *
   * @package Flawless
   */
  class Extended_Taxonomies {

    /**
     * Constructor for the Extended Taxonomies.
     *
     * @author potanin@UD
     * @version 0.0.1
     * @method __construct
     *
     * @constructor
     * @for Extended_Taxonomies
     *
     * @param array $options
     */
    public function __construct( $options = array() ) {

      add_filter( 'flawless::generate_taxonomies', array( __CLASS__, 'generate_taxonomies', 2, 10 ) );

      add_filter( 'flawless::setup_theme_features::after', array( __CLASS__, 'setup_theme_features', 2, 10 ) );

    }

    /**
     * Ported from Flawless Core. Needs testing
     *
     * @method generate_taxonomies
     * @for Extended_Taxonomies
     */
    function generate_taxonomies( $data, $type ) {

      if ( current_theme_supports( 'extended-taxonomies' ) && !post_type_exists( $type ) ) {

        register_post_type( '_tp_' . $type, array(
          'label' => $data[ 'label' ],
          'public' => false,
          'rewrite' => false,
          'labels' => array(
            'name' => $data[ 'label' ],
            'edit_item' => 'Edit Term: ' . $data[ 'label' ]
          ),
          'supports' => array( 'title', 'editor' )
        ) );

        if ( $data[ 'allow_term_thumbnail' ] ) {
          add_post_type_support( '_tp_' . $type, 'thumbnail' );
          add_filter( 'manage_edit-' . $type . '_columns', create_function( '$c', ' return Flawless::array_insert_after( $c, "cb", array( "term_thumbnail" => "" )); ' ) );
          add_filter( 'manage_' . $type . '_custom_column', function ( $null, $column, $term_id ) {
            if ( $column == 'term_thumbnail' ) {
              echo wp_get_attachment_image( get_post_thumbnail_id( get_post_for_extended_term( $term_id )->ID ), array( 75, 75 ) );
            };
          }, 10, 3 );
        }

      }

      return $data;

    }

    function setup_theme_features() {
      global $wpdb;

      if ( current_theme_supports( 'term-meta' ) ) {
        $wpdb->taxonomymeta = $wpdb->prefix . 'taxonomymeta';
      }


    }

    function init_lower() {



      if ( current_theme_supports( 'extended-taxonomies' ) ) {
        add_action( 'wp_insert_post', array( __CLASS__, 'term_updated' ), 9, 2 );
        add_action( 'created_term', array( __CLASS__, 'term_updated' ), 9 );
        add_action( 'edit_term', array( __CLASS__, 'term_updated' ), 9 );
        add_action( 'delete_term', array( __CLASS__, 'delete_term' ), 9, 3 );
        add_action( 'load-edit-tags.php', array( __CLASS__, 'term_editor_loader' ) );
        add_action( 'load-post.php', array( __CLASS__, 'post_editor_loader' ) );
      }

    }

    /**
     * Prevents direct editing of Extended Term post pages by redirecting user to term page.
     *
     * @since 0.5.0
     */
    static function post_editor_loader() {

      if ( !is_numeric( $_GET[ 'post' ] ) ) {
        return;
      }

      $extended_term_id = get_post_meta( $_GET[ 'post' ], 'extended_term_id', true );
      $extended_term_taxonomy = get_post_meta( $_GET[ 'post' ], 'extended_term_taxonomy', true );

      if ( $extended_term_id && $extended_term_taxonomy ) {
        die( wp_redirect( get_edit_term_link( $extended_term_id, $extended_term_taxonomy ) ) );
      }

    }

    /**
     * Pre-header loader for Term Editor, when Extended Taxonomies are enabled.
     *
     * @since 0.5.0
     */
    static function term_editor_loader() {
      global $taxnow, $wpdb;

      $tax = get_taxonomy( $taxnow );
      $tag_ID = (int) $_REQUEST[ 'tag_ID' ];

      if ( $_GET[ 'action' ] == 'edit' && is_numeric( $tag_ID ) ) {
        Flawless::term_updated( $tag_ID );
      }

      if ( $_POST[ 'action' ] == 'editedtag' && $_POST[ 'extended_post_id' ] ) {

        check_admin_referer( 'update-tag_' . $tag_ID );

        if ( !current_user_can( $tax->cap->edit_terms ) ) {
          wp_die( __( 'Cheatin&#8217; uh?' ) );
        }

        $post_id = $_POST[ 'extended_post_id' ];

        if ( current_user_can( 'edit_post', $post_id ) ) {
          foreach ( (array) $_POST[ 'post_data' ] as $meta_key => $meta_value ) {
            $wpdb->update( $wpdb->posts, array( $meta_key => $meta_value ), array( 'ID' => $post_id ) );
          }
        }

        foreach ( (array) $_POST[ 'post_meta' ] as $meta_key => $meta_value ) {
          if ( !empty( $meta_value ) ) {
            update_term_meta( $tag_ID, $meta_key, $meta_value );
          } else {
            delete_term_meta( $tag_ID, $meta_key );
          }

        }

      }

    }

    /**
     * Triggered on term update and creation when Extended Taxonomies are supported.
     *
     * Hooked into: wp_insert_post, edit_term, created_term, deleted_term.
     *
     * @since 0.5.0
     */
    static function term_updated( $term_id, $maybe_post = false, $maybe_taxonomy = false ) {
      global $wpdb;

      //** Determine if this is an post update */
      if ( is_object( $maybe_post ) && is_numeric( $maybe_post->ID ) ) {

        //**  Verify if this is an auto save routine.  */
        if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $maybe_post ) ) {
          return $term_id;
        }

        $post_term_id = get_post_meta( $maybe_post->ID, 'extended_term_id', true );

        if ( !$post_term_id ) {
          return;
        }

        $term_update = wp_update_term( $post_term_id, str_replace( '_tp_', '', $maybe_post->post_type ), array(
          'name' => $maybe_post->post_title,
          'description' => $maybe_post->post_excerpt
        ) );

        remove_filter( 'created_term', array( __CLASS__, 'term_updated' ), 9 );
        remove_filter( 'edit_term', array( __CLASS__, 'term_updated' ), 9 );

        return;

      }

      //** Must be a term creation / update */
      if ( !$maybe_post && $_GET[ 'taxonomy' ] ) {

        $term = $wpdb->get_row( "SELECT name, taxonomy, tt.description, tt.term_id, slug FROM {$wpdb->term_taxonomy} tt LEFT JOIN {$wpdb->terms} t on tt.term_id = t.term_id WHERE tt.term_id = '{$term_id}'" );
        $post = get_post_for_extended_term( $term_id, $_GET[ 'taxonomy' ] );

        //** Prevent the term_updated filter from running again (endless loop */
        remove_filter( 'wp_insert_post', array( __CLASS__, 'term_updated' ), 9 );

        $post_id = wp_insert_post( array(
          'ID' => $post->ID,
          'post_status' => 'publish',
          'post_title' => wp_strip_all_tags( $term->name ),
          'post_type' => '_tp_' . $term->taxonomy,
          'post_excerpt' => $term->description,
          'post_content' => $post->post_content,
          'post_name' => $term->slug
        ), true );

        if ( !is_wp_error( $post_id ) ) {
          update_post_meta( $post_id, 'extended_term_id', $term_id );
          update_post_meta( $post_id, 'extended_term_taxonomy', $term->taxonomy );
        }
      }

    }

    /**
     * Delete Extended Taxonomy post.
     *
     * @since 0.5.0
     */
    static function delete_term( $term_id, $tt_id, $taxonomy ) {
      global $wpdb;

      $post_id = $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE post_type = '{$taxonomy}' AND post_name = '{$term_id}'" );

      if ( $post_id ) {
        wp_delete_post( $post_id, true );
      }

    }

  }

}

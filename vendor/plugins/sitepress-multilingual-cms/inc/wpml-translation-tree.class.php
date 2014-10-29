<?php

class WPML_Translation_Tree {

	public $taxonomy;
	public $root_trid;
	public $tree;

	//todo: implement the root functionality to improve performance in some situations
	public function __construct( $element_type, $root = false ) {
		global $sitepress;
		$this->taxonomy  = $element_type;
		$this->root_trid = $root;
		$this->tree      = false;
		/* If accidentally passed a non-hierarchical taxonomy, we cannot create a tree for it. */
		if ( is_taxonomy_hierarchical( $element_type ) && $sitepress->get_option('sync_taxonomy_parents') ) {
			$this->tree = $this->get_all_elements( $element_type );
		}
	}

	/**
	 * @param $taxonomy string
	 * Gets all the terms in a taxonomy together with their language information and saves that information as a tree into $this->tree.
	 * @return array|bool
	 */
	private function get_all_elements( $taxonomy ) {
		global $wpdb;

		/* Get all the term objects */
		$terms_in_taxonomy = $wpdb->get_results(
			"SELECT icl_translations.element_id AS ttid,
					icl_translations.trid AS trid,
					icl_translations.language_code AS lang,
					wp_tt.parent,
					wp_tt.term_id
			FROM {$wpdb->term_taxonomy} as wp_tt
			JOIN {$wpdb->prefix}icl_translations AS icl_translations
			ON concat('tax_',wp_tt.taxonomy) = icl_translations.element_type AND wp_tt.term_taxonomy_id = icl_translations.element_id
			WHERE wp_tt.taxonomy = '{$taxonomy}'" );

		$trids = array();

		foreach ( $terms_in_taxonomy as $term ) {
			$trids [ $term->trid ] [ $term->lang ] = array(
				'ttid'   => $term->ttid,
				'parent' => $term->parent,
			    'term_id' => $term->term_id
			);
		}

		$trid_tree = $this->parse_tree( $trids, false );

		return $trid_tree;
	}

	/**
	 * @param $trids     array
	 * @param $root_trid bool || object
	 *                   Recursively turns an array of unordered trid objects into a tree.
	 *
	 * @return array|bool
	 */
	private function parse_tree( $trids, $root_trid ) {
		/* Turn them into  an array of trees */
		$return = array();

		foreach ( $trids as $key => $trid ) {
			if ( $this->is_root( $trid, $root_trid ) || $root_trid === false ) {
				unset($trids[$key]);
				$return [ $key ] = array(
					'elements' => $trid,
					'children' => $this->parse_tree( $trids, $trid )
				);
			}
		}

		return empty( $return ) ? false : $return;
	}

	/**
	 * @param $parent
	 * @param $child
	 * Checks if one trid is the root of another. This is the case if at least one parent child relationship between both trids exists.
	 *
	 * @return bool
	 */
	private function is_root( $child, $parent ) {
		foreach ( $child as $c_lang => $child_in_lang ) {
			foreach ( (array) $parent as $p_lang => $parent_in_lang ) {
				if ( $c_lang == $p_lang && $child_in_lang[ 'parent' ] == $parent_in_lang[ 'term_id' ] ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @param $lang string Language in which the taxonomy from which this object was created should be synchronized.
	 *              Synchronizes the parent child hierarchy between translations for the taxonomy this tree was created from.
	 *
	 * @return bool
	 */
	public function sync_tree( $lang ) {

		foreach ( (array) $this->tree as $element ) {
			$this->sync_subtree( $lang, $element );
		}

		return true;
	}

	/**
	 * @param $lang
	 * @param $tree
	 * Helper function for sync_tree.
	 *
	 * @return bool
	 */
	private function sync_subtree( $lang, $tree ) {
		global $wpdb;
		if ( isset( $tree[ 'children' ] ) && $tree[ 'children' ] ) {
			$children = $tree[ 'children' ];
		} else {
			return false;
		}
		if ( ! is_array( $children ) ) {
			return false;
		}

		foreach ( $children as $trid => $element ) {
			if ( isset( $tree[ 'elements' ][ $lang ][ 'ttid' ] ) && isset( $element[ 'elements' ][ $lang ] ) ) {
				$wpdb->update( $wpdb->term_taxonomy, array( 'parent' => $tree[ 'elements' ][ $lang ][ 'term_id' ] ), array( 'term_taxonomy_id' => $element[ 'elements' ][ $lang ][ 'ttid' ] ) );
			}
			/* todo: update tree‚*/
			$this->sync_subtree( $lang, $element );
		}

		return true;
	}

	/**
	 * @param $ttid int Taxonomy Term Id of the term in question
	 * @param $lang string Language of the term. Optional, but using it will improve the performance of this function.
	 *              Fetches the correct parent taxonomy_term_id even when it is not correctly assigned in the term_taxonomy wp core database yet.
	 *
	 * @return bool|int
	 */
	public function get_parent_for_ttid( $ttid, $lang ) {

		if ( ! is_array( $this->tree ) ) {
			return false;
		}

		foreach ( $this->tree as $trid => $element ) {
			$res = $this->get_parent_from_subtree( $ttid, $lang, $element );
			if ( $res ) {
				return $res;
			}
		}

		return false;
	}

	/**
	 * @param $ttid
	 * @param $lang
	 * @param $tree
	 * Helper function for get_parent_for_ttid.
	 *
	 * @return bool
	 */
	private function get_parent_from_subtree( $ttid, $lang, $tree ) {
		if ( isset( $tree[ 'children' ] ) && $tree[ 'children' ] ) {
			$children = $tree[ 'children' ];
		} else {
			return false;
		}
		if ( ! is_array( $children ) ) {
			return false;
		}

		foreach ( $children as $trid => $element ) {
			if ( isset( $tree[ 'elements' ][ $lang ][ 'term_id' ] ) && isset( $element[ 'elements' ][ $lang ] ) && $ttid == $element[ 'elements' ][ $lang ][ 'ttid' ] ) {
				return $tree[ 'elements' ][ $lang ][ 'term_id' ];
			} else {
				$res = $this->get_parent_from_subtree( $ttid, $lang, $element );
				if ( $res ) {
					return $res;
				}
			}
		}

		return false;
	}
}

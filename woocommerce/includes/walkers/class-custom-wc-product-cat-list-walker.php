<?php
/**
 * My_WC_Product_Cat_List_Walker class
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'My_WC_Product_Cat_List_Walker', false ) ) {
	return;
}

class Custom_WC_Product_Cat_List_Walker extends WC_Product_Cat_List_Walker {

	public $tree_type = 'product_cat';
	public $db_fields = array(
		'parent' => 'parent',
		'id'     => 'term_id',
		'slug'   => 'slug',
	);

	public function walk( $elements, $max_depth, ...$args ) {
		$output = '';

		// Invalid parameter or nothing to walk.
		if ( $max_depth < -1 || empty( $elements ) ) {
			return $output;
		}

		$parent_field = $this->db_fields['parent'];

		// Flat display.
		if ( -1 == $max_depth ) {
			$empty_array = array();
			foreach ( $elements as $e ) {
				$this->display_element( $e, $empty_array, 1, 0, $args, $output );
			}
			return $output;
		}

		/*
		 * Need to display in hierarchical order.
		 * Separate elements into two buckets: top level and children elements.
		 * Children_elements is two dimensional array, eg.
		 * Children_elements[10][] contains all sub-elements whose parent is 10.
		 */
		$top_level_elements = array();
		$children_elements  = array();
		foreach ( $elements as $e ) {
			if ( empty( $e->$parent_field ) ) {
				$top_level_elements[] = $e;
			} else {
				$children_elements[ $e->$parent_field ][] = $e;
			}
		}
        
        // if empty cat, make parent category top level - changes by Henrit
        if ( isset($elements[0]) and get_class($elements[0]) == 'WP_Term' and empty( $top_level_elements ) ) {
            $id_field = $this->db_fields['id'];   
            $parents  = array();
            foreach ( $elements as $e ) {
				$parents[] = $e->$parent_field;
			}
            
            $top_level_elements = array();
            $children_elements  = array();
            foreach ( $elements as $e ) {
                if ( in_array( $e->$id_field, $parents ) ) {
                    $top_level_elements[] = $e;
                } else {
                    $children_elements[ $e->$parent_field ][] = $e;
                }
            } 
		}

        
		/*
		 * When none of the elements is top level.
		 * Assume the first one must be root of the sub elements.
		 */
		if ( empty( $top_level_elements ) ) {

			$first = array_slice( $elements, 0, 1 );
			$root  = $first[0];

			$top_level_elements = array();
			$children_elements  = array();
			foreach ( $elements as $e ) {
				if ( $root->$parent_field == $e->$parent_field ) {
					$top_level_elements[] = $e;
				} else {
					$children_elements[ $e->$parent_field ][] = $e;
				}
			}
		}

		foreach ( $top_level_elements as $e ) {
			$this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );
		}

		/*
		 * If we are displaying all levels, and remaining children_elements is not empty,
		 * then we got orphans, which should be displayed regardless.
		 */
		if ( ( 0 == $max_depth ) && count( $children_elements ) > 0 ) {
			$empty_array = array();
			foreach ( $children_elements as $orphans ) {
				foreach ( $orphans as $op ) {
					$this->display_element( $op, $empty_array, 1, 0, $args, $output );
				}
			}
		}

		return $output;
	}
}

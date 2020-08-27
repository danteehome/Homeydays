<?php
add_filter('houzez_radius_filter', 'houzez_radius_filter_callback', 10, 6);
if( !function_exists('houzez_radius_filter_callback') ) {
    function houzez_radius_filter_callback( $query_args, $search_lat, $search_long, $search_radius, $use_radius, $location ) {

        global $wpdb;

        if ( ! ( $use_radius && $search_lat && $search_long && $search_radius ) || ! $location ) {
            return $query_args;
        }

        $radius_unit = houzez_option('radius_unit');
        if( $radius_unit == 'km' ) {
            $earth_radius = 6371;
        } elseif ( $radius_unit == 'mi' ) {
            $earth_radius = 3959;
        } else {
            $earth_radius = 6371;
        }
        //TODO: Modify this part and put singpost API in.
        $sql = $wpdb->prepare( "SELECT $wpdb->posts.ID,
				( %s * acos(
					cos( radians(%s) ) *
					cos( radians( latitude.meta_value ) ) *
					cos( radians( longitude.meta_value ) - radians(%s) ) +
					sin( radians(%s) ) *
					sin( radians( latitude.meta_value ) )
				) )
				AS distance, latitude.meta_value AS latitude, longitude.meta_value AS longitude
				FROM $wpdb->posts
				INNER JOIN $wpdb->postmeta
					AS latitude
					ON $wpdb->posts.ID = latitude.post_id
				INNER JOIN $wpdb->postmeta
					AS longitude
					ON $wpdb->posts.ID = longitude.post_id
				WHERE 1=1
					AND ($wpdb->posts.post_status = 'publish' )
					AND latitude.meta_key='houzez_geolocation_lat'
					AND longitude.meta_key='houzez_geolocation_long'
				HAVING distance < %s
				ORDER BY $wpdb->posts.menu_order ASC, distance ASC",
            $earth_radius,
            $search_lat,
            $search_long,
            $search_lat,
            $search_radius
        );
        $post_ids = $wpdb->get_results( $sql, OBJECT_K );

        if ( empty( $post_ids ) || ! $post_ids ) {
            $post_ids = array(0);
        }

        $query_args[ 'post__in' ] = array_keys( (array) $post_ids );
        return $query_args;
    }
}

add_action( 'wp_ajax_nopriv_houzez_half_map_listings', 'houzez20_half_map_listings' );
add_action( 'wp_ajax_houzez_half_map_listings', 'houzez20_half_map_listings' );
if( !function_exists('houzez20_half_map_listings') ) {
    function houzez20_half_map_listings() {
        global $houzez_local;
        $houzez_local = houzez_get_localization();

    	$tax_query = array();
        $meta_query = array();
        $allowed_html = array();
        $keyword_array = '';
        $keyword_field = houzez_option('keyword_field');
        $search_num_posts = houzez_option('search_num_posts');

        $number_of_prop = $search_num_posts;
		if(!$number_of_prop){
		    $number_of_prop = 9;
		}

        $paged = isset($_GET['paged']) ? ($_GET['paged']) : '';
        $sort_by = isset($_GET['sortby']) ? ($_GET['sortby']) : '';

    	$search_qry = array(
            'post_type' => 'property',
            'posts_per_page' => $number_of_prop,
            'paged' => $paged,
            'post_status' => 'publish'
        );

        $search_location = isset($_GET['search_location']) ? esc_attr($_GET['search_location']) : false;
        $item_layout = isset($_GET['item_layout']) ? esc_attr($_GET['item_layout']) : 'v1';
        $use_radius = isset($_GET['use_radius']) ? esc_attr($_GET['use_radius']) : '';
        $search_lat = isset($_GET['lat']) ? (float)$_GET['lat'] : false;
        $search_long = isset($_GET['lng']) ? (float)$_GET['lng'] : false;
        $search_radius = isset($_GET['search_radius']) ? (int)$_GET['search_radius'] : false;

        $search_qry = apply_filters('houzez_radius_filter', $search_qry, $search_lat, $search_long, $search_radius, $use_radius, $search_location);

        if (isset($_GET['keyword']) && $_GET['keyword'] != '') {
            if ($keyword_field == 'prop_address') {
                
                $keyword_array = houzez_keyword_meta_address();

            } else if ($keyword_field == 'prop_city_state_county') {
            
                $taxlocation[] = sanitize_title(wp_kses($_GET['keyword'], $allowed_html));
		        $_tax_query = Array();
		        $_tax_query['relation'] = 'OR';

		        $_tax_query[] = array(
		            'taxonomy' => 'property_area',
		            'field' => 'slug',
		            'terms' => $taxlocation
		        );

		        $_tax_query[] = array(
		            'taxonomy' => 'property_city',
		            'field' => 'slug',
		            'terms' => $taxlocation
		        );

		        $_tax_query[] = array(
		            'taxonomy' => 'property_state',
		            'field' => 'slug',
		            'terms' => $taxlocation
		        );
		        $tax_query[] = $_tax_query;
                
            } else {
            
                $search_qry = houzez_keyword_search($search_qry);
            }
        }

        $tax_query = apply_filters( 'houzez_taxonomy_search_filter', $tax_query );
		$tax_count = count($tax_query);
        $tax_query['relation'] = 'AND';
        if ($tax_count > 0) {
            $search_qry['tax_query'] = $tax_query;
        }

        $meta_query = apply_filters( 'houzez_meta_search_filter', $meta_query );
        $meta_count = count($meta_query);
        if ($meta_count > 0 || !empty($keyword_array)) {
            $search_qry['meta_query'] = array(
                'relation' => 'AND',
                $keyword_array,
                array(
                    'relation' => 'AND',
                    $meta_query
                ),
            );
        }


        if ( $sort_by == 'a_price' ) {
            $search_qry['orderby'] = 'meta_value_num';
            $search_qry['meta_key'] = 'fave_property_price';
            $search_qry['order'] = 'ASC';
        } else if ( $sort_by == 'd_price' ) {
            $search_qry['orderby'] = 'meta_value_num';
            $search_qry['meta_key'] = 'fave_property_price';
            $search_qry['order'] = 'DESC';
        } else if ( $sort_by == 'featured' ) {
            $search_qry['meta_key'] = 'fave_featured';
            $search_qry['meta_value'] = '1';
            $search_qry['orderby'] = 'meta_value date';
        } else if ( $sort_by == 'a_date' ) {
            $search_qry['orderby'] = 'date';
            $search_qry['order'] = 'ASC';
        } else if ( $sort_by == 'd_date' ) {
            $search_qry['orderby'] = 'date';
            $search_qry['order'] = 'DESC';
        } else if ( $sort_by == 'featured_first' ) {
            $search_qry['orderby'] = 'meta_value date';
            $search_qry['meta_key'] = 'fave_featured';
        } else if ( $sort_by == 'featured_top' ) {
            $search_qry['orderby'] = 'meta_value date';
            $search_qry['meta_key'] = 'fave_featured';
        }

        
        $properties_data = array();
        $query_args = new WP_Query( $search_qry );
        
        ob_start();

        echo '<div class="card-deck">';
        $total_properties = $query_args->found_posts;

        while( $query_args->have_posts() ): $query_args->the_post();

            $property_array_temp = array();

        	$property_array_temp[ 'title' ] = get_the_title();
            $property_array_temp[ 'url' ] = get_permalink();
            $property_array_temp['price'] = houzez_listing_price_v1();
            $property_array_temp['property_id'] = get_the_ID();
            $property_array_temp['pricePin'] = houzez_listing_price_map_pins();

            $address = houzez_get_listing_data('property_map_address');
            if(!empty($address)) {
                $property_array_temp['address'] = $address;
            }

            //Property meta
            $property_array_temp['meta'] = houzez_map_listing_meta();

            $property_location = houzez_get_listing_data('property_location');
            if(!empty($property_location)){
                $lat_lng = explode(',',$property_location);
                $property_array_temp['lat'] = $lat_lng[0];
                $property_array_temp['lng'] = $lat_lng[1];
            }

            //Get marker 
            $property_type = get_the_terms( get_the_ID(), 'property_type' );
            if ( $property_type && ! is_wp_error( $property_type ) ) {
                foreach ( $property_type as $p_type ) {

                    $marker_id = get_term_meta( $p_type->term_id, 'fave_marker_icon', true );
                    $property_array_temp[ 'term_id' ] = $p_type->term_id;

                    if ( ! empty ( $marker_id ) ) {
                        $marker_url = wp_get_attachment_url( $marker_id );

                        if ( $marker_url ) {
                            $property_array_temp[ 'marker' ] = esc_url( $marker_url );

                            $retina_marker_id = get_term_meta( $p_type->term_id, 'fave_marker_retina_icon', true );
                            if ( ! empty ( $retina_marker_id ) ) {
                                $retina_marker_url = wp_get_attachment_url( $retina_marker_id );
                                if ( $retina_marker_url ) {
                                    $property_array_temp[ 'retinaMarker' ] = esc_url( $retina_marker_url );
                                }
                            }
                            break;
                        }
                    }
                }
            }

            //Se default markers if property type has no marker uploaded
            if ( ! isset( $property_array_temp[ 'marker' ] ) ) {
                $property_array_temp[ 'marker' ]       = HOUZEZ_IMAGE . 'map/pin-single-family.png';           
                $property_array_temp[ 'retinaMarker' ] = HOUZEZ_IMAGE . 'map/pin-single-family.png';  
            }

            //Featured image
            if ( has_post_thumbnail() ) {
                $thumbnail_id         = get_post_thumbnail_id();
                $thumbnail_array = wp_get_attachment_image_src( $thumbnail_id, 'houzez-item-image-1' );
                if ( ! empty( $thumbnail_array[ 0 ] ) ) {
                    $property_array_temp[ 'thumbnail' ] = $thumbnail_array[ 0 ];
                }
            }

        	$properties_data[] = $property_array_temp;

            get_template_part('template-parts/listing/item', $item_layout);


        endwhile;

        wp_reset_query();
        echo '</div>';

        echo '<div class="clearfix"></div>';
        houzez_ajax_pagination( $query_args->max_num_pages, $paged, $range = 2 );

        $listings_html = ob_get_contents();
        ob_end_clean();

        $encoded_query = base64_encode( serialize( $query_args->query ) );

        if( count($properties_data) > 0 ) {
            echo json_encode( array( 'getProperties' => true, 'properties' => $properties_data, 'total_results' => $total_properties, 'propHtml' => $listings_html, 'query' => $encoded_query ) );
            exit();
        } else {
            echo json_encode( array( 'getProperties' => false, 'total_results' => $total_properties, 'query' => $encoded_query ) );
            exit();
        }
        die();

	}
}
//End of half map listing

if(!function_exists('houzez20_properties_search')) {
	function houzez20_properties_search($search_qry) {
		$tax_query = array();
        $meta_query = array();
        $allowed_html = array();
        $keyword_array = '';
        $keyword_field = houzez_option('keyword_field');

        $search_location = isset($_GET['search_location']) ? esc_attr($_GET['search_location']) : false;
        $use_radius = 'on';
        $search_lat = isset($_GET['lat']) ? (float)$_GET['lat'] : false;
        $search_long = isset($_GET['lng']) ? (float)$_GET['lng'] : false;
        $search_radius = isset($_GET['radius']) ? (int)$_GET['radius'] : false;

        $search_qry = apply_filters('houzez_radius_filter', $search_qry, $search_lat, $search_long, $search_radius, $use_radius, $search_location);

        if (isset($_GET['keyword']) && $_GET['keyword'] != '') {
            if ($keyword_field == 'prop_address') {
                
                $keyword_array = houzez_keyword_meta_address();

            } else if ($keyword_field == 'prop_city_state_county') {
        
                $taxlocation[] = sanitize_title(wp_kses($_GET['keyword'], $allowed_html));
		        $_tax_query = Array();
		        $_tax_query['relation'] = 'OR';

		        $_tax_query[] = array(
		            'taxonomy' => 'property_area',
		            'field' => 'slug',
		            'terms' => $taxlocation
		        );

		        $_tax_query[] = array(
		            'taxonomy' => 'property_city',
		            'field' => 'slug',
		            'terms' => $taxlocation
		        );

		        $_tax_query[] = array(
		            'taxonomy' => 'property_state',
		            'field' => 'slug',
		            'terms' => $taxlocation
		        );
		        $tax_query[] = $_tax_query;
                
            } else {
            
                $search_qry = houzez_keyword_search($search_qry);
            }
        }

		$tax_query = apply_filters( 'houzez_taxonomy_search_filter', $tax_query );
		$tax_count = count($tax_query);
        $tax_query['relation'] = 'AND';
        if ($tax_count > 0) {
            $search_qry['tax_query'] = $tax_query;
        }

        $meta_query = apply_filters( 'houzez_meta_search_filter', $meta_query );
        $meta_count = count($meta_query);
        if ($meta_count > 0 || !empty($keyword_array)) {
            $search_qry['meta_query'] = array(
                'relation' => 'AND',
                $keyword_array,
                array(
                    'relation' => 'AND',
                    $meta_query
                ),
            );
        }
        /*echo '<pre>';
        print_r($search_qry);*/
        return $search_qry;

	}
	add_filter('houzez20_search_filters', 'houzez20_properties_search');
}
//end of property search

if(!function_exists('houzez_keyword_meta_address')) {
	function houzez_keyword_meta_address() {
		$allowed_html = array();
		$property_id_prefix = houzez_option('property_id_prefix');

		$meta_keywork = wp_kses(stripcslashes($_GET['keyword']), $allowed_html);
        $address_array = array(
            'key' => 'fave_property_map_address',
            'value' => $meta_keywork,
            'type' => 'CHAR',
            'compare' => 'LIKE',
        );

        $street_array = array(
            'key' => 'fave_property_address',
            'value' => $meta_keywork,
            'type' => 'CHAR',
            'compare' => 'LIKE',
        );

        $zip_array = array(
            'key' => 'fave_property_zip',
            'value' => $meta_keywork,
            'type' => 'CHAR',
            'compare' => '=',
        );

        $propid_array = array(
            'key' => 'fave_property_id',
            'value' => str_replace($property_id_prefix, "", $meta_keywork),
            'type' => 'CHAR',
            'compare' => '=',
        );

        $keyword_array = array(
            'relation' => 'OR',
            $address_array,
            $street_array,
            $propid_array,
            $zip_array
        );

        return $keyword_array;
	}
}

if(!function_exists('houzez_search_bedrooms')) {
	function houzez_search_bedrooms($meta_query) {
		$beds_baths_search = houzez_option('beds_baths_search', 'equal');
		$search_criteria = '=';
        if( $beds_baths_search == 'greater') {
            $search_criteria = '>=';
        }

		if (isset($_GET['bedrooms']) && !empty($_GET['bedrooms']) && $_GET['bedrooms'] != 'any') {
            $bedrooms = $_GET['bedrooms'];
            $meta_query[] = array(
                'key' => 'fave_property_bedrooms',
                'value' => $bedrooms,
                'type' => 'CHAR',
                'compare' => $search_criteria,
            );
        }
        return $meta_query;
	}

	add_filter('houzez_meta_search_filter', 'houzez_search_bedrooms');
}

if(!function_exists('houzez_search_bathrooms')) {
	function houzez_search_bathrooms($meta_query) {
		$beds_baths_search = houzez_option('beds_baths_search');
		$search_criteria = '=';
        if( $beds_baths_search == 'greater') {
            $search_criteria = '>=';
        }
        
		if (isset($_GET['bathrooms']) && !empty($_GET['bathrooms']) && $_GET['bathrooms'] != 'any') {
            $bathrooms = $_GET['bathrooms'];
            $meta_query[] = array(
                'key' => 'fave_property_bathrooms',
                'value' => $bathrooms,
                'type' => 'CHAR',
                'compare' => $search_criteria,
            );
        }
        return $meta_query;
	}

	add_filter('houzez_meta_search_filter', 'houzez_search_bathrooms');
}

if(!function_exists('houzez_search_property_id')) {
	function houzez_search_property_id($meta_query) {
		$property_id_prefix = houzez_option('property_id_prefix');

		if (isset($_GET['property_id']) && !empty($_GET['property_id'])) {
            $propid = trim( $_GET['property_id'] );
            $propid = str_replace($property_id_prefix, "", $propid);
            $meta_query[] = array(
                'key' => 'fave_property_id',
                'value' => $propid,
                'type' => 'char',
                'compare' => '=',
            );
        }
        return $meta_query;
	}

	add_filter('houzez_meta_search_filter', 'houzez_search_property_id');
}

if(!function_exists('houzez_search_custom_fields')) {
    function houzez_search_custom_fields($meta_query) {
        if(class_exists('Houzez_Fields_Builder')) {
            $fields_array = Houzez_Fields_Builder::get_form_fields();
            if(!empty($fields_array)):
                foreach ( $fields_array as $value ):
                    $field_title = $value->label;
                    $field_name = $value->field_id;
                    $is_search = $value->is_search;

                    if($is_search == 'yes') {
                        if(isset($_GET[$field_name]) && !empty($_GET[$field_name])) {
                            $meta_query[] = array(
                                'key' => 'fave_'.$field_name,
                                'value' => $_GET[$field_name],
                                'type' => 'CHAR',
                                'compare' => '=',
                            );
                        }
                    }

                endforeach; endif;
        }
        return $meta_query;
    }

    add_filter('houzez_meta_search_filter', 'houzez_search_custom_fields');
}

if(!function_exists('houzez_search_min_max_price')) {
	function houzez_search_min_max_price($meta_query) {
		if (isset($_GET['min-price']) && !empty($_GET['min-price']) && $_GET['min-price'] != 'any' && isset($_GET['max-price']) && !empty($_GET['max-price']) && $_GET['max-price'] != 'any') {
            $min_price = doubleval(houzez_clean($_GET['min-price']));
            $max_price = doubleval(houzez_clean($_GET['max-price']));

            if ($min_price > 0 && $max_price >= $min_price) { 
                $meta_query[] = array(
                    'key' => 'fave_property_price',
                    'value' => array($min_price, $max_price),
                    'type' => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );
            }
        } else if (isset($_GET['min-price']) && !empty($_GET['min-price']) && $_GET['min-price'] != 'any') {
            $min_price = doubleval(houzez_clean($_GET['min-price']));
            if ($min_price > 0) {
                $meta_query[] = array(
                    'key' => 'fave_property_price',
                    'value' => $min_price,
                    'type' => 'NUMERIC',
                    'compare' => '>=',
                );
            }
        } else if (isset($_GET['max-price']) && !empty($_GET['max-price']) && $_GET['max-price'] != 'any') {
            $max_price = doubleval(houzez_clean($_GET['max-price']));
            if ($max_price > 0) {
                $meta_query[] = array(
                    'key' => 'fave_property_price',
                    'value' => $max_price,
                    'type' => 'NUMERIC',
                    'compare' => '<=',
                );
            }
        }
        return $meta_query;
	}

	add_filter('houzez_meta_search_filter', 'houzez_search_min_max_price');
}

if(!function_exists('houzez_search_min_max_area')) {
	function houzez_search_min_max_area($meta_query) {
		if (isset($_GET['min-area']) && !empty($_GET['min-area']) && isset($_GET['max-area']) && !empty($_GET['max-area'])) {
            $min_area = intval($_GET['min-area']);
            $max_area = intval($_GET['max-area']);

            if ($min_area > 0 && $max_area > $min_area) {
                $meta_query[] = array(
                    'key' => 'fave_property_size',
                    'value' => array($min_area, $max_area),
                    'type' => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );
            }

        } else if (isset($_GET['max-area']) && !empty($_GET['max-area'])) {
            $max_area = intval($_GET['max-area']);
            if ($max_area > 0) {
                $meta_query[] = array(
                    'key' => 'fave_property_size',
                    'value' => $max_area,
                    'type' => 'NUMERIC',
                    'compare' => '<=',
                );
            }
        } else if (isset($_GET['min-area']) && !empty($_GET['min-area'])) {
            $min_area = intval($_GET['min-area']);
            if ($min_area > 0) {
                $meta_query[] = array(
                    'key' => 'fave_property_size',
                    'value' => $min_area,
                    'type' => 'NUMERIC',
                    'compare' => '>=',
                );
            }
        }
        return $meta_query;
	}

	add_filter('houzez_meta_search_filter', 'houzez_search_min_max_area');
}

if(!function_exists('houzez_search_land_min_max_area')) {
    function houzez_search_land_min_max_area($meta_query) {
        if (isset($_GET['min-land-area']) && !empty($_GET['min-land-area']) && isset($_GET['max-land-area']) && !empty($_GET['max-land-area'])) {
            $min_area = intval($_GET['min-land-area']);
            $max_area = intval($_GET['max-land-area']);

            if ($min_area > 0 && $max_area > $min_area) {
                $meta_query[] = array(
                    'key' => 'fave_property_land',
                    'value' => array($min_area, $max_area),
                    'type' => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );
            }

        } else if (isset($_GET['max-land-area']) && !empty($_GET['max-land-area'])) {
            $max_area = intval($_GET['max-land-area']);
            if ($max_area > 0) {
                $meta_query[] = array(
                    'key' => 'fave_property_land',
                    'value' => $max_area,
                    'type' => 'NUMERIC',
                    'compare' => '<=',
                );
            }
        } else if (isset($_GET['min-land-area']) && !empty($_GET['min-land-area'])) {
            $min_area = intval($_GET['min-land-area']);
            if ($min_area > 0) {
                $meta_query[] = array(
                    'key' => 'fave_property_land',
                    'value' => $min_area,
                    'type' => 'NUMERIC',
                    'compare' => '>=',
                );
            }
        }
        return $meta_query;
    }

    add_filter('houzez_meta_search_filter', 'houzez_search_land_min_max_area');
}

if(!function_exists('houzez_search_currency')) {
	function houzez_search_currency($meta_query) {
		$multi_currency = houzez_option('multi_currency');
        if($multi_currency == 1 ) {
            if(!empty($_GET['currency'])) {
                $meta_query[] = array(
                    'key' => 'fave_currency',
                    'value' => $_GET['currency'],
                    'type' => 'CHAR',
                    'compare' => '=',
                );
            }
        }
        return $meta_query;
	}

	add_filter('houzez_meta_search_filter', 'houzez_search_currency');
}

if(!function_exists('houzez_search_custom_fields')) {
	function houzez_search_custom_fields($meta_query) {
		if(class_exists('Houzez_Fields_Builder')) {
            $fields_array = Houzez_Fields_Builder::get_form_fields();
            if(!empty($fields_array)):
                foreach ( $fields_array as $value ):
                    $field_title = $value->label;
                    $field_name = $value->field_id;
                    $is_search = $value->is_search;

                    if($is_search == 'yes') {
                        if(isset($_GET[$field_name]) && !empty($_GET[$field_name])) {
                            $meta_query[] = array(
                                'key' => 'fave_'.$field_name,
                                'value' => $_GET[$field_name],
                                'type' => 'CHAR',
                                'compare' => '=',
                            );
                        }
                    }

                endforeach; endif;
        }
        return $meta_query;
	}

	add_filter('houzez_meta_search_filter', 'houzez_search_custom_fields');
}

if(!function_exists('houzez_search_status')) {
	function houzez_search_status($query_arg) {

		if (isset($_GET['status']) && !empty($_GET['status']) && !empty($_GET['status'][0]) && $_GET['status'] != 'all') {
            $query_arg[] = array(
                'taxonomy' => 'property_status',
                'field' => 'slug',
                'terms' => $_GET['status']
            );
        }

        return $query_arg;
	}

	add_filter('houzez_taxonomy_search_filter', 'houzez_search_status');
}

if(!function_exists('houzez_search_type')) {
	function houzez_search_type($query_arg) {

		if (isset($_GET['type']) && !empty($_GET['type']) && !empty($_GET['type'][0]) && $_GET['type'] != 'all') {
            $query_arg[] = array(
                'taxonomy' => 'property_type',
                'field' => 'slug',
                'terms' => $_GET['type']
            );
        }

        return $query_arg;
	}

	add_filter('houzez_taxonomy_search_filter', 'houzez_search_type');
}

if(!function_exists('houzez_search_country')) {
	function houzez_search_country($query_arg) {

		if (isset($_GET['country']) && !empty($_GET['country']) && !empty($_GET['country'][0]) && $_GET['country'] != 'all') {
            $query_arg[] = array(
                'taxonomy' => 'property_country',
                'field' => 'slug',
                'terms' => $_GET['country']
            );
        }

        return $query_arg;
	}

	add_filter('houzez_taxonomy_search_filter', 'houzez_search_country');
}

if(!function_exists('houzez_search_state')) {
	function houzez_search_state($query_arg) {

		if (isset($_GET['states']) && !empty($_GET['states']) && !empty($_GET['states'][0]) && $_GET['states'] != 'all') {
            $query_arg[] = array(
                'taxonomy' => 'property_state',
                'field' => 'slug',
                'terms' => $_GET['states']
            );
        }

        return $query_arg;
	}

	add_filter('houzez_taxonomy_search_filter', 'houzez_search_state');
}

if(!function_exists('houzez_search_city')) {
	function houzez_search_city($query_arg) {

		if (isset($_GET['location']) && !empty($_GET['location']) && !empty($_GET['location'][0]) && $_GET['location'] != 'all') {
            $query_arg[] = array(
                'taxonomy' => 'property_city',
                'field' => 'slug',
                'terms' => $_GET['location']
            );
        }

        return $query_arg;
	}

	add_filter('houzez_taxonomy_search_filter', 'houzez_search_city');
}

if(!function_exists('houzez_search_area')) {
	function houzez_search_area($query_arg) {

		if (isset($_GET['areas']) && !empty($_GET['areas']) && !empty($_GET['areas'][0]) && $_GET['areas'] != 'all') {
            $query_arg[] = array(
                'taxonomy' => 'property_area',
                'field' => 'slug',
                'terms' => $_GET['areas']
            );
        }

        return $query_arg;
	}

	add_filter('houzez_taxonomy_search_filter', 'houzez_search_area');
}

if(!function_exists('houzez_search_features')) {
	function houzez_search_features($query_arg) {

		if (isset($_GET['feature']) && !empty($_GET['feature'])) {
            if (is_array($_GET['feature'])) {
                $features = $_GET['feature'];

                foreach ($features as $feature):
                    $query_arg[] = array(
                        'taxonomy' => 'property_feature',
                        'field' => 'slug',
                        'terms' => $feature
                    );
                endforeach;
            }
        }

        return $query_arg;
	}

	add_filter('houzez_taxonomy_search_filter', 'houzez_search_features');
}

if(!function_exists('houzez_search_label')) {
	function houzez_search_label($query_arg) {

		if (isset($_GET['label']) && !empty($_GET['label']) && !empty($_GET['label'][0]) && $_GET['label'] != 'all') {
            $query_arg[] = array(
                'taxonomy' => 'property_label',
                'field' => 'slug',
                'terms' => $_GET['label']
            );
        }

        return $query_arg;
	}

	add_filter('houzez_taxonomy_search_filter', 'houzez_search_label');
}

if(!function_exists('houzez_search_keyword_taxonomy')) {
	function houzez_search_keyword_taxonomy($tax_query) {
		$allowed_html = array();

		$taxlocation[] = sanitize_title(wp_kses($_GET['keyword'], $allowed_html));

        $_tax_query = Array();
        $_tax_query['relation'] = 'OR';

        $_tax_query[] = array(
            'taxonomy' => 'property_area',
            'field' => 'slug',
            'terms' => $taxlocation
        );

        $_tax_query[] = array(
            'taxonomy' => 'property_city',
            'field' => 'slug',
            'terms' => $taxlocation
        );

        $_tax_query[] = array(
            'taxonomy' => 'property_state',
            'field' => 'slug',
            'terms' => $taxlocation
        );
        $tax_query[] = $_tax_query;

        return $tax_query;
	}

	//add_filter('houzez_taxonomy_search_filter', 'houzez_search_keyword_taxonomy');
}

if (!function_exists( 'houzez_keyword_search')) {
	function houzez_keyword_search($search_qry) {

		if (isset($_GET['keyword'])) {
			$keyword = trim($_GET['keyword']);

			if (!empty($keyword)) {
				$search_qry['s'] = $keyword;
				return $search_qry;
			}
		}
		return $search_qry;
	}
}

if(!function_exists('houzez_get_custom_search_field')) {
    function houzez_get_custom_search_field($key) {

        if(class_exists('Houzez_Fields_Builder')) {
            $field_array = Houzez_Fields_Builder::get_field_by_slug($key);
            $field_title = houzez_wpml_translate_single_string($field_array['label']);

            $field_name = $field_array['field_id'];
            $field_type = $field_array['type'];

            if(isset($_GET[$field_name])) {
                $get_field_name = $_GET[$field_name];
            } else {
                $get_field_name = '';
            }

            if($field_type == 'select') { ?>

                <div class="form-group">
                    <select name="<?php echo esc_attr($field_name);?>" class="selectpicker <?php houzez_ajax_search(); ?> form-control bs-select-hidden" title="<?php echo esc_attr($field_title); ?>" data-live-search="false">
                        
                        <option value=""><?php echo esc_attr($field_title); ?></option>
                        <?php
                        $options = unserialize($field_array['fvalues']);
                        
                        foreach ($options as $key => $val) {

                            if(!empty($key)) {
                                $val = houzez_wpml_translate_single_string($val);
                                echo '<option '.selected( $key, $get_field_name, false).' value="'.$key.'">'.$val.'</option>';
                            }
                        }
                        ?>

                    </select><!-- selectpicker -->
                </div>

            <?php
            } else { ?>

                <div class="form-group">
                    <input name="<?php echo esc_attr($field_name);?>" type="text" class="<?php houzez_ajax_search(); ?> form-control" value="<?php echo esc_attr($get_field_name); ?>" placeholder="<?php echo esc_attr($field_title);?>">
                </div>

            <?php
            }

        }
    }
}
<?php
/**
 * Created by Justin
 * User: Talentspirit
 * Date: 10/9/2020
 * Time: 4:49 PM
 * Since v1.4.0
 */
global $post, $top_area, $map_street_view, $show_mortgage_cal;


get_template_part('property-details/luxury-homes/description-detail'); 
get_template_part('property-details/luxury-homes/features');
get_template_part('property-details/luxury-homes/address');
get_template_part('property-details/luxury-homes/gallery');
get_template_part('property-details/luxury-homes/floor-plans');
// get_template_part('property-details/luxury-homes/sub-listing-main');
// get_template_part('property-details/luxury-homes/video');
// get_template_part('property-details/luxury-homes/agent');
// if( $show_mortgage_cal == 1 || $show_mortgage_cal == '' ) {
//     get_template_part('property-details/luxury-homes/mortgage-calculator');
// }
// get_template_part('property-details/reviews');
// get_template_part('property-details/similar-properties');
// get_template_part('property-details/luxury-homes/energy-class');
// get_template_part('property-details/luxury-homes/virtual-tour');
// get_template_part('property-details/luxury-homes/walkscore');
// get_template_part('property-details/luxury-homes/yelp-nearby');
// get_template_part('property-details/luxury-homes/schedule-a-tour');
// get_template_part('property-details/luxury-homes/availability-calendar');
// get_template_part( 'property-details/adsense-space-1' );
// get_template_part( 'property-details/adsense-space-2' );
// get_template_part( 'property-details/adsense-space-3' );


$layout = houzez_option('property_blocks_luxuryhomes');
$layout = $layout['enabled'];

?>

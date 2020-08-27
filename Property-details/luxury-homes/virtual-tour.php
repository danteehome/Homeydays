<?php
/**
 * User: waqasriaz
 * Date: 5 Sep 2019
 */
$virtual_tour = houzez_get_listing_data('virtual_tour');
//TODO: add a function that slice the iframe. 
//Input:"<iframe src="https://beyond.3dnest.cn/classic/?m=0756986e_2bVh_b6f9" frameborder="0" allowfullscreen="" scrolling="no" scroll= "no" style="overflow:hidden;height:100%;width:100%,position: absolute" height="100%" width="100%" ></iframe>"
//Output: "https://beyond.3dnest.cn/classic/?m=0756986e_2bVh_b6f9"
// $test= 'https://beyond.3dnest.cn/classic/?m=0756986e_2bVh_b6f9';
preg_match('/src="([^"]+)"/', $virtual_tour, $result);
$test = $result[1];

if( !empty( $virtual_tour ) ) { ?>
<div class="fw-property-virtual-tour-wrap fw-property-section-wrap" id="property-virtual-tour-wrap">
	<div class="block-wrap">
		<div class="block-title-wrap">
			<h2><?php echo houzez_option('sps_virtual_tour', '360° Virtual Tour'); ?></h2>
			<a class= "btn btn-primary btn-slim" style="margin-top: 20px;" target="_blank" href=<?php echo $test?>><?php echo houzez_option('sps_virtual_tour', '360° Virtual Tour'); ?></a>	
		</div><!-- block-title-wrap -->
		<div class="block-content-wrap">
			<div class="block-virtual-video-wrap">
				<!-- Copy & Pasted from YouTube -->
				<?php echo $virtual_tour; ?>
			</div>
		</div><!-- block-content-wrap -->
	</div><!-- block-wrap -->
</div><!-- fw-property-virtual-tour-wrap -->
<?php } ?>
<?php
global $post;
$size = 'houzez-item-image-4';
$listing_images = rwmb_meta( 'fave_property_images', 'type=plupload_image&size='.$size, $post->ID );
$i = 0;
$total_images = count($listing_images);
?>
<div class="property-top-wrap">
    <div class="property-banner">
		<div class="visible-on-mobile">
			<div class="tab-content" id="pills-tabContent">
				<?php get_template_part('property-details/partials/media-tabs'); ?>
			</div><!-- tab-content -->
		</div><!-- visible-on-mobile -->

		<div class="container hidden-on-mobile">
			<div class="row">
				
				<?php
				if(!empty($listing_images)) {
					foreach( $listing_images as $image ) { $i++; 
					
						if($i == 1) {
						?>
						<div class="col-md-8">
							<a href="<?php echo esc_url( $image['url'] ); ?>" class="img-wrap-1" data-toggle="modal" data-target="#property-lightbox">
								<img class="img-fluid" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
								<!-- the first image container -->
							</a>
						</div><!-- col-md-8 -->
						<?php } elseif($i == 2 || $i == 3) { ?>

						<?php if($i == 2) { ?>
						<div class="col-md-4">
						<?php } ?>
							<a href="<?php echo esc_url( $image['url'] ); ?>" data-toggle="modal" data-target="#property-lightbox" class="swipebox img-wrap-<?php echo esc_attr($i); ?>">
								<?php if($total_images > 3 && $i == 3) { ?>
								<div class="img-wrap-3-text"><?php echo $total_images-3; ?> <?php echo esc_html__('More', 'houzez'); ?></div>
								<!-- display "$total_images-3 More" -->
								<?php } ?>

								<img class="img-fluid" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
							</a>
						<?php if( ($i == 3 && $total_images == 3) || ( $i == 2 && $total_images == 2 ) || ( $i == 1 && $total_images == 1 ) || $i == 3 ) { ?>
						<!-- If total image<3 close div. If counter = total image <3, end if. -->
						</div><!-- col-md-4 -->
						<?php } ?>
						<?php } else { ?>
						<!-- else if total image > 3, display the modal -->
							<a href="<?php echo esc_url( $image['url'] ); ?>" class="img-wrap-1 gallery-hidden">
								<img class="img-fluid" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
							</a>
						<?php
						}
					}
				}?>
			
				<div class="col-md-12">
					<div class="block-wrap">
						<div class="d-flex property-overview-data">
							<?php get_template_part('property-details/partials/overview-data'); ?>
						</div><!-- d-flex -->
					</div><!-- block-wrap -->
				</div><!-- col-md-12 -->
			</div><!-- row -->
		</div><!-- hidden-on-mobile -->
	</div><!-- property-banner -->
</div><!-- property-top-wrap -->
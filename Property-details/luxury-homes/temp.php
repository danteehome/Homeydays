<div class="floor-plan-right-wrap">
								<h3><?php echo esc_attr( $plan['fave_plan_title'] ); ?></h3>
								
								<?php if( !empty( $plan['fave_plan_price'] ) ) { ?>
								<div>
									<?php esc_html_e( 'Price', 'houzez' ); ?>: 
			                        <strong><?php echo houzez_get_property_price( $plan['fave_plan_price'] ).$price_postfix; ?></strong>
			                     </div>
			                 	<?php } ?>

								<div class="floor-plan-description">
									<p><strong><?php echo esc_html__('Description', 'houzez'); ?>:</strong><br>
										<?php 
										if( !empty( $plan['fave_plan_description'] ) ) { 
											echo wp_kses_post( $plan['fave_plan_description'] ); 
										} 
										?>
									</p>
								</div><!-- floor-plan-description -->

								<!-- three icons and info. -->
								<div class="d-flex">
									<?php if( !empty( $plan['fave_plan_rooms'] ) ) { ?>
									<div class="d-flex fw-property-floor-data-wrap align-items-center">
										<img class="img-fluid" src="<?php echo HOUZEZ_IMAGE; ?>streamline-icon-hotel-double-bed-1@40x40.png" alt="">
										<div class="fw-property-floor-data">
											<?php esc_html_e( 'Rooms', 'houzez' ); ?>:<br>
											<?php echo esc_attr( $plan['fave_plan_rooms'] ); ?>
										</div><!-- fw-property-floor-data -->
									</div><!-- "d-flex -->
									<?php } ?>

									<?php if( !empty( $plan['fave_plan_bathrooms'] ) ) { ?>
									<div class="d-flex fw-property-floor-data-wrap align-items-center">
										<img class="img-fluid" src="<?php echo HOUZEZ_IMAGE; ?>streamline-icon-bathroom-shower-1@40x40.png" alt="">
										<div class="fw-property-floor-data">
											<?php esc_html_e( 'Baths', 'houzez' ); ?>:<br>
											<?php echo esc_attr( $plan['fave_plan_bathrooms'] ); ?>
										</div><!-- fw-property-floor-data -->
									</div><!-- "d-flex -->
									<?php } ?>

									<?php if( !empty( $plan['fave_plan_size'] ) ) { ?>
									<div class="d-flex fw-property-floor-data-wrap align-items-center">
										<img class="img-fluid" src="<?php echo HOUZEZ_IMAGE; ?>streamline-icon-real-estate-dimensions-plan-1@40x40.png" alt="">
										<div class="fw-property-floor-data">
											<?php esc_html_e( 'Size', 'houzez' ); ?>:<br>
											<?php echo esc_attr( $plan['fave_plan_size'] ); ?>
										</div><!-- fw-property-floor-data -->
									</div><!-- "d-flex -->
									<?php } ?>

								</div><!-- d-flex -->
							</div>   <!-- floor-plan-right-wrap --> 
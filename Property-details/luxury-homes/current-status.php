<?php // this is the modified page based on floor-plans to create an occupy status block.
$floor_plans          = get_post_meta( get_the_ID(), 'floor_plans', true );

if( !empty( $floor_plans ) ) {
?>
<div class="fw-property-floor-plans-wrap fw-property-section-wrap" id="property-floor-plans-wrap">
	<div class="block-wrap">
		<div class="block-title-wrap">
			<h2><?php echo "RoyalGreen Elevation Chart" ?></h2>
		</div><!-- block-title-wrap -->
		<div class="block-content-wrap">
			<?php // This is the container for the image and content. ?>
			<div class="tab-content horizontal-tab-content" id="property-tab-content">
				<?php // this function check $floor_plans image
                $j = 0;
                foreach( $floor_plans as $plan ):
                    $j++;
                    if( $j == 1 ) {
                        $active_tab = 'active show';
                    } else {
                        $active_tab = '';
                    }
                    $price_postfix = '';
                    if( !empty( $plan['fave_plan_price_postfix'] ) ) {
                        $price_postfix = ' / '.$plan['fave_plan_price_postfix'];
                    }
                    $filetype = wp_check_filetype($plan['fave_plan_image']);
                    ?>

				<?php // This is the container for the content title. ?>
				<div class="tab-pane fade <?php echo esc_attr($active_tab); ?>" id="floor-<?php echo esc_attr($j); ?>" role="tabpanel">
					<div class="floor-plan-wrap">
						<div class="d-flex align-items-center">
						<?php // This is the container for the image ?>
							<div class="floor-plan-left-wrap">
								<?php if( !empty( $plan['fave_plan_image'] ) ) { ?>
                    
			                        <?php if($filetype['ext'] != 'pdf' ) {?>
			                        <a href="<?php echo esc_url( $plan['fave_plan_image'] ); ?>" target="_blank">
			                            <img class="img-fluid" src="<?php echo esc_url( $plan['fave_plan_image'] ); ?>" alt="image">
			                        </a>
			                        <?php } else { 
			                            
			                            $path = $plan['fave_plan_image'];
			                            $file = basename($path); 
			                            $file = basename($path, ".pdf");
			                            echo '<a href="'.esc_url( $plan['fave_plan_image'] ).'" download>';
			                            echo $file;
			                            echo '</a>';
			                        } ?>
			                    
			                <?php } ?>
							</div><!-- floor-plan-left-wrap -->
							
							<?php // This is the container for description ?>
							<div class="floor-plan-right-wrap" style="background-color:rgba(255,0,255,0.3);">
								<p>hello</p>


							</div>   <!-- floor-plan-right-wrap --> 
						</div><!-- d-flex -->
					</div><!--floor-plan-wrap -->
				</div>

				<?php endforeach; ?>
			</div>
		</div><!-- block-content-wrap -->
	</div><!-- block-wrap -->
</div><!-- fw-property-floor-plans-wrap -->
<?php } ?>
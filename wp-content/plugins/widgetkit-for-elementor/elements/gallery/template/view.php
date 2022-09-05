<?php
// Silence is golden.

    $galleries = $this->get_settings();
    $id = $this->get_id();
    $light_case_animation = $galleries['lightcase_animation'];
    $gallery_tag_arr = [];
?>


	<div class="wk-gallery" <?php echo ($galleries['filter_enable'] == 'yes')? 'wk-filter="target: .js-filter"' : ''; ?> >
		<?php if ($galleries['filter_enable'] == 'yes'): ?>
		<div class="wk-container wk-margin-auto">
			<?php 
				foreach ( $galleries['gallery_content'] as $key => $value) {
					$split_data_arr = explode(',', $value['filter_tag']);
					foreach($split_data_arr as $value){
						$trimmed_value = trim($value);
						if(!in_array($trimmed_value, $gallery_tag_arr)){
							array_push($gallery_tag_arr, $trimmed_value);
						}
					}
				}
				
			?>
			<ul class="wk-flex-<?php echo $galleries['filter_layout_align'];?>" wk-tab>
				<?php if ($galleries['filter_show_title']): ?>
					<li class="wk-active" wk-filter-control><a href="#"><?php echo $galleries['filter_show_title'];?></a></li>
				<?php endif; ?>
				
				<?php foreach($gallery_tag_arr as $tag_name): 
					$tags_sorting = str_replace([" ", "&"], ["_", ""], $tag_name);?>
					<li wk-filter-control=".<?php echo strtolower($tags_sorting.'_'.$id);?>">
				        <a href="#"><?php echo $tag_name; ?></a>
				    </li>
				<?php endforeach; ?>
			</ul> <!-- filter tags -->
			
		</div>			

		<?php endif; ?>


			<?php if ($galleries['colmun_width'] == 'auto'):?>
				<div class="<?php echo ($galleries['filter_enable'] == 'yes')? 'js-filter' : ''; ?> wk-grid-<?php echo $galleries['column_gap'];?> wk-child-width-<?php echo $galleries['colmun_width'];?> wk-child-width-1-2@s" wk-grid="masonry:<?php echo $galleries['masonary_enable'] == 'yes'? 'true' : 'false';?>">

			<?php else: ?>
				<div class="<?php echo ($galleries['filter_enable'] == 'yes')? 'js-filter' : ''; ?> wk-grid-<?php echo $galleries['column_gap'];?>  wk-child-width-<?php echo $galleries['colmun_layout'];?>@m wk-child-width-1-2@s" wk-grid="masonry:<?php echo $galleries['masonary_enable'] == 'yes'? 'true' : 'false';?>">
			<?php endif; ?>
	  		<?php foreach ($galleries['gallery_content'] as $gallery) :?>
	  			<?php $tags = explode(',', $gallery['filter_tag']);?>
		        <div class="<?php foreach($tags as $tag ){
		        	$tags_replace = str_replace([" ", "&"], ["_", ""], $tag);
		        	echo strtolower($tags_replace.'_'.$id);}?>">
		        <!-- 	<div class=""> -->
			            <div class="wk-gallery-card <?php echo $galleries['content_position'] == 'overlay' ? 'wk-position-relative' : '';?>  wk-overflow-hidden <?php echo $galleries['hover_effect'];?> content-<?php echo $galleries['content_position'];?>">

							<?php if ($galleries['content_position'] == 'overlay'): ?>

					            <?php if ($gallery['gallery_thumb_image']['url']): ?>
					            	<img src="<?php echo $gallery['gallery_thumb_image']['url']?>" alt="<?php echo $gallery['gallery_title'];?>">
					            <?php endif ;?>

					            <div class="wk-padding-small wk-position-absolute wk-position-center wk-text-center wk-gallery-body">
						               <?php if ($gallery['demo_link']['url']): ?>
						           		<a href="<?php echo $gallery['demo_link']['url']; ?>" <?php echo $gallery['demo_link']['is_external'] ? 'target="_blank"' : 'nofollow="nofollow"'; ?>>
						            		<h5 class="wk-text-medium wk-margin-small wk-card-title"> <?php echo $gallery['gallery_title'];?>
						            		</h5>
										</a>
										<?php else: ?>
											<h5 class="wk-text-medium wk-margin-small  wk-card-title"> 
												<?php echo $gallery['gallery_title'];?>
											</h5>
						            	<?php endif ;?>
						            	<?php if ($gallery['gallery_desc']): ?>
						            		<p class="wk-text-desc wk-margin-remove wk-padding-small wk-padding-remove-top">
						            			<?php echo $gallery['gallery_desc'];?></p>	
						            	<?php endif; ?>

						            	<?php if ($galleries['lightcase_enable'] == 'yes' || $galleries['link_enable'] == 'yes'):?>
							            	<div class="gallery-lightbox wk-text-center">
							            		<?php if ($galleries['link_enable'] == 'yes'):?>
									            	<a class="icon" href="<?php echo $gallery['demo_link']['url']; ?>" <?php echo $gallery['demo_link']['is_external'] ? 'target="_blank"' : 'nofollow="nofollow"'; ?>>
									            		<span class="fa fa-link"></span>
									            	</a>
								            	<?php endif; ?>
								            	<?php if ($galleries['lightcase_enable'] == 'yes'):?>
													<a class="icon" href="<?php echo $gallery['gallery_thumb_image']['url']?>" <?php echo $galleries['lightcase_enable'] == 'yes'? ' wk-lightbox="animation:' . $light_case_animation . '"' : '';?>><span class="fa fa-search"></span></a>
												<?php endif; ?>
							            	</div>
						            	<?php endif; ?>
									</div> <!-- wk-body -->


								<?php else: ?>
									<div class="caption-button wk-position-relative wk-overflow-hidden">

									    <?php if ($gallery['gallery_thumb_image']['url']): ?>
							            	<?php if ($gallery['demo_link']['url']): ?>
								            	<a class="img-link" href="<?php echo $gallery['demo_link']['url']; ?>" <?php echo $gallery['demo_link']['is_external'] ? 'target="_blank"' : 'nofollow="nofollow"'; ?>>
								            		<img src="<?php echo $gallery['gallery_thumb_image']['url']?>" alt="<?php echo $gallery['gallery_title'];?>">
								            	</a>
							            	<?php else: ?>
							            		<img class="img-link" src="<?php echo $gallery['gallery_thumb_image']['url']?>" alt="<?php echo $gallery['gallery_title'];?>">
							            	<?php endif;?>
							            <?php endif ;?>

						            	<?php if ($galleries['lightcase_enable'] == 'yes' || $galleries['link_enable'] == 'yes'):?>
							            	<div class="gallery-lightbox wk-text-center wk-position-absolute wk-position-center">
							            		<?php if ($galleries['link_enable'] == 'yes'): ?>
							            			 <?php if ($galleries['button_text']): ?>
										            	<a class="button-text" href="<?php echo $gallery['demo_link']['url']; ?>" <?php echo $gallery['demo_link']['is_external'] ? 'target="_blank"' : 'nofollow="nofollow"'; ?>>
										            	 	<?php echo $galleries['button_text']; ?>
										            	</a>
										            <?php else: ?>
										            	<a class="top-icon" href="<?php echo $gallery['demo_link']['url']; ?>" <?php echo $gallery['demo_link']['is_external'] ? 'target="_blank"' : 'nofollow="nofollow"'; ?>>
										            	 	<span class="fa fa-link"></span>
										            	</a>		
										            <?php endif; ?>
							            		<?php endif; ?>

								            	<?php if ($galleries['lightcase_enable'] == 'yes'):?>
													<a class="top-icon" href="<?php echo $gallery['gallery_thumb_image']['url']?>" <?php echo $galleries['lightcase_enable'] == 'yes'? ' wk-lightbox="animation:' . $light_case_animation . '"' : '';?>><span class="fa fa-search"></span></a>
												<?php endif; ?>
							            	</div>
						            	<?php endif; ?>
									</div>

						            <div class="wk-padding-small wk-text-<?php echo $galleries['caption_align'];?> wk-gallery-body">
						               <?php if ($gallery['demo_link']['url']): ?>
						           		<a href="<?php echo $gallery['demo_link']['url']; ?>" <?php echo $gallery['demo_link']['is_external'] ? 'target="_blank"' : 'nofollow="nofollow"'; ?>>
						            		<h5 class="wk-text-medium wk-margin-small wk-card-title wk-margin-remove-top"> <?php echo $gallery['gallery_title'];?>
						            		</h5>
										</a>
										<?php else: ?>
											<h5 class="wk-text-medium wk-margin-small wk-card-title wk-margin-remove-top"> 
												<?php echo $gallery['gallery_title'];?>
											</h5>
						            	<?php endif ;?>
						            	<?php if ($gallery['gallery_desc']): ?>
						            		<p class="wk-text-desc wk-margin-remove wk-padding-remove-top">
						            			<?php echo $gallery['gallery_desc'];?></p>	
						            	<?php endif; ?>
									</div> <!-- wk-body -->

					            <?php endif; ?>

			            </div> <!-- wk-card -->
		           <!--  </div> -->
		        </div> <!-- tags -->
	        <?php endforeach; ?>
	    </div>  <!-- contents -->
	</div> <!-- target -->

    <script>
        jQuery(function($){
            if(!$('body').hasClass('wk-gallery')){
                $('body').addClass('wk-gallery');
            }
        });
    </script>

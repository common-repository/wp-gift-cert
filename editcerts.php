<?php
		$wpgft_cert_arr = get_option('wpgft_buttons');
	 ?>
		<div class="wrap">
			<h2><?php _e('Create/Edit Certificates', 'wpgft-plugin') ?> </h2>
			<input type="button" class="button-primary" value="<?php echo __('Add Button','wpgft-plugin')?>" onclick="window.location='?page=certificate_management&amp;add=true'" />
			 <table class="widefat page fixed" cellpadding="0">
				<thead>
					<tr>
						<th id="cb" class="manage-column column-cb check-column" style="" scope="col">
							<input type="checkbox"/>
						</th>
						<th class="manage-column"><?php echo __('Amount','wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Company', 'wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Description','wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Button Type', 'wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Short Code', 'wpgft-plugin')?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th id="cb" class="manage-column column-cb check-column" style="" scope="col">
							<input type="checkbox"/>
						</th>
						<th class="manage-column"><?php echo __('Amount','wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Company', 'wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Description','wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Button Type', 'wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Short Code', 'wpgft-plugin')?></th>
					</tr>
				</tfoot>
				<tbody>
				<?php
					if($wpgft_cert_arr) {
						foreach($wpgft_cert_arr as $key => $current_button) {
							$company = "";
						
							if ($current_button['company'] == "") {
								$wpgft_options_arr = get_option("wpgft_options");
							
								$company = $wpgft_options_arr['company'];
							} else {
								$company = $current_button['company'];
							}
						?>
							<tr class="<?php echo (ceil($i/2) == ($i/2)) ? "" : "alternate"; ?>">
								<th class="check-column" scope="row">
									<input type="checkbox" value="<?php echo $key?>" name="wpgft_cert_arr[]" />
								</th>
								<td>                    
									<a href="?page=certificate_management&amp;add=true&amp;id=<?php echo $key?>" class="row-title"><?php echo $current_button['amount']?></a>
									<div class="row-actions">
										<span class="edit"><a href="?page=certificate_management&amp;add=true&amp;id=<?php echo $key?>">Edit</a> | </span>
										<span class="delete"><a href="?page=certificate_management&amp;delete=true&amp;id=<?php echo $key?>" onclick="return confirm('Are you sure you want to delete this button?');">Delete</a></span>
									</div>
								</td>
								<td><?php echo $company;?></td>
								<td><?php echo $current_button['description']?></td>
								<td><?php echo $current_button['button']?></td>
								<td><b><?php echo '[wpgft id=' . $key . ']'?></b></td>
							</tr>
						<?php
						}
					} else {
					?>
						<tr><td colspan="4"><?php echo __('You Have not created any certificates to sell yet, ','wpgft-plugin')?> <a href="?page=certificate_management&amp;add=true"><?php echo __('Create one','wpgft-plugin') ?></a></td></tr>
					<?php
					}
				?>
				</tbody>
			</table>
		</div>
		<?php
?>
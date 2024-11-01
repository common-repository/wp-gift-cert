<?php
		global $wpdb;
		if(!isset($_GET['filter_status']) || $_GET['filter_status'] == "") {
			$wpgft_issued = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."wpgft_data ORDER BY ID");
		} else {
			$wpgft_issued = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpgft_data WHERE status='".$_GET['filter_status']."' ORDER BY ID"));
		}
		//Code to generate CSV for export
		
				$field_count = count((array) $wpgft_issued['0'], COUNT_RECURSIVE);
				$i=-1;
				$nextrow = chr(13).chr(10);
				$wrapper = "\"";
				$delimiter = ",";
				//loop through the selected data and build the column headers
				while (++$i < $field_count) {
					$field_name = $wpdb->get_col_info('name', $i);
					$csvoutput .= "\"$field_name\",";
				}
				$csvoutput .= "\r\n";
				
				//  loop through the selected data and build the csv for each row
				foreach ($wpgft_issued as $row){
					foreach ($row as $item => $line){
						$csvoutput .= "\"" . $line . "\"" . ",";
					}
					$csvoutput .= $nextrow;
				}
			
	/*	if(isset($_GET['export'])) {
			add_action('init', 'exportCertificates');
		} */
	 ?>
		<div class="wrap">
			<h2><?php _e('Manage Issued Certificates', 'wpgft-plugin') ?> </h2>
			<div class="alignleft actions">
				<form action="" method="get">
					<?php echo wpgft_status_dropdown() ?>
				</form>
			</div>
			 <table class="widefat page fixed" cellpadding="0">
				<thead>
					<tr>
						<th id="cb" class="manage-column column-cb check-column" style="" scope="col">
							<input type="checkbox"/>
						</th>
						<th class="manage-column"><?php echo __('Cert Number','wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Amount','wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Company','wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Sold To', 'wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Email', 'wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Phone', 'wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Sale Date', 'wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Status', 'wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Resend', 'wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Delete', 'wpgft-plugin')?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th id="cb" class="manage-column column-cb check-column" style="" scope="col">
							<input type="checkbox"/>
						</th>
						<th class="manage-column"><?php echo __('Cert Number','wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Amount','wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Company','wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Sold To', 'wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Email', 'wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Phone', 'wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Sale Date', 'wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Status', 'wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Resend', 'wpgft-plugin')?></th>
						<th class="manage-column"><?php echo __('Delete', 'wpgft-plugin')?></th>
					</tr>
				</tfoot>
				<tbody>
				<?php
					if($wpgft_issued) {
						foreach($wpgft_issued as $wpgft_cert) {
							$current_button = get_current_button($wpgft_cert->button_id);
						?>
							<tr class="<?php echo (ceil($i/2) == ($i/2)) ? "" : "alternate"; ?>">
								<th class="check-column" scope="row">
									<input type="checkbox" value="<?php echo $wpgft_cert->ID?>" name="wpgft_cert_arr[]" />
								</th>
								<td>
									<?php echo $wpgft_cert->cert_num; ?>
								</td>
								<td><?php echo $wpgft_cert->cert_amount; ?></td>
								<td><?php echo $current_button['company']; ?></td>
								<td><?php echo $wpgft_cert->sold_to; ?></td>
								<td><?php echo $wpgft_cert->sold_to_email; ?></td>
								<td><?php echo $wpgft_cert->sold_to_phone; ?></td>
								<td><?php echo $wpgft_cert->sale_date; ?></td>
								<td><select class='selector' title='<?php echo $wpgft_cert->ID; ?>' name='<?php echo $wpgft_cert->ID; ?>'>
									<option value="Issued" <?php if($wpgft_cert->status == "Issued") echo 'selected="selected"' ?>>Issued</option>
									<option value="Redeemed" <?php if($wpgft_cert->status == "Redeemed") echo 'selected="selected"' ?>>Redeemed</option>
									<option value="Invalid" <?php if($wpgft_cert->status == "Invalid") echo 'selected="selected"' ?>>Invalid</option>
									<option value="Pending" <?php if($wpgft_cert->status == "Pending") echo 'selected="selected"' ?>>Pending</option>
								</select>
								</td>
								<td><a href="<?php echo get_option('siteurl') ?>/wp-admin/admin.php?page=manage_sold&amp;resend=true&amp;cert_id=<?php echo $wpgft_cert->ID ?>">Resend</a></td>
								<td><a href="?page=manage_sold&amp;delete=true&amp;id=<?php echo $wpgft_cert->ID ?>" onclick="return confirm('Are you sure you want to delete this certificate?');">X</a></td>
							</tr>
						<?php
						}
					} else {
					?>
						<tr><td colspan="4"><?php echo __('No Certificates have been sold, ','wpgft-plugin')?> </td></tr>
					<?php
					}
				?>
				</tbody>
			</table>
			<form action="?page=manage_sold" method="post">
			<input type="hidden" name="export" value='true' />
			<input type="hidden" name="data" value="<?php echo htmlspecialchars($csvoutput); ?>" />
			<input class='button-primary' type="submit" id='export' value="Export to Excel" /></a>
			</form>
		</div>
		<?php

		function wpgft_status_dropdown() {
			
			$options = "<option value=''>".__('View All Statuses', 'wpgft')."</option>\r\n";
			$options .= "<option value='Issued'>".__('Issued', 'wpgft')."</option>\r\n";
			$options .= "<option value='Redeemed'>".__('Redeemed', 'wpgft')."</option>\r\n";
			$options .= "<option value='Invalid'>".__('Invalid', 'wpgft')."</option>\r\n";
			$options .= "<option value='Pending'>".__('Pending', 'wpgft')."</option>\r\n";
			$concat = "<input type='hidden' name='page' value='manage_sold' />";
			$concat .= "<select name='filter_status'id='filter_status' value=''>".$options."</select>\r\n";
			$concat .= "<button class='button' id='submit_filter_status'>Filter</button>\r\n";
			return $concat;
		}
			

?>
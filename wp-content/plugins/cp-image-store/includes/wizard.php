<?php
if ( isset( $_POST['cpis_wizard'] ) && wp_verify_nonce( $_POST['cpis_wizard'], plugin_basename( __FILE__ ) ) )
{
	$options['paypal']['paypal_email'] = (!empty($_POST['cpis_paypal_email'])) ? sanitize_email($_POST['cpis_paypal_email']) : '';
	$options['store']['items_page']    = (!empty($_POST['cpis_items_page']) && 0 < ($cpis_items_page = @intval($_POST['cpis_items_page']))) ? $cpis_items_page : 10;

	$options['store']['columns']       = (!empty($_POST['cpis_columns']) && 0 < ($columns = @intval($_POST['cpis_columns']))) ? $columns : 1;


	if(!empty($_POST['cpis_shop_page_title']) && ($cpis_shop_page_title = sanitize_text_field($_POST['cpis_shop_page_title'])))
	{
		$shortcode = '[codepeople-image-store]';
		$page_id = wp_insert_post(
			array(
				'comment_status' => 'closed',
				'post_title' => $cpis_shop_page_title,
				'post_content' => $shortcode,
				'post_status' => 'publish',
				'post_type' => 'page'
			)
		);

		$options['store']['store_url'] = get_permalink($page_id);
	}

	update_option( 'cpis_options', $options );
	print '<div class="updated notice">'.__('Store Wizard Completed', CPIS_TEXT_DOMAIN).'</div>';
	if(isset($_POST['cpis_wizard_goto']) && $_POST['cpis_wizard_goto'] == 'images')
	{
?>
	<script>document.location.href="<?php print esc_js(admin_url('post-new.php?post_type=cpis_image')); ?>";</script>
<?php
	}
}
$cpis_has_been_configured = get_option('cpis_has_been_configured', false);
if($options['paypal']['paypal_email'] == "" && !$cpis_has_been_configured)
{
	?>
	<h1 style="text-align:center;"><?php _e('Images Store Wizard', CPIS_TEXT_DOMAIN); ?></h1>
	<form id="cpis_wizard" method="post" action="<?php echo admin_url('admin.php?page=image-store-menu-settings'); ?>">
		<div>
			<h3 class='hndle' style="padding:5px;"><span><?php _e('Step 1 of 2', CPIS_TEXT_DOMAIN); ?>: <?php _e('Payment Gateway', CPIS_TEXT_DOMAIN); ?></span></h3>
			<hr />
			<table class="form-table">
				<tr valign="top">
					<th scope="row" style="white-space:nowrap;">
						<?php _e('Enter the email address associated to your PayPal account', CPIS_TEXT_DOMAIN); ?>
					</th>
					<td>
						<input type="text" name="cpis_paypal_email" size="40" placeholder="<?php _e('Email address', CPIS_TEXT_DOMAIN); ?>" /><br />
						<i style="font-weight:normal;"><?php _e('Leave in blank if you want distribute your images for free.', CPIS_TEXT_DOMAIN); ?></i>
					</td>
				</tr>
			</table>
			<div style="border:1px dotted #333333; margin-top:10px; margin-bottom:10px; padding: 10px;">Please, remember that the Instant Payment Notification (IPN) must be enabled in your PayPal account, because if the IPN is disabled PayPal does not notify the payments to your website. Please, visit the following link: <a href="https://developer.paypal.com/docs/classic/ipn/integration-guide/IPNSetup/#id089EG030E5Z" target="_blank">How to enable the IPN?</a>. PayPal needs the URL to the IPN Script in your website, however, you simply should enter the URL to the home page.</div>
			<input type="button" class="button" value="<?php esc_attr_e('Next step', CPIS_TEXT_DOMAIN); ?>" onclick="jQuery(this).closest('div').hide().next('div').show();">
		</div>
		<div style="display:none;">
			<h3 class='hndle' style="padding:5px;"><span><?php _e('Step 2 of 2', CPIS_TEXT_DOMAIN); ?>: <?php _e('Store Page', CPIS_TEXT_DOMAIN); ?></span></h3>
			<hr />
			<table class="form-table">
				<tr valign="top">
					<th><?php _e('Enter the shop page\'s title', CPIS_TEXT_DOMAIN); ?></th>
					<td>
						<input type="text" name="cpis_shop_page_title" size="40" /><br />
						<i><?php _e('Leave in blank if you want to configure the shop\'s page after.', CPIS_TEXT_DOMAIN); ?></i>
					</td>
				</tr>
				<tr valign="top">
					<th><?php _e('Products per page', CPIS_TEXT_DOMAIN); ?></th>
					<td><input type="text" name="cpis_items_page" value="<?php echo @intval($options['store'][ 'items_page' ]); ?>" /></td>
				</tr>
				<tr valign="top">
					<th><?php _e('Number of columns', CPIS_TEXT_DOMAIN); ?></th>
					<td><input type="text" name="cpis_columns" value="3" /></td>
				</tr>
			</table>
			<input type="hidden" id="cpis_wizard_goto" name="cpis_wizard_goto" value="settings" />
			<input type="button" class="button" value="<?php esc_attr_e('Previous step', CPIS_TEXT_DOMAIN); ?>" onclick="jQuery(this).closest('div').hide().prev('div').show();" />
			<input type="submit" class="button button-primary" value="<?php esc_attr_e('Save wizard and create my first image', CPIS_TEXT_DOMAIN); ?>" onclick="jQuery('#cpis_wizard_goto').val('images');" />
			<input type="submit" class="button button-primary" value="<?php esc_attr_e('Save wizad and go to the store\'s settings', CPIS_TEXT_DOMAIN); ?>" />
		</div>
		<?php wp_nonce_field( plugin_basename( __FILE__ ), 'cpis_wizard' ); ?>
	</form>
	<script>jQuery(document).on('keydown', '#cpis_wizard input[type="text"]', function(e){var code = e.keyCode || e.which;if(code == 13) {e.preventDefault();e.stopPropagation();return false;}});</script>
	<?php
	update_option('cpis_has_been_configured', true);
	$wizard_active = true;
}
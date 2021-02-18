<?php
	global $wpdb;

	function cpis_make_seed() {
		list($usec, $sec) = explode(' ', microtime());
		return (float) $sec + ((float) $usec * 100000);
	}

    $options = get_option( 'cpis_options' );
    $currency = ( !empty( $options[ 'paypal' ][ 'currency' ] ) ) ? $options[ 'paypal' ][ 'currency' ] : 'USD';
    $language = ( !empty( $options[ 'paypal' ][ 'language' ] ) ) ? $options[ 'paypal' ][ 'language' ] : 'EN';
	$paypal_email = $options[ 'paypal' ][ 'paypal_email' ];

	$baseurl = CPIS_H_URL.'?cpis-action=ipn';

    $returnurl = _cpis_create_pages( 'cpis-download-page', 'Download Page' );
    $returnurl .= ( ( strpos( $returnurl, '?' ) === false ) ? '?' : '&' ).'cpis-action=download';
	$returnurl = cpis_complete_url($returnurl);

	$cancel_url = $_SERVER[ 'HTTP_REFERER' ];
    if( empty( $cancel_url ) ) $cancel_url = CPIS_H_URL;

    $amount = 0;
    $title = '';
    $id = '|';

    if( $paypal_email ){ // Check for sealer email
        mt_srand( cpis_make_seed() );
        $randval = mt_rand( 1,999999 );

        $number = 0;

        $purchase_id = md5( $randval.uniqid( '', true ) );

        if( !empty( $_POST['image_file'] ) ){
            $connector = "";
            $filter = "";
			if(!is_array($_POST[ 'image_file' ])) $_POST[ 'image_file' ] = array($_POST[ 'image_file' ]);
            foreach( $_POST[ 'image_file' ] as $image_id){
                $filter .= $wpdb->prepare($connector."file.id=%d", @intval($image_id));
                $connector = " OR ";
            }

            $products = $wpdb->get_results( "SELECT file.id as id, file.price as price, posts.post_title as title FROM ((".$wpdb->prefix.CPIS_IMAGE_FILE." as image_file INNER JOIN ".$wpdb->prefix."posts as posts ON posts.ID = image_file.id_image) INNER JOIN ".$wpdb->prefix.CPIS_FILE." as file ON image_file.id_file = file.id) WHERE posts.post_status='publish' AND ($filter)" );

            if($products){
                foreach( $products as $product ){
                    $amount += $product->price;
                    $title .= $product->title.'('.$product->price.')';
                    $number++;
                    $id .= 'id[]='.$product->id.'|';
                }
            }
        }

        $amount = round( $amount, 2 );

        if($amount > 0){

			// Removes invalid characters from products names
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
?>
<form action="https://www.<?php print( ( $options[ 'paypal' ][ 'activate_sandbox' ] ) ? 'sandbox.' : '' ); ?>paypal.com/cgi-bin/webscr" name="ppform<?php print $randval; ?>" method="post">
<input type="hidden" name="business" value="<?php print esc_attr($paypal_email); ?>" />
<input type="hidden" name="item_name" value="<?php print esc_attr($title); ?>" />
<input type="hidden" name="item_number" value="Item Number <?php print esc_attr($number); ?>" />
<input type="hidden" name="amount" value="<?php print esc_attr($amount); ?>" />
<input type="hidden" name="currency_code" value="<?php print esc_attr($currency); ?>" />
<input type="hidden" name="lc" value="<?php print esc_attr($language); ?>" />
<input type="hidden" name="return" value="<?php print esc_url($returnurl.'&purchase_id='.$purchase_id); ?>" />
<input type="hidden" name="cancel_return" value="<?php print esc_url($cancel_url); ?>" />
<input type="hidden" name="notify_url" value="<?php print esc_url($baseurl.$id.'purchase_id='.$purchase_id.'|rtn_act=purchased_product_cpis'); ?>" />
<input type="hidden" name="cmd" value="_xclick" />
<input type="hidden" name="page_style" value="Primary" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="no_note" value="1" />
<input type="hidden" name="bn" value="NetFactorSL_SI_Custom" />
<input type="hidden" name="ipn_test" value="1" />
<?php
	if(!empty($options[ 'paypal' ]['tax']))
		print '<input type="hidden" name="tax_rate" value="'.esc_attr($options[ 'paypal' ]['tax']).'" />';
?>
</form>
<?php do_action('cpis_paypal_form_html_before_submit', $products, $purchase_id);?>
<script type="text/javascript">document.ppform<?php print $randval; ?>.submit();</script>
<?php
            exit;
        }
    }

	header('location: '.$cancel_url);
?>
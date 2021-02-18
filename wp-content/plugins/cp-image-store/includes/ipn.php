<?php
	/* Short and sweet */
    error_reporting( E_ERROR | E_PARSE );
	echo 'Start IPN';
    global $wpdb;

	$ipn_parameters = array();
	$_parameters = explode('|', sanitize_text_field($_GET['cpis-action']));
	foreach($_parameters as $_parameter)
	{
		$_parameter_parts = explode('=',$_parameter);
		if(count($_parameter_parts) == 2)
		{
			if($_parameter_parts[0] == 'id[]')
			{
				if(!isset($ipn_parameters['id']) || !is_array($ipn_parameters['id'])) $ipn_parameters['id'] = array();
				$ipn_parameters['id'][] = $_parameter_parts[1];
			}
			else
				$ipn_parameters[$_parameter_parts[0]] = $_parameter_parts[1];
		}
	}

    function register_purchase( $product_id, $purchase_id, $email, $amount, $paypal_data, $purchase_note ){
        global $wpdb;
        return $wpdb->insert(
            $wpdb->prefix.CPIS_PURCHASE,
            array(
                'product_id'  => $product_id,
                'purchase_id' => $purchase_id,
                'date'		  => date( 'Y-m-d H:i:s'),
                'email'		  => $email,
                'amount'	  => $amount,
                'paypal_data' => $paypal_data,
                'note'        => $purchase_note
            ),
            array( '%d', '%s', '%s', '%s', '%f', '%s', '%s' )
        );
    }

	$item_name = sanitize_text_field($_POST[ 'item_name' ]);
	$item_number = sanitize_text_field($_POST[ 'item_number' ]);
	$payment_status = sanitize_text_field($_POST[ 'payment_status' ]);
	$payment_amount = @floatval($_POST[ 'mc_gross' ]);
	if( !empty( $_POST[ 'tax' ] ) ) $payment_amount -= @floatval($_POST[ 'tax' ]);
	$payment_currency = sanitize_text_field($_POST[ 'mc_currency' ]);
	$txn_id = sanitize_text_field($_POST[ 'txn_id' ]);
	$receiver_email = sanitize_text_field($_POST[ 'receiver_email' ]);
	$payer_email = sanitize_text_field($_POST[ 'payer_email' ]);
	$payment_type = sanitize_text_field($_POST[ 'payment_type' ]);

	if ( $payment_status != 'Completed' && $payment_type != 'echeck' ) exit;
	if ( $payment_type == 'echeck' && $payment_status == 'Completed' ) exit;

    $paypal_data = "";
	foreach ( $_POST as $item => $value ) $paypal_data .= sanitize_text_field($item)."=".sanitize_text_field($value)."\r\n";


    if( !isset( $ipn_parameters[ 'purchase_id' ] ) ) exit;
    $purchase_id = $ipn_parameters[ 'purchase_id' ];

    $options = get_option( 'cpis_options' );

    if(!isset($ipn_parameters['id'])) exit;

    $ids = $ipn_parameters['id'];
    $products = array();
    $total = 0;

    foreach( $ids as $id ){

        $file = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT file.price as price, image_file.id_image as id_image, file.id as id FROM ".$wpdb->prefix.CPIS_FILE." as file, ".$wpdb->prefix.CPIS_IMAGE_FILE." as image_file WHERE file.id=image_file.id_file AND image_file.id_file=%d",
                $id
            )
        );

        if( is_null( $file ) ) exit;
        $products[] = $file;
        $total += $file->price;
    }

    $total = round( $total, 2 );

    if ( $payment_amount < $total && abs( $payment_amount - $total ) > 0.2 ) exit;

    foreach( $products as $product ){
        if( register_purchase( $product->id, $purchase_id, $payer_email, $payment_amount, $paypal_data,  '' ) ) {
            $wpdb->query( $wpdb->prepare( "UPDATE ".$wpdb->prefix.CPIS_IMAGE." SET purchases=purchases+1 WHERE id=%d", $product->id_image ) );
        }
    }

	do_action('cpis_paypal_ipn_received', $_POST, $products);

	$notification_from_email = $options[ 'notification' ][ 'from' ];
	$notification_to_email = $options[ 'notification' ][ 'to' ];

	$notification_to_payer_subject = $options[ 'notification' ][ 'subject_payer' ];
	$notification_to_payer_message  = $options[ 'notification' ][ 'notification_payer' ];

	$notification_to_seller_subject = $options[ 'notification' ][ 'subject_seller' ];
	$notification_to_seller_message = $options[ 'notification' ][ 'notification_seller' ];

    $cpis_d_url = _cpis_create_pages( 'cpis-download-page', 'Download Page' );
    $cpis_d_url .= ( ( strpos( $cpis_d_url, '?' ) === false ) ? "?" : "&" )."cpis-action=download";
	$cpis_d_url = cpis_complete_url($cpis_d_url);

	$information_payer = "Product: {$item_name}\n".
						 "Amount: {$payment_amount} {$payment_currency}\n".
						 "Download Link: ".$cpis_d_url."&purchase_id={$ipn_parameters[ 'purchase_id' ]}\n";

	$information_seller = "Product: {$item_name}\n".
						  "Amount: {$payment_amount} {$payment_currency}\n".
						  "Buyer Email: {$payer_email}\n".
						  "Download Link: ".$cpis_d_url."&purchase_id={$ipn_parameters['purchase_id']}\n";

	$current_datetime = date('Y-m-d h:ia');

	// Get the buyer name from the buyer email,
	// only if there is an user with the same email than buyer
	$buyer_name = "";
	$buyer_user = get_user_by('email', $payer_email);
	if($buyer_user)
	{
		if($buyer_user->first_name)
		{
			$buyer_name = $buyer_user->first_name;
			if($buyer_user->last_name) $buyer_name .= ' '.$buyer_user->last_name;
		}
		else $buyer_name = $buyer_user->display_name;
	}

	$notification_to_payer_message  = str_replace(
		array(
			"%INFORMATION%",
			"%DATETIME%",
			"%BUYERNAME%"
		),
		array(
			$information_payer,
			$current_datetime,
			$buyer_name
		),
		$notification_to_payer_message
	);

	$notification_to_seller_message = str_replace(
		array(
			"%INFORMATION%",
			"%DATETIME%",
			"%BUYERNAME%"
		),
		array(
			$information_seller,
			$current_datetime,
			$buyer_name
		),
		$notification_to_seller_message
	);

	// Send email to payer
	try
	{
		wp_mail($payer_email, $notification_to_payer_subject, $notification_to_payer_message,
				"From: \"$notification_from_email\" <$notification_from_email>\r\n".
				"Content-Type: text/plain; charset=utf-8\n".
				"X-Mailer: PHP/" . phpversion());
	}
	catch( Exception $err ){}

    // Send email to seller
    if( !empty( $notification_to_email ) ){
		try
		{
			wp_mail($notification_to_email , $notification_to_seller_subject, $notification_to_seller_message,
					"From: \"$notification_from_email\" <$notification_from_email>\r\n".
					"Content-Type: text/plain; charset=utf-8\n".
					"X-Mailer: PHP/" . phpversion());
		}
		catch( Exception $err ){}
    }

   echo 'OK';
   exit();
?>
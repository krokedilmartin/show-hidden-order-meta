add_filter( 'is_protected_meta', '__return_false', 999 );
add_filter( 'acf/settings/remove_wp_meta_box', '__return_false' );


use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use WC_Order;

// Add action to add the custom meta box
add_action( 'add_meta_boxes', 'add_xyz_metabox' );

function add_xyz_metabox() {
	// Check if the CustomOrdersTableController class and the custom_orders_table_usage_is_enabled method exist
	if ( class_exists( '\Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController' ) ) {
		$custom_orders_table_controller = wc_get_container()->get( CustomOrdersTableController::class );

		if ( $custom_orders_table_controller && $custom_orders_table_controller->custom_orders_table_usage_is_enabled() ) {
			$screen = wc_get_page_screen_id( 'shop-order' );
		} else {
			$screen = 'shop_order';
		}
	} else {
		$screen = 'shop_order';
	}

	// Add the meta box
	add_meta_box(
		'xyz',
		'Custom Meta Box',
		'render_xyz_metabox',
		$screen,
		'side',
		'high'
	);
}

// Render the meta box
function render_xyz_metabox( $post ) {
	// Get the order object
	$order = wc_get_order( $post->ID );

	// Check if the order object is valid
	if ( $order instanceof WC_Order ) {
		// Fetch all meta data from the order
		$order_meta_data = $order->get_meta_data();

		if ( ! empty( $order_meta_data ) ) {
			echo '<div>';
			foreach ( $order_meta_data as $meta ) {
				// Each meta object contains a key-value pair
				$meta_key   = $meta->key;
				$meta_value = $meta->value;

				echo '<p><strong>' . esc_html( $meta_key ) . ':</strong> ' . esc_html( print_r( $meta_value, true ) ) . '</p>';
			}
			echo '</div>';
		} else {
			echo '<p>No meta data found.</p>';
		}
	} else {
		echo '<p>Order not found or invalid order object.</p>';
	}
}

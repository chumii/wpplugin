<?php

class Book3r {

	public function __construct() {
		$this->load_dependencies();
	}

	private function load_dependencies() {
		require_once plugin_dir_path(__FILE__) . 'class-book3r-booking-form.php';
		require_once plugin_dir_path(__FILE__) . 'class-book3r-customers.php';
		require_once plugin_dir_path(__FILE__) . 'class-book3r-booking-requests.php';

		new Book3r_Booking_Form();
		new Book3r_Customers();
		new Book3r_Booking_Requests();
	}

	public function run() {
		add_action('admin_menu', array($this, 'add_admin_menu'));
	}

	public function add_admin_menu() {
		add_menu_page(
			__('Book3r Dashboard', 'book3r'),
			__('Book3r', 'book3r'),
			'manage_options',
			'book3r-dashboard',
			array($this, 'display_dashboard'),
			'dashicons-admin-home',
			6
		);
	
		add_submenu_page(
			'book3r-dashboard',
			__('Dashboard', 'book3r'),
			__('Dashboard', 'book3r'),
			'manage_options',
			'book3r-dashboard',
			array($this, 'display_dashboard')
		);
	
		add_submenu_page(
			'book3r-dashboard',
			__('Customers', 'book3r'),
			__('Customers', 'book3r'),
			'manage_options',
			'book3r-customers',
			array($this, 'display_customers')
		);
	
		add_submenu_page(
			'book3r-dashboard',
			__('Booking Requests', 'book3r'),
			__('Booking Requests', 'book3r'),
			'manage_options',
			'book3r-booking-requests',
			array($this, 'display_booking_requests')
		);
	
		add_submenu_page(
			null,
			__('Edit Customer', 'book3r'),
			__('Edit Customer', 'book3r'),
			'manage_options',
			'book3r-edit-customer',
			array($this, 'display_edit_customer')
		);
	
		add_submenu_page(
			null,
			__('Edit Booking Request', 'book3r'),
			__('Edit Booking Request', 'book3r'),
			'manage_options',
			'book3r-edit-booking',
			array($this, 'display_edit_booking')
		);
	}

	public function display_dashboard() {
		global $wpdb;
		$customer_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}book3r_customers");
		$booking_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}book3r_bookings");
		?>
		<div class="wrap">
			<h1><?php _e('Book3r Dashboard', 'book3r'); ?></h1>
			<p><?php _e('Welcome to the Book3r plugin dashboard. Here you will find information about your bookings, customers, and other modules.', 'book3r'); ?></p>
			<p><?php _e('Total Customers:', 'book3r'); ?> <?php echo $customer_count; ?></p>
			<p><?php _e('Total Booking Requests:', 'book3r'); ?> <?php echo $booking_count; ?></p>
		</div>
		<?php
	}

	public function display_customers() {
		$customers = new Book3r_Customers();
		$customers->display_customers_page();
	}

	public function display_edit_customer() {
		$customers = new Book3r_Customers();
		$customers->display_edit_customer_page();
	}

	public function display_booking_requests() {
		$booking_requests = new Book3r_Booking_Requests();
		$booking_requests->display_booking_requests_page();
	}

	public function display_edit_booking() {
		$booking_requests = new Book3r_Booking_Requests();
		$booking_requests->display_edit_booking_page();
	}
}

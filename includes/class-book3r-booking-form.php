<?php

class Book3r_Booking_Form {

	public function __construct() {
		add_shortcode('book3r_booking_form', array($this, 'display_booking_form'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_action('wp_ajax_nopriv_book3r_submit_booking', array($this, 'handle_form_submission'));
		add_action('wp_ajax_book3r_submit_booking', array($this, 'handle_form_submission'));
	}

	public function enqueue_scripts() {
		wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('book3r-booking-form', plugin_dir_url(__FILE__) . 'js/book3r-booking-form.js', array('jquery'), null, true);

		wp_localize_script('book3r-booking-form', 'book3r_ajax_object', array(
			'ajax_url' => admin_url('admin-ajax.php')
		));
	}

	public function display_booking_form() {
		ob_start();
		?>
		<form id="book3r-booking-form" method="POST">
			<label for="arrival_date"><?php _e('Arrival Date', 'book3r'); ?></label>
			<input type="text" id="arrival_date" name="arrival_date" required>
			
			<label for="departure_date"><?php _e('Departure Date', 'book3r'); ?></label>
			<input type="text" id="departure_date" name="departure_date" required>
			
			<label for="preferred_room"><?php _e('Preferred Room', 'book3r'); ?></label>
			<input type="text" id="preferred_room" name="preferred_room" required>
			
			<label for="num_guests"><?php _e('Number of Guests', 'book3r'); ?></label>
			<input type="number" id="num_guests" name="num_guests" required>
			
			<label for="children_under_6"><?php _e('Children Under 6 Years', 'book3r'); ?></label>
			<input type="number" id="children_under_6" name="children_under_6" required>
			
			<label for="first_name"><?php _e('First Name', 'book3r'); ?></label>
			<input type="text" id="first_name" name="first_name" required>
			
			<label for="last_name"><?php _e('Last Name', 'book3r'); ?></label>
			<input type="text" id="last_name" name="last_name" required>
			
			<label for="email"><?php _e('Email Address', 'book3r'); ?></label>
			<input type="email" id="email" name="email" required>
			
			<label for="phone"><?php _e('Phone Number', 'book3r'); ?></label>
			<input type="text" id="phone" name="phone" required>
			
			<label for="address"><?php _e('Address', 'book3r'); ?></label>
			<input type="text" id="address" name="address" required>
			
			<label for="postal_code"><?php _e('Postal Code', 'book3r'); ?></label>
			<input type="text" id="postal_code" name="postal_code" required>
			
			<label for="city"><?php _e('City', 'book3r'); ?></label>
			<input type="text" id="city" name="city" required>
			
			<label for="country"><?php _e('Country', 'book3r'); ?></label>
			<input type="text" id="country" name="country" required>
			
			<label for="message"><?php _e('Message', 'book3r'); ?></label>
			<textarea id="message" name="message"></textarea>
			
			<input type="hidden" name="action" value="book3r_submit_booking">
			<?php wp_nonce_field('book3r_booking_form_nonce', 'book3r_booking_form_nonce_field'); ?>
			
			<input type="submit" value="<?php _e('Submit', 'book3r'); ?>">
		</form>
		<div id="book3r-booking-form-response"></div>
		<?php
		return ob_get_clean();
	}

	public function handle_form_submission() {
		check_ajax_referer('book3r_booking_form_nonce', 'book3r_booking_form_nonce_field');

		$data = array(
			'arrival_date' => sanitize_text_field($_POST['arrival_date']),
			'departure_date' => sanitize_text_field($_POST['departure_date']),
			'preferred_room' => sanitize_text_field($_POST['preferred_room']),
			'num_guests' => intval($_POST['num_guests']),
			'children_under_6' => intval($_POST['children_under_6']),
			'first_name' => sanitize_text_field($_POST['first_name']),
			'last_name' => sanitize_text_field($_POST['last_name']),
			'email' => sanitize_email($_POST['email']),
			'phone' => sanitize_text_field($_POST['phone']),
			'address' => sanitize_textarea_field($_POST['address']),
			'postal_code' => sanitize_text_field($_POST['postal_code']),
			'city' => sanitize_text_field($_POST['city']),
			'country' => sanitize_text_field($_POST['country']),
			'message' => sanitize_textarea_field($_POST['message']),
			'status' => 'New',
			'created_at' => current_time('mysql')
		);

		global $wpdb;
		$table_name = $wpdb->prefix . 'book3r_bookings';
		$wpdb->insert($table_name, $data);

		$this->maybe_update_customer($data);

		// Send emails
		$this->send_email_notifications($data);

		wp_send_json_success(__('Your booking request has been submitted.', 'book3r'));
	}

	private function maybe_update_customer($data) {
		global $wpdb;
		$customer_table = $wpdb->prefix . 'book3r_customers';

		$customer = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $customer_table WHERE first_name = %s AND last_name = %s AND email = %s",
				$data['first_name'],
				$data['last_name'],
				$data['email']
			)
		);

		if ($customer) {
			$updated_data = array(
				'phone' => $data['phone'],
				'address' => $data['address'],
				'postal_code' => $data['postal_code'],
				'city' => $data['city'],
				'country' => $data['country'],
			);
			$wpdb->update(
				$customer_table,
				$updated_data,
				array('id' => $customer->id)
			);
		} else {
			$customer_data = array(
				'first_name' => $data['first_name'],
				'last_name' => $data['last_name'],
				'email' => $data['email'],
				'phone' => $data['phone'],
				'address' => $data['address'],
				'postal_code' => $data['postal_code'],
				'city' => $data['city'],
				'country' => $data['country'],
				'created_at' => current_time('mysql')
			);
			$wpdb->insert($customer_table, $customer_data);
		}
	}

	private function send_email_notifications($data) {
		$admin_email = get_option('admin_email');
		
		// Guest email
		$guest_subject = __('Thank You for Your Booking Request', 'book3r');
		$guest_message = __("Thank you for your booking request. Here are the details:\n\n", 'book3r');
		$guest_message .= __('Arrival Date', 'book3r') . ": {$data['arrival_date']}\n";
		$guest_message .= __('Departure Date', 'book3r') . ": {$data['departure_date']}\n";
		$guest_message .= __('Preferred Room', 'book3r') . ": {$data['preferred_room']}\n";
		$guest_message .= __('Number of Guests', 'book3r') . ": {$data['num_guests']}\n";
		$guest_message .= __('Children Under 6 Years', 'book3r') . ": {$data['children_under_6']}\n";
		$guest_message .= __('First Name', 'book3r') . ": {$data['first_name']}\n";
		$guest_message .= __('Last Name', 'book3r') . ": {$data['last_name']}\n";
		$guest_message .= __('Email Address', 'book3r') . ": {$data['email']}\n";
		$guest_message .= __('Phone Number', 'book3r') . ": {$data['phone']}\n";
		$guest_message .= __('Address', 'book3r') . ": {$data['address']}\n";
		$guest_message .= __('Postal Code', 'book3r') . ": {$data['postal_code']}\n";
		$guest_message .= __('City', 'book3r') . ": {$data['city']}\n";
		$guest_message .= __('Country', 'book3r') . ": {$data['country']}\n";
		$guest_message .= __('Message', 'book3r') . ": {$data['message']}\n";

		// Admin email
		$admin_subject = __('New Booking Request', 'book3r');
		$admin_message = __("A new booking request has been submitted.\n\n", 'book3r');
		foreach ($data as $key => $value) {
			$admin_message .= ucfirst(str_replace('_', ' ', $key)) . ": $value\n";
		}

		// Send the emails
		wp_mail($data['email'], $guest_subject, $guest_message);
		wp_mail($admin_email, $admin_subject, $admin_message);
	}
}

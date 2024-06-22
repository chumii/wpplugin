<?php

class Book3r_Booking_Requests {

	public function __construct() {
		// Any initialization if needed
	}

	public function display_booking_requests_page() {
		global $wpdb;
		$bookings = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}book3r_bookings");
		?>
		<div class="wrap">
			<h1><?php _e('Booking Requests', 'book3r'); ?></h1>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php _e('Arrival Date', 'book3r'); ?></th>
						<th><?php _e('Departure Date', 'book3r'); ?></th>
						<th><?php _e('Preferred Room', 'book3r'); ?></th>
						<th><?php _e('Number of Guests', 'book3r'); ?></th>
						<th><?php _e('Children Under 6', 'book3r'); ?></th>
						<th><?php _e('First Name', 'book3r'); ?></th>
						<th><?php _e('Last Name', 'book3r'); ?></th>
						<th><?php _e('Email', 'book3r'); ?></th>
						<th><?php _e('Phone', 'book3r'); ?></th>
						<th><?php _e('Address', 'book3r'); ?></th>
						<th><?php _e('Postal Code', 'book3r'); ?></th>
						<th><?php _e('City', 'book3r'); ?></th>
						<th><?php _e('Country', 'book3r'); ?></th>
						<th><?php _e('Message', 'book3r'); ?></th>
						<th><?php _e('Status', 'book3r'); ?></th>
						<th><?php _e('Created At', 'book3r'); ?></th>
						<th><?php _e('Actions', 'book3r'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($bookings as $booking): ?>
					<tr>
						<td><?php echo esc_html($booking->arrival_date); ?></td>
						<td><?php echo esc_html($booking->departure_date); ?></td>
						<td><?php echo esc_html($booking->preferred_room); ?></td>
						<td><?php echo esc_html($booking->num_guests); ?></td>
						<td><?php echo esc_html($booking->children_under_6); ?></td>
						<td><?php echo esc_html($booking->first_name); ?></td>
						<td><?php echo esc_html($booking->last_name); ?></td>
						<td><?php echo esc_html($booking->email); ?></td>
						<td><?php echo esc_html($booking->phone); ?></td>
						<td><?php echo esc_html($booking->address); ?></td>
						<td><?php echo esc_html($booking->postal_code); ?></td>
						<td><?php echo esc_html($booking->city); ?></td>
						<td><?php echo esc_html($booking->country); ?></td>
						<td><?php echo esc_html($booking->message); ?></td>
						<td><?php echo esc_html($booking->status); ?></td>
						<td><?php echo esc_html($booking->created_at); ?></td>
						<td>
							<a href="<?php echo admin_url('admin.php?page=book3r-edit-booking&id=' . $booking->id); ?>"><?php _e('Edit', 'book3r'); ?></a>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	public function display_edit_booking_page() {
		global $wpdb;
	
		$booking_id = intval($_GET['id']);
		$booking = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}book3r_bookings WHERE id = %d", $booking_id));
	
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
				'status' => sanitize_text_field($_POST['status'])
			);
	
			$wpdb->update("{$wpdb->prefix}book3r_bookings", $data, array('id' => $booking_id));
	
			// Use ob_start() and ob_end_flush() to ensure no output before redirection
			ob_start();
			wp_safe_redirect(admin_url('admin.php?page=book3r-booking-requests'));
			ob_end_flush();
			exit;
		}
		?>
		<div class="wrap">
			<h1><?php _e('Edit Booking Request', 'book3r'); ?></h1>
			<form method="post">
				<table class="form-table">
					<tr>
						<th><?php _e('Arrival Date', 'book3r'); ?></th>
						<td><input type="text" name="arrival_date" value="<?php echo esc_attr($booking->arrival_date); ?>" required></td>
					</tr>
					<tr>
						<th><?php _e('Departure Date', 'book3r'); ?></th>
						<td><input type="text" name="departure_date" value="<?php echo esc_attr($booking->departure_date); ?>" required></td>
					</tr>
					<tr>
						<th><?php _e('Preferred Room', 'book3r'); ?></th>
						<td><input type="text" name="preferred_room" value="<?php echo esc_attr($booking->preferred_room); ?>" required></td>
					</tr>
					<tr>
						<th><?php _e('Number of Guests', 'book3r'); ?></th>
						<td><input type="number" name="num_guests" value="<?php echo esc_attr($booking->num_guests); ?>" required></td>
					</tr>
					<tr>
						<th><?php _e('Children Under 6', 'book3r'); ?></th>
						<td><input type="number" name="children_under_6" value="<?php echo esc_attr($booking->children_under_6); ?>" required></td>
					</tr>
					<tr>
						<th><?php _e('First Name', 'book3r'); ?></th>
						<td><input type="text" name="first_name" value="<?php echo esc_attr($booking->first_name); ?>" required></td>
					</tr>
					<tr>
						<th><?php _e('Last Name', 'book3r'); ?></th>
						<td><input type="text" name="last_name" value="<?php echo esc_attr($booking->last_name); ?>" required></td>
					</tr>
					<tr>
						<th><?php _e('Email', 'book3r'); ?></th>
						<td><input type="email" name="email" value="<?php echo esc_attr($booking->email); ?>" required></td>
					</tr>
					<tr>
						<th><?php _e('Phone', 'book3r'); ?></th>
						<td><input type="text" name="phone" value="<?php echo esc_attr($booking->phone); ?>" required></td>
					</tr>
					<tr>
						<th><?php _e('Address', 'book3r'); ?></th>
						<td><textarea name="address" required><?php echo esc_textarea($booking->address); ?></textarea></td>
					</tr>
					<tr>
						<th><?php _e('Postal Code', 'book3r'); ?></th>
						<td><input type="text" name="postal_code" value="<?php echo esc_attr($booking->postal_code); ?>" required></td>
					</tr>
					<tr>
						<th><?php _e('City', 'book3r'); ?></th>
						<td><input type="text" name="city" value="<?php echo esc_attr($booking->city); ?>" required></td>
					</tr>
					<tr>
						<th><?php _e('Country', 'book3r'); ?></th>
						<td><input type="text" name="country" value="<?php echo esc_attr($booking->country); ?>" required></td>
					</tr>
					<tr>
						<th><?php _e('Message', 'book3r'); ?></th>
						<td><textarea name="message"><?php echo esc_textarea($booking->message); ?></textarea></td>
					</tr>
					<tr>
						<th><?php _e('Status', 'book3r'); ?></th>
						<td>
							<select name="status">
								<option value="New" <?php selected($booking->status, 'New'); ?>><?php _e('New', 'book3r'); ?></option>
								<option value="Accepted" <?php selected($booking->status, 'Accepted'); ?>><?php _e('Accepted', 'book3r'); ?></option>
								<option value="Declined" <?php selected($booking->status, 'Declined'); ?>><?php _e('Declined', 'book3r'); ?></option>
								<option value="In Process" <?php selected($booking->status, 'In Process'); ?>><?php _e('In Process', 'book3r'); ?></option>
							</select>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes', 'book3r'); ?>"></p>
			</form>
		</div>
		<?php
	}
	
}

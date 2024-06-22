<?php

class Book3r_Customers {

	public function __construct() {
		// init
	}

	public function display_customers_page() {
		global $wpdb;
		$customers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}book3r_customers");
		?>
		<div class="wrap">
			<h1>Customer</h1>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Address</th>
						<th>Postal Code</th>
						<th>City</th>
						<th>Country</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($customers as $customer): ?>
					<tr>
						<td><?php echo esc_html($customer->first_name); ?></td>
						<td><?php echo esc_html($customer->last_name); ?></td>
						<td><?php echo esc_html($customer->email); ?></td>
						<td><?php echo esc_html($customer->phone); ?></td>
						<td><?php echo esc_html($customer->address); ?></td>
						<td><?php echo esc_html($customer->postal_code); ?></td>
						<td><?php echo esc_html($customer->city); ?></td>
						<td><?php echo esc_html($customer->country); ?></td>
						<td>
							<a href="<?php echo admin_url('admin.php?page=book3r-edit-customer&id=' . $customer->id); ?>">Edit</a>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	public function display_edit_customer_page() {
		global $wpdb;

		ob_start();

		$customer_id = intval($_GET['id']);
		$customer = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}book3r_customers WHERE id = %d", $customer_id));

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$data = array(
				'first_name' => sanitize_text_field($_POST['first_name']),
				'last_name' => sanitize_text_field($_POST['last_name']),
				'email' => sanitize_email($_POST['email']),
				'phone' => sanitize_text_field($_POST['phone']),
				'address' => sanitize_textarea_field($_POST['address']),
				'postal_code' => sanitize_text_field($_POST['postal_code']),
				'city' => sanitize_text_field($_POST['city']),
				'country' => sanitize_text_field($_POST['country'])
			);

			$result = $wpdb->update("{$wpdb->prefix}book3r_customers", $data, array('id' => $customer_id));

			if ($result !== false) {
				error_log('Customer updated successfully, redirecting...');
			} else {
				error_log('Failed to update customer.');
			}

			wp_safe_redirect(admin_url('admin.php?page=book3r-customers'));
			exit;
		}

		?>
		<div class="wrap">
			<h1>Edit Customer</h1>
			<form method="post">
				<table class="form-table">
					<tr>
						<th>First Name</th>
						<td><input type="text" name="first_name" value="<?php echo esc_attr($customer->first_name); ?>" required></td>
					</tr>
					<tr>
						<th>Last Name</th>
						<td><input type="text" name="last_name" value="<?php echo esc_attr($customer->last_name); ?>" required></td>
					</tr>
					<tr>
						<th>Email</th>
						<td><input type="email" name="email" value="<?php echo esc_attr($customer->email); ?>" required></td>
					</tr>
					<tr>
						<th>Phone</th>
						<td><input type="text" name="phone" value="<?php echo esc_attr($customer->phone); ?>" required></td>
					</tr>
					<tr>
						<th>Address</th>
						<td><textarea name="address" required><?php echo esc_textarea($customer->address); ?></textarea></td>
					</tr>
					<tr>
						<th>Postal Code</th>
						<td><input type="text" name="postal_code" value="<?php echo esc_attr($customer->postal_code); ?>" required></td>
					</tr>
					<tr>
						<th>City</th>
						<td><input type="text" name="city" value="<?php echo esc_attr($customer->city); ?>" required></td>
					</tr>
					<tr>
						<th>Country</th>
						<td><input type="text" name="country" value="<?php echo esc_attr($customer->country); ?>" required></td>
					</tr>
				</table>
				<p class="submit"><input type="submit" class="button-primary" value="Save Changes"></p>
			</form>
		</div>
		<?php

		ob_end_flush();
	}
}
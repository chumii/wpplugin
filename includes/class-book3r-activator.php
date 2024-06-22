<?php

class Book3r_Activator {

	public static function activate() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Create the bookings table
		$table_name = $wpdb->prefix . 'book3r_bookings';
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			arrival_date date NOT NULL,
			departure_date date NOT NULL,
			preferred_room varchar(255) NOT NULL,
			num_guests int NOT NULL,
			children_under_6 int NOT NULL,
			first_name varchar(255) NOT NULL,
			last_name varchar(255) NOT NULL,
			email varchar(255) NOT NULL,
			phone varchar(255) NOT NULL,
			address text NOT NULL,
			postal_code varchar(20) NOT NULL,
			city varchar(100) NOT NULL,
			country varchar(100) NOT NULL,
			message text NOT NULL,
			status varchar(20) NOT NULL DEFAULT 'New',
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);

		// Create the customers table
		$table_name = $wpdb->prefix . 'book3r_customers';
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			first_name varchar(255) NOT NULL,
			last_name varchar(255) NOT NULL,
			email varchar(255) NOT NULL,
			phone varchar(255) NOT NULL,
			address text NOT NULL,
			postal_code varchar(20) NOT NULL,
			city varchar(100) NOT NULL,
			country varchar(100) NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY (id)
		) $charset_collate;";

		dbDelta($sql);
	}
}

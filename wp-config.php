<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'canonica_wp347' );

/** MySQL database username */
define( 'DB_USER', 'canonica_wp347' );

/** MySQL database password */
define( 'DB_PASSWORD', 'e11(Spa-J6' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'xuya3de6lxi3bpzefh733kxmie1vqrw7wondozres71mzkjreuoaq8a0p1cyha8u' );
define( 'SECURE_AUTH_KEY',  'jpieazlglo8sq3hmxsnty8xi2fimcooxjwblognl0ydaf1thzs8icveghhhsrwwf' );
define( 'LOGGED_IN_KEY',    'sll9qoj0dxfrtrmmf2nrg4dgbeneh6ndzevudaog3dn4atkunro1ozjznsfapi8i' );
define( 'NONCE_KEY',        'qarc2ssg8wveoh2idbrujdehso3ai3i9wk8gn51upbzpk0kepnpxhgqn37u4cicq' );
define( 'AUTH_SALT',        '800avewl1qqawfawiyt0nuummbyvswpoasev4vy8alf7zbwtxjgxcib0oibh12ak' );
define( 'SECURE_AUTH_SALT', 'i2apgpeqrj6tx1crkhbjtmqrmk6ahdxhddyw3dtunas5vmsaii2f15awhwwbddzw' );
define( 'LOGGED_IN_SALT',   'wchujwpy23nvqhyjafdvsgdvkacszq5bdu4xpq3xumufomrh6vwsf6qiwggtw3gq' );
define( 'NONCE_SALT',       '6qfgjmvgkemijpimrruurxjjwxrthsb4fxhtn0babrjq3j5tamy9l4yiylkb4pc9' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpww_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
@include_once('/var/lib/sec/wp-settings-pre.php'); // Added by SiteGround WordPress management system
require_once ABSPATH . 'wp-settings.php';



@include_once('/var/lib/sec/wp-settings.php'); // Added by SiteGround WordPress management system

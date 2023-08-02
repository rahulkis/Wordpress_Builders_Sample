<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'builder_demo' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'Uadmin+18.04#' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'X_dso[*]mcL7pe,M3hVZ@r)0=2es^k!QD^&mn5%a],P>fD:*&gb6:m9cU(J*Jmw#' );
define( 'SECURE_AUTH_KEY',  ' .8q`NFb3;!aa%%[Azjx%lZj}bv9Q<05>wm2L()AgqVU#a4%6fY:db9a7Q0>HLIs' );
define( 'LOGGED_IN_KEY',    'e,:SIbv>u?hL#gkO6u[Ny.]29,8gZ+DlO.ok+;Ua<RPko6Q(4dyL< lO1-;tkcah' );
define( 'NONCE_KEY',        '_9oMNp&}Ips`|-e[M0Y=7-vS-1hO`g~pHE8TZlNwx_E##@LU+z>v:c#^xGa#Q@6:' );
define( 'AUTH_SALT',        'xyN3l~|X`4:FM~,RReX5qc~]$BH#d$HBSBviK(?1&pPb8@(OM+Q 9HBb%71-GbOs' );
define( 'SECURE_AUTH_SALT', '5V:ufEC/|clpwF7m7xdp6Hst3`Dc!VeK%utJ,ml/2I!1bBbr6eWwq4eX/ISG{#V-' );
define( 'LOGGED_IN_SALT',   'rQ0D$u@sJx1f,6,i8*.BKh(P2-*JsLB40p9ME2I_g#oLut5x}04]o(TxZF$dEx(y' );
define( 'NONCE_SALT',       'RHhSowr:tU-^A8eJeyq8A<CRo~i%qhFytCgZw<*D[G9/(2B=T4fPHzlKE!?nlg[3' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

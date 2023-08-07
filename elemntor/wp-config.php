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
define( 'DB_NAME', 'wordpress_demo' );

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
define( 'AUTH_KEY',         '^sxpIh=z$Q3T$^Q`~0P|tH8b9:VH68$-lFe6wcv`qw7!}g+)Ici:BA|W02PiY>E.' );
define( 'SECURE_AUTH_KEY',  'o/fD(6Y63A9pX^G<^-z-YDYL0F4$EowVgrHh8<r/[Ak?90nf!~tYbF},:1EUaXGm' );
define( 'LOGGED_IN_KEY',    '/7B,hc`9hQE(oe02AsVw_JtJkM#|Dyti),9S%o$:>x:<1j?60iae]K45p3%hG[2r' );
define( 'NONCE_KEY',        'w|RZ,FV{pDK/`kY[+,8@?^{XS9tF6jRli|X=42>^wgV=_B[QxEBvDcagb9!R9*H}' );
define( 'AUTH_SALT',        'jih,kKfh5s0LJ *=%AFc;gE>,^=^eq0rF.%`rl$)&/VUa;pa9U(E67t$8S~TGtKe' );
define( 'SECURE_AUTH_SALT', 'c(.-TbQn9=y0oxHCW }W9o74Y%nA*K+BujdVW UJTJI$`rI8kP1Z*[>$(PcejygC' );
define( 'LOGGED_IN_SALT',   'nRD.S]igoOAWJ5l9Kq+1H7*?bv~wwGp{;1(_^)LQ%p%O+rYTp`Z{v~C(%&f+=Vp6' );
define( 'NONCE_SALT',       '{N(.i%+;)dhSv<A$8-*2hxDhO_VXgUFm^%eXj[kKePNX@Hfx-^El![uLQWJ#el2<' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';
define('FS_METHOD', 'direct');
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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

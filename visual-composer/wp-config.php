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
define( 'DB_NAME', 'Builder_demo_2' );

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
define('FS_METHOD', 'direct');

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
define( 'AUTH_KEY',         '5xr?Ach{OsF5cJ?_s=V{Tl&28zp!xBIn+,xbe}C9!dX`x.H{Q%n%6 o8q_w7L6~ ' );
define( 'SECURE_AUTH_KEY',  '9jGkxZ|hbDdw(v<DS)*z`$|1(tMeeYN[PQ@zjRfA;W`{ZUNuc){@wSQ.wX?)6=iS' );
define( 'LOGGED_IN_KEY',    '0%#]HvMKV4L~)+1|j3Q9Bp,rgv)4)bRf)Y==R&coOb*Xa:TfsDq65ed$+E{OgLg-' );
define( 'NONCE_KEY',        'uTTZ&jUfM_^[#N8Fptin0>~Xotu1e<vEHu =W+*$$Q$Je(Ux|u9&=RR@K/]z_h96' );
define( 'AUTH_SALT',        '|IQIH &Lu#:pSU18O}F1n^ZpWh3IMP;Dt Q(&/^Vi8tN]tXSl`Ap[mEi*LMh/}A~' );
define( 'SECURE_AUTH_SALT', '9ip]ewYp~b4I+n4]%El {!l.4Z%sXJMJeFOX/ZXPP{!,#tRb3h,)I0`F|vxNr*:.' );
define( 'LOGGED_IN_SALT',   'rgv>;X-P&#KXn`9{ZS~cr7ZK7/TG|N2Y#E&%,NK_C}i&$$#9cd+&6}(-~>2JHf=*' );
define( 'NONCE_SALT',       '*:N}L(WRTR5S1$<)FCvaK#4F#%{k/%FS$:j5JbZ 6rh~hjhM^UyTV5@/V3{3X|&G' );

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

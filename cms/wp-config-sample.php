<?php
define( 'WP_HOME', 'http://localhost/ogis' );
define( 'WP_SITEURL', 'http://localhost/ogis/cms' );

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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'ogis' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'rootroot' );

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
define( 'AUTH_KEY',         'kytlL?*kV^%@cpx/adV1s?IuXHiak]<euP<b|L5[)a4L.qy>R<?d RtA]dKUS3F4' );
define( 'SECURE_AUTH_KEY',  'l#Eo9Pg^h=6fe>%8|T0nhvQHK4XS<?(N#+GMU7gzaj@IY64j)0eyHlQHYYs#H.Fh' );
define( 'LOGGED_IN_KEY',    '(/W1%|-c<((Wn*mnmx6jQQC?;7^#,2]/)3)k3u6pXeHyHc,i(zR|E!RH`-@2])dd' );
define( 'NONCE_KEY',        '(0o]Nn| xD}fc*oBIYD+8GIirwg07d6sxjB[,AzgiYdHl;Mcb.B]@nH%O_e&UvgO' );
define( 'AUTH_SALT',        '4Tmmh<WpTS9D*~l_z&kB)jeI&@K{UZY^SyGpF1K)3|W;Oe*#rMJG8<+-Q];abEE}' );
define( 'SECURE_AUTH_SALT', '3K}[ZI AG<5:xqukZ8x;@s};l`r]vMa=%@]bnWXj`]G#`}z#jRCI8x~-xaGF/qX[' );
define( 'LOGGED_IN_SALT',   'tn09tGFI%f-XV7`v_ahk%}x4USmzMjXjdfUE}nT|PUSx{HyPrP6xBhoN:7w%}x(&' );
define( 'NONCE_SALT',       'Jb!ltoOI0! R2_wH{B$,GJG&~7ZYT&&wS K6~l,_-E$GHiXzFcxM@j=l}O!|kJsu' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'ogis_';

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

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

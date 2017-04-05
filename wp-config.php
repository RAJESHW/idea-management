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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'xproot');

/** MySQL database password */
define('DB_PASSWORD', 'xproot');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '=4/b8Tc7QX5}ht>3(%4qB.+AZ=0hv3#cZ)XV1&eQHfC`,OoW?kJ]ZcY:;6;%dC.f');
define('SECURE_AUTH_KEY',  'gbNX{xQ[0/O$!hql{-(c*KI1{u=0!6E/T(-42=/Yd10x58Kve/.=T[^ZwauDUmO2');
define('LOGGED_IN_KEY',    'gifUoyPj*>ga;H9ip*vUqHP)7`Gzi<oD=CKXstU5ZT;&b_::5Ym^qS&59#M.px7j');
define('NONCE_KEY',        'KtfqdWo)Z5Dr%H,jt-83]H8Kl,Gwb;]>wfiS7t./bA<RaG:$?eV(lX LAkzYI4hE');
define('AUTH_SALT',        'Uk5(,8.?4-0O9lj?T5{[&%}c|AS`|?Z)={L7-af+OOn>-WuGh4iC3=KSK3zic0][');
define('SECURE_AUTH_SALT', '+SE$vNpK@ksn7n8}Nv5_-mDE#1L|(e.mv*#yCIMpI#oTi=xOFWwdJm`CawgCDkVk');
define('LOGGED_IN_SALT',   'hXT-e_ ^WiE&CcZ,DzIQ:2,uWLQ)I6_+!IeoeY)Y!#n9S:4YG5r0~@{1_6xH<GM9');
define('NONCE_SALT',       '@B#{P8S2TQ)n(l%5?<v7^Oj=yJN/<{VE50WYX~yi-@lVy6H{%iT+*q%rN5j6wUDl');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

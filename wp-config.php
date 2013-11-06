<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

define('WP_SITEURL', 'http://' . $_SERVER['SERVER_NAME'] . '/wordpress');
define('WP_HOME',    'http://' . $_SERVER['SERVER_NAME']);
define('WP_CONTENT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/wp-content');
define('WP_CONTENT_URL', 'http://' . $_SERVER['SERVER_NAME'] . '/wp-content');

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'XXX');

/** MySQL database password */
define('DB_PASSWORD', 'XXX');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         '66iKa6e)3ih0]$b&9wL*)_zZFR?GaK;-%gi5hPH57}[~e_!-,]Gu(Le_eFGS/V;9');
define('SECURE_AUTH_KEY',  '|1E^{+MLg]5Oy-_/N)P;&-4-&|]8|]S-OV( 4.$gbg_aL/vu{M//)%JX>X_+%TTs');
define('LOGGED_IN_KEY',    'GJl+CCaa%>}a:Zs&C`-XE}}?ie]9yMD?D4**pV/1|Yn1 -%RA+ Ai6MwhfTXO^72');
define('NONCE_KEY',        'MLv!!ocqhP$ ~v.~D=4|_VHfkipR#R!md6]QW!vXJXtnNRlN2/5JIlv-}66xi{3A');
define('AUTH_SALT',        'f{w#PEd9Z5[ebF9@2&Fj_K4-4*&&+H%8@Of3IrpB?cJaick^9x6,FUv-/KD!>(ml');
define('SECURE_AUTH_SALT', 'iB|pnhzZ0^rA]JZX$KYGU-v|/W6tA4Z3Priq{h6hZqs !DQxwzF-0O`{l}tVk;Ds');
define('LOGGED_IN_SALT',   '.:{aT7_WyvChknl%sS,RNgBk&t2]H+`=P./b>^)8c:iII-&%i:;teSTX+]G8WNp-');
define('NONCE_SALT',       'HPf[Qi%tQ- 9F)klj.f7Gnrrt[.?ypVZ$XDW_!e8p$sWY73SHauv Q4q+wMg7z{D');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');


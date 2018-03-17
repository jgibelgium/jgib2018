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
define('DB_NAME', 'jgib2017');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'ais&ui1NcG--[gm4pc[l47<#imfl8Fi]4rAQcHt6)yVRLSj*,HgfbK%O*5nx2Azw');
define('SECURE_AUTH_KEY',  '#*r}b{}v>XACSBVhVk~o46F9OJLP#Z%-]Kyo8>6~Z/DNxyWi V{`hpN BI{$1Fa#');
define('LOGGED_IN_KEY',    'jJnvq!$ska{j[VC7a4p8#^Bt vlweK%FO_SOnB(WX)D.O2{fPC:U0eXZ^As3TI51');
define('NONCE_KEY',        '_|!9<yBf564)}}.!=U7%e^!.2<-L^ddKzOjyg!CBj/Ikm,i$R;|N@~lmuHZfv:f=');
define('AUTH_SALT',        'ojKa5/9fPMI?tM^FeI]QXVjJL2@]<hO[a(1A @!Bk+1BW&z$5HVex]{Fw 4}#v1G');
define('SECURE_AUTH_SALT', 'U302khg^>RZhwd! z*!>3jcYshKs].4ubptov1-|R;;f|Fj$Jnc{! h}=#=]aPR6');
define('LOGGED_IN_SALT',   'b+kLKraB]6}`mkK+t-)zNVL_!iUb.[G3wy:qY)~xJ jAlq&yz2~iNgm0zx;~s+xx');
define('NONCE_SALT',       'VXvx}qwsViMCvC4iZ=)*-v)|V6p$[,rUrBhSj3>sqF|%PmiD:xljQ[Rn%PYS*P ;');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'jg_';

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

define('WP_MEMORY_LIMIT', '128MB');

define('WP_POST_REVISIONS', 3);

define('AUTOSAVE_INTERVAL', 999999);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

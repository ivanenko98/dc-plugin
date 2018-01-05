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
define('DB_NAME', 'dchplugin');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'Grass951235789');

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
define('AUTH_KEY',         '5*=Az<U6iH0fc19ov$jqyT7?oA#p!0sy!O:VLmbb~u:WR`}^yNF#Q_H|XUwgufy(');
define('SECURE_AUTH_KEY',  '-{4Nj:^AJWh)Ij>dza4l9[.mvS(w0|,uRf,*CpL4q$V<Nt(5).)}5IBpf}X$WW^z');
define('LOGGED_IN_KEY',    'OF1l)Bl_6zG30_W@8~HI4@u$SJNiK@EdA0Q?no#Bm17Ghl?E0-1Qp^LxrNX9af^m');
define('NONCE_KEY',        '0G7Dd{2zi-WJGO9XFPcV$@RAkxJSHOrsR$yaS_25h|ObO/jxW6=`iD=6x8U+Ez;!');
define('AUTH_SALT',        'KbY&r}r]6>Ax)ht2;ZO`f_nd~d2e5NAYkT3}VuvN3 &iYItEO-A$==^Jap:GQeT_');
define('SECURE_AUTH_SALT', '#xx:k]C?X;UAYbE)s[oWu;^G:%Z(!GiXQPMb+w2fQ<Nd4kt8rRYQK2~).#Z,)W`{');
define('LOGGED_IN_SALT',   '0v$i3P%5& VjUc(&=14J/wStVfO<,i,]N&Zv%ddOTW:DDv|w#|5:&Rt#p:bxR<)j');
define('NONCE_SALT',       'X;#ilC[`+,xD 3xe&jE2,>m_|vrsZloYQC2Xj)<<u?NjF#bU-T>,4GkPYC@0%ZXC');

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

<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

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

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'dbo87jtbb8ija' );

/** MySQL database username */
define( 'DB_USER', 'jvkqbzbyrgzpyi' );

/** MySQL database password */
define( 'DB_PASSWORD', '419210eea216e975b4ff208b559d5e14c8467407bf88d5a7a865e33319843a06' );

/** MySQL hostname */
define( 'DB_HOST', 'ec2-54-217-218-80.eu-west-1.compute.amazonaws.com' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '-N&u:1r&tTxI@)3.6g(#F)#Q>gb[u}|w,5P.<ja4nYbgeM7Ir7zXfU3Y?%FR;CO`');
define('SECURE_AUTH_KEY',  'ldoSX<jIV]|b$r+rnGy`rBdJle21su5$w,H;|P]}Qka5![W:SM,$D}uo`&vyXYZ2');
define('LOGGED_IN_KEY',    ',|w(7>0s]xaNc/O]73< F(=k4s`D*!wgez_#(m0-vjya +xKW!$|i1_&Y6f_9jQ/');
define('NONCE_KEY',        'garw)eKLQ;%~]c/H4e<xx$W_=+IxQk(4pgl3}(b=> PPAwwzdL]1Mv]nq}fI%C?0');
define('AUTH_SALT',        '!KT:?BTm^fxff8f8AW!.r0u,Ep<y&mgqll0KmAdOn`i!I>Zo=TY<N0O|Cd9%{Pw%');
define('SECURE_AUTH_SALT', 'wqHjglK}X;]kN7_6FAGcA!ty2%~xX%dug5hOpCRsWIP/$+e]<<4+_/2R3)q lXp<');
define('LOGGED_IN_SALT',   'PiU_+6ULAl-a2{^| VE9.~ze9##hEtquj|~S/QIgh:RTe~{?08%b&r*7[mJP=K)s');
define('NONCE_SALT',       'B6;pZ8+jF-60)-~,I|=}}k+2SV5Ohk@:0zY0I~zeZ>9t[B+Uv(}plX#= I4!>w:0');


/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en « wp-config.php » et remplir les
 * valeurs.
 *
 * Ce fichier contient les réglages de configuration suivants :
 *
 * Réglages MySQL
 * Préfixe de table
 * Clés secrètes
 * Langue utilisée
 * ABSPATH
 *
 * @link https://fr.wordpress.org/support/article/editing-wp-config-php/.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'wordpress' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'root' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', '' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'localhost' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/**
 * Type de collation de la base de données.
 * N’y touchez que si vous savez ce que vous faites.
 */
define( 'DB_COLLATE', '' );

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clés secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'hlONp9#<|OsZNO-[BLPd)_ 6Z!%|^YlGA,;<*Me${%Q7(@R&dt^gkKHyf[<aOaG2' );
define( 'SECURE_AUTH_KEY',  'yC1*`,_10)b6,Ig*?RIlc~|Z~oNENDyMVB!Vzc|TIO5=+V5K<~;GXk86;cGXdJ+u' );
define( 'LOGGED_IN_KEY',    '[h!nY2n?P;:i+(H5`!z%i}6o3[SeE2_X<>Zzll2{}k,Q5JP>=pZ]:([g-Z|d=ZOk' );
define( 'NONCE_KEY',        'uU]f&}WH5jZY^Cx[s6g 0#O0f*;8m70 z(%#XuWL4n 5[*M`3lj8nR4N;c#dwjM6' );
define( 'AUTH_SALT',        'z^f(GxgG2~4QGn993B:xJ^(gFpH~Y-q:_*5F3B_[16mAbT`N`J4[ 0x2B.d;Ym,e' );
define( 'SECURE_AUTH_SALT', 'AFL=.V5om u [^}6u/!pOv1X0Jt9HFj[P$1F^ jLTQSO,QtH)Ssw34mS}kEyzAD2' );
define( 'LOGGED_IN_SALT',   'ZwCE1kwE+3&)8cj[YT[BwO X T)[I]rNjh`;zb,$i&5L6J* ?{m<_ESF,.b|C7@{' );
define( 'NONCE_SALT',       ')Gj|KQB_!5FQE3q8/m^kj[sVZUO5`lX|13u`<{Oko+dI:SC)g<>xNQ-gR,zn-QUE' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortement recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://fr.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( ! defined( 'ABSPATH' ) )
  define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once( ABSPATH . 'wp-settings.php' );

<?php
/**
 * Plugin Name: Talk: Spatie Ray
 * Description: WP-CLI demo commands for a Spatie Ray talk (`wp wcnl <scenario>`).
 * Version: 1.0.0
 * Author: Digital Passenger
 * Requires PHP: 8.3
 */

declare(strict_types=1);

namespace TalkSpatieRay;

use ReflectionClass;
use WP_CLI;
use WP_CLI\DocParser;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/IlluminateStrPolyfill.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once __DIR__ . '/src/Command.php';
	require_once __DIR__ . '/src/OrderedCommands.php';

	// Talk order — also controls `wp wcnl` listing (WP-CLI otherwise sorts A–Z).
	$talk_order = [
		'helpers',
		'overkill',
		'mail',
		'queries',
		'pause',
		'disable',
		'end',
	];

	$doc       = ( new ReflectionClass( Command::class ) )->getDocComment() ?: '';
	$container = new OrderedCommands( WP_CLI::get_root_command(), 'wcnl', new DocParser( $doc ) );
	WP_CLI::get_root_command()->add_subcommand( 'wcnl', $container );

	foreach ( $talk_order as $method ) {
		WP_CLI::add_command( "wcnl {$method}", [ Command::class, $method ] );
	}
}

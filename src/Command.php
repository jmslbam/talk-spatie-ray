<?php

declare(strict_types=1);

namespace TalkSpatieRay;

use WP_CLI;
use WP_CLI_Command;
use WP_Query;

/**
 * Spatie Ray talk demo's.
 *
 * Run these in talk order. Each command sends output to the Ray desktop app.
 *
 * @when after_wp_load
 */
final class Command extends WP_CLI_Command {


	/**
	 * Output helpers: sizes, table, json, xml, file, image, link, html, text.
	 *
	 * ## EXAMPLES
	 *
	 *     wp wcnl helpers
	 */
	public function helpers( array $args, array $assoc_args ): void {

		// With label
	$this->new_screen( '1. helpers()' );

		ray( 'small' )->small()->red()->label( 'handig wanneer tijdens berekeningen' );
		ray( 'regular' );
		ray( 'large' )->large()->blue()->label( 'voor en na een berekening' );

		$nested_array = [
			'a' => 1,
			'b' => [ 'c' => 3 ],
		];

		ray( $nested_array );
		ray( $nested_array )->expand(2);

		ray()->table(
			[
				'First'  => 'First value',
				'Second' => 'Second value',
				'Third'  => 'Third value',
			],
			'Table'
		);

		$object          = new \stdClass();
		$object->company = 'Spatie';
		ray()->toJson(
			[
				'a' => 1,
				'b' => [ 'c' => 3 ],
			],
			$object
		);

		$json_string = wp_json_encode(
			[
				'a' => 1,
				'b' => [ 'c' => 3 ],
			]
		);
		ray()->json( $json_string );

		ray()->xml( '<one><two><three>3</three></two></one>' );

		if ( class_exists( \Carbon\Carbon::class ) ) {
			ray()->carbon( new \Carbon\Carbon() );
		}

		ray()->file( dirname( __DIR__ ) . '/assets/demo.txt' );

		ray()->image( 'https://jaimemartinez.nl/wp-content/themes/run-tief/dist/images/jaime-martinez-profile.webp' );

		// ray()->link( 'jaimemartinez.nl', 'Jaime Martínez homepage' );
		// ray()->url( 'jaimemartinez.nl' );

		$test_page = get_page_by_path( 'portfolio' );
		if ( $test_page instanceof \WP_Post ) {
			ray()->html( $test_page->post_content );
		} else {
			WP_CLI::warning( 'Page with slug "portfolio" not found; skipped ray()->html().' );
		}

		ray()->text( $test_page->post_content );

		ray()->text( '  whitespace formatting' . PHP_EOL . '   is preserved as well.' );

		// Deliberatly trigger error > https://github.com/jmslbam/ray-php-errors
		ray()->text('asdf');
	}

	/**
	 * To prevent overloading your Ray with a lot of messages
	 *
	 * @param array $args
	 * @param array $assoc_args
	 * @return void
	 */
	public function overkill( array $args, array $assoc_args ): void {
		$this->new_screen( '5. overkill()' );

		foreach ( range( 1, 10 ) as $i ) {
			// No ->blue(): color payloads also count toward limit().
			ray()->limit( 6 )->text( "B #{$i}" ); // exactly 6
			ray()->text( "C #{$i}" )->green(); // all 10
		}
	}

	/**
	 * WordPress: capture outgoing mail with showMails().
	 *
	 * ## EXAMPLES
	 *
	 *     wp wcnl mail
	 */
	public function mail( array $args, array $assoc_args ): void {
		$this->new_screen( '7. showMails()' );

		ray()->showMails();

		$sent = wp_mail(
			'talk@example.test',
			'Talk: Spatie Ray',
			'Dit mailtje zie je in Ray via ray()->showMails().'
		);

		ray()->stopShowingMails();

		if ( $sent ) {
			return;
		}

		WP_CLI::warning( 'wp_mail() returned false (local mail often fails). phpmailer_init still fired, so Ray should show the mail.' );
	}

	/**
	 * WordPress: showQueries() start and stop.
	 *
	 * ## EXAMPLES
	 *
	 *     wp wcnl queries
	 */
	public function queries( array $args, array $assoc_args ): void {
		$this->new_screen( '8. showQueries() start / stop' );
		$this->enable_query_logging();

		ray( '--- queries ON ---' )->green();
		ray()->showQueries();

		new WP_Query(
			[
				'post_type'      => 'post',
				'posts_per_page' => 1,
				'no_found_rows'  => true,
			]
		);

		ray()->stopShowingQueries();
		ray( '--- queries OFF ---' )->gray();

		new WP_Query(
			[
				'post_type'      => 'page',
				'posts_per_page' => 1,
				'no_found_rows'  => true,
			]
		);

		ray( 'De tweede WP_Query zou niet als SQL in Ray moeten staan.' );

		WP_CLI::success( 'showQueries() was on for the first WP_Query, off for the second.' );
	}

	/**
	 * Pause PHP until you click Continue in Ray.
	 *
	 * See https://myray.app/blog/pausing-and-disabling-ray-execution
	 *
	 * ## EXAMPLES
	 *
	 *     wp wcnl pause
	 */
	public function pause( array $args, array $assoc_args ): void {
		$this->new_screen( '9. pause()' );

		ray( 'voor pause()' );
		WP_CLI::log( 'Execution is paused. Click Continue in Ray to resume, or Stop execution to abort.' );

		try {
			ray()->pause();
		} catch ( \Throwable $exception ) {
			WP_CLI::error( $exception->getMessage() );
		}

		ray( 'na Continue' );
		WP_CLI::success( 'Continued after ray()->pause().' );
	}

	/**
	 * Temporarily disable and re-enable Ray dumps.
	 *
	 * See https://myray.app/blog/pausing-and-disabling-ray-execution
	 *
	 * ## EXAMPLES
	 *
	 *     wp wcnl disable
	 */
	public function disable( array $args, array $assoc_args ): void {
		$this->new_screen( '10. disable() / enable()' );

		ray( 'one' ); // shown

		ray()->disable();
		ray( 'two' ); // not shown
		WP_CLI::log( sprintf( 'After disable(): enabled=%s', ray()->enabled() ? 'true' : 'false' ) );

		ray()->enable();
		ray( 'three' ); // shown

		WP_CLI::success( 'Ray showed "one" and "three", not "two".' );
	}

	public function end( array $args, array $assoc_args ): void {
		$this->new_screen( '' );
		ray()->notify('Pfiat enk 🇦🇹');
		ray()->confetti();
	}

	private function new_screen( string $title ): void {
		$this->ensure_ray();
		$this->focus_ray_app();
		ray()->newScreen( $title );
		ray()->showApp();
	}

	private function ensure_ray(): void {
		if ( ! function_exists( 'ray' ) ) {
			WP_CLI::error( 'function ray() is missing. Activate Spatie Ray (spatie-ray or wordpress-ray) first.' );
		}

		if ( class_exists( \Spatie\WordPressRay\Ray::class ) ) {
			\Spatie\WordPressRay\Ray::bootForWordPress();
		}
	}

	/**
	 * showApp() shows the window but often won't steal focus on macOS,
	 * and does nothing if Ray isn't running yet.
	 */
	private function focus_ray_app(): void {
		if ( PHP_OS_FAMILY !== 'Darwin' ) {
			return;
		}

		$script = 'tell application "Ray" to activate';
		exec( 'osascript -e ' . escapeshellarg( $script ) . ' >/dev/null 2>&1' );
	}

	private function enable_query_logging(): void {
		global $wpdb;

		if ( ! defined( 'SAVEQUERIES' ) ) {
			define( 'SAVEQUERIES', true );
		}

		$wpdb->save_queries = true;
	}
}

<?php
/**
 * Minimal polyfill for Illuminate\Support\Str.
 *
 * Spatie Ray's link()/url() methods use Str::startsWith(), but illuminate/support
 * is only a require-dev dependency of spatie/ray — so it is missing in WordPress.
 */

declare(strict_types=1);

namespace Illuminate\Support;
return;
if ( class_exists( Str::class ) ) {
	return;
}

final class Str {

	/**
	 * @param  string        $haystack
	 * @param  string|array  $needles
	 */
	public static function startsWith( $haystack, $needles ): bool {
		foreach ( (array) $needles as $needle ) {
			if ( (string) $needle !== '' && str_starts_with( (string) $haystack, (string) $needle ) ) {
				return true;
			}
		}

		return false;
	}
}

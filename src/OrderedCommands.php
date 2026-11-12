<?php

declare(strict_types=1);

namespace TalkSpatieRay;

use WP_CLI\Dispatcher\CompositeCommand;

/**
 * Like CompositeCommand, but keeps registration order (WP-CLI ksorts by default).
 */
final class OrderedCommands extends CompositeCommand {

	/**
	 * @return array<string, mixed>
	 */
	public function get_subcommands() {
		return $this->subcommands;
	}
}

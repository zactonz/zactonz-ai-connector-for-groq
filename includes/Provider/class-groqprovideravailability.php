<?php
/**
 * Availability check for the Groq connector.
 *
 * @package Zactonz\AiConnectorForGroq\Provider
 */

declare( strict_types=1 );

namespace Zactonz\AiConnectorForGroq\Provider;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Zactonz\AiConnectorForGroq\Settings\GroqSettings;
use WordPress\AiClient\Providers\Contracts\ProviderAvailabilityInterface;

/**
 * Reports whether the connector holds usable credentials.
 *
 * @since 1.0.0
 */
class GroqProviderAvailability implements ProviderAvailabilityInterface {

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.0.0
	 */
	public function isConfigured(): bool {
		return GroqSettings::has_credentials();
	}
}

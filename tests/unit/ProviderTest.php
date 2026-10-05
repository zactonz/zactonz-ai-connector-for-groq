<?php

declare( strict_types=1 );

namespace Zactonz\AiConnectorForGroq\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Zactonz\AiConnectorForGroq\Provider\GroqProfile;
use Zactonz\AiConnectorForGroq\Provider\GroqProvider;
use Zactonz\AiConnectorForGroq\Settings\GroqSettings;

class ProviderTest extends TestCase {

	protected function setUp(): void {
		zctz_test_reset_credentials();
		$GLOBALS['zctz_test_options'] = array();
	}

	public function test_the_provider_reports_its_identity(): void {
		$metadata = GroqProvider::metadata();

		$this->assertSame( GroqProfile::id(), $metadata->getId() );
		$this->assertSame( GroqProfile::name(), $metadata->getName() );
		$this->assertNotSame( '', GroqProfile::api_key_url() );
	}

	public function test_the_bundled_logo_exists(): void {
		$this->assertFileExists( dirname( __DIR__, 2 ) . '/includes/Provider/logo.svg' );
	}

	public function test_urls_are_built_from_the_configured_base_url(): void {
		$this->assertSame( GroqSettings::get_base_url(), GroqProvider::url() );
		$this->assertSame( GroqSettings::get_base_url() . '/models', GroqProvider::url( '/models' ) );
		$this->assertSame( GroqSettings::get_base_url() . '/models', GroqProvider::url( 'models' ) );
	}

	public function test_availability_follows_the_stored_credentials(): void {
		$this->assertFalse( GroqProvider::availability()->isConfigured() );

		zctz_test_seed_settings();

		$this->assertTrue( GroqProvider::availability()->isConfigured() );
	}

	public function test_the_advertised_capabilities_match_the_profile(): void {
		$this->assertTrue( GroqProfile::supports( 'text' ) );

		foreach ( array( 'vision', 'tools', 'structured', 'embedding', 'image', 'reasoning' ) as $capability ) {
			$this->assertIsBool( GroqProfile::supports( $capability ) );
		}
	}
}

<?php

declare( strict_types=1 );

namespace Zactonz\AiConnectorForGroq\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WordPress\AiClient\AiClient;
use WordPress\AiClient\Providers\Http\DTO\ApiKeyRequestAuthentication;
use Zactonz\AiConnectorForGroq\Provider\GroqProfile;
use Zactonz\AiConnectorForGroq\Settings\GroqSettings;

class SettingsTest extends TestCase {

	protected function setUp(): void {
		zctz_test_reset_credentials();
		$GLOBALS['zctz_test_options']         = array();
		$GLOBALS['zctz_test_settings_errors'] = array();
		$GLOBALS['zctz_test_http_responses']  = array();
		$GLOBALS['zctz_test_http_requests']   = array();
	}

	public function test_the_api_key_is_stored_outside_the_settings_array(): void {
		$settings  = new GroqSettings();
		$sanitized = $settings->sanitize_settings( array( 'api_key' => 'secret-key' ) );

		$this->assertArrayNotHasKey( 'api_key', $sanitized );
		$this->assertSame( 'secret-key', GroqSettings::get_saved_api_key() );
		$this->assertSame( 'secret-key', GroqSettings::get_api_key() );
	}

	public function test_an_empty_api_key_keeps_the_stored_one(): void {
		$settings = new GroqSettings();
		$settings->sanitize_settings( array( 'api_key' => 'secret-key' ) );
		$settings->sanitize_settings( array( 'api_key' => '' ) );

		$this->assertSame( 'secret-key', GroqSettings::get_saved_api_key() );
	}

	public function test_the_key_can_be_cleared_on_request(): void {
		$settings = new GroqSettings();
		$settings->sanitize_settings( array( 'api_key' => 'secret-key' ) );
		$settings->sanitize_settings( array( 'clear_api_key' => '1' ) );

		$this->assertSame( '', GroqSettings::get_saved_api_key() );
		$this->assertFalse( GroqSettings::has_credentials() );
	}

	public function test_an_environment_variable_overrides_the_stored_key(): void {
		$settings = new GroqSettings();
		$settings->sanitize_settings( array( 'api_key' => 'stored-key' ) );

		putenv( GroqProfile::api_key_constant() . '=environment-key' );
		$this->assertSame( 'environment-key', GroqSettings::get_api_key() );
		$this->assertSame( 'environment-key', GroqSettings::get_api_key_override() );

		putenv( GroqProfile::api_key_constant() );
		$this->assertSame( 'stored-key', GroqSettings::get_api_key() );
	}

	public function test_default_models_are_preserved_across_saves(): void {
		$settings  = new GroqSettings();
		$sanitized = $settings->sanitize_settings( array( 'model_text' => 'first-model' ) );
		$GLOBALS['zctz_test_options']['zctz_groq_settings'] = $sanitized;

		$this->assertSame( 'first-model', GroqSettings::get_preferred_model( 'text' ) );

		$sanitized = $settings->sanitize_settings( array( 'api_key' => 'secret-key' ) );
		$this->assertSame( 'first-model', $sanitized['model_text'] );
	}

	public function test_a_timeout_outside_the_allowed_range_is_rejected(): void {
		$settings  = new GroqSettings();
		$sanitized = $settings->sanitize_settings( array( 'request_timeout' => '4000' ) );

		$this->assertSame( '', $sanitized['request_timeout'] );
		$this->assertNotEmpty( $GLOBALS['zctz_test_settings_errors'] );

		$GLOBALS['zctz_test_options']['zctz_groq_settings'] = $sanitized;
		$this->assertSame( GroqProfile::default_timeout(), GroqSettings::get_text_request_timeout() );
	}

	public function test_a_valid_timeout_is_kept(): void {
		$settings  = new GroqSettings();
		$sanitized = $settings->sanitize_settings( array( 'request_timeout' => '240' ) );
		$GLOBALS['zctz_test_options']['zctz_groq_settings'] = $sanitized;

		$this->assertSame( 240.0, GroqSettings::get_text_request_timeout() );
	}

	public function test_an_unknown_reasoning_mode_falls_back_to_the_model_default(): void {
		$settings  = new GroqSettings();
		$sanitized = $settings->sanitize_settings( array( 'reasoning' => 'extreme' ) );

		$this->assertSame( 'default', $sanitized['reasoning'] );
	}

	public function test_the_base_url_gains_a_scheme_and_loses_a_trailing_slash(): void {
		$settings  = new GroqSettings();
		$sanitized = $settings->sanitize_settings( array( 'base_url' => 'proxy.example.test/v1/' ) );

		$this->assertSame( 'https://proxy.example.test/v1', $sanitized['base_url'] );
	}

	public function test_the_default_base_url_is_used_when_none_is_stored(): void {
		if ( '' === GroqProfile::default_base_url() ) {
			$this->markTestSkipped( 'This provider builds its base URL from its own connection fields.' );
		}

		$this->assertSame( GroqProfile::default_base_url(), GroqSettings::get_base_url() );
	}

	public function test_credentials_are_required_before_a_connection_check(): void {
		$result = GroqSettings::verify_connection();

		$this->assertTrue( is_wp_error( $result ) );
		$this->assertSame( 'missing_credentials', $result->get_error_code() );
	}

	public function test_a_rejected_credential_is_reported(): void {
		zctz_test_seed_settings();
		zctz_test_queue_http_response( array( 'error' => 'unauthorized' ), 401 );

		$result = GroqSettings::verify_connection();

		$this->assertTrue( is_wp_error( $result ) );
		$this->assertSame( 'invalid_credentials', $result->get_error_code() );
	}

	public function test_an_unreadable_model_list_is_reported(): void {
		zctz_test_seed_settings();
		zctz_test_queue_http_response( array( 'unexpected' => true ), 200 );

		$result = GroqSettings::verify_connection();

		$this->assertTrue( is_wp_error( $result ) );
		$this->assertSame( 'invalid_response', $result->get_error_code() );
	}

	public function test_a_valid_model_list_confirms_the_connection(): void {
		zctz_test_seed_settings();
		zctz_test_queue_http_response( json_decode( (string) file_get_contents( dirname( __DIR__ ) . '/fixtures/models.json' ), true ), 200 );

		$this->assertTrue( GroqSettings::verify_connection() );

		$request = end( $GLOBALS['zctz_test_http_requests'] );
		$this->assertSame( GroqSettings::get_models_url(), $request['url'] );
		$this->assertArrayHasKey( GroqProfile::auth_header(), $request['args']['headers'] );
	}

	public function test_the_api_key_lives_in_the_option_wordpress_registers_for_the_connector(): void {
		$expected = 'connectors_ai_' . str_replace( '-', '_', GroqProfile::id() ) . '_api_key';

		$this->assertSame( $expected, GroqSettings::api_key_option() );
	}

	public function test_a_key_entered_on_the_core_connectors_screen_is_used(): void {
		// Seeds whatever else this provider needs, so the assertion below is about
		// the key core handed to the AI Client and nothing else.
		zctz_test_seed_settings();
		$GLOBALS['zctz_test_options'][ GroqSettings::api_key_option() ] = 'key-from-core-screen';
		AiClient::defaultRegistry()->setProviderRequestAuthentication(
			GroqProfile::id(),
			new ApiKeyRequestAuthentication( 'key-from-core-screen' )
		);

		$this->assertSame( 'key-from-core-screen', GroqSettings::get_api_key() );
		$this->assertTrue( GroqSettings::has_credentials() );
	}

	public function test_the_key_is_read_through_the_ai_client_not_the_option(): void {
		zctz_test_seed_settings();
		$GLOBALS['zctz_test_options'][ GroqSettings::api_key_option() ] = 'only-in-the-option';
		zctz_test_reset_credentials();

		$this->assertSame( '', GroqSettings::get_saved_api_key() );
	}

	public function test_saving_on_the_connector_screen_writes_the_key_core_reads(): void {
		$settings = new GroqSettings();
		$settings->sanitize_settings( array( 'api_key' => 'key-from-plugin-screen' ) );

		$this->assertSame(
			'key-from-plugin-screen',
			$GLOBALS['zctz_test_options'][ GroqSettings::api_key_option() ]
		);
	}

	public function test_clearing_the_key_clears_the_option_core_reads(): void {
		$settings = new GroqSettings();
		$settings->sanitize_settings( array( 'api_key' => 'key-to-remove' ) );
		$settings->sanitize_settings( array( 'clear_api_key' => '1' ) );

		$this->assertSame( '', GroqSettings::get_api_key() );
		$this->assertFalse( GroqSettings::has_credentials() );
	}

	public function test_no_second_copy_of_the_key_is_stored(): void {
		$settings = new GroqSettings();
		$settings->sanitize_settings( array( 'api_key' => 'only-one-copy' ) );

		$holders = array();
		foreach ( $GLOBALS['zctz_test_options'] as $name => $value ) {
			if ( is_string( $value ) && 'only-one-copy' === $value ) {
				$holders[] = $name;
			}
		}

		$this->assertSame( array( GroqSettings::api_key_option() ), $holders );
	}

	public function test_the_screen_still_knows_a_key_is_stored_when_the_credential_is_unreadable(): void {
		$settings = new GroqSettings();

		// WordPress persists what the sanitize callback returns, so mirror that.
		$GLOBALS['zctz_test_options']['zctz_groq_settings'] = $settings->sanitize_settings( array( 'api_key' => 'stored-key' ) );

		$this->assertTrue( GroqSettings::has_stored_api_key() );

		// Stand in for a request where WordPress never handed the key to the AI
		// Client, which is what happens with AI support switched off.
		zctz_test_reset_credentials();

		$this->assertSame( '', GroqSettings::get_saved_api_key() );
		$this->assertTrue(
			GroqSettings::has_stored_api_key(),
			'The screen must still offer to remove a key that is stored.'
		);
	}

	public function test_clearing_the_key_clears_the_stored_flag(): void {
		$settings = new GroqSettings();
		$GLOBALS['zctz_test_options']['zctz_groq_settings'] = $settings->sanitize_settings( array( 'api_key' => 'stored-key' ) );
		$GLOBALS['zctz_test_options']['zctz_groq_settings'] = $settings->sanitize_settings( array( 'clear_api_key' => '1' ) );

		$this->assertFalse( GroqSettings::has_stored_api_key() );
	}

	public function test_a_save_that_does_not_touch_the_key_keeps_the_stored_flag(): void {
		$settings = new GroqSettings();
		$GLOBALS['zctz_test_options']['zctz_groq_settings'] = $settings->sanitize_settings( array( 'api_key' => 'stored-key' ) );

		$sanitized = $settings->sanitize_settings( array( 'request_timeout' => '60' ) );

		$this->assertTrue( $sanitized['has_api_key'] );
		$this->assertTrue( GroqSettings::has_stored_api_key() );
	}

	public function test_nothing_is_stored_before_a_key_is_entered(): void {
		$this->assertFalse( GroqSettings::has_stored_api_key() );
	}

	public function test_the_stored_flag_is_a_boolean_not_the_credential(): void {
		$settings  = new GroqSettings();
		$sanitized = $settings->sanitize_settings( array( 'api_key' => 'secret-value' ) );

		$this->assertIsBool( $sanitized['has_api_key'] );
		$this->assertNotContains( 'secret-value', $sanitized, 'The settings array must never carry the credential.' );
	}
}

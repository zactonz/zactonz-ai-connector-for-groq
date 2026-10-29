<?php
/**
 * Static description of the Groq API.
 *
 * Generated from providers/providers.php. Every value that differs between the
 * connectors in this family is held here, so the rest of the plugin is shared.
 *
 * @package Zactonz\AiConnectorForGroq\Provider
 */

declare( strict_types=1 );

namespace Zactonz\AiConnectorForGroq\Provider;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class holding the provider profile.
 *
 * @since 1.0.0
 */
class GroqProfile {

	/**
	 * Returns the raw profile data.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, mixed> Profile data.
	 */
	public static function data(): array {
		// phpcs:disable PluginCheck.CodeAnalysis.AIProvider.DirectIntegration -- Provider addresses registered with the WordPress AI Client, not a direct integration.
		return array(
			'releases'          => array(
				array(
					'version' => '1.0.2',
					'date'    => '2026-10-29',
					'notice'  => 'Fixes a reply arriving empty when the provider answers in content chunks, stops models meant for other tasks being offered as text models, and makes the reasoning effort selector agree with what the connector accepts.',
					'changes' => array(
						'Fixed a reply arriving empty when the provider answers with a list of content chunks rather than a single string, which the AI Client\'s parser does not read. The chunks were being dropped, so the request looked successful and returned nothing.',
						'Fixed models the provider serves for other tasks - transcription, speech, moderation and the like - being offered as text models. The task the provider declares is now believed, rather than guessed from the model\'s name.',
						'Fixed entries the provider marks as internal reaching the model picker. They answer the listing but are not products.',
						'Fixed the reasoning effort selector being able to offer a level the connector would not then accept. The list the selector is built from and the list that is accepted are now the same one, and are tested against each other.',
						'Changed the reasoning effort description, which said a model that always reasons ignores the setting. Which levels a model accepts is the provider\'s business, and a model may ignore or refuse the level chosen.',
						'Changed the base URL override wording, which did not say that WordPress restricts which hosts and ports an outbound request may reach.',
					),
				),
				array(
					'version' => '1.0.1',
					'date'    => '2026-10-05',
					'notice'  => 'Obtains the API key through the WordPress AI Client, and keeps the settings screen accurate on a site with AI support switched off.',
					'changes' => array(
						'Changed the saved API key to be read back from the WordPress AI Client rather than straight from the option WordPress registers for the connector, which is how a connector is meant to obtain its credentials.',
						'Fixed the settings screen reporting no saved API key, and hiding the control that removes it, on a site where AI support is switched off. The key is read back from the AI Client, which WordPress only populates while it is wiring connectors.',
						'Fixed the model-discovery answer in the FAQ, which described capability detection for embeddings this connector does not offer, and claimed capabilities are read from the provider rather than guessed. Where Groq publishes no per-model capability data the connector falls back to inference.',
					),
				),
				array(
					'version' => '1.0.0',
					'date'    => '2026-09-17',
					'notice'  => 'Initial release.',
					'changes' => array(
						'initial',
					),
				),
			),
			'name'              => 'Groq',
			'article'           => 'A',
			'class'             => 'Groq',
			'provider_id'       => 'groq',
			'menu_title'        => 'Groq',
			'const_prefix'      => 'ZCTZ_GROQ',
			'option_prefix'     => 'zctz_groq',
			'api_key_constant'  => 'GROQ_API_KEY',
			'homepage'          => 'https://groq.com/',
			'api_key_url'       => 'https://console.groq.com/keys',
			'docs_url'          => 'https://console.groq.com/docs',
			'default_base_url'  => 'https://api.groq.com/openai/v1',
			'auth'              => 'bearer',
			'auth_header'       => 'Authorization',
			'models_path'       => 'models',
			'short_description' => 'Adds a Groq connector to Settings > Connectors for the WordPress AI Client, for very low latency inference.',
			'description'       => 'Low latency text generation, vision, and tool calling on GroqCloud.',
			'summary'           => 'Groq runs open models on its own LPU hardware and is built around one thing: speed. This connector makes GroqCloud a first-class WordPress AI provider, discovering every model on your account together with its context window, and keeping the request timeout short because Groq rarely needs a long one.',
			'tags'              => 'connector, groq, ai, ai-client, llm',
			'reasoning_pattern' => 'gpt-oss|deepseek-r1|qwen3',
			'capabilities'      => array(
				'text'              => true,
				'vision'            => true,
				'tools'             => true,
				'structured'        => true,
				'structured_schema' => true,
				'embedding'         => false,
				'image'             => false,
				'reasoning'         => true,
			),
			'default_timeout'   => 120,
			'model_notes'       => 'Models are listed from your GroqCloud account.',
			'trademark'         => 'Groq and GroqCloud are trademarks of Groq, Inc.',
			'terms_url'         => 'https://groq.com/terms-of-use',
			'privacy_url'       => 'https://groq.com/privacy-policy',
			'service_note'      => '',
			'logo_color'        => '#F43E01',
			'logo_text'         => '#FFFFFF',
			'tagline'           => 'Very low latency inference on GroqCloud',
			'asset_color'       => '#F43E01',
			'asset_text'        => '#FFFFFF',
		);
		// phpcs:enable PluginCheck.CodeAnalysis.AIProvider.DirectIntegration
	}

	/**
	 * Returns one profile value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Profile key.
	 * @param mixed  $default_value Value returned when the key is absent.
	 * @return mixed Profile value.
	 */
	public static function get( string $key, $default_value = '' ) {
		$data = self::data();

		return array_key_exists( $key, $data ) ? $data[ $key ] : $default_value;
	}

	/**
	 * Returns the AI Client provider ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string Provider ID.
	 */
	public static function id(): string {
		return (string) self::get( 'provider_id' );
	}

	/**
	 * Returns the provider display name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Provider name.
	 */
	public static function name(): string {
		return (string) self::get( 'name' );
	}

	/**
	 * Returns the provider home page.
	 *
	 * @since 1.0.0
	 *
	 * @return string Home page URL.
	 */
	public static function homepage(): string {
		return (string) self::get( 'homepage' );
	}

	/**
	 * Returns the page where an API key is issued.
	 *
	 * @since 1.0.0
	 *
	 * @return string API key URL.
	 */
	public static function api_key_url(): string {
		return (string) self::get( 'api_key_url' );
	}

	/**
	 * Returns the provider documentation URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string Documentation URL.
	 */
	public static function docs_url(): string {
		return (string) self::get( 'docs_url' );
	}

	/**
	 * Returns the default API base URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string Base URL without a trailing slash.
	 */
	public static function default_base_url(): string {
		return rtrim( (string) self::get( 'default_base_url' ), '/' );
	}

	/**
	 * Returns the authentication scheme.
	 *
	 * @since 1.0.0
	 *
	 * @return string One of bearer, header, or sigv4.
	 */
	public static function auth_scheme(): string {
		return (string) self::get( 'auth', 'bearer' );
	}

	/**
	 * Returns the header carrying the credential.
	 *
	 * @since 1.0.0
	 *
	 * @return string Header name.
	 */
	public static function auth_header(): string {
		return (string) self::get( 'auth_header', 'Authorization' );
	}

	/**
	 * Returns the constant and environment variable used to override the API key.
	 *
	 * @since 1.0.0
	 *
	 * @return string Constant name.
	 */
	public static function api_key_constant(): string {
		return (string) self::get( 'api_key_constant' );
	}

	/**
	 * Returns the path of the model listing endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @return string Relative path.
	 */
	public static function models_path(): string {
		return (string) self::get( 'models_path', 'models' );
	}

	/**
	 * Returns the fallback model listing path, if the provider has one.
	 *
	 * @since 1.0.0
	 *
	 * @return string Relative path, or an empty string.
	 */
	public static function models_fallback_path(): string {
		return (string) self::get( 'models_fallback', '' );
	}

	/**
	 * Returns the default request timeout in seconds.
	 *
	 * @since 1.0.0
	 *
	 * @return float Timeout in seconds.
	 */
	public static function default_timeout(): float {
		$timeout = self::get( 'default_timeout', 180 );

		return is_numeric( $timeout ) ? (float) $timeout : 180.0;
	}

	/**
	 * Checks whether the provider can offer one capability at all.
	 *
	 * A true value only allows the capability. Whether an individual model
	 * advertises it is decided from the provider's own model listing.
	 *
	 * @since 1.0.0
	 *
	 * @param string $capability Capability key.
	 * @return bool True when the capability is possible for this provider.
	 */
	public static function supports( string $capability ): bool {
		$capabilities = self::get( 'capabilities', array() );

		return is_array( $capabilities ) && ! empty( $capabilities[ $capability ] );
	}

	/**
	 * Returns provider-specific headers sent with every request.
	 *
	 * @since 1.0.0
	 *
	 * @return array<string, string> Header map.
	 */
	public static function extra_headers(): array {
		$headers = self::get( 'extra_headers', array() );
		if ( ! is_array( $headers ) || empty( $headers ) ) {
			return array();
		}

		$replacements = array(
			'{site_url}'  => function_exists( 'home_url' ) ? (string) home_url() : '',
			'{site_name}' => function_exists( 'get_bloginfo' ) ? (string) get_bloginfo( 'name' ) : '',
		);

		$resolved = array();
		foreach ( $headers as $name => $value ) {
			$value = strtr( (string) $value, $replacements );
			if ( '' === $value ) {
				continue;
			}
			$resolved[ (string) $name ] = $value;
		}

		return $resolved;
	}

	/**
	 * Returns the admin note describing where models come from.
	 *
	 * @since 1.0.0
	 *
	 * @return string Admin note.
	 */
	public static function model_notes(): string {
		return (string) self::get( 'model_notes' );
	}
}

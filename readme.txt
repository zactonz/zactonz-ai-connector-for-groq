=== Zactonz AI Connector for Groq ===
Contributors:      zactonz
Tags:              connector, groq, ai, ai-client, llm
Requires at least: 7.0
Tested up to:      7.1
Stable tag:        1.0.2
Requires PHP:      7.4
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Adds a Groq connector to Settings > Connectors for the WordPress AI Client, for very low latency inference.

== Description ==

This plugin by [Zactonz Technologies](https://zactonz.com/) adds [Groq](https://groq.com/) as an AI provider for the WordPress AI Client. It is built for the WordPress **Settings > Connectors** screen and for the plugin directory connector search, so a site owner who needs Groq can find it, install it, paste a key, and have every AI feature in WordPress start using it.

Groq runs open models on its own LPU hardware and is built around one thing: speed. This connector makes GroqCloud a first-class WordPress AI provider, discovering every model on your account together with its context window, and keeping the request timeout short because Groq rarely needs a long one.

**Disclaimer:** this connector is developed by Zactonz Technologies and is not affiliated with, endorsed by, or sponsored by Groq. Groq and GroqCloud are trademarks of Groq, Inc.

**Features:**

* Text generation with every Groq chat model your credentials can reach
* Automatic model discovery, taking each model's capabilities from the provider's own catalogue wherever it publishes them
* Separate default model per capability, or automatic selection by the WordPress AI Client
* Vision input on models that accept images
* Tool calling on models that report function support
* Structured output with a JSON schema on models that support it
* Reasoning effort control for the default text model
* Cancellable server-sent event streaming for content, reasoning, and tool-call chunks
* A configurable request timeout for text generation
* API key supplied by a PHP constant or an environment variable instead of the database
* An API base URL override for a proxy or gateway, within the hosts and ports WordPress permits outbound requests to
* Redacted diagnostics and a WordPress Site Health connectivity test

**Requirements:**

* PHP 7.4 or higher
* WordPress 7.0 or higher
* A Groq account and API key

== External services ==

This plugin sends requests to Groq, a third-party service, and does nothing without it. Requests go to the Groq API at `https://api.groq.com/openai/v1`, or to the API base URL you set in the connector settings.

**What is sent and when:** the API key you configure, and the prompts, images, tool definitions and generation settings that a WordPress AI feature passes to the AI Client, each time such a feature runs a request through this connector. The connector also requests the model catalogue when its settings screen loads, when a WordPress AI feature asks which models are available, and when you run diagnostics or the Site Health test. No other data is sent.

**Service provider:** Groq ([terms of service](https://groq.com/terms-of-use), [privacy policy](https://groq.com/privacy-policy)).

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/zactonz-ai-connector-for-groq/`, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Settings > Connectors** and add your Groq API key.
4. Go to **Settings > Groq** to review the discovered models and choose a default model per capability.

== Frequently Asked Questions ==

= Where do I get an API key? =

Create one on the Groq dashboard: https://console.groq.com/keys

= Can I keep the key out of the database? =

Yes. Define `GROQ_API_KEY` in `wp-config.php`, or set an environment variable of the same name. When either is present the connector uses it, the settings field is disabled, and nothing is written to the database.

= Which models will I see? =

Models are listed from your GroqCloud account. Each model is offered as vision, tool-calling or structured-output capable according to what Groq publishes about it, and the connector falls back to the model identifier only where Groq publishes nothing.

= Does this replace the connectors in WordPress core? =

No. WordPress core ships its own connectors. This plugin adds Groq alongside them, and you can keep several connectors active at once and pick a default model per capability.

= Is my key ever displayed or logged? =

No. The key is stored in its own option, is never rendered back into the settings screen, and is excluded from the diagnostics report and from Site Health debug information.

== Changelog ==

= 1.0.2 =

* Fixed a reply arriving empty when the provider answers with a list of content chunks rather than a single string, which the AI Client's parser does not read. The chunks were being dropped, so the request looked successful and returned nothing.
* Fixed models the provider serves for other tasks - transcription, speech, moderation and the like - being offered as text models. The task the provider declares is now believed, rather than guessed from the model's name.
* Fixed entries the provider marks as internal reaching the model picker. They answer the listing but are not products.
* Fixed the reasoning effort selector being able to offer a level the connector would not then accept. The list the selector is built from and the list that is accepted are now the same one, and are tested against each other.
* Changed the reasoning effort description, which said a model that always reasons ignores the setting. Which levels a model accepts is the provider's business, and a model may ignore or refuse the level chosen.
* Changed the base URL override wording, which did not say that WordPress restricts which hosts and ports an outbound request may reach.

= 1.0.1 =

* Changed the saved API key to be read back from the WordPress AI Client rather than straight from the option WordPress registers for the connector, which is how a connector is meant to obtain its credentials.
* Fixed the settings screen reporting no saved API key, and hiding the control that removes it, on a site where AI support is switched off. The key is read back from the AI Client, which WordPress only populates while it is wiring connectors.
* Fixed the model-discovery answer in the FAQ, which described capability detection for embeddings this connector does not offer, and claimed capabilities are read from the provider rather than guessed. Where Groq publishes no per-model capability data the connector falls back to inference.

= 1.0.0 =

* Initial release.
* Registers Groq with the WordPress AI Client and the Settings > Connectors screen.
* Discovers models from the provider and advertises only the capabilities the provider reports.
* Adds per-capability default model selection, reasoning effort, a configurable timeout, and an API base URL override.
* Streams text responses token by token through the WordPress HTTP API, aggregating content, reasoning, tool calls, and usage.
* Adds a redacted diagnostics report and a WordPress Site Health connectivity test.
* Supports an API key supplied by a PHP constant or an environment variable.

== Upgrade Notice ==

= 1.0.2 =

Fixes a reply arriving empty when the provider answers in content chunks, stops models meant for other tasks being offered as text models, and makes the reasoning effort selector agree with what the connector accepts.

= 1.0.1 =

Obtains the API key through the WordPress AI Client, and keeps the settings screen accurate on a site with AI support switched off.

= 1.0.0 =

Initial release.

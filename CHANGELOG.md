# Changelog

## 1.0.2 - 2026-10-29

- Fixed a reply arriving empty when the provider answers with a list of content chunks rather than a single string, which the AI Client's parser does not read. The chunks were being dropped, so the request looked successful and returned nothing.
- Fixed models the provider serves for other tasks - transcription, speech, moderation and the like - being offered as text models. The task the provider declares is now believed, rather than guessed from the model's name.
- Fixed entries the provider marks as internal reaching the model picker. They answer the listing but are not products.
- Fixed the reasoning effort selector being able to offer a level the connector would not then accept. The list the selector is built from and the list that is accepted are now the same one, and are tested against each other.
- Changed the reasoning effort description, which said a model that always reasons ignores the setting. Which levels a model accepts is the provider's business, and a model may ignore or refuse the level chosen.
- Changed the base URL override wording, which did not say that WordPress restricts which hosts and ports an outbound request may reach.

## 1.0.1 - 2026-10-05

- Changed the saved API key to be read back from the WordPress AI Client rather than straight from the option WordPress registers for the connector, which is how a connector is meant to obtain its credentials.
- Fixed the settings screen reporting no saved API key, and hiding the control that removes it, on a site where AI support is switched off. The key is read back from the AI Client, which WordPress only populates while it is wiring connectors.
- Fixed the model-discovery answer in the FAQ, which described capability detection for embeddings this connector does not offer, and claimed capabilities are read from the provider rather than guessed. Where Groq publishes no per-model capability data the connector falls back to inference.

## 1.0.0 - 2026-09-17

- Initial release.
- Registers Groq with the WordPress AI Client and the Settings > Connectors screen.
- Discovers models from the provider and advertises only the capabilities the provider reports.
- Adds per-capability default model selection, reasoning effort, a configurable timeout, and an API base URL override.
- Streams text responses token by token through the WordPress HTTP API, aggregating content, reasoning, tool calls, and usage.
- Adds a redacted diagnostics report and a WordPress Site Health connectivity test.
- Supports an API key supplied by a PHP constant or an environment variable.

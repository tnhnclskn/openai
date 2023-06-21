# OpenAI API Advanced for Chat Completions with Functions

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/tnhnclskn/openai/blob/main/LICENSE)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/tnhnclskn/openai.svg?style=flat-square)](https://packagist.org/packages/tnhnclskn/openai)
[![Total Downloads](https://img.shields.io/packagist/dt/tnhnclskn/openai.svg?style=flat-square)](https://packagist.org/packages/tnhnclskn/openai)

Tnhnclskn/OpenAI is a library that provides advanced chat completions with functions for OpenAI API.

## Installation

You can install the library using Composer. Run the following command in your project's root directory:

```bash
$ composer require tnhnclskn/openai
```

## Usage with functions

```php
<?php

use Tnhnclskn\OpenAI\OpenAI;
use Tnhnclskn\OpenAI\Chat\BaseFunction;

require_once __DIR__ . '/vendor/autoload.php';

class GetCurrentWeather extends BaseFunction
{
    public static function name(): string
    {
        return 'get_current_weather';
    }

    public static function description(): ?string
    {
        return 'Get the current weather in a given location';
    }

    public static function parameters(): array
    {
        return [
            static::parameter('location', 'string', 'The city and state, e.g. San Francisco, CA')->required(),
            static::parameter('unit', 'string')->enum(['celcius', 'fahrenheit']),
        ];
    }

    public function __construct(
        private string $location,
        private string $unit = 'farhenheit',
    ) {
    }

    public function handle(): string
    {
        return $this->json([
            "location" => $this->location,
            "temperature" => "72",
            "unit" => $this->unit,
            "forecast" => ["sunny", "windy"],
        ]);
    }
}

$client = new OpenAI($organizationId, $secretKey);
$chat = $client->chat('You are a helpful assistant.');

$chat->loadFunction(GetCurrentWeather::class);
$reply = $chat->prompt('What\'s the weather like in Boston?');
// Reply: The weather in Boston is currently 72°F and sunny with some winds.
```

## Usage only prompt

```php
<?php

use Tnhnclskn\OpenAI\OpenAI;

require_once __DIR__ . '/vendor/autoload.php';

$client = new OpenAI($organizationId, $secretKey);
$chat = $client->chat('You are a helpful assistant.');

$reply = $chat->prompt('What\'s the yellow flower?');
// Reply: The yellow flower is a common name that could refer to various types of flowers. Some examples include sunflowers, daffodils, marigolds, dandelions, or buttercups. Can you provide any additional information or description about the flower you are referring to?
```

## Export messages for continue conversation

```php
// Export messages
$messages = $chat->exportMessages();

// Import messages for continue conversation
$chat->importMessages($messages);
```

## License

This project is licensed under the MIT License. For more information, see the [LICENSE](LICENSE) file.

## Contributing

If you want to contribute to this project, please read the [CONTRIBUTING](CONTRIBUTING.md) file.

## Support

If you have any questions or need support, please use the [GitHub Issues](https://github.com/tnhnclskn/openai/issues) section.

## Acknowledgements

This project was created with contributions from the following individuals:

- [Tunahan Caliskan](https://github.com/tnhnclskn)

## Contact

Email: mail@tunahancaliskan.com

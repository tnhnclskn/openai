<?php

use Tnhnclskn\OpenAI\OpenAI;
use Tnhnclskn\OpenAI\Chat\BaseFunction;

require_once __DIR__ . '/../vendor/autoload.php';

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

$organizationId = 'org-1234';
$secretKey = 'sk-xxxx';

$client = new OpenAI($organizationId, $secretKey);
$chat = $client->chat('You are a helpful assistant.');
$chat->loadFunction(GetCurrentWeather::class);

$reply = $chat->prompt('What\'s the weather like in Boston?');
// Reply: The weather in Boston is currently 72°F and sunny with some winds.

echo $reply . PHP_EOL;

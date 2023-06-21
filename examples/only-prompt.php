<?php

use Tnhnclskn\OpenAI\OpenAI;

require_once __DIR__ . '/../vendor/autoload.php';

$organizationId = 'org-1234';
$secretKey = 'sk-xxxx';

$client = new OpenAI($organizationId, $secretKey);
$chat = $client->chat('You are a helpful assistant.');

$reply = $chat->prompt('What\'s the yellow flower?');
// Reply: The yellow flower is a common name that could refer to various types of flowers. Some examples include sunflowers, daffodils, marigolds, dandelions, or buttercups. Can you provide any additional information or description about the flower you are referring to?

echo $reply . PHP_EOL;

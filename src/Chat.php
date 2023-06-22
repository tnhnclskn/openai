<?php

namespace Tnhnclskn\OpenAI;

use Tnhnclskn\OpenAI\Chat\FunctionNotFoundException;

class Chat
{
    const ROLE_SYSTEM = 'system';
    const ROLE_USER = 'user';
    const ROLE_ASSISTANT = 'assistant';
    const ROLE_FUNCTION = 'function';

    private array $messages = [];
    private array $functions = [];
    private string $model;
    private $onMessage = null;

    public function __construct(
        private OpenAI $openAI,
        private string $systemMessage = '',
        array $functions = []
    ) {
        $this->model = $openAI->config('chat_model');
        $this->loadFunctions($functions);
    }

    public function onMessage(callable $onMessage): self
    {
        $this->onMessage = $onMessage;
        return $this;
    }

    private function message(array $message): self
    {
        if ($this->onMessage) ($this->onMessage)($message);
        $this->messages[] = $message;
        return $this;
    }

    private function messages(): array
    {
        $messages = [];

        if ($this->systemMessage) $messages[] = [
            'role' => self::ROLE_SYSTEM,
            'content' => $this->systemMessage,
        ];

        $messages = array_merge($messages, $this->messages);

        return $messages;
    }

    public function importMessages(array $messages): self
    {
        $this->messages = array_merge($this->messages, $messages);
        return $this;
    }

    public function exportMessages(): array
    {
        return $this->messages;
    }

    public function loadFunctions(array $functions): self
    {
        foreach ($functions as $function)
            $this->loadFunction($function);

        return $this;
    }

    public function loadFunction(string $function): self
    {
        $this->functions[$function::name()] = $function;
        return $this;
    }

    public function prompt(string $content): string
    {
        $this->message([
            'role' => self::ROLE_USER,
            'content' => $content
        ]);

        return $this->request();
    }

    private function request(): string
    {
        $data = [
            'model' => $this->model,
            'messages' => $this->messages(),
        ];

        if ($functions = $this->toFunctions())
            $data['functions'] = $functions;

        $response = $this->openAI->post('/v1/chat/completions', $data);

        $message = $response['choices'][0]['message'];
        return $this->handleMessage($message);
    }

    private function handleMessage(array $message): string
    {
        $this->message($message);

        if (isset($message['function_call']))
            return $this->handleFunction($message['function_call']);

        return $message['content'];
    }

    private function handleFunction(array $functionCall): string
    {
        $functionName = $functionCall['name'];
        $arguments = json_decode($functionCall['arguments'], true);

        if (!isset($this->functions[$functionName]))
            throw new FunctionNotFoundException($functionName);

        $functionClass = $this->functions[$functionName];
        $function = new $functionClass(...$arguments);
        $response = $function->handle();

        $this->message([
            'role' => self::ROLE_FUNCTION,
            'name' => $functionName,
            'content' => $response,
        ]);

        return $this->request();
    }

    private function toFunctions(): array
    {
        return array_values(array_map(function ($function) {
            return $function::toFunction();
        }, $this->functions));
    }
}

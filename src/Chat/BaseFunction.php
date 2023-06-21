<?php

namespace Tnhnclskn\OpenAI\Chat;

abstract class BaseFunction
{
    abstract public static function name(): string;
    abstract public static function description(): ?string;
    abstract public static function parameters(): array;
    abstract public function handle(): string;

    public static function toFunction(): array
    {
        return array_filter([
            'name' => static::name(),
            'description' => static::description(),
            'parameters' => static::toParameters(),
        ], fn ($value) => $value !== null);
    }

    private static function toProperties(): object
    {
        $properties = [];
        foreach (static::parameters() as $parameter) {
            $properties[$parameter->name] = $parameter->toArray();
        }
        return (object) $properties;
    }

    private static function toRequired(): array
    {
        $properties = [];
        foreach (static::parameters() as $parameter) {
            if ($parameter->required)
                $properties[] = $parameter->name;
        }
        return $properties;
    }

    private static function toParameters(): array
    {
        $parameters = static::parameters();
        return [
            'type' => 'object',
            'properties' => static::toProperties(),
            'required' => static::toRequired(),
        ];
    }

    protected static function parameter(string $name, string $type, ?string $description = null, bool $required = false): Parameter
    {
        return new Parameter($name, $type, $description, $required);
    }

    protected function json(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}

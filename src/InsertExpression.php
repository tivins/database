<?php

namespace Tivins\Database;

class InsertExpression
{
    private array $parameters;

    public function __construct(
        private string $expression,
        mixed ...$parameters
    )
    {
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

}
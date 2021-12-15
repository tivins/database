<?php

namespace Tivins\Database\Tests\data;

enum Fruits: string
{
    case Apple  = 'apple';
    case Banana = 'banana';
    case Peach  = 'peach';

    public function toString(): string
    {
        return $this->value;
    }
}
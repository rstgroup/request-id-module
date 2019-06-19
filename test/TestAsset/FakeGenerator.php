<?php

namespace RstGroup\RequestIdModule\Test\TestAsset;

use PhpMiddleware\RequestId\Generator\GeneratorInterface;

final class FakeGenerator implements GeneratorInterface
{
    public function generateRequestId(): string
    {
        return 'abc123';
    }
}
<?php

namespace RstGroup\RequestIdModule\Test\TestAsset;

use PhpMiddleware\RequestId\Generator\GeneratorInterface;

class FakeGenerator implements GeneratorInterface
{
    public function generateRequestId()
    {
        return 'abc123';
    }
}
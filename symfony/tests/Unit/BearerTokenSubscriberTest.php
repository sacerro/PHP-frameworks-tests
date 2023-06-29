<?php

namespace App\Tests\Unit;

use App\EventSubscriber\BearerTokenSubscriber;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class BearerTokenSubscriberTest extends TestCase
{
    public function caseDataProvider(): array
    {
        return [
            ['', false],
            ['{}', true],
            ['{}[]()', true],
            ['{)', false],
            ['[{]}', false],
            ['{([])}', true],
            ['(((((((()', false],
        ];
    }

    /**
     * @param string $bearer
     * @param bool   $expected
     *
     * @return void
     * @dataProvider caseDataProvider
     * @throws \ReflectionException
     */
    public function testBearerTokenValidator(string $bearer, bool $expected): void
    {
        $subscriber = new BearerTokenSubscriber();
        $reflection = new ReflectionClass($subscriber);
        $method = $reflection->getMethod('validateToken');
        $result = $method->invoke($subscriber, $bearer);

        $this->assertEquals($expected, $result);
    }
}

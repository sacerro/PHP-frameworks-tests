<?php

namespace Tests\Unit;

use App\Http\Middleware\BearerValidation as BearerMiddleware;
use ReflectionClass;
use Tests\TestCase;
use ReflectionException;

class BearerValidationUnitTest extends TestCase
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
     * @param bool   $expectedResult
     *
     * @return void
     * @throws ReflectionException
     * @dataProvider caseDataProvider
     */
    public function testBearerTokenValidator(string $bearer, bool $expectedResult): void
    {
        $bearerValidationMiddleware = new BearerMiddleware();
        $reflection = new ReflectionClass($bearerValidationMiddleware);
        $method = $reflection->getMethod('validateToken');
        $result = $method->invoke($bearerValidationMiddleware, $bearer);
        $this->assertEquals($expectedResult, $result, 'Unexpected result.');
    }
}

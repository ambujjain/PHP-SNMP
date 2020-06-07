<?php

declare(strict_types=1);

namespace SimPod\PhpSnmp\Tests\Transport;

use Exception;
use Http\Client\Curl\Client;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use SimPod\PhpSnmp\Exception\EndOfMibReached;
use SimPod\PhpSnmp\Exception\GeneralException;
use SimPod\PhpSnmp\Exception\NoSuchInstanceExists;
use SimPod\PhpSnmp\Exception\NoSuchObjectExists;
use SimPod\PhpSnmp\Transport\ApiSnmpClient;
use function Safe\json_decode;
use function Safe\json_encode;
use const JSON_BIGINT_AS_STRING;

final class ApiSnmpClientTest extends TestCase
{
    /** @var Client|MockObject */
    private $client;

    private static function jsonIsIdentical(string $expected, string $actual) : bool
    {
        return json_encode(json_decode($expected, true, 4, JSON_BIGINT_AS_STRING)) === $actual;
    }

    public function testGet() : void
    {
        $apiSnmp = $this->createApiSnmp();

        $response = <<<JSON
{
    "result": [
        ".1.3.6.1.2.1.25.2.3.1.2.1",
        ".1.3.6.1.2.1.25.2.1.2",
        ".1.3.6.1.2.1.25.2.3.1.2.4",
        ".1.3.6.1.2.1.25.2.1.9"
    ]
}
JSON;
        $this->client->method('sendRequest')
            ->with(
                self::callback(
                    static function (RequestInterface $request) : bool {
                        $json = <<<JSON
{
    "request_type": "get",
    "host": "127.0.0.1",
    "community": "public",
    "oids": [
        ".1.3.6.1.2.1.25.2.3.1.2.1",
        ".1.3.6.1.2.1.25.2.3.1.2.4"
    ],
    "version": "2c",
    "timeout": 1,
    "retries": 3
}
JSON;

                        return (string) $request->getUri() === 'http://localhost/snmp-proxy'
                            && self::jsonIsIdentical($json, (string) $request->getBody());
                    }
                )
            )
            ->willReturn($this->createResponse($response));

        $result = $apiSnmp->get(['.1.3.6.1.2.1.25.2.3.1.2.1', '.1.3.6.1.2.1.25.2.3.1.2.4']);

        self::assertSame(
            [
                '.1.3.6.1.2.1.25.2.3.1.2.1' => '.1.3.6.1.2.1.25.2.1.2',
                '.1.3.6.1.2.1.25.2.3.1.2.4' => '.1.3.6.1.2.1.25.2.1.9',
            ],
            $result
        );
    }

    public function testGetNext() : void
    {
        $apiSnmp = $this->createApiSnmp();

        $response = <<<JSON
{
    "result": [
        ".1.3.6.1.2.1.25.2.3.1.2.1",
        ".1.3.6.1.2.1.25.2.1.2",
        ".1.3.6.1.2.1.25.2.3.1.2.4",
        ".1.3.6.1.2.1.25.2.1.9"
    ]
}
JSON;
        $this->client->method('sendRequest')
            ->with(
                self::callback(
                    static function (RequestInterface $request) : bool {
                        $json = <<<JSON
{
    "request_type": "getNext",
    "host": "127.0.0.1",
    "community": "public",
    "oids": [
        ".1.3.6.1.2.1.25.2.3.1.2",
        ".1.3.6.1.2.1.25.2.3.1.2.3"
    ],
    "version": "2c",
    "timeout": 1,
    "retries": 3
}
JSON;

                        return (string) $request->getUri() === 'http://localhost/snmp-proxy'
                            && self::jsonIsIdentical($json, (string) $request->getBody());
                    }
                )
            )
            ->willReturn($this->createResponse($response));

        $result = $apiSnmp->getNext(['.1.3.6.1.2.1.25.2.3.1.2', '.1.3.6.1.2.1.25.2.3.1.2.3']);

        self::assertSame(
            [
                '.1.3.6.1.2.1.25.2.3.1.2.1' => '.1.3.6.1.2.1.25.2.1.2',
                '.1.3.6.1.2.1.25.2.3.1.2.4' => '.1.3.6.1.2.1.25.2.1.9',
            ],
            $result
        );
    }

    public function testWalk() : void
    {
        $apiSnmp = $this->createApiSnmp();

        $response = <<<JSON
{
    "result": [
        ".1.3.6.1.2.1.31.1.1.1.15.1000001",
        100000,
        ".1.3.6.1.2.1.31.1.1.1.15.1000003",
        60000,
        ".1.3.6.1.2.1.31.1.1.1.15.1000005",
        80000
    ]
}
JSON;
        $this->client->method('sendRequest')
            ->with(
                self::callback(
                    static function (RequestInterface $request) : bool {
                        $json = <<<JSON
{
    "request_type": "walk",
    "host": "127.0.0.1",
    "community": "public",
    "oids": [".1.3.6.1.2.1.31.1.1.1.15"],
    "version": "2c",
    "timeout": 1,
    "retries": 3
}
JSON;

                        return (string) $request->getUri() === 'http://localhost/snmp-proxy'
                            && self::jsonIsIdentical($json, (string) $request->getBody());
                    }
                )
            )
            ->willReturn($this->createResponse($response));

        $result = $apiSnmp->walk('.1.3.6.1.2.1.31.1.1.1.15');

        self::assertSame(
            [
                '.1.3.6.1.2.1.31.1.1.1.15.1000001' => 100000,
                '.1.3.6.1.2.1.31.1.1.1.15.1000003' => 60000,
                '.1.3.6.1.2.1.31.1.1.1.15.1000005' => 80000,
            ],
            $result
        );
    }

    public function testThatParametersAreCorrectlyPropagatedToTheJsonRequest() : void
    {
        $this->client = $this->createMock(Client::class);
        $psr71Factory = new Psr17Factory();

        $apiSnmp = new ApiSnmpClient(
            $this->client,
            $psr71Factory,
            $psr71Factory,
            'http://somewhere',
            'lorem',
            'ipsum',
            50,
            5,
            '1'
        );

        $response = <<<JSON
{
    "result": [
        ".1.3.6.1.2.1.2.2.1.2.1000009",
        "Port-Channel9"
    ]
}
JSON;
        $this->client->method('sendRequest')
            ->with(
                self::callback(
                    static function (RequestInterface $request) : bool {
                        $json = <<<JSON
{
    "request_type": "get",
    "host": "lorem",
    "community": "ipsum",
    "oids": [".1.3.6.1.2.1.2.2.1.2.1000009"],
    "version": "1",
    "timeout": 50,
    "retries": 5
}
JSON;

                        return (string) $request->getUri() === 'http://somewhere/snmp-proxy'
                            && self::jsonIsIdentical($json, (string) $request->getBody());
                    }
                )
            )
            ->willReturn($this->createResponse($response));

        $result = $apiSnmp->get(['.1.3.6.1.2.1.2.2.1.2.1000009']);

        self::assertSame(['.1.3.6.1.2.1.2.2.1.2.1000009' => 'Port-Channel9'], $result);
    }

    public function testWalkWithEndOfMibError() : void
    {
        $apiSnmp = $this->createApiSnmp();

        $this->client->method('sendRequest')
            ->willReturn($this->createResponse('{"error": "end of mib: .1.15"}'));

        $this->expectException(EndOfMibReached::class);
        $this->expectExceptionMessage(
            'No more variables left in this MIB View (It is past the end of the MIB tree), tried oid: .1.15'
        );

        $apiSnmp->walk('.1.15');
    }

    public function testWalkWithNoSuchInstanceError() : void
    {
        $apiSnmp = $this->createApiSnmp();

        $this->client->method('sendRequest')
            ->willReturn($this->createResponse('{"error": "no such instance: .1.3.6.1.2.1.1.1"}'));

        $this->expectException(NoSuchInstanceExists::class);
        $this->expectExceptionMessage('No Such Instance currently exists at this OID: .1.3.6.1.2.1.1.1');

        $apiSnmp->walk('.1.3.6.1.2.1.1.1');
    }

    public function testWalkWithNoSuchObjectError() : void
    {
        $apiSnmp = $this->createApiSnmp();

        $this->client->method('sendRequest')
            ->willReturn($this->createResponse('{"error": "no such object: .1.4"}'));

        $this->expectException(NoSuchObjectExists::class);
        $this->expectExceptionMessage('No Such Object available on this agent at this OID: .1.4');

        $apiSnmp->walk('.1.4');
    }

    public function testWalkWithRequestError() : void
    {
        $apiSnmp = $this->createApiSnmp();

        $this->client->method('sendRequest')
            ->willThrowException(new Exception('some error'));

        $this->expectException(GeneralException::class);
        $this->expectExceptionMessage('Unexpected error: some error');

        $apiSnmp->walk('.1.4');
    }

    public function testWalkWithUnexpectedError() : void
    {
        $apiSnmp = $this->createApiSnmp();

        $this->client->method('sendRequest')
            ->willReturn($this->createResponse('{"error": "something unexpected happened"}'));

        $this->expectException(GeneralException::class);
        $this->expectExceptionMessage('Unexpected error: something unexpected happened');

        $apiSnmp->walk('.1.4');
    }

    private function createApiSnmp() : ApiSnmpClient
    {
        $this->client = $this->createMock(Client::class);
        $psr71Factory = new Psr17Factory();

        return new ApiSnmpClient($this->client, $psr71Factory, $psr71Factory, 'http://localhost');
    }

    private function createResponse(string $body) : ResponseInterface
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn($body);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);

        return $response;
    }
}
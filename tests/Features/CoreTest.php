<?php

namespace Crypt4\Jantung\Tests\Features;

use Crypt4\Jantung\Exceptions\TransporterException;
use Crypt4\Jantung\Tests\TestCase;
use Crypt4\Jantung\Transporter\Http;
use Crypt4\Jantung\Transporter\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class CoreTest extends TestCase
{
    /**
     * Test Log Transporter.
     */
    public function test_default_log_transporter(): void
    {
        $transporter = (new Log());
        $transporter->configure();

        $this->assertTrue($transporter->test());

        $this->assertTrue($transporter->verify());

        unlink($transporter->getFilePath());
    }

    /**
     * Test Custom Path of Log Transporter.
     */
    public function test_custom_log_transporter(): void
    {
        $transporter = (new Log());
        $transporter->configure([
            'path' => dirname(__FILE__, 2).DIRECTORY_SEPARATOR.'logs',
        ]);

        $this->assertTrue($transporter->test());

        $this->assertTrue($transporter->verify());

        unlink($transporter->getFilePath());
        rmdir($transporter->getPath());
    }

    /**
     * Test Http Transporter.
     */
    public function test_http_transporter_exceptions(): void
    {
        $this->expectException(TransporterException::class);
        $this->expectExceptionMessage('Missing API Token');

        $transporter = new Http();
        $transporter->configure();

        $this->expectException(TransporterException::class);
        $this->expectExceptionMessage('Missing Application Token');

        $transporter->configure([
            'key' => 'unittest-key',
        ]);
    }

    /**
     * Test Http Transporter.
     */
    public function test_http_transporter(): void
    {
        $headers = [
            'Accept' => 'application/vnd.jantung.'.Http::VERSION.'+json',
            'Authorization' => 'Bearer unittest-key',
            'Jantung-Token' => 'unittest-token',
            'Jantung-Transporter-Id' => '07f44616ac3c5812d914d8ea537b0df70abd69205cc278019547e27bddabf3e1',
            'Content-Type' => 'application/json',
        ];

        $mock = new MockHandler([
            new Response(200, $headers),
            new Response(200, $headers),
            new Response(200, $headers),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client([
            'handler' => $handler,
            'headers' => $headers,
        ]);

        $transporter = new Http();
        $transporter->configure([
            'key' => 'unittest-key',
            'token' => 'unittest-token',
        ]);
        $transporter->setClient($client);

        $this->assertTrue($transporter->test());
        $this->assertTrue($transporter->verify());
        $this->assertTrue($transporter->send([
            'type' => 'Query',
        ])->getStatusCode() == 200);
    }
}

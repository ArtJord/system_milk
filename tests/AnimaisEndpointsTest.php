<?php
use PHPUnit\Framework\TestCase;

final class AnimaisEndpointsTest extends TestCase
{
    private string $base;
    private $ctx;

    protected function setUp(): void
    {
        $this->base = getenv('BASE_URL') ?: 'http://localhost:8001';
        $this->ctx = stream_context_create(['http' => [
            'method' => 'GET',
            'header' => 'Authorization: Bearer '.(getenv('TOKEN')?:'')
        ]]);
    }

    public function testListarVacasRetornaArray()
    {
        $json = file_get_contents($this->base.'/vacas', false, $this->ctx);
        $this->assertNotFalse($json);
        $data = json_decode($json, true);
        $this->assertIsArray($data);
    }
}

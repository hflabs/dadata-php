<?php

namespace Dadata;

final class DadataTest extends \PHPUnit\Framework\TestCase
{
    public function testSecretIsOptional()
    {
        $api = new DadataClient("token");
        $this->assertInstanceOf(DadataClient::class, $api);
    }
}

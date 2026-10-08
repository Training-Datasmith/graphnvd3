<?php

class ModuleMetadataTest extends GraphNvD3TestCase
{
    public function testConfigXmlMatchesConstructorIdentity()
    {
        $this->resetStubs();
        $module = new GraphNvD3();
        $config = $this->parseConfigXmlValues(dirname(dirname(__DIR__)) . '/config.xml');
        $this->assertArrayHasKeys($config, ['name', 'author', 'tab', 'need_instance', 'displayName', 'version']);
        $this->assertSame('graphnvd3', $config['name']);
        $this->assertSame('PrestaShop', $config['author']);
        $this->assertSame('administration', $config['tab']);
        $this->assertSame('0', $config['need_instance']);
        $this->assertSame('NVD3 Charts', $config['displayName']);
        $this->assertSame($module->version, $config['version']);
    }
}

<?php

class GraphNvD3ConstructorTest extends GraphNvD3TestCase
{
    public function testModuleModeSetsMetadata()
    {
        $this->resetStubs();
        $module = new GraphNvD3();
        $this->assertSame(1, Module::$constructCallCount);
        $this->assertSame('graphnvd3', $module->name);
        $this->assertSame('administration', $module->tab);
        $this->assertSame('2.0.3', $module->version);
        $this->assertSame('PrestaShop', $module->author);
        $this->assertSame(0, $module->need_instance);
        $this->assertSame('NVD3 Charts', $module->displayName);
        $this->assertSame(
            'Enable the NVD3 charting code for your own uses, providing you with ever so useful graphs.',
            $module->description
        );
        $this->assertSame('1.7.1.0', $module->ps_versions_compliancy['min']);
        $this->assertSame(_PS_VERSION_, $module->ps_versions_compliancy['max']);
        $this->assertCount(2, Module::$transCalls);
        $this->assertSame('Modules.Graphnvd3.Admin', Module::$transCalls[0]['domain']);
        $this->assertSame('Modules.Graphnvd3.Admin', Module::$transCalls[1]['domain']);
    }

    public function testDrawerModeOnlyForwardsType()
    {
        $this->resetStubs();
        $drawer = new GraphNvD3('pie');
        $this->assertSame(0, Module::$constructCallCount);
        $this->assertSame('pie', $drawer->getType());
    }
}

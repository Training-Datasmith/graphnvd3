<?php

class GraphNvD3InstallTest extends GraphNvD3TestCase
{
    public function testInstallRegistersBothHooks()
    {
        $this->resetStubs();
        $module = new GraphNvD3();
        $this->assertTrue($module->install());
        $this->assertSame(
            array('GraphEngine', 'actionAdminControllerSetMedia'),
            Module::$registeredHooks
        );
    }

    public function testInstallReturnsFalseWhenParentFails()
    {
        $this->resetStubs();
        Module::$installReturn = false;
        $module = new GraphNvD3();
        $this->assertFalse($module->install());
        $this->assertSame(array(), Module::$registeredHooks);
    }

    public function testInstallStopsWhenGraphEngineHookRejects()
    {
        $this->resetStubs();
        Module::$registerHookFailOn = 'GraphEngine';
        $module = new GraphNvD3();
        $this->assertFalse($module->install());
        $this->assertSame(array('GraphEngine'), Module::$registeredHooks);
    }

    public function testInstallReturnsFalseWhenMediaHookRejects()
    {
        $this->resetStubs();
        Module::$registerHookFailOn = 'actionAdminControllerSetMedia';
        $module = new GraphNvD3();
        $this->assertFalse($module->install());
        $this->assertSame(
            array('GraphEngine', 'actionAdminControllerSetMedia'),
            Module::$registeredHooks
        );
    }
}

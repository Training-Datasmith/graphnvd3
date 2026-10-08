<?php

class GraphNvD3MediaHookTest extends GraphNvD3TestCase
{
    public function testRegistersD3AndThemeAssets()
    {
        $this->resetStubs();
        $module = new GraphNvD3();
        $controller = new GraphNvD3MediaHookTestController();
        $employee = new stdClass();
        $employee->bo_theme = 'classic-test';
        $module->context = new stdClass();
        $module->context->controller = $controller;
        $module->context->employee = $employee;

        $module->hookActionAdminControllerSetMedia(array());

        $this->assertCount(2, $controller->js);
        $this->assertSame('/js/vendor/d3.v3.min.js', $controller->js[0]);
        $this->assertSame(
            '/shop/admin-dev/themes/classic-test/js/vendor/nv.d3.min.js',
            $controller->js[1]
        );
        $this->assertCount(1, $controller->css);
        $this->assertSame(
            '/shop/admin-dev/themes/classic-test/css/vendor/nv.d3.css',
            $controller->css[0]
        );
    }
}

class GraphNvD3MediaHookTestController
{
    public $js = array();
    public $css = array();

    public function addJS($paths)
    {
        $this->js = $paths;
    }

    public function addCSS($path)
    {
        $this->css[] = $path;
    }
}

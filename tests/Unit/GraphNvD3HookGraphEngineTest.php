<?php

class GraphNvD3HookGraphEngineTest extends GraphNvD3TestCase
{
    public function testLineMarkupKeepsPercentWidthAndEscapesDrawer()
    {
        $this->resetStubs();
        $drawerUrl = 'http://shop.test/admin/index.php?token=a"b\\c';
        $markup = GraphNvD3::hookGraphEngine(
            array('width' => '100%', 'height' => 270, 'type' => 'line'),
            $drawerUrl
        );
        $style = $this->parseSvgStyleDeclarations($markup);
        $this->assertSame('100%', $style['width']);
        $this->assertSame('270px', $style['height']);
        $this->assertSame(1, preg_match('/nv\.models\.lineChart\s*\(\s*\)/', $markup));
        $this->assertSame(1, preg_match('/chart\.legend\.align\s*\(\s*false\s*\)/', $markup));
        $this->assertSame($drawerUrl, $this->decodeAjaxUrlFromMarkup($markup));
        $divId = $this->matchOne('/id\s*=\s*"nvd3_chart_(\d+)"/', $markup, 1);
        $selectId = $this->matchOne('/d3\.select\s*\(\s*"#nvd3_chart_(\d+)\s+svg"\s*\)/', $markup, 1);
        $this->assertSame($divId, $selectId);
    }

    public function testPieMarkupUsesPixelWidth()
    {
        $this->resetStubs();
        $markup = GraphNvD3::hookGraphEngine(
            array('width' => '640', 'height' => '480', 'type' => 'pie'),
            'http://example.test/draw'
        );
        $style = $this->parseSvgStyleDeclarations($markup);
        $this->assertSame('640px', $style['width']);
        $this->assertSame('480px', $style['height']);
        $this->assertSame(1, preg_match('/nv\.models\.pieChart\s*\(\s*\)/', $markup));
        $this->assertSame(1, preg_match('/\.showLabels\s*\(\s*true\s*\)/', $markup));
        $this->assertSame(1, preg_match('/\.showLegend\s*\(\s*false\s*\)/', $markup));
        $this->assertSame(1, preg_match('/function\s*\(\s*d\s*\)\s*\{\s*return\s+d\.label\s*;\s*\}/', $markup));
        $this->assertSame(1, preg_match('/function\s*\(\s*d\s*\)\s*\{\s*return\s+d\.value\s*;\s*\}/', $markup));
    }

    public function testPercentWidthStripsSurroundingSpaces()
    {
        $this->resetStubs();
        $markup = GraphNvD3::hookGraphEngine(
            array('width' => '50 %', 'height' => 10, 'type' => 'line'),
            'http://example.test/draw'
        );
        $style = $this->parseSvgStyleDeclarations($markup);
        $this->assertSame('50%', $style['width']);
        $this->assertSame('10px', $style['height']);
    }

    public function testPixelSuffixIsNotDoubled()
    {
        $this->resetStubs();
        $markup = GraphNvD3::hookGraphEngine(
            array('width' => '800px', 'height' => '270px', 'type' => 'line'),
            'http://example.test/draw'
        );
        $style = $this->parseSvgStyleDeclarations($markup);
        $this->assertSame('800px', $style['width']);
        $this->assertSame('270px', $style['height']);
        $this->assertFalse(strpos($style['width'], 'pxpx') !== false);
        $this->assertFalse(strpos($style['height'], 'pxpx') !== false);
    }

    public function testSuccessiveCallsUseDistinctMatchingIds()
    {
        $this->resetStubs();
        $first = GraphNvD3::hookGraphEngine(
            array('width' => '100%', 'height' => 100, 'type' => 'line'),
            'http://example.test/one'
        );
        $second = GraphNvD3::hookGraphEngine(
            array('width' => '100%', 'height' => 100, 'type' => 'line'),
            'http://example.test/two'
        );
        $firstDiv = (int) $this->matchOne('/id\s*=\s*"nvd3_chart_(\d+)"/', $first, 1);
        $firstSelect = (int) $this->matchOne('/d3\.select\s*\(\s*"#nvd3_chart_(\d+)\s+svg"\s*\)/', $first, 1);
        $secondDiv = (int) $this->matchOne('/id\s*=\s*"nvd3_chart_(\d+)"/', $second, 1);
        $secondSelect = (int) $this->matchOne('/d3\.select\s*\(\s*"#nvd3_chart_(\d+)\s+svg"\s*\)/', $second, 1);
        $this->assertSame($firstDiv, $firstSelect);
        $this->assertSame($secondDiv, $secondSelect);
        $this->assertSame($firstDiv + 1, $secondDiv);
    }
}

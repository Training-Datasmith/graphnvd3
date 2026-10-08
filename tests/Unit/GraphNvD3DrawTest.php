<?php

class GraphNvD3DrawTest extends GraphNvD3TestCase
{
    private function makeDrawer(array $values, array $legend, array $titles)
    {
        $drawer = new GraphNvD3('line');
        $drawer->createValues($values);
        $drawer->setLegend($legend);
        $drawer->setTitles($titles);

        return $drawer;
    }

    public function testLineChartSingleSeriesShape()
    {
        $this->resetStubs();
        $drawer = $this->makeDrawer(
            array(0 => 4, 1 => 9),
            array(),
            array('main' => 'Orders', 'x' => 'Month', 'y' => 'Total')
        );
        $decoded = $this->captureDrawOutput($drawer, 'line');
        $this->assertArrayHasKeys($decoded, array('axisLabels', 'data'));
        $this->assertArrayHasKeys($decoded['axisLabels'], array('xAxis', 'yAxis'));
        $this->assertSame('Month', $decoded['axisLabels']['xAxis']);
        $this->assertSame('Total', $decoded['axisLabels']['yAxis']);
        $this->assertCount(1, $decoded['data']);
        $this->assertArrayHasKeys($decoded['data'][0], array('values', 'key'));
        $this->assertSame('Orders', $decoded['data'][0]['key']);
        $this->assertCount(2, $decoded['data'][0]['values']);
        $this->assertSame(0, $decoded['data'][0]['values'][0]['x']);
        $this->assertSame(4, $decoded['data'][0]['values'][0]['y']);
        $this->assertSame(1, $decoded['data'][0]['values'][1]['x']);
        $this->assertSame(9, $decoded['data'][0]['values'][1]['y']);
    }

    public function testLineChartKeepsNonZeroKeys()
    {
        $this->resetStubs();
        $drawer = $this->makeDrawer(
            array(12 => 2, 13 => 5),
            array(),
            array('main' => 'M', 'x' => 'X', 'y' => 'Y')
        );
        $decoded = $this->captureDrawOutput($drawer, 'line');
        $this->assertArrayHasKeys($decoded, array('data'));
        $this->assertCount(1, $decoded['data']);
        $this->assertCount(2, $decoded['data'][0]['values']);
        $this->assertSame(12, $decoded['data'][0]['values'][0]['x']);
        $this->assertSame(13, $decoded['data'][0]['values'][1]['x']);
    }

    public function testCanonicalIntegerStringsBecomeJsonNumbers()
    {
        $this->resetStubs();
        $drawer = $this->makeDrawer(
            array('Jan' => '0', 'Feb' => '12'),
            array(),
            array('main' => 'M', 'x' => 'X', 'y' => 'Y')
        );
        $decoded = $this->captureDrawOutput($drawer, 'line');
        $points = $decoded['data'][0]['values'];
        $this->assertSame('Jan', $points[0]['x']);
        $this->assertSame('Feb', $points[1]['x']);
        $this->assertSame(0, $points[0]['y']);
        $this->assertSame(12, $points[1]['y']);
    }

    public function testPieChartUsesLegendLabels()
    {
        $this->resetStubs();
        $drawer = $this->makeDrawer(
            array(5, 7),
            array('France', 'Germany'),
            array('main' => 'M', 'x' => 'X', 'y' => 'Y')
        );
        $decoded = $this->captureDrawOutput($drawer, 'pie');
        $this->assertArrayHasKeys($decoded, array('data'));
        $this->assertCount(2, $decoded['data']);
        $this->assertArrayHasKeys($decoded['data'][0], array('label', 'value'));
        $this->assertSame('France', $decoded['data'][0]['label']);
        $this->assertSame(5, $decoded['data'][0]['value']);
        $this->assertSame('Germany', $decoded['data'][1]['label']);
        $this->assertSame(7, $decoded['data'][1]['value']);
    }

    public function testLeadingZeroNumericStringDoesNotBreakJson()
    {
        $this->resetStubs();
        $drawer = $this->makeDrawer(
            array('01' => '12'),
            array(),
            array('main' => 'M', 'x' => 'X', 'y' => 'Y')
        );
        $decoded = $this->captureDrawOutput($drawer, 'line');
        $this->assertArrayHasKeys($decoded, array('data'));
        $point = $decoded['data'][0]['values'][0];
        $this->assertArrayHasKeys($point, array('x', 'y'));
        $this->assertSame('01', $point['x']);
        $this->assertSame(12, $point['y']);
    }

    public function testMultiSeriesUsesPerLayerKeys()
    {
        $this->resetStubs();
        $drawer = $this->makeDrawer(
            array(
                0 => array(0 => 1, 1 => 2),
                1 => array(0 => 8, 1 => 9),
            ),
            array(),
            array('main' => array('Visits', 'Sales'), 'x' => 'X', 'y' => 'Y')
        );
        $decoded = $this->captureDrawOutput($drawer, 'line');
        $this->assertCount(2, $decoded['data']);
        $this->assertSame('Visits', $decoded['data'][0]['key']);
        $this->assertSame('Sales', $decoded['data'][1]['key']);
    }

    public function testEmptyPieIsEmptyList()
    {
        $this->resetStubs();
        $drawer = $this->makeDrawer(array(), array(), array('main' => 'M', 'x' => 'X', 'y' => 'Y'));
        $decoded = $this->captureDrawOutput($drawer, 'pie');
        $this->assertArrayHasKeys($decoded, array('data'));
        $this->assertSame(array(), $decoded['data']);
    }

    public function testNullAxisLabelsStayNull()
    {
        $this->resetStubs();
        $drawer = $this->makeDrawer(
            array(0 => 1),
            array(),
            array('main' => 'M', 'x' => null, 'y' => null)
        );
        $decoded = $this->captureDrawOutput($drawer, 'line');
        $this->assertArrayHasKeys($decoded, array('axisLabels'));
        $this->assertArrayHasKeys($decoded['axisLabels'], array('xAxis', 'yAxis'));
        $this->assertNull($decoded['axisLabels']['xAxis']);
        $this->assertNull($decoded['axisLabels']['yAxis']);
    }
}

<?php

class GraphNvD3DrawTest extends GraphNvD3TestCase
{
    private function makeDrawer(array $values, array $legend, array $titles, $type)
    {
        $drawer = new GraphNvD3($type);
        $drawer->createValues($values);
        $drawer->setLegend($legend);
        $drawer->setTitles($titles);

        return $drawer;
    }

    public function testLineChartSingleSeriesShape()
    {
        $this->resetStubs();
        $type = 'line';
        $drawer = $this->makeDrawer(
            [0 => 4, 1 => 9],
            [],
            ['main' => 'Orders', 'x' => 'Month', 'y' => 'Total'],
            $type
        );
        $decoded = $this->captureDrawOutput($drawer, $type);
        $this->assertArrayHasKeys($decoded, ['axisLabels', 'data']);
        $this->assertArrayHasKeys($decoded['axisLabels'], ['xAxis', 'yAxis']);
        $this->assertSame('Month', $decoded['axisLabels']['xAxis']);
        $this->assertSame('Total', $decoded['axisLabels']['yAxis']);
        $this->assertCount(1, $decoded['data']);
        $this->assertArrayHasKeys($decoded['data'][0], ['values', 'key']);
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
        $type = 'line';
        $drawer = $this->makeDrawer(
            [12 => 2, 13 => 5],
            [],
            ['main' => 'M', 'x' => 'X', 'y' => 'Y'],
            $type
        );
        $decoded = $this->captureDrawOutput($drawer, $type);
        $this->assertArrayHasKeys($decoded, ['data']);
        $this->assertCount(1, $decoded['data']);
        $this->assertCount(2, $decoded['data'][0]['values']);
        $this->assertSame(12, $decoded['data'][0]['values'][0]['x']);
        $this->assertSame(13, $decoded['data'][0]['values'][1]['x']);
    }

    public function testCanonicalIntegerStringsBecomeJsonNumbers()
    {
        $this->resetStubs();
        $type = 'line';
        $drawer = $this->makeDrawer(
            ['Jan' => '0', 'Feb' => '12'],
            [],
            ['main' => 'M', 'x' => 'X', 'y' => 'Y'],
            $type
        );
        $decoded = $this->captureDrawOutput($drawer, $type);
        $points = $decoded['data'][0]['values'];
        $this->assertSame('Jan', $points[0]['x']);
        $this->assertSame('Feb', $points[1]['x']);
        $this->assertSame(0, $points[0]['y']);
        $this->assertSame(12, $points[1]['y']);
    }

    public function testPieChartUsesLegendLabels()
    {
        $this->resetStubs();
        $type = 'pie';
        $drawer = $this->makeDrawer(
            [5, 7],
            ['France', 'Germany'],
            ['main' => 'M', 'x' => 'X', 'y' => 'Y'],
            $type
        );
        $decoded = $this->captureDrawOutput($drawer, $type);
        $this->assertArrayHasKeys($decoded, ['data']);
        $this->assertCount(2, $decoded['data']);
        $this->assertArrayHasKeys($decoded['data'][0], ['label', 'value']);
        $this->assertSame('France', $decoded['data'][0]['label']);
        $this->assertSame(5, $decoded['data'][0]['value']);
        $this->assertSame('Germany', $decoded['data'][1]['label']);
        $this->assertSame(7, $decoded['data'][1]['value']);
    }

    public function testLeadingZeroNumericStringDoesNotBreakJson()
    {
        $this->resetStubs();
        $type = 'line';
        $drawer = $this->makeDrawer(
            ['01' => '12'],
            [],
            ['main' => 'M', 'x' => 'X', 'y' => 'Y'],
            $type
        );
        $decoded = $this->captureDrawOutput($drawer, $type);
        $this->assertArrayHasKeys($decoded, ['data']);
        $point = $decoded['data'][0]['values'][0];
        $this->assertArrayHasKeys($point, ['x', 'y']);
        $this->assertSame('01', $point['x']);
        $this->assertSame(12, $point['y']);
    }

    public function testMultiSeriesUsesPerLayerKeys()
    {
        $this->resetStubs();
        $type = 'line';
        $drawer = $this->makeDrawer(
            [
                0 => [0 => 1, 1 => 2],
                1 => [0 => 8, 1 => 9],
            ],
            [],
            ['main' => ['Visits', 'Sales'], 'x' => 'X', 'y' => 'Y'],
            $type
        );
        $decoded = $this->captureDrawOutput($drawer, $type);
        $this->assertCount(2, $decoded['data']);
        $this->assertSame('Visits', $decoded['data'][0]['key']);
        $this->assertSame('Sales', $decoded['data'][1]['key']);
        $this->assertSame(0, $decoded['data'][0]['values'][0]['x']);
        $this->assertSame(1, $decoded['data'][0]['values'][0]['y']);
        $this->assertSame(1, $decoded['data'][0]['values'][1]['x']);
        $this->assertSame(2, $decoded['data'][0]['values'][1]['y']);
        $this->assertSame(0, $decoded['data'][1]['values'][0]['x']);
        $this->assertSame(8, $decoded['data'][1]['values'][0]['y']);
        $this->assertSame(1, $decoded['data'][1]['values'][1]['x']);
        $this->assertSame(9, $decoded['data'][1]['values'][1]['y']);
    }

    public function testEmptyPieIsEmptyList()
    {
        $this->resetStubs();
        $type = 'pie';
        $drawer = $this->makeDrawer([], [], ['main' => 'M', 'x' => 'X', 'y' => 'Y'], $type);
        $decoded = $this->captureDrawOutput($drawer, $type);
        $this->assertArrayHasKeys($decoded, ['data']);
        $this->assertSame([], $decoded['data']);
    }

    public function testNullAxisLabelsStayNull()
    {
        $this->resetStubs();
        $type = 'line';
        $drawer = $this->makeDrawer(
            [0 => 1],
            [],
            ['main' => 'M', 'x' => null, 'y' => null],
            $type
        );
        $decoded = $this->captureDrawOutput($drawer, $type);
        $this->assertArrayHasKeys($decoded, ['axisLabels']);
        $this->assertArrayHasKeys($decoded['axisLabels'], ['xAxis', 'yAxis']);
        $this->assertNull($decoded['axisLabels']['xAxis']);
        $this->assertNull($decoded['axisLabels']['yAxis']);
    }
}

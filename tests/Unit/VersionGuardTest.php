<?php

class VersionGuardTest extends GraphNvD3TestCase
{
    public function testFileExitsWhenPsVersionMissing()
    {
        $script = dirname(dirname(__DIR__)) . '/graphnvd3.php';
        $cmd = 'php -d error_reporting=-1 -r ' . escapeshellarg(
            'include ' . var_export($script, true) . '; echo "NO_GUARD\n";'
        );
        $output = array();
        $exitCode = 0;
        exec($cmd, $output, $exitCode);
        $joined = implode("\n", $output);
        $this->assertSame(0, $exitCode);
        $this->assertFalse(strpos($joined, 'NO_GUARD') !== false);
    }
}

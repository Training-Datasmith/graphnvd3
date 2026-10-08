<?php

class FrontControllerGuardTest extends GraphNvD3TestCase
{
    /**
     * @param string $path
     *
     * @return HttpResponse
     */
    private function fetchGuardScript($path)
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
        $this->assertTrue(is_resource($socket));
        $name = stream_socket_get_name($socket, false);
        $this->assertSame(1, preg_match('/:(\d+)$/', $name, $portMatch));
        $port = (int) $portMatch[1];
        fclose($socket);

        $docRoot = dirname(dirname(__DIR__));
        $cmd = 'php -d error_reporting=-1 -S 127.0.0.1:' . $port . ' -t ' . escapeshellarg($docRoot);
        $descriptors = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        );
        $process = proc_open($cmd, $descriptors, $pipes);
        $this->assertTrue(is_resource($process));

        $ready = false;
        for ($attempt = 0; $attempt < 50; ++$attempt) {
            $conn = @fsockopen('127.0.0.1', $port, $connErrno, $connErrstr, 0.2);
            if (is_resource($conn)) {
                fclose($conn);
                $ready = true;
                break;
            }
            usleep(50000);
        }
        $this->assertTrue($ready);

        $fp = fsockopen('127.0.0.1', $port, $reqErrno, $reqErrstr, 2.0);
        $this->assertTrue(is_resource($fp));
        $request = "GET {$path} HTTP/1.0\r\nHost: 127.0.0.1\r\nConnection: close\r\n\r\n";
        fwrite($fp, $request);
        $raw = stream_get_contents($fp);
        fclose($fp);

        proc_terminate($process);
        proc_close($process);

        return HttpResponse::parse($raw);
    }

    public function testRootIndexRedirects()
    {
        $this->resetStubs();
        $response = $this->fetchGuardScript('/index.php');
        $this->assertSame(302, $response->status);
        $this->assertSame('../', $response->firstHeader('Location'));
        $this->assertSame('', $response->body);
        $this->assertSame('Mon, 26 Jul 1997 05:00:00 GMT', $response->firstHeader('Expires'));
        $this->assertSame(
            array('must-revalidate', 'no-cache', 'no-store', 'post-check=0', 'pre-check=0'),
            $response->cacheControlTokens()
        );
        $this->assertSame('no-cache', $response->firstHeader('Pragma'));
        $lastModified = $response->firstHeader('Last-Modified');
        $this->assertNotNull($lastModified);
        $this->assertSame(1, preg_match('/^[A-Za-z]{3}, [0-9]{2} [A-Za-z]{3} [0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2} GMT$/', $lastModified));
    }

    public function testTestsIndexRedirects()
    {
        $this->resetStubs();
        $response = $this->fetchGuardScript('/tests/index.php');
        $this->assertSame(302, $response->status);
        $this->assertSame('../', $response->firstHeader('Location'));
        $this->assertSame('', $response->body);
    }

    public function testPhpstanIndexRedirects()
    {
        $this->resetStubs();
        $response = $this->fetchGuardScript('/tests/phpstan/index.php');
        $this->assertSame(302, $response->status);
        $this->assertSame('../', $response->firstHeader('Location'));
        $this->assertSame('', $response->body);
    }
}

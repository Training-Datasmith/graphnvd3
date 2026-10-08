<?php

class HttpResponse
{
    /** @var int */
    public $status;

    /** @var array<string, array<int, string>> */
    public $headers;

    /** @var string */
    public $body;

    /**
     * @param string $raw
     *
     * @return HttpResponse
     */
    public static function parse($raw)
    {
        $delimiterPos = strpos($raw, "\r\n\r\n");
        if ($delimiterPos === false) {
            $delimiterPos = strpos($raw, "\n\n");
        }
        if ($delimiterPos === false) {
            throw new InvalidArgumentException('HTTP response delimiter not found');
        }

        $headerBlock = substr($raw, 0, $delimiterPos);
        $skip = strpos($raw, "\r\n\r\n") === $delimiterPos ? 4 : 2;
        $bodyStart = $delimiterPos + $skip;
        $body = $bodyStart >= strlen($raw) ? '' : substr($raw, $bodyStart);

        $lines = preg_split("/\r\n|\n/", $headerBlock);
        if (!is_array($lines) || count($lines) < 1) {
            throw new InvalidArgumentException('HTTP header block is empty');
        }

        $statusLine = $lines[0];
        if (preg_match('/^HTTP\/\d\.\d\s+(\d{3})/', $statusLine, $m) !== 1) {
            throw new InvalidArgumentException('HTTP status line missing');
        }

        $response = new self();
        $response->status = (int) $m[1];
        $response->headers = [];
        $response->body = $body;

        for ($i = 1; $i < count($lines); ++$i) {
            $line = $lines[$i];
            if ($line === '') {
                continue;
            }
            $colon = strpos($line, ':');
            if ($colon === false) {
                continue;
            }
            $name = strtolower(trim(substr($line, 0, $colon)));
            $value = trim(substr($line, $colon + 1));
            if (!isset($response->headers[$name])) {
                $response->headers[$name] = [];
            }
            $response->headers[$name][] = $value;
        }

        return $response;
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function firstHeader($name)
    {
        $key = strtolower($name);
        if (!isset($this->headers[$key]) || count($this->headers[$key]) === 0) {
            return null;
        }

        return $this->headers[$key][0];
    }

    /**
     * @return array<int, string>
     */
    public function cacheControlTokens()
    {
        $key = 'cache-control';
        if (!isset($this->headers[$key])) {
            return [];
        }
        $tokens = [];
        foreach ($this->headers[$key] as $value) {
            $parts = explode(',', $value);
            foreach ($parts as $part) {
                $token = strtolower(trim($part));
                if ($token !== '') {
                    $tokens[] = $token;
                }
            }
        }
        sort($tokens);

        return $tokens;
    }
}

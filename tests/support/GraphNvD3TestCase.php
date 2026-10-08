<?php

abstract class GraphNvD3TestCase extends PHPUnit\Framework\TestCase
{
    protected function resetStubs()
    {
        Module::reset();
        Tools::reset();
    }

    /**
     * @param string $pattern
     * @param string $subject
     * @param int $group
     *
     * @return string
     */
    protected function matchOne($pattern, $subject, $group = 0)
    {
        $matches = [];
        $this->assertSame(1, preg_match($pattern, $subject, $matches));

        return $matches[$group];
    }

    /**
     * @param string $json
     *
     * @return array
     */
    protected function decodeDrawJson($json)
    {
        $decoded = json_decode($json, true);
        $this->assertSame(JSON_ERROR_NONE, json_last_error());
        $this->assertTrue(is_array($decoded));

        return $decoded;
    }

    /**
     * @param array $array
     * @param array|string $keys
     */
    protected function assertArrayHasKeys($array, $keys)
    {
        $this->assertTrue(is_array($array));
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $array);
        }
    }

    /**
     * @param string $markup
     *
     * @return array<string, string>
     */
    protected function parseSvgStyleDeclarations($markup)
    {
        $style = $this->matchOne('/<svg[^>]*\sstyle\s*=\s*"([^"]*)"/s', $markup, 1);
        $declarations = [];
        foreach (explode(';', $style) as $chunk) {
            $chunk = trim($chunk);
            if ($chunk === '') {
                continue;
            }
            $eq = strpos($chunk, ':');
            if ($eq === false) {
                continue;
            }
            $prop = strtolower(trim(substr($chunk, 0, $eq)));
            $val = trim(substr($chunk, $eq + 1));
            $declarations[$prop] = $val;
        }

        return $declarations;
    }

    /**
     * @param string $markup
     *
     * @return string
     */
    protected function decodeAjaxUrlFromMarkup($markup)
    {
        $literal = $this->matchOne('/url\s*:\s*"((?:\\\\.|[^"\\\\])*)"/s', $markup, 1);

        return stripcslashes($literal);
    }

    /**
     * @param string $path
     *
     * @return array<string, string>
     */
    protected function parseConfigXmlValues($path)
    {
        $xml = file_get_contents($path);
        $this->assertTrue(is_string($xml) && $xml !== '');
        $values = [];
        if (preg_match_all('/<([a-zA-Z0-9_]+)>(?:<!\[CDATA\[(.*?)\]\]>|([^<]*))<\/\1>/s', $xml, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                if (strpos($m[0], '<![CDATA[') !== false) {
                    $values[$m[1]] = $m[2];
                } else {
                    $values[$m[1]] = $m[3];
                }
            }
        }

        return $values;
    }

    /**
     * @param GraphNvD3 $drawer
     * @param string $type
     *
     * @return array
     */
    protected function captureDrawOutput(GraphNvD3 $drawer, $type)
    {
        Tools::reset(['type' => $type]);
        ob_start();
        $drawer->draw();
        $output = ob_get_clean();

        return $this->decodeDrawJson($output);
    }
}

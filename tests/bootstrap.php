<?php
/**
 * PHPUnit bootstrap for graphnvd3 (PHP 5.4+).
 */

error_reporting(-1);

if (!class_exists('PHPUnit\\Framework\\TestCase') && class_exists('PHPUnit_Framework_TestCase')) {
    class_alias('PHPUnit_Framework_TestCase', 'PHPUnit\\Framework\\TestCase');
}

require_once __DIR__ . '/stubs/PrestaShopHost.php';
require_once __DIR__ . '/support/GraphNvD3TestCase.php';
require_once __DIR__ . '/support/HttpResponse.php';

require_once dirname(__DIR__) . '/graphnvd3.php';

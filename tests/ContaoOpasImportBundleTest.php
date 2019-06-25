<?php

namespace Floxn\ContaoOpasImportBundle\Tests;

use Floxn\ContaoOpasImportBundle\ContaoOpasImportBundle;
use PHPUnit\Framework\TestCase;

class ContaoOpasImportBundleTest extends TestCase
{
    public function testCanBeInstantiated()
    {
        $bundle = new ContaoOpasImportBundle();

        $this->assertInstanceOf('Floxn\ContaoOpasImportBundle\ContaoOpasImportBundle', $bundle);
    }
}

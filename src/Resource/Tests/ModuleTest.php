<?php
/**
 * Copyright (c) 2016. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace Apparat\Resource\Tests;

use Apparat\Kernel\Tests\AbstractTest;
use Apparat\Resource\Module;

/**
 * Module tests
 *
 * @package Apparat\Kernel
 * @subpackage Apparat\Kernel\Tests
 */
class ModuleTest extends AbstractTest
{
    /**
     * Test the module's auto-run feature
     */
    public function testModuleAutorun()
    {
        include dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Autorun.php';
        $this->assertEquals(Module::NAME, (new Module())->getName());
    }
}

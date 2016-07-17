<?php

namespace Saxulum\Tests\ModelImporter\Progress;

use Saxulum\ModelImporter\Progress\NullProgress;

/**
 * @covers Saxulum\ModelImporter\Progress\NullProgress
 */
class NullProgressTest extends \PHPUnit_Framework_TestCase
{
    public function testAdvance()
    {
        $progress = new NullProgress();
        $progress->advance();
    }
}

<?php

namespace Saxulum\Tests\ModelImporter;

use Saxulum\ModelImporter\NotImportableException;

/**
 * @covers Saxulum\ModelImporter\NotImportableException
 */
class NotImportableExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testMessage()
    {
        $exception = new NotImportableException(NotImportableException::ACTION_CREATE);

        self::assertSame(
            'Model with identifier {identifier} is not importable, cause create failed',
            $exception->getMessage()
        );
    }
}

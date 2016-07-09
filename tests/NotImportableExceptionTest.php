<?php

namespace Saxulum\Tests\ModelImporter;

use Saxulum\ModelImporter\NotImportableException;

class NotImportableExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testMessage()
    {
        $exception = new NotImportableException('create');

        self::assertSame(
            'Model with identifier {identifier} is not importable, cause create failed',
            $exception->getMessage()
        );
    }
}

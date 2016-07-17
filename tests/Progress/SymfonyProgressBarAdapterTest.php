<?php

namespace Saxulum\Tests\ModelImporter\Progress;

use Saxulum\ModelImporter\Progress\SymfonyProgressBarAdapter;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * @covers Saxulum\ModelImporter\Progress\SymfonyProgressBarAdapter
 */
class SymfonyProgressBarAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testAdvance()
    {
        $progressBar = $this->getProgressBar();

        $progressBarAdapter = new SymfonyProgressBarAdapter($progressBar);

        self::assertSame(0, $progressBar->advance);

        $progressBarAdapter->advance();

        self::assertSame(1, $progressBar->advance);

        $progressBarAdapter->advance();

        self::assertSame(2, $progressBar->advance);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ProgressBar|\stdClass
     */
    protected function getProgressBar()
    {
        /** @var ProgressBar|\PHPUnit_Framework_MockObject_MockObject|\stdClass $progressBar */
        $progressBar = $this
            ->getMockBuilder(ProgressBar::class)
            ->disableOriginalConstructor()
            ->setMethods(['advance'])
            ->getMock();

        $progressBar->advance = 0;

        $progressBar
            ->expects(self::any())
            ->method('advance')
            ->willReturnCallback(function () use ($progressBar) {
                ++$progressBar->advance;
            });

        return $progressBar;
    }
}

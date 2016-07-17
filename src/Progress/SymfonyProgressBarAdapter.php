<?php

namespace Saxulum\ModelImporter\Progress;

use Symfony\Component\Console\Helper\ProgressBar;

class SymfonyProgressBarAdapter implements ProgressInterface
{
    /**
     * @var ProgressBar
     */
    protected $progressBar;

    /**
     * @param ProgressBar $progressBar
     */
    public function __construct(ProgressBar $progressBar)
    {
        $this->progressBar = $progressBar;
    }

    public function advance()
    {
        $this->progressBar->advance();
    }
}

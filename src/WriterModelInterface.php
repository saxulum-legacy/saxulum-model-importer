<?php

namespace Saxulum\ModelImporter;

interface WriterModelInterface
{
    /**
     * @param string|int $readerIdentifier
     */
    public function setReaderIdentifier($readerIdentifier);

    /**
     * @return string|int
     */
    public function getReaderIdentifier();

    /**
     * @param \DateTime $lastImportDate
     */
    public function setLastImportDate(\DateTime $lastImportDate);

    /**
     * @return \DateTime|null
     */
    public function getLastImportDate();
}

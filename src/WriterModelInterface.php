<?php

namespace Saxulum\ModelImporter;

interface WriterModelInterface
{
    /**
     * @param string|int $identifier
     */
    public function setImportIdentifier($identifier);

    /**
     * @return string|int
     */
    public function getImportIdentifier();

    /**
     * @param \DateTime $lastImportDate
     */
    public function setLastImportDate(\DateTime $lastImportDate);

    /**
     * @return \DateTime|null
     */
    public function getLastImportDate();
}

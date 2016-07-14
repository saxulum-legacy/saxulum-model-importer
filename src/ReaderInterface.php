<?php

namespace Saxulum\ModelImporter;

interface ReaderInterface
{
    /**
     * @param int $offset
     * @param int $limit
     *
     * @return ReaderModelInterface[]|array
     */
    public function getReaderModels($offset, $limit);

    public function clearReaderModels();
}

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
    public function getModels($offset, $limit);
}

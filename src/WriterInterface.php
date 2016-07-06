<?php

namespace Saxulum\ModelImporter;

interface WriterInterface
{
    /**
     * @param ReaderModelInterface $readerModel
     *
     * @return WriterModelInterface|null
     */
    public function find(ReaderModelInterface $readerModel);

    /**
     * @param ReaderModelInterface $readerModel
     *
     * @return WriterModelInterface
     */
    public function create(ReaderModelInterface $readerModel);

    /**
     * @param WriterModelInterface $model
     * @param ReaderModelInterface $readerModel
     */
    public function update(WriterModelInterface $model, ReaderModelInterface $readerModel);

    /**
     * @param WriterModelInterface $model
     */
    public function persist(WriterModelInterface $model);

    /**
     * @param WriterModelInterface[]|array $models
     */
    public function flush(array $models);

    /**
     * @param \DateTime $lastImportDate
     */
    public function removeAllOutdated(\DateTime $lastImportDate);
}
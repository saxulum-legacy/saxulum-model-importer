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
     *
     * @throws NotImportableException
     */
    public function create(ReaderModelInterface $readerModel);

    /**
     * @param WriterModelInterface $model
     * @param ReaderModelInterface $readerModel
     *
     * @throws NotImportableException
     */
    public function update(WriterModelInterface $model, ReaderModelInterface $readerModel);

    /**
     * @param WriterModelInterface $model
     *
     * @throws NotImportableException
     */
    public function persist(WriterModelInterface $model);

    public function flush();

    /**
     * @param \DateTime $lastImportDate
     */
    public function removeAllOutdated(\DateTime $lastImportDate);
}

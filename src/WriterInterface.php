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
     * @param WriterModelInterface $writerModel
     * @param ReaderModelInterface $readerModel
     *
     * @throws NotImportableException
     */
    public function update(WriterModelInterface $writerModel, ReaderModelInterface $readerModel);

    /**
     * @param WriterModelInterface $writerModel
     *
     * @throws NotImportableException
     */
    public function persist(WriterModelInterface $writerModel);

    public function flush();

    public function clear();

    /**
     * @param \DateTime $lastImportDate
     */
    public function removeAllOutdated(\DateTime $lastImportDate);
}

<?php

namespace Saxulum\ModelImporter;

interface WriterInterface
{
    /**
     * @param ReaderModelInterface $readerModel
     *
     * @return WriterModelInterface|null
     */
    public function findWriterModel(ReaderModelInterface $readerModel);

    /**
     * @param ReaderModelInterface $readerModel
     *
     * @return WriterModelInterface
     *
     * @throws NotImportableException
     */
    public function createWriterModel(ReaderModelInterface $readerModel);

    /**
     * @param WriterModelInterface $writerModel
     * @param ReaderModelInterface $readerModel
     *
     * @throws NotImportableException
     */
    public function updateWriterModel(WriterModelInterface $writerModel, ReaderModelInterface $readerModel);

    /**
     * @param WriterModelInterface $writerModel
     *
     * @throws NotImportableException
     */
    public function persistWriterModel(WriterModelInterface $writerModel);

    /**
     * @param WriterModelInterface[]|array $writeModels
     */
    public function flushWriterModels(array $writeModels);

    public function clearWriterModels();

    /**
     * @param \DateTime $lastImportDate
     */
    public function removeWriterModels(\DateTime $lastImportDate);
}

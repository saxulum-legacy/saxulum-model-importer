<?php

namespace Saxulum\ModelImporter;

use Psr\Log\LoggerInterface;

class Importer
{
    /**
     * @var ReaderInterface
     */
    protected $reader;

    /**
     * @var WriterInterface
     */
    protected $writer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param int $limit
     */
    public function import($limit = 100)
    {
        $importDate = new \DateTime();

        $this->logger->info('Import started at {importDate}', ['importData' => $importDate]);

        $offset = 0;

        while ([] !== $readerModels = $this->reader->getModels($offset, $limit)) {
            $this->logger->info('Read, offset: {offset}, limit: {limit}', ['offset' => $offset, 'limit' => $limit]);
            $writerModels = [];
            foreach ($readerModels as $readerModel) {
                $writerModel = $this->writer->find($readerModel);
                if (null === $writerModel) {
                    $writerModel = $this->writer->create($readerModel);
                    $this->logger->info(
                        'Created new model with reader identifier {readerIdentifier}',
                        ['readerIdentifier' => $readerModel->getIdentifier()]
                    );
                } else {
                    $this->writer->update($writerModel, $readerModel);
                    $this->logger->info(
                        'Updated model with reader identifier {readerIdentifier}',
                        ['readerIdentifier' => $readerModel->getIdentifier()]
                    );
                }

                $writerModel->setReaderIdentifier($readerModel->getIdentifier());
                $writerModel->setLastImportDate($importDate);

                $this->writer->persist($writerModel);
                $this->logger->info(
                    'Persisted model with reader identifier {readerIdentifier}',
                    ['readerIdentifier' => $readerModel->getIdentifier()]
                );

                $writerModels[] = $writerModel;
            }

            $this->writer->flush($writerModels);
            $this->logger->info('Flushed models');

            $offset += $limit;
        }

        $this->writer->removeAllOutdated($importDate);
        $this->logger->info('Removed all outdates');
    }
}

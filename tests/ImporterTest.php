<?php

namespace Saxulum\Tests\ModelImporter;

use Psr\Log\AbstractLogger;
use Saxulum\ModelImporter\Importer;
use Saxulum\ModelImporter\NotImportableException;
use Saxulum\ModelImporter\ReaderInterface;
use Saxulum\ModelImporter\ReaderModelInterface;
use Saxulum\ModelImporter\WriterInterface;
use Saxulum\ModelImporter\WriterModelInterface;

class ImporterTest extends \PHPUnit_Framework_TestCase
{
    public function testWithoutData()
    {
        $readerData = [];
        $writerData = [];

        $reader = $this->getReader($readerData);
        $writer = $this->getWriter($writerData);
        $logger = new TestLogger();

        $importer = new Importer($reader, $writer, $logger);

        $importDate = $importer->import(2);

        self::assertInstanceOf(\DateTime::class, $importDate);

        self::assertSame(
            [
                [
                    'level' => 'info',
                    'message' => 'Import started at {importDate}',
                    'context' => [
                        'importDate' => $importDate,
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Removed all outdates',
                    'context' => [],
                ],
            ],
            $logger->getLogs()
        );
    }

    public function testWithDataOnReaderSide()
    {
        $readerData = [
            $this->getReaderModel('id1'),
            $this->getReaderModel('id2'),
            $this->getReaderModel('id3'),
            $this->getReaderModel('id4'),
            $this->getReaderModel('id5'),
        ];

        $writerData = [];

        $reader = $this->getReader($readerData);
        $writer = $this->getWriter($writerData);
        $logger = new TestLogger();

        $importer = new Importer($reader, $writer, $logger);

        $importDate = $importer->import(2);

        self::assertInstanceOf(\DateTime::class, $importDate);

        self::assertCount(5, $writerData);
        self::assertInstanceOf(WriterModelInterface::class, $writerData[0]);
        self::assertInstanceOf(WriterModelInterface::class, $writerData[1]);
        self::assertInstanceOf(WriterModelInterface::class, $writerData[2]);
        self::assertInstanceOf(WriterModelInterface::class, $writerData[3]);
        self::assertInstanceOf(WriterModelInterface::class, $writerData[4]);

        self::assertSame('id1', $writerData[0]->getImportIdentifier());
        self::assertSame('id2', $writerData[1]->getImportIdentifier());
        self::assertSame('id3', $writerData[2]->getImportIdentifier());
        self::assertSame('id4', $writerData[3]->getImportIdentifier());
        self::assertSame('id5', $writerData[4]->getImportIdentifier());

        self::assertSame($importDate, $writerData[0]->getLastImportDate());
        self::assertSame($importDate, $writerData[1]->getLastImportDate());
        self::assertSame($importDate, $writerData[2]->getLastImportDate());
        self::assertSame($importDate, $writerData[3]->getLastImportDate());
        self::assertSame($importDate, $writerData[4]->getLastImportDate());

        self::assertSame(
            [
                [
                    'level' => 'info',
                    'message' => 'Import started at {importDate}',
                    'context' => [
                        'importDate' => $importDate,
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Read, offset: {offset}, limit: {limit}',
                    'context' => [
                        'offset' => 0,
                        'limit' => 2,
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Created model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id1',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Persisted model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id1',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Created model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id2',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Persisted model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id2',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Flushed models',
                    'context' => [],
                ],
                [
                    'level' => 'info',
                    'message' => 'Read, offset: {offset}, limit: {limit}',
                    'context' => [
                        'offset' => 2,
                        'limit' => 2,
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Created model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id3',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Persisted model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id3',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Created model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id4',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Persisted model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id4',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Flushed models',
                    'context' => [],
                ],
                [
                    'level' => 'info',
                    'message' => 'Read, offset: {offset}, limit: {limit}',
                    'context' => [
                        'offset' => 4,
                        'limit' => 2,
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Created model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id5',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Persisted model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id5',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Flushed models',
                    'context' => [],
                ],
                [
                    'level' => 'info',
                    'message' => 'Removed all outdates',
                    'context' => [],
                ],
            ],
            $logger->getLogs()
        );
    }

    public function testWithDataOnBothSide()
    {
        $readerData = [
            $this->getReaderModel('id1'),
            $this->getReaderModel('id2'),
            $this->getReaderModel('id3'),
            $this->getReaderModel('id4'),
        ];

        $lastImportDate = new \DateTime('yesterday');

        $writerData = [
            $this->getWriterModel('id1', $lastImportDate),
            $this->getWriterModel('id2', $lastImportDate),
            $this->getWriterModel('id5', $lastImportDate),
        ];

        $reader = $this->getReader($readerData);
        $writer = $this->getWriter($writerData);
        $logger = new TestLogger();

        $importer = new Importer($reader, $writer, $logger);

        $importDate = $importer->import(2);

        self::assertInstanceOf(\DateTime::class, $importDate);

        self::assertCount(4, $writerData);
        self::assertInstanceOf(WriterModelInterface::class, $writerData[0]);
        self::assertInstanceOf(WriterModelInterface::class, $writerData[1]);
        self::assertInstanceOf(WriterModelInterface::class, $writerData[2]);
        self::assertInstanceOf(WriterModelInterface::class, $writerData[3]);

        self::assertSame('id1', $writerData[0]->getImportIdentifier());
        self::assertSame('id2', $writerData[1]->getImportIdentifier());
        self::assertSame('id3', $writerData[2]->getImportIdentifier());
        self::assertSame('id4', $writerData[3]->getImportIdentifier());

        self::assertSame($importDate, $writerData[0]->getLastImportDate());
        self::assertSame($importDate, $writerData[1]->getLastImportDate());
        self::assertSame($importDate, $writerData[2]->getLastImportDate());
        self::assertSame($importDate, $writerData[3]->getLastImportDate());

        self::assertSame(
            [
                [
                    'level' => 'info',
                    'message' => 'Import started at {importDate}',
                    'context' => [
                        'importDate' => $importDate,
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Read, offset: {offset}, limit: {limit}',
                    'context' => [
                        'offset' => 0,
                        'limit' => 2,
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Updated model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id1',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Persisted model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id1',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Updated model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id2',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Persisted model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id2',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Flushed models',
                    'context' => [],
                ],
                [
                    'level' => 'info',
                    'message' => 'Read, offset: {offset}, limit: {limit}',
                    'context' => [
                        'offset' => 2,
                        'limit' => 2,
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Created model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id3',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Persisted model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id3',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Created model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id4',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Persisted model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id4',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Flushed models',
                    'context' => [],
                ],
                [
                    'level' => 'info',
                    'message' => 'Removed all outdates',
                    'context' => [],
                ],
            ],
            $logger->getLogs()
        );
    }

    public function testWithNotImportableDataOnReaderSide()
    {
        $readerData = [
            $this->getReaderModel('id1'),
            $this->getReaderModel('id2'),
            $this->getReaderModel('id3'),
            $this->getReaderModel('id4'),
            $this->getReaderModel('id5'),
        ];

        $lastImportDate = new \DateTime('yesterday');

        $writerData = [
            $this->getWriterModel('id2', $lastImportDate),
        ];

        $reader = $this->getReader($readerData);
        $writer = $this->getWriter($writerData, ['id1'], ['id2'], ['id3']);
        $logger = new TestLogger();

        $importer = new Importer($reader, $writer, $logger);

        $importDate = $importer->import(2);

        self::assertInstanceOf(\DateTime::class, $importDate);

        self::assertCount(2, $writerData);
        self::assertInstanceOf(WriterModelInterface::class, $writerData[0]);
        self::assertInstanceOf(WriterModelInterface::class, $writerData[1]);

        self::assertSame('id4', $writerData[0]->getImportIdentifier());
        self::assertSame('id5', $writerData[1]->getImportIdentifier());

        self::assertSame($importDate, $writerData[0]->getLastImportDate());
        self::assertSame($importDate, $writerData[1]->getLastImportDate());

        self::assertSame(
            [
                [
                    'level' => 'info',
                    'message' => 'Import started at {importDate}',
                    'context' => [
                        'importDate' => $importDate,
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Read, offset: {offset}, limit: {limit}',
                    'context' => [
                        'offset' => 0,
                        'limit' => 2,
                    ],
                ],
                [
                    'level' => 'warning',
                    'message' => 'Model with identifier {identifier} is not importable, cause create failed',
                    'context' => [
                        'identifier' => 'id1',
                    ],
                ],
                [
                    'level' => 'warning',
                    'message' => 'Model with identifier {identifier} is not importable, cause update failed',
                    'context' => [
                        'identifier' => 'id2',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Flushed models',
                    'context' => [
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Read, offset: {offset}, limit: {limit}',
                    'context' => [
                        'offset' => 2,
                        'limit' => 2,
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Created model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id3',
                    ],
                ],
                [
                    'level' => 'warning',
                    'message' => 'Model with identifier {identifier} is not importable, cause persist failed',
                    'context' => [
                        'identifier' => 'id3',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Created model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id4',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Persisted model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id4',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Flushed models',
                    'context' => [
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Read, offset: {offset}, limit: {limit}',
                    'context' => [
                        'offset' => 4,
                        'limit' => 2,
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Created model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id5',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Persisted model with identifier {identifier}',
                    'context' => [
                        'identifier' => 'id5',
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Flushed models',
                    'context' => [
                    ],
                ],
                [
                    'level' => 'info',
                    'message' => 'Removed all outdates',
                    'context' => [
                    ],
                ],
            ],
            $logger->getLogs()
        );
    }

    /**
     * @param ReaderModelInterface[]|array $data
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|ReaderInterface
     */
    protected function getReader(array &$data)
    {
        /** @var ReaderInterface|\PHPUnit_Framework_MockObject_MockObject $reader */
        $reader = $this
            ->getMockBuilder(ReaderInterface::class)
            ->setMethods(['getModels', 'clear'])
            ->getMockForAbstractClass();

        $reader
            ->expects(self::any())
            ->method('getModels')
            ->willReturnCallback(function ($offset, $limit) use ($data) {
                return array_slice($data, $offset, $limit);
            });

        $reader
            ->expects(self::any())
            ->method('clear');

        return $reader;
    }

    /**
     * @param string $identifier
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|ReaderModelInterface|\stdClass
     */
    protected function getReaderModel($identifier)
    {
        /** @var ReaderModelInterface|\PHPUnit_Framework_MockObject_MockObject|\stdClass $readerModel */
        $readerModel = $this
            ->getMockBuilder(ReaderModelInterface::class)
            ->setMethods(['getImportIdentifier'])
            ->getMockForAbstractClass();

        $readerModel->identifier = $identifier;

        $readerModel
            ->expects(self::any())
            ->method('getImportIdentifier')
            ->willReturnCallback(function () use ($readerModel) {
                return $readerModel->identifier;
            });

        return $readerModel;
    }

    /**
     * @param WriterModelInterface[]|array $data
     * @param string[]|array               $notCreatable
     * @param string[]|array               $notUpdateable
     * @param string[]|array               $notPersistable
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|WriterInterface
     */
    protected function getWriter(
        array &$data,
        array $notCreatable = [],
        array $notUpdateable = [],
        array $notPersistable = []
    ) {
        /** @var WriterInterface|\PHPUnit_Framework_MockObject_MockObject $writer */
        $writer = $this
            ->getMockBuilder(WriterInterface::class)
            ->setMethods(['find', 'create', 'update', 'persist', 'flush', 'clear', 'removeAllOutdated'])
            ->getMockForAbstractClass();

        $writer
            ->expects(self::any())
            ->method('find')
            ->willReturnCallback(function (ReaderModelInterface $readerModel) use ($data) {
                foreach ($data as $writerModel) {
                    if ($writerModel->getImportIdentifier() === $readerModel->getImportIdentifier()) {
                        return $writerModel;
                    }
                }
            });

        $writer
            ->expects(self::any())
            ->method('create')
            ->willReturnCallback(function (ReaderModelInterface $readerModel) use ($notCreatable) {
                if (in_array($readerModel->getImportIdentifier(), $notCreatable, true)) {
                    throw new NotImportableException('create');
                }

                $writerModel = $this->getWriterModel();
                $writerModel->setImportIdentifier($readerModel->getImportIdentifier());

                return $writerModel;
            });

        $writer
            ->expects(self::any())
            ->method('update')
            ->willReturnCallback(
                function (WriterModelInterface $writerModel, ReaderModelInterface $readerModel) use ($notUpdateable) {
                    if (in_array($readerModel->getImportIdentifier(), $notUpdateable, true)) {
                        throw new NotImportableException('update');
                    }

                    return $writerModel;
                }
            );

        $writer
            ->expects(self::any())
            ->method('persist')
            ->willReturnCallback(function (WriterModelInterface $writerModel) use (&$persistCache, $notPersistable) {
                if (in_array($writerModel->getImportIdentifier(), $notPersistable, true)) {
                    throw new NotImportableException('persist');
                }
            });

        $writer
            ->expects(self::any())
            ->method('flush')
            ->willReturnCallback(function (array $writerModels) use (&$data) {
                foreach ($writerModels as $writerModel) {
                    if (!in_array($writerModel, $data, true)) {
                        $data[] = $writerModel;
                    }
                }
            });

        $writer
            ->expects(self::any())
            ->method('clear');

        $writer
            ->expects(self::any())
            ->method('removeAllOutdated')
            ->willReturnCallback(function (\DateTime $lastImportDate) use (&$data) {
                foreach ($data as $i => $writerModel) {
                    if ($writerModel->getLastImportDate()->format('YmdHis') !== $lastImportDate->format('YmdHis')) {
                        array_splice($data, $i, 1);
                    }
                }
            });

        return $writer;
    }

    /**
     * @param string|null    $identifier
     * @param \DateTime|null $lastImportDate
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|WriterModelInterface|\stdClass
     */
    protected function getWriterModel($identifier = null, \DateTime $lastImportDate = null)
    {
        /** @var WriterModelInterface|\PHPUnit_Framework_MockObject_MockObject|\stdClass $writerModel */
        $writerModel = $this
            ->getMockBuilder(WriterModelInterface::class)
            ->setMethods(['setImportIdentifier', 'getImportIdentifier', 'setLastImportDate', 'getLastImportDate'])
            ->getMockForAbstractClass();

        $writerModel->identifier = $identifier;
        $writerModel->lastImportDate = $lastImportDate;

        $writerModel
            ->expects(self::any())
            ->method('setImportIdentifier')
            ->willReturnCallback(function ($identifier) use ($writerModel) {
                $writerModel->identifier = $identifier;

            });

        $writerModel
            ->expects(self::any())
            ->method('getImportIdentifier')
            ->willReturnCallback(function () use ($writerModel) {
                return $writerModel->identifier;
            });

        $writerModel
            ->expects(self::any())
            ->method('setLastImportDate')
            ->willReturnCallback(function (\DateTime $lastImportDate) use ($writerModel) {
                $writerModel->lastImportDate = $lastImportDate;
            });

        $writerModel
            ->expects(self::any())
            ->method('getLastImportDate')
            ->willReturnCallback(function () use ($writerModel) {
                return $writerModel->lastImportDate;
            });

        return $writerModel;
    }
}

class TestLogger extends AbstractLogger
{
    /**
     * @var array
     */
    protected $logs = [];

    /**
     * @param string $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = [])
    {
        $this->logs[] = ['level' => $level, 'message' => $message, 'context' => $context];
    }

    /**
     * @return array
     */
    public function getLogs()
    {
        return $this->logs;
    }
}

# saxulum-model-importer

[![Build Status](https://api.travis-ci.org/saxulum/saxulum-model-importer.png?branch=master)](https://travis-ci.org/saxulum/saxulum-model-importer)
[![Total Downloads](https://poser.pugx.org/saxulum/saxulum-model-importer/downloads.png)](https://packagist.org/packages/saxulum/saxulum-model-importer)
[![Latest Stable Version](https://poser.pugx.org/saxulum/saxulum-model-importer/v/stable.png)](https://packagist.org/packages/saxulum/saxulum-model-importer)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/saxulum/saxulum-model-importer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/saxulum/saxulum-model-importer/?branch=master)

## Description

A simple to use model importer, as a user you do not need to check if a create or update is needed, to loop...

## Requirements

 * php: ~5.5|~7.0
 * psr/log: ~1.0

## Installation

Through [Composer](http://getcomposer.org) as [saxulum/saxulum-model-importer][1].

## Usage

### Sample Implementation using Doctrine 2 ORM

```{.php}
$em = ...

$importer = new Importer(new Reader($em), new Writer($em));
$importer->import();
```

```{.php}
class Reader implements ReaderInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @ReaderModelInterface[]|array
     */
    public function getReaderModels($offset, $limit)
    {
        $qb = $this->em->getRepository(ReaderEntity::class)->createQueryBuilder('r');
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function clearReaderModels()
    {
        $this->em->clear(ReaderEntity::class);
    }
}
```

```{.php}
class ReaderEntity implements ReaderModelInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getImportIdentifier()
    {
        return $this->getId();
    }
}
```

```{.php}
class Writer implements WriterInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param ReaderModelInterface $readerModel
     *
     * @return WriterModelInterface|null
     */
    public function findWriterModel(ReaderModelInterface $readerModel)
    {
        return $this->em->getRepository(WriterEntity::class)
            ->findOneBy(['importIdentifier' => $readerModel->getImportIdentifier()]);
    }

    /**
     * @param ReaderModelInterface $readerModel
     *
     * @return WriterModelInterface
     *
     * @throws NotImportableException
     */
    public function createWriterModel(ReaderModelInterface $readerModel)
    {
        $writerModel = new WriterEntity();
        $writerModel->setName($readerModel->getName());

        return $writerModel;
    }

    /**
     * @param WriterModelInterface $writerModel
     * @param ReaderModelInterface $readerModel
     *
     * @throws NotImportableException
     */
    public function updateWriterModel(WriterModelInterface $writerModel, ReaderModelInterface $readerModel)
    {
        $writerModel->setName($readerModel->getName());
    }

    /**
     * @param WriterModelInterface $writerModel
     *
     * @throws NotImportableException
     */
    public function persistWriterModel(WriterModelInterface $writerModel)
    {
        $this->em->persist($writerModel);
    }

    public function flushWriterModels(array $writeModels)
    {
        $this->em->flush($writeModels);
    }

    public function clearWriterModels()
    {
        $this->em->clear(WriterEntity::class);
    }

    /**
     * @param \DateTime $lastImportDate
     */
    public function removeWriterModels(\DateTime $lastImportDate)
    {
        $qb = $this->em->createQueryBuilder();
        $qb->delete(WriterEntity::class, 'w');
        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->isNull('w.lastImportDate'),
                $qb->expr()->neq('w.lastImportDate', ':lastImportDate')
            )
        );
        $qb->setParameter('lastImportDate', $lastImportDate);

        $qb->getQuery()->execute();
    }
}
```

```{.php}
class WriterEntity implements WriterModelInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $importIdentifier;

    /**
     * @var \DateTime
     */
    protected $lastImportDate;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $importIdentifier
     */
    public function setImportIdentifier($importIdentifier)
    {
        $this->importIdentifier = $importIdentifier;
    }

    /**
     * @return int
     */
    public function getImportIdentifier()
    {
        return $this->importIdentifier;
    }

    /**
     * @param \DateTime $lastImportDate
     */
    public function setLastImportDate(\DateTime $lastImportDate)
    {
        $this->lastImportDate = $lastImportDate;
    }

    /**
     * @return \DateTime
     */
    public function getLastImportDate()
    {
        return $this->lastImportDate;
    }
}
```

[1]: https://packagist.org/packages/saxulum/saxulum-model-importer

## Copyright

Dominik Zogg <dominik.zogg@gmail.com>

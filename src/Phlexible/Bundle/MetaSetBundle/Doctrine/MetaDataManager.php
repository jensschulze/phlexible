<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\DataSourceBundle\Model\DataSourceManagerInterface;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\MetaSetBundle\Entity\MetaSet;
use Phlexible\Bundle\MetaSetBundle\Model\MetaData;
use Phlexible\Bundle\MetaSetBundle\Model\MetaDataInterface;
use Phlexible\Bundle\MetaSetBundle\Model\MetaDataManagerInterface;

/**
 * Meta data manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaDataManager implements MetaDataManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var DataSourceManagerInterface
     */
    private $dataSourceManager;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @param EntityManager              $entityManager
     * @param DataSourceManagerInterface $dataSourceManager
     * @param string                     $tableName
     */
    public function __construct(EntityManager $entityManager, DataSourceManagerInterface $dataSourceManager, $tableName)
    {
        $this->entityManager = $entityManager;
        $this->dataSourceManager = $dataSourceManager;
        $this->tableName = $tableName;
    }

    /**
     * {@inheritdoc}
     */
    public function findByMetaSetAndIdentifiers(MetaSet $metaSet, array $identifiers)
    {
        $metaDatas = $this->doFindByMetaSetAndIdentifiers($metaSet, $identifiers);

        if (!count($metaDatas)) {
            return null;
        }

        return current($metaDatas);
    }

    /**
     * {@inheritdoc}
     */
    public function findByMetaSet(MetaSet $metaSet)
    {
        return $this->doFindByMetaSetAndIdentifiers($metaSet);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->doFindByMetaSetAndIdentifiers();
    }

    /**
     * {@inheritdoc}
     */
    public function createMetaData(MetaSet $metaSet)
    {
        $metaData = new MetaData();
        $metaData->setMetaSet($metaSet);

        return $metaData;
    }

    /**
     * @param MetaDataInterface $metaData
     */
    public function updateMetaData(MetaDataInterface $metaData)
    {
        $baseData = array(
            'set_id' => $metaData->getMetaSet()->getId(),
        );
        foreach ($metaData->getIdentifiers() as $field => $value) {
            $baseData[$field] = $value;
        }

        $connection = $this->entityManager->getConnection();

        foreach ($metaData->getLanguages() as $language) {
            foreach ($metaData->getMetaSet()->getFields() as $field) {

                // TODO: löschen?
                if (!$metaData->get($field->getName(), $language)) {
                    continue;
                }

                $value = $metaData->get($field->getName(), $language);

                if ('suggest' === $field->getType()) {
                    $dataSourceId = $field->getOptions();
                    $dataSource = $this->dataSourceManager->find($dataSourceId);
                    foreach (explode(',', $value) as $singleValue) {
                        $dataSource->addValueForLanguage($language, $singleValue, true);
                    }
                    $this->dataSourceManager->updateDataSource($dataSource);
                }

                $insertData = $baseData;

                $insertData['id'] = Uuid::generate();
                $insertData['field_id'] = $field->getId();
                $insertData['value'] = $value;
                $insertData['language'] = $language;

                $connection->insert($this->tableName, $insertData);
            }
        }

        // TODO: job!
        //$this->_queueDataSourceCleanup();
    }

    /**
     * @param MetaSet $metaSet
     * @param array   $identifiers
     *
     * @return MetaData[]
     */
    private function doFindByMetaSetAndIdentifiers(MetaSet $metaSet = null, array $identifiers = array())
    {
        $connection = $this->entityManager->getConnection();

        $qb = $connection->createQueryBuilder();
        $qb
            ->select('m.*')
            ->from($this->tableName, 'm');

        if ($metaSet) {
            $qb->where($qb->expr()->eq('m.set_id', $qb->expr()->literal($metaSet->getId())));
        }

        foreach ($identifiers as $field => $value) {
            $qb->andWhere($qb->expr()->eq("m.$field", $qb->expr()->literal($value)));
        }

        $rows = $connection->fetchAll($qb->getSQL());

        $metaDatas = array();

        foreach ($rows as $row) {
            $id = '';
            foreach ($identifiers as $value) {
                $id .= $value . '_';
            }
            $id .= $row['set_id'];

            if (!isset($metaDatas[$id])) {
                $metaData = new MetaData();
                $metaData
                    ->setIdentifiers($identifiers)
                    ->setMetaSet($metaSet);
                $metaDatas[$id] = $metaData;
            } else {
                $metaData = $metaDatas[$id];
            }

            $metaData->set($row['field_id'], $row['value'], $row['language']);
        }

        return $metaDatas;
    }

}
<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller\Tree;

use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;
use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeStructureNode;
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeVersion;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Tree saver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeSaver
{
    /**
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @param ElementtypeService $elementtypeService
     */
    public function __construct(ElementtypeService $elementtypeService)
    {
        $this->elementtypeService = $elementtypeService;
    }

    /**
     * Save an Element Type data tree
     *
     * @param Request       $request
     * @param UserInterface $user
     *
     * @return ElementtypeVersion
     */
    public function save(Request $request, UserInterface $user)
    {
        $elementtypeId = $request->get('element_type_id', false);
        $data = json_decode($request->get('data'), true);

        if (!$elementtypeId) {
            throw new \Exception('No elementtype ID.');
        }

        $rootData = $data[0];
        $rootType = $rootData['type'];
        $rootProperties = $rootData['properties'];
        $rootConfig = $rootProperties['root'];
        $rootMappings = !empty($rootProperties['mappings']) ? $rootProperties['mappings'] : null;
        $rootDsId = !empty($rootData['ds_id']) ? $rootData['ds_id'] : Uuid::generate();

        if (!isset($rootData['type']) || ($rootData['type'] != 'root' && $rootData['type'] != 'referenceroot')) {
            throw new \Exception('Invalid root node.');
        }

        if (!isset($rootConfig['unique_id']) || !trim($rootConfig['unique_id'])) {
            throw new \Exception('No unique ID.');
        }

        $uniqueId = trim($rootConfig['unique_id']);
        $title = trim($rootConfig['title']);
        $icon = trim($rootConfig['icon']);
        $hideChildren = !empty($rootConfig['hide_children']);
        $defaultTab = strlen($rootConfig['default_tab']) ? $rootConfig['default_tab'] : null;
        $defaultContentTab = strlen($rootConfig['default_content_tab']) ? $rootConfig['default_content_tab'] : null;
        $comment = trim($rootConfig['comment']) ?: null;

        $elementtype = $this->elementtypeService->findElementtype($elementtypeId);
        $priorElementtypeVersion = $this->elementtypeService->findLatestElementtypeVersion($elementtype);

        $elementtypeVersion = clone $priorElementtypeVersion;
        $elementtypeVersion
            ->setVersion($elementtypeVersion->getVersion() + 1)
            ->setDefaultContentTab($defaultContentTab)
            ->setMappings($rootMappings)
            ->setComment($comment)
            ->setCreateUserId($user->getId())
            ->setCreatedAt(new \DateTime());

        $elementtype
            ->setUniqueId($uniqueId)
            ->setTitle($title)
            ->setIcon($icon)
            ->setHideChildren($hideChildren)
            ->setDefaultTab($defaultTab)
            ->setLatestVersion($elementtypeVersion->getVersion());

        $fieldData = $rootData['children'];
        $elementtypeStructure = $this->buildElementtypeStructure($elementtypeVersion, $rootType, $rootDsId, $user, $fieldData);

        $this->elementtypeService->updateElementtype($elementtype, false);
        $this->elementtypeService->updateElementtypeVersion($elementtypeVersion, false);
        $this->elementtypeService->updateElementtypeStructure($elementtypeStructure, false);

        /*
        // update elementtypes that use this elementtype as reference

        $updateData = array(
            'reference_version' => $version,
        );
        $db->update($db->prefix.'elementtype_structure', $updateData, 'reference_id = '.$db->quote($elementtypeId));

        $select = $db->select()
                     ->distinct()
                     ->from($db->prefix . 'elementtype_structure', array('id', 'element_type_id', 'version'))
                     ->where('reference_id = ?', $elementtypeId);

        $candidates = $db->fetchAll($select);

        $select = $db->select()
                     ->from($db->prefix . 'elementtype_version', new Zend_Db_Expr('MAX(version)'))
                     ->where('element_type_id = ?');

        foreach ($candidates as $row)
        {
            $maxVersion = $db->fetchOne($select, $row['element_type_id']);

            if ($row['version'] != $maxVersion)
            {
                continue;
            }

            $newElementVersion = $manager->copyVersion($row['element_type_id'], $row['version'], null, null, true);

            $db->update($db->prefix . 'elementtype_structure', array(
                'reference_version' => $version,
            ), 'reference_id = '.$db->quote($elementtypeId).' AND
                element_type_id = '.$db->quote($row['element_type_id']).' AND
                version = '.$db->quote($newElementVersion));
        }
        */

        return $elementtypeVersion;
    }

    /**
     * @param ElementtypeVersion $elementtypeVersion
     * @param string             $rootType
     * @param string             $rootDsId
     * @param UserInterface      $user
     * @param array              $data
     *
     * @return ElementtypeStructure
     */
    private function buildElementtypeStructure(ElementtypeVersion $elementtypeVersion, $rootType, $rootDsId, UserInterface $user, array $data)
    {
        $elementtype = $elementtypeVersion->getElementtype();

        $elementtypeStructure = new ElementtypeStructure();
        $elementtypeStructure
            ->setElementtypeVersion($elementtypeVersion);

        $sort = 1;

        $rootNode = new ElementtypeStructureNode();
        $rootNode
            ->setElementtype($elementtype)
            ->setVersion($elementtypeVersion->getVersion())
            ->setElementtypeStructure($elementtypeStructure)
            ->setDsId($rootDsId)
            ->setType($rootType)
            ->setName('root')
            ->setSort($sort);

        $elementtypeStructure->addNode($rootNode);

        $this->iterateData($elementtypeVersion, $elementtypeStructure, $rootNode, $user, $sort, $data);

        return $elementtypeStructure;
    }

    private function iterateData(ElementtypeVersion $elementtypeVersion, ElementtypeStructure $elementtypeStructure, ElementtypeStructureNode $rootNode, UserInterface $user, $sort, array $data)
    {
        foreach ($data as $row) {
            if (!$row['parent_ds_id']) {
                $row['parent_ds_id'] = $rootNode->getDsId();
            }
            $node = new ElementtypeStructureNode();
            $parentNode = $elementtypeStructure->getNode($row['parent_ds_id']);

            $node
                ->setElementtype($elementtypeVersion->getElementtype())
                ->setVersion($elementtypeVersion->getVersion())
                ->setElementtypeStructure($elementtypeStructure)
                ->setDsId(!empty($row['ds_id']) ? $row['ds_id'] : Uuid::generate())
                ->setParentDsId($parentNode->getDsId())
                ->setParentNode($parentNode)
                ->setSort(++$sort);

            if ($row['type'] == 'reference' && isset($row['reference']['new'])) {
                $firstChild = $row['children'][0];
                $referenceElementtypeVersion = $this->elementtypeService->createElementtype(
                    'reference',
                    'reference_' . $firstChild['properties']['field']['working_title'] . '_' . uniqid(),
                    'Reference ' . $firstChild['properties']['field']['working_title'],
                    '_fallback.gif',
                    $user->getId(),
                    false
                );
                $referenceElementtype = $referenceElementtypeVersion->getElementtype();
                $referenceRootDsId = Uuid::generate();
                foreach ($row['children'] as $index => $referenceRow) {
                    $row['children'][$index]['parent_ds_id'] = $referenceRootDsId;
                }
                $referenceElementtypeStructure = $this->buildElementtypeStructure(
                    $referenceElementtypeVersion,
                    'referenceroot',
                    $referenceRootDsId,
                    $user,
                    $row['children']
                );

                $this->elementtypeService->updateElementtypeStructure($referenceElementtypeStructure, false);

                $node
                    ->setType('reference')
                    ->setName('reference_' . $referenceElementtype->getId())
                    ->setReferenceElementtype($referenceElementtype)
                    ->setReferenceVersion($referenceElementtypeVersion->getVersion());

                $elementtypeStructure->addNode($node);
            } elseif ($row['type'] == 'reference') {
                $referenceElementtype = $this->elementtypeService->findElementtype($row['reference']['refID']);

                $node
                    ->setType('reference')
                    ->setName('reference_' . $referenceElementtype->getId())
                    ->setReferenceElementtype($referenceElementtype)
                    ->setReferenceVersion($row['reference']['refVersion']);

                $elementtypeStructure->addNode($node);
            } else {
                $properties = $row['properties'];

                $node
                    ->setType($properties['field']['type'])
                    ->setName(trim($properties['field']['working_title']))
                    ->setComment(trim($properties['field']['comment']) ?: null)
                    ->setConfiguration(!empty($properties['configuration']) ? $properties['configuration'] : null)
                    ->setValidation(!empty($properties['validation']) ? $properties['validation'] : null)
                    ->setLabels(!empty($properties['labels']) ? $properties['labels'] : null)
                    ->setOptions(!empty($properties['options']) ? $properties['options'] : null)
                    ->setContentChannels(
                        !empty($properties['content_channels']) ? $properties['content_channels'] : null
                    );

                $elementtypeStructure->addNode($node);

                if (!empty($row['children'])) {
                    $sort = $this->iterateData($elementtypeVersion, $elementtypeStructure, $rootNode, $user, $sort, $row['children']);
                }
            }
        }

        return $sort;
    }
}
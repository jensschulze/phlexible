<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Icon;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Icon resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class IconResolver
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @param RouterInterface        $router
     * @param ElementService         $elementService
     * @param TeaserManagerInterface $teaserManager
     */
    public function __construct(
        RouterInterface $router,
        ElementService $elementService,
        TeaserManagerInterface $teaserManager)
    {
        $this->router = $router;
        $this->elementService = $elementService;
        $this->teaserManager = $teaserManager;
    }

    /**
     * Resolve icon
     *
     * @param string $icon
     *
     * @return string
     */
    public function resolveIcon($icon)
    {
        return '/bundles/phlexibleelementtype/elementtypes/' . $icon;
    }

    /**
     * Resolve element type to icon
     *
     * @param Elementtype $elementtype
     *
     * @return string
     */
    public function resolveElementtype(Elementtype $elementtype)
    {
        $icon = $elementtype->getIcon();

        return $this->resolveIcon($icon);
    }

    /**
     * Resolve element to icon
     *
     * @param Element $element
     *
     * @return string
     */
    public function resolveElement(Element $element)
    {
        $elementtype = $this->elementService->findElementtype($element);

        return $this->resolveElementtype($elementtype);
    }

    /**
     * Resolve tree node to icon
     *
     * @param TreeNodeInterface $treeNode
     * @param string            $language
     *
     * @return string
     */
    public function resolveTreeNode(TreeNodeInterface $treeNode, $language)
    {
        $parameters = [];

        if (!$treeNode->isRoot()) {
            $tree = $treeNode->getTree();

            if ($tree->isPublished($treeNode, $language)) {
                $parameters['status'] = $tree->isAsync($treeNode, $language) ? 'async': 'online';
            }

            if ($tree->isInstance($treeNode)) {
                $parameters['instance'] = $tree->isInstanceMaster($treeNode) ? 'master' : 'slave';
            }

            if ($treeNode->getSortMode() !== TreeInterface::SORT_MODE_FREE) {
                $parameters['sort'] = $treeNode->getSortMode() . '_' . $treeNode->getSortDir();
            }
        }

        $element = $this->elementService->findElement($treeNode->getTypeId());

        if (!count($parameters)) {
            return $this->resolveElement($element);
        }

        $elementtype = $this->elementService->findElementtype($element);

        $parameters['icon'] = $elementtype->getIcon();

        return $this->router->generate('elements_icon', $parameters);
    }

    /**
     * Resolve teaser to icon
     *
     * @param Teaser $teaser
     * @param string $language
     *
     * @return string
     */
    public function resolveTeaser(Teaser $teaser, $language)
    {
        $parameters = [];

        if ($this->teaserManager->isPublished($teaser, $language)) {
            $parameters['status'] = $this->teaserManager->isAsync($teaser, $language) ? 'async': 'online';
        }

        if ($this->teaserManager->isInstance($teaser)) {
            $parameters['instance'] = $this->teaserManager->isInstanceMaster($teaser) ? 'master' : 'slave';
        }

        $element = $this->elementService->findElement($teaser->getTypeId());

        if (!count($parameters)) {
            return $this->resolveElement($element);
        }

        $elementtype = $this->elementService->findElementtype($element);

        $parameters['icon'] = $elementtype->getIcon();

        return $this->router->generate('elements_icon', $parameters);
    }
}

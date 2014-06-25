<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendAssetBundle\Collector;

/**
 * Collector interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface CollectorInterface
{
    /**
     * Collect
     *
     * @return BlockCollection
     */
    public function collect();
}

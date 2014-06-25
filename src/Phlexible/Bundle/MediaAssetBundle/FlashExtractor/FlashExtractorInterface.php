<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\FlashExtractor;

/**
 * Flash extractor interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface FlashExtractorInterface
{
    /**
     * Check if requirements for flash extractor are given
     *
     * @return boolean
     */
    public function isAvailable();

    /**
     * Check if extractor supports the given asset
     *
     * @param Asset $asset
     * @return boolean
     */
    public function supports(Asset $asset);

    /**
     * Extract flash from file
     *
     * @param Asset $asset
     * @return string
     */
    public function extract(Asset $asset);
}

<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\ImageExtractor;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Image extractor interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ImageExtractorInterface
{
    /**
     * Check if requirements for image extractor are given
     *
     * @return boolean
     */
    public function isAvailable();

    /**
     * Check if extractor supports the given asset
     *
     * @param FileInterface $file
     *
     * @return boolean
     */
    public function supports(FileInterface $file);

    /**
     * Extract image from file
     *
     * @param FileInterface $file
     *
     * @return string
     */
    public function extract(FileInterface $file);
}

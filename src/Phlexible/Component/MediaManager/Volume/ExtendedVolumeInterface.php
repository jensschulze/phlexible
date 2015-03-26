<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaManager\Volume;

use Phlexible\Component\Volume\VolumeInterface;

/**
 * Extended volume interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ExtendedVolumeInterface extends VolumeInterface
{
    /**
     * @param ExtendedFolderInterface $folder
     * @param array                   $metasets
     * @param string                  $userId
     *
     * @return ExtendedFolderInterface
     */
    public function setFolderMetasets(ExtendedFolderInterface $folder, array $metasets, $userId);

    /**
     * @param ExtendedFileInterface $file
     * @param array                 $metasets
     * @param string                $userId
     *
     * @return ExtendedFileInterface
     */
    public function setFileMetasets(ExtendedFileInterface $file, array $metasets, $userId);

    /**
     * @param ExtendedFileInterface $file
     * @param string                $mediaType
     * @param string                $userId
     *
     * @return ExtendedFileInterface
     */
    public function setFileMediaType(ExtendedFileInterface $file, $mediaType, $userId);
}
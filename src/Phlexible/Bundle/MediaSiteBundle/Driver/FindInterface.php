<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\Folder\FolderInterface;

/**
 * Find interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface FindInterface {

    /**
     * @return FolderInterface
     */
    public function findRootFolder();

    /**
     * @param string $id
     *
     * @return FolderInterface
     */
    public function findFolder($id);

    /**
     * @param int $fileId
     *
     * @return FolderInterface
     */
    public function findFolderByFileId($fileId);

    /**
     * @param string $path
     *
     * @return FolderInterface
     */
    public function findFolderByPath($path);

    /**
     * @param FolderInterface $parentFolder
     *
     * @return FolderInterface[]
     */
    public function findFoldersByParentFolder(FolderInterface $parentFolder);

    /**
     * @param FolderInterface $parentFolder
     *
     * @return int
     */
    public function countFoldersByParentFolder(FolderInterface $parentFolder);

    /**
     * @param int $id
     * @param int $version
     *
     * @return FileInterface
     */
    public function findFile($id, $version = 1);

    /**
     * @param string $path
     * @param int    $version
     *
     * @return FileInterface
     */
    public function findFileByPath($path, $version = 1);

    /**
     * @param int $id
     *
     * @return FileInterface[]
     */
    public function findFileVersions($id);

    /**
     * @param FolderInterface $folder
     * @param string          $order
     * @param int             $limit
     * @param int             $start
     * @param bool            $includeHidden
     *
     * @return FileInterface[]
     */
    public function findFilesByFolder(FolderInterface $folder, $order = null, $limit = null, $start = null, $includeHidden = false);

    /**
     * @param FolderInterface $folder
     *
     * @return int
     */
    public function countFilesByFolder(FolderInterface $folder);

    /**
     * @param int $limit
     *
     * @return FileInterface[]
     */
    public function findLatestFiles($limit = 20);
}
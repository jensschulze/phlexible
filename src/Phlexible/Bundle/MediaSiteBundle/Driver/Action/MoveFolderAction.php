<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver\Action;

use Phlexible\Bundle\MediaSiteBundle\Folder\FolderInterface;

/**
 * Move folder action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MoveFolderAction extends FolderAction
{
    /**
     * @var FolderInterface
     */
    private $folder;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $userId;

    /**
     * @param FolderInterface $folder
     * @param FolderInterface $targetFolder
     * @param \DateTime       $date
     * @param string          $userId
     */
    public function __construct(FolderInterface $folder, FolderInterface $targetFolder, \DateTime $date, $userId)
    {
        parent::__construct($folder);

        $this->targetFolder = $targetFolder;
        $this->date = $date;
        $this->userId = $userId;
    }

    /**
     * @return FolderInterface
     */
    public function getTargetFolder()
    {
        return $this->folder;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    public function getUserId()
    {
        return $this->userId;
    }
}

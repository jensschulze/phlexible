<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\DeleteFolderAction;

/**
 * Before delete folder event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BeforeDeleteFolderEvent extends AbstractActionEvent
{
    /**
     * @return DeleteFolderAction
     */
    public function getAction()
    {
        return parent::getAction();
    }
}
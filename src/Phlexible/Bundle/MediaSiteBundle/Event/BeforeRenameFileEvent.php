<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\RenameFileAction;

/**
 * Before rename file event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BeforeRenameFileEvent extends AbstractActionEvent
{
    /**
     * @return RenameFileAction
     */
    public function getAction()
    {
        return parent::getAction();
    }
}
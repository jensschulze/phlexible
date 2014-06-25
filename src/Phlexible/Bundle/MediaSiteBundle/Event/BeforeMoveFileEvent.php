<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\MoveFileAction;

/**
 * Before move file event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BeforeMoveFileEvent extends AbstractActionEvent
{
    /**
     * @return MoveFileAction
     */
    public function getAction()
    {
        return parent::getAction();
    }
}
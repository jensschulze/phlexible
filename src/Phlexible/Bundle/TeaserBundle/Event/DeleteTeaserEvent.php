<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Event;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;

/**
 * Delete teaser event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DeleteTeaserEvent extends TeaserEvent
{
    /**
     * @var int
     */
    private $teaserId;

    /**
     * @var string
     */
    private $userId;

    /**
     * @param Teaser $teaser
     * @param int    $teaserId
     * @param string $userId
     */
    public function __construct(Teaser $teaser, $teaserId, $userId)
    {
        parent::__construct($teaser);

        $this->teaserId = $teaserId;
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getTeaserId()
    {
        return $this->teaserId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
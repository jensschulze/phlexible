<?php
/**
 * MAKEweb
 *
 * PHP Version 5
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Generator.php 2312 2007-01-25 18:46:27Z swentz $
 */

/**
 * Stop Inherit Inherited Teaser Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_StopInheritInheritedTeaser extends Makeweb_Teasers_Event_BeforeStopInheritInheritedTeaser
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::STOP_INHERIT_INHERITED_TEASER;

    /**
     * @var integer
     */
    protected $_stopInheritId = null;

    /**
     * Constructor
     *
     * @param integer $treeId
     * @param integer $eid
     * @param integer $teaserEid
     * @param ineger  $layoutarreaId
     * @param integer $stopInheritId
     */
    public function __construct($treeId, $eid, $teaserEid, $layoutarreaId, $stopInheritId)
    {
        parent::__construct($treeId, $eid, $teaserEid, $layoutarreaId);

        $this->_stopInheritId = $stopInheritId;
    }

    /**
     * Return tree ID
     *
     * @return integer
     */
    public function getStopInheritId()
    {
        return $this->_stopInheritId;
    }
}

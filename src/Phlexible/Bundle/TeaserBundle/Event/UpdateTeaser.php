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
 * Update Teaser Event
 *
 * @category    MAKEweb
 * @package     Makeweb_Teasers
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Teasers_Event_UpdateTeaser extends Makeweb_Teasers_Event_BeforeUpdateTeaser
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Teasers_Event::UPDATE_TEASER;
}

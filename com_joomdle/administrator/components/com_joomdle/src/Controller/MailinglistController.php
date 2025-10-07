<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Administrator\Controller;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomdle\Component\Joomdle\Administrator\Helper\MailinglistHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Component\ComponentHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\ShopHelper;

/**
 * Mailinglist controller.
 *
 * @since  1.0.0
 */
class MailinglistController extends BaseController
{
    public function studentspublish()
    {
        if (!Factory::getApplication()->getSession()->checkToken()) {
            exit(Text::_('JINVALID_TOKEN'));
        }

        $cid = $this->input->get('cid', array ());


        if (count($cid) < 1) {
            $error = Text::_('COM_JOOMDLE_WARNING_MUST_SELECT');
            Factory::getApplication()->enqueueMessage($error, 'error');
            return;
        }

        MailinglistHelper::saveListsStudents ($cid);

        $this->setMessage(Text::_('COM_JOOMDLE_MAILING_LIST_PUBLISHED'));
        $this->setRedirect( 'index.php?option=com_joomdle&view=mailinglist' );
    }

    public function studentsunpublish()
    {
        if (!Factory::getApplication()->getSession()->checkToken()) {
            exit(Text::_('JINVALID_TOKEN'));
        }

        $cid   = $this->input->get('cid', array ());

        if (count($cid) < 1) {
            $error = Text::_('COM_JOOMDLE_WARNING_MUST_SELECT');
            Factory::getApplication()->enqueueMessage($error, 'error');
            return;
        }

        MailinglistHelper::deleteMailingLists ($cid, 'course_students');

        $this->setMessage(Text::_('COM_JOOMDLE_MAILING_LIST_UNPUBLISHED'));
        $this->setRedirect( 'index.php?option=com_joomdle&view=mailinglist' );
    }

    public function teacherspublish()
    {
        if (!Factory::getApplication()->getSession()->checkToken()) {
            exit(Text::_('JINVALID_TOKEN'));
        }

        $cid = $this->input->get('cid', array ());

        if (count($cid) < 1) {
            $error = Text::_('COM_JOOMDLE_WARNING_MUST_SELECT');
            Factory::getApplication()->enqueueMessage($error, 'error');
            return;
        }

        MailinglistHelper::saveListsTeachers ($cid);

        $this->setMessage(Text::_('COM_JOOMDLE_MAILING_LIST_PUBLISHED'));
        $this->setRedirect( 'index.php?option=com_joomdle&view=mailinglist' );
    }

    public function teachersunpublish()
    {
        if (!Factory::getApplication()->getSession()->checkToken()) {
            exit(Text::_('JINVALID_TOKEN'));
        }

        $cid   = $this->input->get('cid', array ());

        if (count($cid) < 1) {
            $error = Text::_('COM_JOOMDLE_WARNING_MUST_SELECT');
            Factory::getApplication()->enqueueMessage($error, 'error');
            return;
        }

        MailinglistHelper::delete ($cid, 'course_teachers');

        $this->setMessage(Text::_('COM_JOOMDLE_MAILING_LIST_UNPUBLISHED'));
        $this->setRedirect( 'index.php?option=com_joomdle&view=mailinglist' );
    }
}

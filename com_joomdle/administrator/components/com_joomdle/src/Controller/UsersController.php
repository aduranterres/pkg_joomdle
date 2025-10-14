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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Utilities\ArrayHelper;

/**
 * Users controller.
 *
 * @since  1.0.0
 */
class UsersController extends BaseController
{
    public function addtomoodle()
    {
        if (!Factory::getApplication()->getSession()->checkToken()) {
            exit(Text::_('JINVALID_TOKEN'));
        }

        $cid   = $this->input->get('cid', array ());
        ArrayHelper::toInteger($cid);

        if (count($cid) < 1) {
            $error = Text::_('COM_JOOMDLE_WARNING_MUST_SELECT');
            Factory::getApplication()->enqueueMessage($error, 'error');
            return;
        }
        $this->getModel()->addMoodleUsers($cid);

        $this->setMessage(Text::_('COM_JOOMDLE_USERS_ADDED_TO_MOODLE'));
        $this->setRedirect('index.php?option=com_joomdle&view=users');
    }

    public function addtojoomla()
    {
        if (!Factory::getApplication()->getSession()->checkToken()) {
            exit(Text::_('JINVALID_TOKEN'));
        }

        $cid   = $this->input->get('cid', array ());
        ArrayHelper::toInteger($cid);

        if (count($cid) < 1) {
            $error = Text::_('COM_JOOMDLE_WARNING_MUST_SELECT');
            Factory::getApplication()->enqueueMessage($error, 'error');
            return;
        }
        if ($this->getModel()->addJoomlaUsers($cid)) {
            $this->setMessage(Text::_('COM_JOOMDLE_USERS_ADDED_TO_JOOMLA'));
        }

        $this->setRedirect('index.php?option=com_joomdle&view=users');
    }

    public function migratetojoomdle()
    {
        if (!Factory::getApplication()->getSession()->checkToken()) {
            exit(Text::_('JINVALID_TOKEN'));
        }

        $cid   = $this->input->get('cid', array ());
        ArrayHelper::toInteger($cid);

        if (count($cid) < 1) {
            $error = Text::_('COM_JOOMDLE_WARNING_MUST_SELECT');
            Factory::getApplication()->enqueueMessage($error, 'error');
            return;
        }
        $this->getModel()->migrateUsersToJoomdle($cid);

        $this->setMessage(Text::_('COM_JOOMDLE_USERS_MIGRATED_TO_JOOMDLE'));
        $this->setRedirect('index.php?option=com_joomdle&view=users');
    }

    public function syncprofiletomoodle()
    {
        if (!Factory::getApplication()->getSession()->checkToken()) {
            exit(Text::_('JINVALID_TOKEN'));
        }

        $cid   = $this->input->get('cid', array ());
        ArrayHelper::toInteger($cid);

        if (count($cid) < 1) {
            $error = Text::_('COM_JOOMDLE_WARNING_MUST_SELECT');
            Factory::getApplication()->enqueueMessage($error, 'error');
            return;
        }
        $this->getModel()->syncMoodleProfiles($cid);

        $this->setMessage(Text::_('COM_JOOMDLE_PROFILE_SYNCED_TO_MOODLE'));
        $this->setRedirect('index.php?option=com_joomdle&view=users');
    }

    public function syncprofiletojoomla()
    {
        if (!Factory::getApplication()->getSession()->checkToken()) {
            exit(Text::_('JINVALID_TOKEN'));
        }

        $cid   = $this->input->get('cid', array ());
        ArrayHelper::toInteger($cid);

        if (count($cid) < 1) {
            $error = Text::_('COM_JOOMDLE_WARNING_MUST_SELECT');
            Factory::getApplication()->enqueueMessage($error, 'error');
            return;
        }
        $this->getModel()->syncJoomlaProfiles($cid);

        $this->setMessage(Text::_('COM_JOOMDLE_PROFILE_SYNCED_TO_JOOMLA'));
        $this->setRedirect('index.php?option=com_joomdle&view=users');
    }
}

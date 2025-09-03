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
use Joomla\CMS\Component\ComponentHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\ShopHelper;

/**
 * Shop controller.
 *
 * @since  1.0.0
 */
class ShopController extends BaseController
{
    public function publish()
    {
        if (!Factory::getApplication()->getSession()->checkToken()) {
            exit(Text::_('JINVALID_TOKEN'));
        }

        // Check that a shop category is set
        $params = ComponentHelper::getParams('com_joomdle');
        if ($params->get('courses_category') == 'no') {
            $error = Text::_('COM_JOOMDLE_SHOP_CATEGORY_NOT_SET');
            Factory::getApplication()->enqueueMessage($error, 'error');
            $this->setRedirect('index.php?option=com_joomdle&view=shop');
            return;
        }

        $cid = $this->input->get('cid', array ());

        if (count($cid) < 1) {
            $error = Text::_('COM_JOOMDLE_WARNING_MUST_SELECT');
            Factory::getApplication()->enqueueMessage($error, 'error');
            return;
        }

        ShopHelper::publishCourses($cid);

        $this->setMessage(Text::_('COM_JOOMDLE_SHOP_COURSES_PUBLISHED'));
        $this->setRedirect('index.php?option=com_joomdle&view=shop');
    }

    public function unpublish()
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

        ShopHelper::dontSellCourses($cid);

        $this->setMessage(Text::_('COM_JOOMDLE_SHOP_COURSES_UNPUBLISHED'));
        $this->setRedirect('index.php?option=com_joomdle&view=shop');
    }

    public function reload()
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

        ShopHelper::reloadCourses($cid);

        $this->setMessage(Text::_('COM_JOOMDLE_SHOP_COURSES_RELOADED'));
        $this->setRedirect('index.php?option=com_joomdle&view=shop');
    }

    public function delete()
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

        ShopHelper::deleteCourses($cid);

        $this->setMessage(Text::_('COM_JOOMDLE_SHOP_COURSES_DELETED'));
        $this->setRedirect('index.php?option=com_joomdle&view=shop');
    }
}

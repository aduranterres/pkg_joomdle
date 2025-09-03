<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Administrator\View\Users;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomdle\Component\Joomdle\Administrator\Helper\JoomdleHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\MVC\View\GenericDataException;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * View class for a list of Users.
 *
 * @since  2.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected $items;

    protected $pagination;

    protected $state;

    /**
     * Display the view
     *
     * @param   string  $tpl  Template name
     *
     * @return void
     *
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        $this->sidebar = Sidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   2.0.0
     */
    protected function addToolbar()
    {
        $canDo = JoomdleHelper::getActions();

        ToolbarHelper::title(Text::_('COM_JOOMDLE_TITLE_USERS'), "generic");

        $toolbar    = $this->getDocument()->getToolbar();

        $toolbar->standardButton('addtojoomla')
            ->text('COM_JOOMDLE_ADD_USERS_TO_JOOMLA')
            ->task('users.addtojoomla')
            ->icon('fas fa-plus')
            ->listCheck(true);
        $toolbar->standardButton('addtomoodle')
            ->text('COM_JOOMDLE_ADD_USERS_TO_MOODLE')
            ->task('users.addtomoodle')
            ->icon('fas fa-plus')
            ->listCheck(true);
        $toolbar->standardButton('migratetojoomdle')
            ->text('COM_JOOMDLE_MIGRATE_USERS_TO_JOOMDLE')
            ->task('users.migratetojoomdle')
            ->icon('fas fa-check')
            ->listCheck(true);

        $dropdown = $toolbar->dropdownButton('status-group')
            ->text('JTOOLBAR_CHANGE_STATUS')
            ->toggleSplit(false)
            ->icon('fas fa-ellipsis-h')
            ->buttonClass('btn btn-action')
            ->listCheck(true);

        $childBar = $dropdown->getChildToolbar();

        $childBar->standardButton('syncprofiletomoodle')
            ->text('COM_JOOMDLE_SYNC_MOODLE_PROFILES')
            ->task('users.syncprofiletomoodle')
            ->icon('fas fa-forward')
            ->listCheck(true);
        $childBar->standardButton('syncprofiletojoomla')
            ->text('COM_JOOMDLE_SYNC_JOOMLA_PROFILES')
            ->task('users.syncprofiletojoomla')
            ->icon('fas fa-forward')
            ->listCheck(true);
        $toolbar->standardButton('uploadpasswordfile')
            ->text('COM_JOOMDLE_UPLOAD_PASSWORD_FILE')
            ->task('uploadpasswords.uploadpasswordfile')
            ->icon('fas fa-upload');

        if ($canDo->get('core.admin')) {
            $toolbar->preferences('com_joomdle');
        }

        // Set sidebar action
        Sidebar::setAction('index.php?option=com_joomdle&view=users');
    }
}

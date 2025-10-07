<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Administrator\View\Shop;

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
 * View class for a list of mappings.
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

        ToolbarHelper::title(Text::_('COM_JOOMDLE_TITLE_SHOP'), "generic");

        $toolbar    = $this->getDocument()->getToolbar();

        if ($canDo->get('core.create')) {
            $toolbar->addNew('bundle.add')
                ->text('COM_JOOMDLE_NEW_BUNDLE');
        }

        if ($canDo->get('core.delete')) {
            $toolbar->delete('shop.delete')
                ->text('JTOOLBAR_DELETE')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }

        $toolbar->standardButton('shop.reload')
            ->text('COM_JOOMDLE_RELOAD_FROM_MOODLE')
            ->task('shop.reload')
            ->icon('fas fa-check')
            ->listCheck(true);

        if ($canDo->get('core.admin')) {
            $toolbar->preferences('com_joomdle');
        }

        // Set sidebar action
        Sidebar::setAction('index.php?option=com_joomdle&view=shop');
    }
}

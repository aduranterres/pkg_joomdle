<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Administrator\View\Config;

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

    protected $fieldsets;

    protected $formControl;

    protected $form;

    protected $data;

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
        $this->form = $this->get('Form');
        $this->data = $this->get('Data');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->fieldsets   = $this->form ? $this->form->getFieldsets() : null;
        $this->formControl = $this->form ? $this->form->getFormControl() : null;

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

        ToolbarHelper::title(Text::_('COM_JOOMDLE_TITLE_CONFIG'), "cog config");

        $toolbar = $this->getDocument()->getToolbar();

        if ($canDo->get('core.edit') || ($canDo->get('core.create'))) {
            ToolbarHelper::apply('config.apply', 'JTOOLBAR_APPLY');
            ToolbarHelper::cancel('config.cancel');

            $toolbar->standardButton('regeneratejoomlatoken')
                ->text('COM_JOOMDLE_REGENERATE_JOOMLA_TOKEN')
                ->task('config.regeneratejoomlatoken')
                ->icon('fas fa-refresh')
                ->listCheck(false);
        }

        // Set sidebar action
        Sidebar::setAction('index.php?option=com_joomdle&view=config');
    }
}

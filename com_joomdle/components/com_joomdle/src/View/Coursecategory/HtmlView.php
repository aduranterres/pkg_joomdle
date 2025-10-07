<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Site\View\Coursecategory;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * HTML Joomdle view.
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected $items;

    protected $pagination;

    protected $state;

    protected $params;

    protected $pageclass_sfx = '';

    protected $courses;

    protected $categories;

    protected $cat_name;

    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void|boolean
     *
     * @throws \Exception
     * @since  4.0.0
     */
    public function display($tpl = null)
    {
        // Get model data.
        $this->courses = $this->get('Courses');
        $this->categories = $this->get('Categories');
        $this->cat_name = $this->get('Categoryname');
        $this->state = $this->get('State');

        // Check for errors.
        if (\count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        // Create a shortcut to the parameters.
        $this->params = $this->state->params;

        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx', ''));

        $this->prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepares the document
     *
     * @return  void
     *
     * @throws \Exception
     *
     * @since  4.0.0
     */
    protected function prepareDocument()
    {
        /** @var CMSApplication $app */
        $app = Factory::getApplication();

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $app->getMenu()->getActive();

        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', Text::_('COM_JOOMDLE_COURSE_CATEGORY'));
        }

        $title = $this->params->def('page_title', Text::_('COM_JOOMDLE_COURSE_CATEGORY'));

        $this->setDocumentTitle($title);

        $pathway = $app->getPathWay();
        $pathway->addItem($title, '');
    }
}

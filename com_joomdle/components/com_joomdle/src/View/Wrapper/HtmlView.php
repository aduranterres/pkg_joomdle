<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Site\View\Wrapper;

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

    protected $wrapper_url;


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
        $app  = Factory::getApplication();

        // Get model data.
        $this->state = $this->get('State');

        $data = array ();
        $mtype = $app->input->get('moodle_page_type');
        if (!$mtype) {
            $mtype = $this->state->params->get('moodle_page_type');
        }
        $data['mtype'] = $mtype;

        $id = $app->input->get('id');
        if (!$id) {
            $id = $this->state->params->get('course_id');
        }

        $data['id'] = $id;
        $data['time'] = $app->input->get('time');
        $data['topic'] = $app->input->get('topic');
        $data['hash'] = $app->input->get('hash');
        $data['section'] = $app->input->get('section');
        $data['customurl'] = $this->state->params->get('customurl');
        $data['gotourl'] = $app->input->get('gotourl', '', 'STRING');

        $this->wrapper_url = $this->getModel()->getUrl($data);

        $lang = $app->input->get('lang');
        if ($lang) {
            $this->wrapper_url .= "&lang=$lang";
        }

        $redirect = $app->input->get('redirect');
        if ($redirect) {
            $this->wrapper_url .= "&redirect=$redirect";
        }

        // Create a shortcut to the parameters.
        $this->params = $this->state->params;

        $theme = $this->params->get('theme');
        if ($theme) {
            $this->wrapper_url .= "&theme=" . $theme;
        }

        $layout = $app->input->get('layout');
        if ($layout == 'getout') {
            $tpl = 'getout';
        } elseif ($this->state->params->get('crossdomain_autoheight')) {
            $tpl = 'cross';
        }

        // Check for errors.
        if (\count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

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
            $this->params->def('page_heading', Text::_('COM_JOOMDLE_TITLE_WRAPPER'));
        }

        $title = $this->params->def('page_title', Text::_('COM_JOOMDLE_TITLE_WRAPPER'));

        $this->setDocumentTitle($title);

        $pathway = $app->getPathWay();
        $pathway->addItem($title, '');
    }
}

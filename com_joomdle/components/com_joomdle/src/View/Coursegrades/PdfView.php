<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Site\View\Coursegrades;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use TCPDF;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * HTML Joomdle view.
 *
 * @since  4.0.0
 */
class PdfView extends BaseHtmlView
{
    protected $items;

    protected $pagination;

    protected $state;

    protected $params;

    protected $pageclass_sfx = '';

    protected $course;

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
        $this->items = $this->get('Items');
        $this->course = $this->get('Course');
        $this->state = $this->get('State');

        // Check for errors.
        if (\count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        // User not enroled and no guest access.
        if ((!$this->course['enroled']) && (!$this->course['guest'])) {
            return;
        }

        // Create a shortcut to the parameters.
        $this->params = $this->state->params;

        $tpl = "catspdf";

        $this->prepareDocument();

        $htmlcontent = parent::loadTemplate($tpl);

        require_once(JPATH_SITE . '/libraries/tcpdf/tcpdf.php');
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $header = $this->course['fullname'];

        $pdf->SetHeaderData('', 0, $header);

        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

        $pdf->setFontSubsetting(false);

        $pdf->SetFont('times', '', 8);
        // add a page
        $pdf->AddPage("L");

        // output the HTML content
        $pdf->writeHTML($htmlcontent, true, 0, true, 0);

        $pdf->Output("grades.pdf", 'D');
        exit();
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
            $this->params->def('page_heading', Text::_('COM_JOOMDLE_COURSE_GRADES'));
        }

        $title = $this->params->def('page_title', Text::_('COM_JOOMDLE_COURSE_GRADES'));

        $this->setDocumentTitle($title);

        $pathway = $app->getPathWay();
        $pathway->addItem($title, '');
    }
}

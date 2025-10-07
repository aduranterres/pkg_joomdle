<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Administrator\View\Default;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Installer\Installer;

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
        parent::display($tpl);
    }

    public function showButton($link, $image, $text)
    {
        ?>
        <a href="<?php echo $link; ?>">
            <button  type="button" class="panel_btn btn-default" style="width:140px;height:120px"> 
                <?php echo HTMLHelper::_('image', "com_joomdle/" . $image, '', null, true); ?>
                <br>
                <span><?php echo $text; ?></span>
            </button>
        </a>
        <?php
    }

    public function renderAbout()
    {
        $xmlfile = JPATH_ADMINISTRATOR . '/components/com_joomdle/joomdle.xml';

        $version = '';
        if (file_exists($xmlfile)) {
            if ($data = Installer::parseXMLInstallFile($xmlfile)) {
                $version =  $data['version'];
            }
        }

        $output = '<div style="padding: 5px;">';
        $output .= Text::sprintf('COM_JOOMDLE_ABOUT_TEXT_VERSION', $version);
        $output .= '<P>' . Text::sprintf('COM_JOOMDLE_ABOUT_TEXT_PROVIDES');
        $output .= '<P>' . Text::sprintf('COM_JOOMDLE_ABOUT_TEXT_SUPPORT');
        $output .= '<P>' . Text::sprintf('COM_JOOMDLE_ABOUT_SUBSCRIBE');
        $output .= '<P>' . Text::sprintf('COM_JOOMDLE_ABOUT_TEXT_JED');
        $output .= '</div>';

        return $output;
    }
}

<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Administrator\Helper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

/**
 * System helper.
 *
 * @since  1.0.0
 */
class SystemHelper
{
    public static function actionbutton($course_info, $free_courses_button = 'enrol', $paid_courses_button = 'buy', $button_text = '')
    {
        $course_id = $course_info['remoteid'];
        $user = Factory::getApplication()->getIdentity();
        $username = $user->username;
        $is_enroled = $course_info['enroled'];
        $guest = $course_info['guest'];

        $params = ComponentHelper::getParams('com_joomdle');
        $show_experience = $params->get('show_detail_application_experience');
        $show_motivation = $params->get('show_detail_application_motivation');
        $goto_course_button = $params->get('goto_course_button');
        $linkstarget = $params->get('linkstarget');

        $html = "";
        if ((($is_enroled) || ($guest))) {
            if ($goto_course_button) {
                if (!$button_text) {
                    $button_text = Text::_('COM_JOOMDLE_GO_TO_COURSE');
                }

                $data = array ();
                $data['moodle_page_type'] = 'course';
                $data['id'] = $course_id;

                $url = ContentHelper::getJumpURL($data);

                if ($linkstarget == 'new') {
                    $html .= '<FORM>
                        <INPUT TYPE="BUTTON" VALUE="' . $button_text . '"
                        ONCLICK="window.open (\'' . $url . '\')">
                        </FORM>';
                } else {
                    $html .= '<FORM>
                    <INPUT TYPE="BUTTON" VALUE="' . $button_text . '"
                    ONCLICK="window.location.href=\'' . $url . '\'">
                    </FORM>';
                }
            }
        } elseif (((!array_key_exists('cost', $course_info)) || (!$course_info['cost'])) /* &&  (!JoomdleHelperShop::is_course_on_sell ($course_id)) */) { // FIXME SHOP
            if ($free_courses_button == 'goto') {
                if (!$button_text) {
                    $button_text = Text::_('COM_JOOMDLE_GO_TO_COURSE');
                }
                $url = JoomdleHelperContent::get_course_url($course_id);

                if ($linkstarget == 'new') {
                             $html .= '<FORM>
                       <INPUT TYPE="BUTTON" VALUE="  ' . $button_text . '  "
                            ONCLICK="window.open (\'' . $url . '\')">
                       </FORM>';
                } else {
                              $html .= '<FORM>
                       <INPUT TYPE="BUTTON" VALUE="' . $button_text . '"
                            ONCLICK="window.location.href=\'' . $url . '\'">
                       </FORM>';
                }
            } elseif ($free_courses_button == 'enrol') {
                if (!$button_text) {
                    $button_text = Text::_('COM_JOOMDLE_ENROL_INTO_COURSE');
                }
                $url = (URI::root() . "index.php?option=com_joomdle&task=course.enrol&course_id=$course_id");
                $can_enrol = $course_info['self_enrolment'] && $course_info['in_enrol_date'];
                if ($can_enrol) {
                           $html .= '<FORM>
           <INPUT TYPE="BUTTON" VALUE="' . $button_text . '"
            ONCLICK="window.location.href=\'' . $url . '\'">
           </FORM>';
                }
            }
        } else { //courses in shop
            require_once(JPATH_ADMINISTRATOR . '/components/com_joomdle/helpers/shop.php');
            if ($paid_courses_button == 'buy') {
                if (JoomdleHelperShop::is_course_on_sell($course_info['remoteid'])) {
                    if (!$button_text) {
                           $button_text = Text::_('COM_JOOMDLE_BUY_COURSE');
                    }
                       $url = Route::_(JoomdleHelperShop::get_sell_url($course_info['remoteid']));
                       $can_enrol = $course_info['in_enrol_date'];
                    if ($can_enrol) {
                                   $html .= '
                                               <FORM>
                                               <INPUT TYPE="BUTTON" VALUE="  ' . $button_text . '  "
                                ONCLICK="window.location.href=\'' . $url . '\'">
                                               </FORM>';
                    }
                }
            } elseif ($paid_courses_button == 'paypal') {
                              $url = Route::_("index.php?option=com_joomdle&view=buycourse&course_id=$course_id");
                              $html .= '<br><a href="' . $url . '"><img
src="https://www.paypal.com/en_US/i/logo/PayPal_mark_60x38.gif"></a>';
            }
        }


        return $html;
    }
}

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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
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
        $is_enroled = $course_info['enroled'];
        $guest = $course_info['guest'];

        $params = ComponentHelper::getParams('com_joomdle');
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
        } elseif (((!array_key_exists('cost', $course_info)) || (!$course_info['cost'])) && (!ShopHelper::isCourseOnSell($course_id))) {
            if ($free_courses_button == 'goto') {
                if (!$button_text) {
                    $button_text = Text::_('COM_JOOMDLE_GO_TO_COURSE');
                }

                $data = array ();
                $data['moodle_page_type'] = 'course';
                $data['id'] = $course_id;

                $url = ContentHelper::getJumpURL($data);

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
            if ($paid_courses_button == 'buy') {
                if (ShopHelper::isCourseOnSell($course_info['remoteid'])) {
                    if (!$button_text) {
                           $button_text = Text::_('COM_JOOMDLE_BUY_COURSE');
                    }
                       $url = Route::_(Uri::root() . ltrim(ShopHelper::getSellUrl($course_info['remoteid']), '/'));
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
                $html .= '<br><a href="' . $url . '"><img src="https://www.paypal.com/en_US/i/logo/PayPal_mark_60x38.gif"></a>';
            }
        }

        return $html;
    }
}

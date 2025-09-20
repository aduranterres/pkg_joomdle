<?php

/**
 * @package     Joomdle
 * @subpackage  mod_joomdle_courses
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Component\ComponentHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;

// no direct access
defined('_JEXEC') or die('Restricted access');

$itemid = ContentHelper::getMenuItem();
?>

<ul class="joomdlecourses">
<?php
    $i = 0;
if (is_array($courses)) {
    foreach ($courses as $id => $course) {
        $id = $course['remoteid'];
        if ($linkto == 'moodle') {
        // Link to Moodle (wrapper or not)
            if ($default_itemid) {
                $itemid = $default_itemid;
            }

            $data = ['id' => $id, 'moodle_page_type' => 'course'];
            $url = ContentHelper::getJumpURL($data);

            echo "<li><a $target href=\"" . $url . "\">" . $course['fullname'] . "</a></li>";
        } else {
            // Link to detail view
            $comp_params = ComponentHelper::getParams('com_joomdle');
            $joomdle_itemid = $comp_params->get('joomdle_itemid');

            if ($joomdle_itemid) {
                $itemid = $joomdle_itemid;
            }

            $url = Route::_("index.php?option=com_joomdle&view=detail&cat_id=" . $course['cat_id'] . ":" .
            OutputFilter::stringURLSafe($course['cat_name']) . "&course_id=" . $course['remoteid'] .
            ':' . OutputFilter::stringURLSafe($course['fullname']) . "&Itemid=$itemid");
            echo "<li><a href=\"" . $url . "\">" . $course['fullname'] . "</a></li>";
        }

        $i++;
        if ($i >= $limit) { // Show only this number of latest courses
            break;
        }
    }
}
?>
</ul>

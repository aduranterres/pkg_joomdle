<?php

/**
 * @package     Joomdle
 * @subpackage  mod_joomdle_my_courses
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Filter\OutputFilter;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;

// no direct access
defined('_JEXEC') or die('Restricted access');

// Show message and return if there are no courses to show
if ((!is_array($courses)) || (!count($courses))) {
    echo $nocourses_text;
    return;
}

$comp_params = ComponentHelper::getParams('com_joomdle');
$default_itemid = $comp_params->get('default_itemid');
$joomdle_itemid = $comp_params->get('joomdle_itemid');
$linkstarget = $comp_params->get('linkstarget');

if ($linkto == 'moodle') {
    if ($default_itemid) {
        $itemid = $default_itemid;
    } else {
        $itemid = ContentHelper::getMenuItem();
    }
} elseif ($linkto == 'detail') {
    $itemid = ContentHelper::getMenuItem();

    if ($joomdle_itemid) {
        $itemid = $joomdle_itemid;
    }
}

$target = "";
if ($linkstarget == 'wrapper') {
    $open_in_wrapper = 1;
} else {
    $open_in_wrapper = 0;

    if ($linkstarget == "new") {
        $target = " target='_blank'";
    }
}

$lang = ContentHelper::getLang();
$prev_cat = 0;
?>

<ul class="joomdlecourses">
<?php
    $group_by_category = $params->get('group_by_category');

if (is_array($courses)) {
    foreach ($courses as $id => $course) {
        $id = $course['id'];

        if ($group_by_category) {
            // Group by category
            if ($course['category'] != $prev_cat) :
                $prev_cat = $course['category'];
                $cat_name = $course['cat_name'];
                ?>
                </ul>
                <h4>
                <?php echo $cat_name; ?>
                </h4>
                <ul>
                <?php
            endif;
        }

        if ($linkto == 'moodle') {
            // Link to Moodle (wrapper or not)
            $id = $course['id'];
            $data = ['id' => $id, 'moodle_page_type' => 'course'];
            if ($lang) {
                $data['lang'] = $lang;
            }
            $url = ContentHelper::getJumpURL($data);

            echo "<li><a $target href=\"" . $url . "\">" . $course['fullname'] . "</a></li>";
        } elseif ($linkto == 'detail') {
            // Link to detail view
            $redirect_url = Route::_("index.php?option=com_joomdle&view=detail&course_id=" . $course['id'] . ':' . OutputFilter::stringURLSafe($course['fullname']) . "&Itemid=$itemid");
            echo "<li><a href=\"" . $redirect_url . "\">" . $course['fullname'] . "</a></li>";
        }

        if ($show_unenrol_link) {
            if ($course['can_unenrol']) {
                $redirect_url = "index.php?option=com_joomdle&view=course&task=course.unenrol&course_id=" . $course['id'];
                echo "<a href=\"" . $redirect_url . "\"> (" . Text::_('COM_JOOMDLE_UNENROL') . ")</a>";
            }
        }
    }
}
?>
</ul>

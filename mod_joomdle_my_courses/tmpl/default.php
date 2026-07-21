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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Filter\OutputFilter;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;

// no direct access
defined('_JEXEC') or die('Restricted access');

Factory::getApplication()->getDocument()->getWebAssetManager()
    ->registerAndUseStyle('mod_joomdle_my_courses.joomdle', 'media/com_joomdle/css/joomdle.css');

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

        $course_link = '';

        if ($linkto == 'moodle') {
            // Link to Moodle (wrapper or not)
            $id = $course['id'];
            $data = ['id' => $id, 'moodle_page_type' => 'course'];
            if ($lang) {
                $data['lang'] = $lang;
            }
            $url = ContentHelper::getJumpURL($data);

            $course_link = "<a $target href=\"" . $url . "\">" . $course['fullname'] . "</a>";
        } elseif ($linkto == 'detail') {
            // Link to detail view
            $redirect_url = Route::_("index.php?option=com_joomdle&view=detail&course_id=" . $course['id'] . ':' . OutputFilter::stringURLSafe($course['fullname']) . "&Itemid=$itemid");
            $course_link = "<a href=\"" . $redirect_url . "\">" . $course['fullname'] . "</a>";
        }

        echo '<li>' . $course_link;

        if ($show_unenrol_link) {
            if ($course['can_unenrol']) {
                ?>
                <form action="<?php echo Route::_('index.php?option=com_joomdle'); ?>" method="post" class="joomdle_unenrol_form">
                    <input type="hidden" name="task" value="course.unenrol">
                    <input type="hidden" name="course_id" value="<?php echo (int) $course['id']; ?>">
                    <?php echo HTMLHelper::_('form.token'); ?>
                    <button type="submit" class="joomdle_unenrol_link">(<?php echo Text::_('COM_JOOMDLE_UNENROL'); ?>)</button>
                </form>
                <?php
            }
        }

        echo '</li>';
    }
}
?>
</ul>

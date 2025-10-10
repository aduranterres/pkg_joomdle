<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\SystemHelper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die('Restricted access');
?>

<div class="joomdle-courselist<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

<?php
$itemid = ContentHelper::getMenuItem();
$free_courses_button = $this->params->get('free_courses_button');
$paid_courses_button = $this->params->get('paid_courses_button');
$show_buttons = $this->params->get('coursesabc_show_buttons');

if (is_array($this->items)) {
    foreach ($this->items as $course) : ?>
        <?php
        $cat_id = $course['cat_id'];
        $course_id = $course['remoteid'];

        $course_slug = ApplicationHelper::stringURLSafe($course['fullname']);
        $cat_slug = ApplicationHelper::stringURLSafe($course['cat_name']);

        ?>
    <div class="joomdle_course_list_item">
        <div class="joomdle_item_title joomdle_course_list_item_title">
        <?php $url = Route::_("index.php?option=com_joomdle&view=detail&cat_id=$cat_id-$cat_slug&course_id=$course_id-$course_slug&Itemid=$itemid"); ?>
        <?php  echo "<a href=\"$url\">" . $course['fullname'] . "</a>"; ?>
        </div>
        <?php if ($course['summary']) : ?>
        <div class="joomdle_item_content joomdle_course_list_item_description">
                <div class="joomdle_course_description">
                <?php
                if (count($course['summary_files'])) {
                    foreach ($course['summary_files'] as $file) :
                        ?>
                            <img hspace="5" vspace="5" align="left" src="<?php echo $file['url']; ?>">
                        <?php
                    endforeach;
                }
                ?>
                <?php echo $course['summary']; ?>
                </div>
                 <?php if ($show_buttons) : ?>
                <div class="joomdle_course_buttons">
                        <?php
                        echo SystemHelper::actionbutton($course, $free_courses_button, $paid_courses_button);
                        ?>
                </div>
                 <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach;
};  ?>
</div>

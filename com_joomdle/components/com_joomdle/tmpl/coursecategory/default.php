<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomdle\Component\Joomdle\Administrator\Helper\SystemHelper;

$free_courses_button = $this->params->get('free_courses_button');
$paid_courses_button = $this->params->get('paid_courses_button');
$show_buttons = $this->params->get('coursecategory_show_buttons');

$unicodeslugs = Factory::getConfig()->get('unicodeslugs');
?>
<div class="joomdle-coursecategory<?php echo $this->pageclass_sfx;?>">
    <h1>
        <?php echo $this->cat_name; ?>
    </h1>

    <?php if ($this->params->get('coursecategory_show_category_info')) : ?>
        <?php if (is_array($this->courses) && (count($this->courses))) : ?>
        <div class="joomdle_list_description">
            <?php echo $this->courses[0]['cat_description'];?>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="joomdle_categories">
        <?php if ((is_array($this->categories)) && (count($this->categories)) && (is_array($this->courses)) && (count($this->courses))) : ?>
                <h4>
                <?php
                    echo Text::_('COM_JOOMDLE_COURSE_CATEGORIES_IN');
                    echo " " . $this->cat_name;
                ?> 
                </h4>
        <?php endif;?>

    <?php
    if (is_array($this->categories)) {
        foreach ($this->categories as $cat) : ?>
                <?php
                if ($unicodeslugs == 1) {
                    $cat_slug = OutputFilter::stringURLUnicodeSlug($cat['name']);
                } else {
                    $cat_slug = OutputFilter::stringURLSafe($cat['name']);
                }
                ?>

            <div class="joomdle_category_list_item">
                <div class="joomdle_card">
                    <div class="joomdle_category_info">

                <div class="joomdle_category_list_item_title">
                    <?php $url = JRoute::_("index.php?option=com_joomdle&view=coursecategory&cat_id=" . $cat['id'] . '-' . $cat_slug); ?>
                    <?php  echo "<a href=\"$url\">" . $cat['name'] . "</a>"; ?>
                </div>
                <?php if ($cat['description']) : ?>
                <div class="joomdle_course_list_item_description">
                    <?php echo SystemHelper::fixTextFormat($cat['description']); ?>
                </div>
                <?php endif; ?>
            </div>
                </div>
                    </div>
        <?php endforeach;
    }; ?>
    </div>
    <br>

    <div class="joomdle_courses">
    <?php if ((is_array($this->categories)) && (count($this->categories)) && (is_array($this->courses)) && (count($this->courses))) : ?>
    <h4>
                    <?php echo Text::_('COM_JOOMDLE_COURSES_IN');
                    echo " " . $this->cat_name;
                    ?> 
    </h4>
    <?php endif; ?>

    <?php
    if (is_array($this->courses)) {
        foreach ($this->courses as $course) : ?>
    <div class="joomdle_course_list_item">
        <div class="joomdle_card">
            <div class="joomdle_course_info">
                <div class=" joomdle_course_list_item_title">
                    <?php
                    if ($unicodeslugs == 1) {
                        $slug = OutputFilter::stringURLUnicodeSlug($course['fullname']);
                        $cat_slug = OutputFilter::stringURLUnicodeSlug($course['cat_name']);
                    } else {
                        $slug = OutputFilter::stringURLSafe($course['fullname']);
                        $cat_slug = OutputFilter::stringURLSafe($course['cat_name']);
                    }

                    $url = JRoute::_("index.php?option=com_joomdle&view=detail&course_id=" . $course['remoteid'] . '-' . $slug); ?>
                    <?php  echo "<a href=\"$url\">" . $course['fullname'] . "</a><br>"; ?>
                </div>
                <?php if ($course['summary']) : ?>
                <div class=" joomdle_course_list_item_description">
                    <div class="joomdle_course_description">
                    <?php
                    if (count($course['summary_files'])) {
                        foreach ($course['summary_files'] as $file) :
                            ?>
                            <div class="joomdle_course_image">
                                <img hspace="5" vspace="5" align="left" src="<?php echo $file['url']; ?>">
                            </div>
                            <?php
                        endforeach;
                    }
                    ?>

                    <?php echo $course['summary']; ?>
                    </div>
                    <?php if ($show_buttons) : ?>
                    <div class="joomdle_course_buttons">
                        <?php echo SystemHelper::actionbutton($course, $free_courses_button, $paid_courses_button) ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <br>
        <?php endforeach;
    }; ?>
    </div>
</div>

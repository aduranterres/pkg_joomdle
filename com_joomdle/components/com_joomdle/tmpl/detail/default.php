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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\SystemHelper;

$course_info = $this->course_info;
$itemid = ContentHelper::getMenuItem();

$show_contents_link = $this->params->get('show_contents_link');
$show_topics_link = $this->params->get('show_topìcs_link');
$show_grading_system_link = $this->params->get('show_grading_system_link');
$show_teachers_link = $this->params->get('show_teachers_link');
$show_category = $this->params->get('show_detail_category', 1);
$show_summary = $this->params->get('show_detail_summary', 1);
$show_language = $this->params->get('show_detail_language', 0);
$show_startdate = $this->params->get('show_detail_startdate', 1);
$show_enroldates = $this->params->get('show_detail_enroldates', 0);
$show_enrolperiod = $this->params->get('show_detail_enrolperiod', 1);
$show_topicsnumber = $this->params->get('show_detail_topicsnumber', 1);
$show_cost = $this->params->get('show_detail_cost', 1);
$show_motivation = $this->params->get('show_detail_application_motivation', 'no');
$show_experience = $this->params->get('show_detail_application_experience', 'no');
$free_courses_button = $this->params->get('free_courses_button');
$paid_courses_button = $this->params->get('paid_courses_button');

$user = Factory::getApplication()->getIdentity();
$user_logged = $user->id;

if (!array_key_exists('cost', $course_info)) {
    $course_info['cost'] = 0;
}
?>

<div class="joomdle-coursedetails<?php echo $this->pageclass_sfx;?>">

    <div class="joomdle_course_name">
                <h1><?php echo $course_info['fullname']; ?></h1>
    </div>

<?php   if ($show_category) : ?>
    <div class="joomdle_course_category">
        <b><?php echo Text::_('COM_JOOMDLE_CATEGORY'); ?>:&nbsp;</b><?php echo $course_info['cat_name']; ?>
    </div>
<?php endif; ?>

<?php   if ($show_summary) : ?>
    <div class="joomdle_course_description">
        <div>
        <b><?php echo Text::_('COM_JOOMDLE_SUMMARY'); ?>:&nbsp;</b>
        </div>
        <?php
        if (count($course_info['summary_files'])) {
            foreach ($course_info['summary_files'] as $file) :
                ?>
                    <div class="joomdle_course_image">
                        <img hspace="5" vspace="5" align="left" src="<?php echo $file['url']; ?>">
                    </div>
                <?php
            endforeach;
        }
        ?>

        <?php echo $course_info['summary']; ?>
    </div>
    <div style="clear:both;"></div>
<?php endif; ?>

<?php   if ($show_language) : ?>
    <?php if ($course_info['lang']) : ?>
    <div class="joomdle_course_language">
        <b><?php echo Text::_('COM_JOOMDLE_LANGUAGE'); ?>:&nbsp;</b><?php echo ContentHelper::getLanguageStr($course_info['lang']); ?>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?php   if ($show_startdate) : ?>
    <div class="joomdle_course_startdate">
        <b><?php echo Text::_('COM_JOOMDLE_START_DATE'); ?>:&nbsp;</b>
        <?php echo HTMLHelper::_('date', date('Y-m-d', $course_info['startdate']), Text::_('DATE_FORMAT_LC1')); ?>

    </div>
<?php endif; ?>


<?php   if ($show_enroldates) : ?>
    <?php if ((array_key_exists('enrolstartdate', $course_info)) && ($course_info['enrolstartdate'])) : ?>
    <div class="joomdle_course_enrolstartdate">
        <b><?php echo Text::_('COM_JOOMDLE_ENROLMENT_START_DATE'); ?>:&nbsp;</b>
        <?php echo HTMLHelper::_('date', date('Y-m-d', $course_info['enrolstartdate']), Text::_('DATE_FORMAT_LC1')); ?>
    </div>
    <?php endif; ?>

    <?php if ((array_key_exists('enrolenddate', $course_info)) && ($course_info['enrolenddate'])) : ?>
    <div class="joomdle_course_enrolenddate">
        <b><?php echo Text::_('COM_JOOMDLE_ENROLMENT_END_DATE'); ?>:&nbsp;</b>
        <?php echo HTMLHelper::_('date', date('Y-m-d', $course_info['enrolenddate']), Text::_('DATE_FORMAT_LC1')); ?>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?php   if ($show_enrolperiod) : ?>
    <?php if (array_key_exists('enrolperiod', $course_info)) : ?>
    <div class="joomdle_course_enrolperiod">
        <b><?php echo Text::_('COM_JOOMDLE_ENROLMENT_DURATION'); ?>:&nbsp;</b><?php
        if ($course_info['enrolperiod'] == 0) {
            echo Text::_('COM_JOOMDLE_UNLIMITED');
        } else {
            echo ($course_info['enrolperiod'] / 86400) . " " . Text::_('COM_JOOMDLE_DAYS');
        }
        ?>
    </div>
    <?php endif; ?>
<?php endif; ?>


<?php   if ($show_cost) : ?>
    <?php if ($course_info['cost']) : ?>
    <div class="joomdle_course_cost">
        <b><?php echo Text::_('COM_JOOMDLE_COST'); ?>:&nbsp;</b><?php echo $course_info['cost'] . "(" . $course_info['currency'] . ")"; ?>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?php $index_url = URI::base() . "index.php"; ?>
<?php   if ($show_topicsnumber) : ?>
    <div class="joomdle_course_topicsnumber">
        <b><?php echo Text::_('COM_JOOMDLE_TOPICS'); ?>:&nbsp;</b><?php echo $course_info['numsections']; ?>
    </div>
<?php endif; ?>

    <div class="joomdle_course_links">
    <?php
        $cat_id = $course_info['cat_id'];
        $course_id = $course_info['remoteid'];
        $cat_slug = OutputFilter::stringURLSafe($course_info['cat_name']);
        $course_slug = OutputFilter::stringURLSafe($course_info['fullname']);

    if ($show_topics_link) : ?>
        <?php $url = Route::_("index.php?option=com_joomdle&view=topics&course_id=$course_id-$course_slug"); ?>
        <P><b><?php  echo "<a href=\"$url\">" . Text::_('COM_JOOMDLE_COURSE_TOPICS') . "</a><br>"; ?></b>
    <?php endif; ?>
    <?php
    if ($show_grading_system_link) : ?>
        <?php $url = Route::_("index.php?option=com_joomdle&view=coursegradecategories&cat_id=$cat_id-$cat_slug&course_id=$course_id-$course_slug&Itemid=$itemid"); ?>
        <P><b><?php  echo "<a href=\"$url\">" . Text::_('COM_JOOMDLE_COURSE_GRADING_SYSTEM') . "</a><br>"; ?></b>
    <?php endif; ?>
    <?php
    if ($show_teachers_link) : ?>
        <?php $url = Route::_("index.php?option=com_joomdle&view=teachers&cat_id=$cat_id-$cat_slug&course_id=$course_id-$course_slug&Itemid=$itemid"); ?>
        <P><b><?php  echo "<a href=\"$url\">" . Text::_('COM_JOOMDLE_COURSE_TEACHERS') . "</a><br>"; ?></b>
    <?php endif; ?>
    </div>

    <div class="joomdle_course_buttons">
        <?php echo SystemHelper::actionbutton($course_info, $free_courses_button, $paid_courses_button) ?>
    </div>
</div>

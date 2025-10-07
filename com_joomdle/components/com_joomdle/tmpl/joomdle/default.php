<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Router\Route;
use Joomdle\Component\Joomdle\Administrator\Helper\SystemHelper;

$free_courses_button = $this->params->get('free_courses_button');
$paid_courses_button = $this->params->get('paid_courses_button');
$show_buttons = $this->params->get('show_buttons');
$show_description = $this->params->get('show_description');
?>
<div class="joomdle-courselist<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

    <?php
    if (is_array($this->items)) {
        foreach ($this->items as $item) : ?>
            <?php
            $cat_id = $item['cat_id'];
            $course_id = $item['remoteid'];

            $course_slug = ApplicationHelper::stringURLSafe($item['fullname']);
            $cat_slug = ApplicationHelper::stringURLSafe($item['cat_name']);

            ?>
    <div class="joomdle_course_list_item">
        <div class="joomdle_card">
            <?php
            // Show image if description and files exist
            if (($show_description) && ($item['summary']) && count($item['summary_files'])) :
                foreach ($item['summary_files'] as $file) : ?>
                    <div class="joomdle_course_image">
                        <img src="<?php echo $file['url']; ?>" alt="Course image">
                    </div>
                <?php endforeach;
            endif;
            ?>

            <div class="joomdle_course_info">
                <?php if ($show_description) : ?>
                    <h3 class="joomdle_course_title">
                <?php else : ?>
                    <h3 class="joomdle_course_title joomdle_item_full">
                <?php endif; ?>

                    <?php
                        $url = Route::_("index.php?option=com_joomdle&view=detail&course_id=$course_id-$course_slug");
                        echo "<a href=\"$url\">" . $item['fullname'] . "</a>";
                    ?>
                </h3>

                    <?php if (($show_description) && ($item['summary'])) : ?>
                    <p class="joomdle_course_description">
                        <?php echo $item['summary']; ?>
                    </p>
                    <?php endif; ?>

                    <?php if ($show_buttons) : ?>
                    <div class="joomdle_course_buttons">
                        <?php
                            echo SystemHelper::actionButton($item, $free_courses_button, $paid_courses_button);
                        ?>
                    </div>
                    <?php endif; ?>
            </div>
        </div>
    </div>

        <?php endforeach;
    }; ?>
</div>

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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filter\OutputFilter;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;

$linkto = $this->params->get('mycourses_linkto');
$default_itemid = $this->params->get('default_itemid');
$joomdle_itemid = $this->params->get('joomdle_itemid');
$courseview_itemid = $this->params->get('courseview_itemid');
if ($linkto == 'moodle') {
    if ($default_itemid) {
        $itemid = $default_itemid;
    } else {
        $itemid = ContentHelper::getMenuItem();
    }
} elseif ($linkto == 'detail') {
        // Get the best menu item id we can get
        $itemid = ContentHelper::getMenuItem();

    if ($joomdle_itemid) {
        $itemid = $joomdle_itemid;
    }
}

$linkstarget = $this->params->get('linkstarget');
if ($linkstarget == "new") {
     $target = " target='_blank'";
} else {
    $target = "";
}

$show_unenrol_link = $this->params->get('show_unenrol_link');
$show_images_and_summary = $this->params->get('show_images_and_summary');

$unicodeslugs = Factory::getConfig()->get('unicodeslugs');
?>

<div class="joomdle-mycourses<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

    <?php if ($show_images_and_summary) : ?>
    <div class="joomdle_mycourses_no_list">
    <?php else : ?>
    <div class="joomdle_mycourses">
    <?php endif; ?>

<?php
$lang = ContentHelper::getLang();
$prev_cat = 0;
$i = 0;
if (is_array($this->items)) {
    foreach ($this->items as $id => $course) :
        $i++;
        if ($course['category'] != $prev_cat) :
            $prev_cat = $course['category'];
            $cat_name = $course['cat_name'];
            if ($i > 1) :
                ?>
</ul>
            <?php endif; ?>
<h4>
            <?php echo $cat_name; ?>
</h4>
        <ul>
            <?php
        endif;
        ?>
            <li>
                <?php
                if ($unicodeslugs == 1) {
                    $slug = OutputFilter::stringURLUnicodeSlug($course['fullname']);
                } else {
                    $slug = OutputFilter::stringURLSafe($course['fullname']);
                }
                ?>

                <?php
                if ($linkto == 'moodle') {
                    $link = $this->jump_url . "&mtype=course&id=" . $course['id'] . "&Itemid=$itemid";
                    if ($lang) {
                        $link .= "&lang=$lang";
                    }
                    $redirect_url = $link;

                    $course_link = "<a $target href=\"$link\">" . $course['fullname'] . "</a>";
                } elseif ($linkto == 'detail') {
                    // Link to detail view
                    $redirect_url = JRoute::_("index.php?option=com_joomdle&view=detail&course_id=" . $course['id'] . '-' . $slug .
                    "&Itemid=$itemid");
                    $course_link = "<a href=\"" . $redirect_url . "\">" . $course['fullname'] . "</a>";
                }

                if ($show_images_and_summary) {
                    if (count($course['summary_files'])) {
                        foreach ($course['summary_files'] as $file) :
                            echo "<a $target href=\"$link\">";
                            ?>
                                <div class="joomdle_course_image">
                                    <img style="float:none;" hspace="5" vspace="5" align="left" src="<?php echo $file['url']; ?>">
                                </div>
                            <?php
                            echo "</a>";
                        endforeach;
                    }
                }

                    echo $course_link;

                if ($show_unenrol_link) {
                    if ($course['can_unenrol']) {
                        $redirect_url = "index.php?option=com_joomdle&task=course.unenrol&course_id=" . $course['id'];
                        echo "<a href=\"" . $redirect_url . "\"> (" . Text::_('COM_JOOMDLE_UNENROL') . ")</a>";
                    }
                }

                if ($show_images_and_summary) {
                    echo "<div class='course_summary'>" . $course['summary'] . "</div>";
                    echo "<div class='clear_float'></div>";
                }

                ?>
            </li>


    <?php endforeach;
}; ?>
    </div>
</div>

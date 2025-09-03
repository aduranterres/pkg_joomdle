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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filter\OutputFilter;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;

if (!count($this->items)) {
    echo '<span class="joomdle_nocourses_message">' . $this->params->get('nocourses_text') . "</span>";
    return;
}

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
    <ul>
    <?php
    $lang = ContentHelper::getLang();
    if (is_array($this->items)) {
        foreach ($this->items as $id => $item) :  ?>
                <?php
                if ($unicodeslugs == 1) {
                    $slug = OutputFilter::stringURLUnicodeSlug($item['fullname']);
                } else {
                    $slug = OutputFilter::stringURLSafe($item['fullname']);
                }
                ?>
            <li>
                <?php
                if ($linkto == 'moodle') {
                    $data = array ();
                    $data['moodle_page_type'] = 'course';
                    $data['id'] = $item['id'];
                    if ($lang) {
                        $data['lang'] = $lang;
                    }

                    $link = ContentHelper::getJumpURL($data);
                    $redirect_url = $link;

                    $course_link =  "<a $target href=\"$link\">" . $item['fullname'] . "</a>";
                } elseif ($linkto == 'detail') {
                    // Link to detail view
                    $redirect_url = Route::_("index.php?option=com_joomdle&view=detail&course_id=" . $item['id'] . '-' . $slug .
                    "&Itemid=$itemid");
                    $course_link =  "<a href=\"" . $redirect_url . "\">" . $item['fullname'] . "</a>";
                }

                if ($show_images_and_summary) {
                    if (count($item['summary_files'])) {
                        foreach ($item['summary_files'] as $file) :
                            echo "<a $target href=\"$redirect_url\">";
                            ?>
                                <div class='joomdle_course_image'>
                                    <img style="float:none;" hspace="5" vspace="5" align="left" src="<?php echo $file['url']; ?>">
                                </div>
                            <?php
                            echo "</a>";
                        endforeach;
                    }
                }

                echo $course_link;

                if ($show_unenrol_link) {
                    if ($item['can_unenrol']) {
                        $redirect_url = "index.php?option=com_joomdle&task=course.unenrol&course_id=" . $item['id'];
                        echo "<a href=\"" . $redirect_url . "\"> (" . Text::_('COM_JOOMDLE_UNENROL') . ")</a>";
                    }
                }

                if ($show_images_and_summary) {
                    echo "<div class='course_summary'>" . $item['summary'] . "</div>";
                    echo "<div class='clear_float'></div>";
                }

                ?>
            </li>
        <?php endforeach;
    }; ?>
    </ul>
    </div>
</div>

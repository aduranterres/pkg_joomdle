<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;


?>

<div class="joomdle-itemlist<?php echo $this->pageclass_sfx;?>">
    <h1>
        <?php echo $this->course['fullname'] . ': '; ?>
        <?php echo Text::_('COM_JOOMDLE_COURSE_EVENTS'); ?>
    </h1>

<?php
$lang = ContentHelper::getLang();
foreach ($this->items as $item) : ?>
    <div class="joomdle_item_list_item">
        <div class="joomdle_item_title joomdle_item_list_item_date">
          <?php

            $linkstarget = $this->params->get('linkstarget');
            if ($linkstarget == "new") {
                     $target = " target='_blank'";
            } else {
                $target = "";
            }

            $data = [];
            $data['moodle_page_type'] = 'event';
            $data['id'] = $item['id'];
            $jump_url = ContentHelper::getJumpURL($data);

            $link = $jump_url . "&mtype=item&id=" . $item['courseid'] . "&time=" . $item['timestart'];
            if ($lang) {
                $link .= "&lang=$lang";
            }

            echo "<a $target href=\"$link\">" . HTMLHelper::_('date', $item['timestart'], Text::_('DATE_FORMAT_LC2')) . "</a>";

            ?>
        </div>
        <div class="joomdle_item_content joomdle_item_list_item_name">
                <?php echo $item['name']; ?>
        </div>
    </div>
<?php endforeach; ?>
</div>

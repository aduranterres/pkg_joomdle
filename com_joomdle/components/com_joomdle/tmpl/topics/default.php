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

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('chosen', 'media/com_joomdle/css/joomdle.css');


$show_topics_numbers = $this->params->get('topics_show_numbers');
?>
<div class="joomdle-topiclist<?php echo $this->pageclass_sfx;?>">
    <h1>
                <?php echo $this->course['fullname'] . ': '; ?>
                <?php echo Text::_('COM_JOOMDLE_TOPICS'); ?>
    </h1>
<?php
if (is_array($this->items)) {
    foreach ($this->items as $item) : ?>
        <?php if (($item['summary']) || ($item['name'])) : ?>
    <div class="joomdle_course_list_item">
        <div class="joomdle_card">
            <div class="joomdle_course_info">
            <?php if ($show_topics_numbers) : ?>
                <div class="joomdle_course_title">
                <?php
                $title = '';
                if ($item['name']) {
                    $title = $item['name'];
                } else {
                    if ($item['section']) {
                        $title =  Text::_('COM_JOOMDLE_SECTION') . " ";
                        $title .= $item['section'] ;
                    } else {
                        $title =  Text::_('COM_JOOMDLE_INTRO');
                    }
                }
                echo "<b>$title</b>";
                ?>
                </div>
            <?php endif; ?>
                <div class="joomdle_course_description">
                    <?php echo $item['summary']; ?>
                </div>
            </div>
        </div>
    </div>
    <br>
        <?php endif; ?>
    <?php endforeach;
}; ?>

<?php if ($this->params->get('show_back_links')) : ?>
    <div>
    <P align="center">
    <a href="javascript: history.go(-1)"><?php echo Text::_('COM_JOOMDLE_BACK'); ?></a>
    </P>
    </div>
<?php endif; ?>

</div>

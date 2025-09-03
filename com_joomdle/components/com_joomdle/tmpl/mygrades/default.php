<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
?>

<div class="joomdle-gradelist<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get('pageclass_sfx'); ?>">
<?php
foreach ($this->items as $item) :
    $tasks = $item['grades'];
    if ((!is_array($tasks)) || (!count($tasks))) {
        continue;
    }

    ?>
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get('pageclass_sfx'); ?>">
            <h4>
                <?php echo Text::_('COM_JOOMDLE_COURSE_TASKS'); ?>:
                <?php echo $item['fullname']; ?>
            </h4>
        </td>
        <td width="30" height="20" class="sectiontableheader<?php echo $this->params->get('pageclass_sfx'); ?>"  nowrap="nowrap">
            <h4>
                <?php echo Text::_('COM_JOOMDLE_GRADE'); ?>
            </h4>
        </td>
</tr>
    <?php

    $odd = 0;
    foreach ($tasks as $task) :
        if ($task['itemname']) :
            ?>
        <tr class="sectiontableentry<?php echo $odd + 1; ?>">
                        <?php $odd++;
                        $odd = $odd % 2; ?>
                <td height="20">
                        <?php echo $task['itemname']; ?>
                </td>
                <td height="20">
                        <?php echo $task['finalgrade']; ?>
                </td>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endforeach; ?>

</table>
</div>

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
    <h1>
        <?php echo $this->course['fullname'] . ': '; ?>
        <?php echo Text::_('COM_JOOMDLE_GRADING_SYSTEM'); ?>
    </h1>

    <table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="table <?php echo $this->params->get('pageclass_sfx'); ?>">
        <tr>
            <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get('pageclass_sfx'); ?>">
                    <?php echo Text::_('COM_JOOMDLE_TASKS'); ?>
            </td>
            <td width="30" height="20" class="sectiontableheader<?php echo $this->params->get('pageclass_sfx'); ?>" style="text-align:center;" nowrap="nowrap">
                    <?php echo Text::_('COM_JOOMDLE_VALUE'); ?>
            </td>
        </tr>

        <?php
        if (is_array($this->items)) {
            foreach ($this->items as $gcat) : ?>
        <tr class="sectiontableentry">
            <td height="20" align="left">
                <?php echo $gcat['fullname']; ?>
            </td>
            <td height="20" align="right">
                <?php printf("%.2f", $gcat['grademax']); ?>%
            </td>
        </tr>
            <?php endforeach;
        }; ?>
    </table>

<?php if ($this->params->get('show_back_links')) : ?>
    <div>
    <P align="center">
    <a href="javascript: history.go(-1)"><?php echo Text::_('COM_JOOMDLE_BACK'); ?></a>
    </P>
    </div>
<?php endif; ?>

</div>

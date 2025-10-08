<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access'); ?>

<div class="joomdle-gradelist<?php echo $this->pageclass_sfx;?>">
    <table width="100%" cellpadding="4" cellspacing="0" border="1" align="center" class="contentpane<?php echo $this->params->get('pageclass_sfx'); ?>">
        <tr>
                <td width="25%" height="20" class="sectiontableheader<?php echo $this->params->get('pageclass_sfx'); ?>">
                        <?php echo Text::_('COM_JOOMDLE_CATEGORY'); ?>
                </td>
                <td width="30%" height="20" class="sectiontableheader<?php echo $this->params->get('pageclass_sfx'); ?>" style="text-align:center;" nowrap="nowrap">
                        <?php echo Text::_('COM_JOOMDLE_ASSESSMENT_TITLE'); ?>
                </td>
                <td width="10%" height="20" class="sectiontableheader<?php echo $this->params->get('pageclass_sfx'); ?>" style="text-align:center;" nowrap="nowrap">
                        <?php echo Text::_('COM_JOOMDLE_DUE_DATE'); ?>
                </td>
                <td width="5%" height="20" class="sectiontableheader<?php echo $this->params->get('pageclass_sfx'); ?>" style="text-align:center;" nowrap="nowrap">
                        <?php echo Text::_('COM_JOOMDLE_RANGE'); ?>
                </td>
                <td width="5%" height="20" class="sectiontableheader<?php echo $this->params->get('pageclass_sfx'); ?>" style="text-align:center;" nowrap="nowrap">
                        <?php echo Text::_('COM_JOOMDLE_MARK'); ?>
                </td>
        <?php if ($this->items['config']['showlettergrade']) :
            ?>
                <td width="5%" height="20" class="sectiontableheader<?php echo $this->params->get('pageclass_sfx'); ?>" style="text-align:center;" nowrap="nowrap">
                        <?php echo Text::_('COM_JOOMDLE_LETTER_GRADE'); ?>
                </td>
            <?php
        endif; ?>
                <td width="15%" height="20" class="sectiontableheader<?php echo $this->params->get('pageclass_sfx'); ?>" style="text-align:center;" nowrap="nowrap">
                        <?php echo Text::_('COM_JOOMDLE_FEEDBACK'); ?>
                </td>
        </tr>

<?php
$i = 0;
$odd = 0;
$total = array_shift($this->items['data']);
if (is_array($this->items)) {
    foreach ($this->items['data'] as $gcat) :
        $n = count($gcat['items']);
        ?>
        <?php
        $cat_shown =  false;
        foreach ($gcat['items'] as $item) :
            ?>
        <tr>
            <?php if (!$cat_shown) :
                ?>
            <td rowspan="<?php echo $n + 1; ?>" valign="top">
                <?php
                echo $gcat['fullname']; ?>
                <br>
                <?php printf("%d", $gcat['grademax']); ?>% <?php echo Text::_('COM_JOOMDLE_OF_TOTAL');
                $cat_shown = true;
                ?>
            </td>
                <?php
            endif; ?>
            <td width="30%">
            <?php echo $item['name']; ?>
            </td>
            <td width="10%" style="text-align:center;">
            <?php
            if ($item['due']) {
                echo HTMLHelper::_('date', $item['due'], Text::_('DATE_FORMAT_LC4'));
            }
            ?>      
            </td>
            <td width="5%"  style="text-align:center;">
            <?php printf("%d", $item['grademax']); ?>
            </td>
            <td width="5%" style="text-align:center;">
            <?php if ($item['finalgrade'] < 0) :
                ?>
                -
                <?php
            else :
                ?>
                <?php echo $item['finalgrade']; ?>
                <?php
            endif; ?>
            </td>
            <?php if ($this->items['config']['showlettergrade']) :
                ?>
            <td width="5%" style="text-align:center;">
                <?php echo $item['letter']; ?>
            </td>
                <?php
            endif; ?>

            <td width="15%">
                <?php echo $item['feedback']; ?>
            </td>
        </tr>
            <?php
        endforeach; ?>
        <tr>
            <td>
                    <?php echo Text::_('COM_JOOMDLE_CATEGORY_TOTAL'); ?>
            </td>
            <td>
            </td>
            <td align="center">
            <?php printf("%d", $gcat['grademax']); ?>%
            </td>
            <td align="center">
            <?php if ($gcat['finalgrade'] < 0) :
                ?>
                -
                <?php
            else :
                ?>
                <?php echo $gcat['finalgrade']; ?>
                <?php
            endif; ?>
            </td>
            <?php if ($this->items['config']['showlettergrade']) :
                ?>
            <td width="5%" style="text-align:center;">
                <?php echo $gcat['letter']; ?>
            </td>
                <?php
            endif; ?>
            <td>
            </td>
        </tr>

        <?php
    endforeach;
}; ?>

        <tr>
            <td>
                <?php echo Text::_('COM_JOOMDLE_SUBJECT_TOTAL'); ?>
            </td>
            <td>
            </td>
            <td>
            </td>
            <td align="center">
                <?php printf("%d", $total['grademax']); ?>
            </td>
            <td align="center">
            <?php if ($total['finalgrade'] < 0) :
                ?>
                -
                <?php
            else :
                ?>
                <?php echo $total['finalgrade']; ?>
                <?php
            endif; ?>
            </td>
            <?php if ($this->items['config']['showlettergrade']) :
                ?>
            <td width="5%" style="text-align:center;">
                <?php echo $total['letter']; ?>
            </td>
                <?php
            endif; ?>
        </tr>
    </table>
</div>

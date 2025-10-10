<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomdle\Component\Joomdle\Administrator\Helper\MailinglistHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

// Show message if not configured
if ($this->message) {
    echo $this->message;
    return;
}

$i = 0;
?>

<form action="index.php" method="POST" id="adminForm" name="adminForm">
    <table class="table">
        <thead>
            <tr>
                <th width="10">ID</th>
                <th width="10"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" /></th>
                <th width="600"><?php echo Text::_('COM_JOOMDLE_COURSE'); ?></th>
                <th class="center"><?php echo Text::_('COM_JOOMDLE_MAILING_LIST_STUDENTS'); ?></th>
                <th class="center"><?php echo Text::_('COM_JOOMDLE_MAILING_LIST_TEACHERS'); ?></th>
            </tr>              
        </thead>
        <tbody>
        <?php
        $row = MailinglistHelper::getGeneralLists();
        $checked = HTMLHelper::_('grid.id', $i, $row->id);
        $row->published = $row->published_students;
        $published_students      = HTMLHelper::_('jgrid.published', $row->published, $i, 'mailinglist.students');

        $row->published = $row->published_teachers;
        $published_teachers      = HTMLHelper::_('jgrid.published', $row->published, $i, 'mailinglist.teachers');
        $i++;
        ?>
            <tr>
                <td><?php echo $row->id;?></td>
                <td><?php echo $checked; ?></td>
                <td><?php echo $row->fullname;?></td>
                <td class="center"><?php echo $published_students; ?> </td>
                <td class="center"><?php echo $published_teachers; ?> </td>
            </tr>
        </tbody>
    </table>
    <table class="table">
        <thead>
            <tr>
                <th width="10">ID</th>
                <th width="10"></th>
                <th width="600"><?php echo Text::_('COM_JOOMDLE_COURSE'); ?></th>
                <th class="center"><?php echo Text::_('COM_JOOMDLE_MAILING_LIST_STUDENTS'); ?></th>
                <th class="center"><?php echo Text::_('COM_JOOMDLE_MAILING_LIST_TEACHERS'); ?></th>
            </tr>              
        </thead>
        <tbody>
        <?php
        foreach ($this->items as $row) {
            $i++;
            $checked = HTMLHelper::_('grid.id', $i, $row->id);
            $row->published = $row->published_students;
            $published_students      = HTMLHelper::_('jgrid.published', $row->published, $i, 'mailinglist.students');
            $row->published = $row->published_teachers;
            $published_teachers      = HTMLHelper::_('jgrid.published', $row->published, $i, 'mailinglist.teachers');
            ?>
            <tr>
                <td><?php echo $row->id;?></td>
                <td><?php echo $checked; ?></td>
                <td><?php echo $row->fullname;?></td>
                <td class="center"><?php echo $published_students; ?> </td>
                <td class="center"><?php echo $published_teachers; ?> </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
      
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>   
    <input type="hidden" name="option" value="com_joomdle"/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

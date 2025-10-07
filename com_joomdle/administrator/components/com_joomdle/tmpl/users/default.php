<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
?>

<form action="<?php echo Route::_('index.php?option=com_joomdle&view=users'); ?>" method="post"
      name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
            <?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
                <table class="table" id="courseList">
                    <thead>
                    <tr>
                        <th class="w-1 text-center">
                            <input type="checkbox" autocomplete="off" class="form-check-input" name="checkall-toggle" value=""
                                   title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
                        </th>
                        <th class='left'>
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_JOOMDLE_USERNAME', 'username', $listDirn, $listOrder); ?>
                        </th>
                        <th class='left'>
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_JOOMDLE_NAME', 'name', $listDirn, $listOrder); ?>
                        </th>
                        <th class='left'>
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_JOOMDLE_EMAIL', 'email', $listDirn, $listOrder); ?>
                        </th>
                        <th class='left'>
                            <?php echo Text::_('COM_JOOMDLE_JOOMLA_ACCOUNT'); ?>
                        </th>
                        <th class='left'>
                            <?php echo Text::_('COM_JOOMDLE_MOODLE_ACCOUNT'); ?>
                        </th>
                        <th class='left'>
                            <?php echo Text::_('COM_JOOMDLE_JOOMDLE_USER'); ?>
                        </th>
                        <th scope="col" class="w-3 d-none d-lg-table-cell" >
                            <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->items as $i => $item) : ?>
                        <tr class="row<?php echo $i % 2; ?>" data-draggable-group='1' data-transition>
                            <td class="text-center">
                                <?php if (!$item['admin']) : ?>
                                    <?php echo HTMLHelper::_('grid.id', $i, $item['id']); ?>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php echo $this->escape($item['username']); ?>
                            </td>
                            <td>
                                <?php echo $this->escape($item['name']); ?>
                            </td>
                            <td>
                                <?php echo $item['email']; ?>
                            </td>
                            <td class="text-center">
                            <?php
                            if (!empty($item['j_account'])) {
                                echo '<span class="icon-check text-success"></span>';
                            }
                            ?>
                            </td>
                            <td class="text-center">
                            <?php
                            if (!empty($item['m_account'])) {
                                echo '<span class="icon-check text-success"></span>';
                            }
                            ?>
                            </td>
                            <td class="text-center">
                            <?php
                            if (($item['auth'] == 'joomdle')) {
                                echo '<span class="icon-check text-success"></span>';
                            }
                            ?>
                            </td>
                            <td class="d-none d-lg-table-cell">
                            <?php echo $item['id']; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <?php echo $this->pagination->getListFooter(); ?>

                <input type="hidden" name="task" value=""/>
                <input type="hidden" name="boxchecked" value="0"/>
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>

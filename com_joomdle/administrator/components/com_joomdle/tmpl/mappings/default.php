<?php

/**
  * @package      Joomdle
  * @copyright    Qontori Pte Ltd
  * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
  */

\defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
?>
<form action="<?php echo Route::_('index.php?option=com_joomdle&view=mappings'); ?>" method="post" id="adminForm" name="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
                <?php if (empty($this->items)) :
                    ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                    <?php
                else :
                    ?>
                    <table class="table" id="mappinglist">
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_JOOMDLE_JOOMLA_COMPONENT', 'joomla_app', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col">
                                    <?php echo Text::_('COM_JOOMDLE_JOOMLA_FIELD'); ?>
                                </th>
                                <th scope="col">
                                    <?php echo Text::_('COM_JOOMDLE_MOODLE_FIELD'); ?>
                                </th>
                                <th scope="col" class="w-5 d-none d-md-table-cell">
                                    <?php echo Text::_('JGRID_HEADING_ID'); ?>
                                </th>
                            </tr>              
                        </thead>
                        <tbody>
                        <?php
                        foreach ($this->items as $i => $item) :
                            ?>
                            <tr class="row<?php echo $i % 2; ?>">
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->joomla_field); ?>
                                </td>
                                <td>
                                    <?php echo $item->joomla_app;?>
                                </td>
                                <td>
                                    <a href='index.php?option=com_joomdle&task=mapping.edit&id=<?php echo $item->id;?>'><?php echo $item->joomla_field_name; ?></a>
                                </td>
                                <td>
                                    <a href='index.php?option=com_joomdle&task=mapping.edit&id=<?php echo $item->id;?>'><?php echo $item->moodle_field_name; ?></a>
                                </td>
                                <td>
                                    <?php echo $item->id;?>
                                </td>
                           </tr>
                            <?php
                        endforeach;
                        ?>
                        </tbody>
                    </table>

                    <?php // load the pagination. ?>
                    <?php echo $this->pagination->getListFooter(); ?>

                    <?php
                endif; ?>

                   <input type="hidden" name="task" value=""/>
                   <input type="hidden" name="boxchecked" value="0"/>   
                   <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>

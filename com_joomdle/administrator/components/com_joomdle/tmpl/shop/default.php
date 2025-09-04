<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
?>

<form action="<?php echo Route::_('index.php?option=com_joomdle&view=shop'); ?>" method="post" id="adminForm" name="adminForm">
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
                    <table class="table" id="shopList">
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_JOOMDLE_COURSE', 'name', $listDirn, $listOrder); ?>
                                </th>
                                <th class="col text-center">
                                    <?php echo Text::_('COM_JOOMDLE_SELL_ON_SHOP'); ?>
                                </th>
                                <th scope="col" class="w-5 d-none d-md-table-cell">
                                    <?php echo Text::_('JGRID_HEADING_ID'); ?>
                                </th>
                            </tr>              
                        </thead>
                        <tbody>
                            <?php
                            foreach ($this->items as $i => $item) :
                                if ((property_exists($item, 'is_bundle')) && ($item->is_bundle)) {
                                    $checked = HTMLHelper::_('grid.id', $i, "bundle_" . $item->id);
                                } else {
                                    $checked = HTMLHelper::_('grid.id', $i, $item->id);
                                }
                                   $published = HTMLHelper::_('jgrid.published', $item->published, $i, 'shop.');
                                ?>
                            <tr class="row<?php echo $i % 2; ?>">
                                <td><?php echo $checked; ?></td>
                                <td>
                                <?php if ((property_exists($item, 'is_bundle')) && ($item->is_bundle)) :
                                    ?>
                                    <a href="index.php?option=com_joomdle&view=bundle&task=bundle.edit&id=<?php echo $item->id; ?>"><?php echo $item->name;?></a>
                                    <?php
                                else :
                                    ?>
                                    <?php echo '(' . $item->shortname . ') ' . $item->fullname; ?>
                                    <?php
                                endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo $published; ?>
                             </td>
                              <td class="text-center">
                                    <?php echo $item->id; ?>
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

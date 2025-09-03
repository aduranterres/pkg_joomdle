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

?>
  <?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
  <?php else : ?>
        <div id="j-main-container">
  <?php endif;?>

       <table  width="100%"  class="table table-striped">
             <thead>
                    <tr>
                           <th width="30%"><?php echo Text::_('COM_JOOMDLE_CHECK'); ?></th>
                           <th width="10%"><?php echo Text::_('COM_JOOMDLE_STATUS'); ?></th>
                           <th><?php echo Text::_('COM_JOOMDLE_ERROR'); ?></th>
                    </tr>
             </thead>
             <tbody>
                    <?php
                    $k = 0;
                    $i = 0;
                    foreach ($this->system_info as $row) {
                        ?>
                           <tr class="<?php echo "row$k";?>">
                                  <td><?php echo $row['description'];?></td>
                                  <td align="center"><?php echo ($row['value'] == 1) ? HTMLHelper::image('media/com_joomdle/images/tick.png', 'Ok') : HTMLHelper::image('media/com_joomdle/images/publish_r.png', 'Error'); ?></td>
                                  <td align="center"><?php echo $row['error']; ?> </td>
                           </tr>
                        <?php
                        $k = 1 - $k;
                        $i++;
                    }
                    ?>
             </tbody>
       </table>
       <br>
<center><?php echo Text::_('COM_JOOMDLE_FOR_PROBLEM_RESOLUTION_SEE'); ?>: <a target='_blank' href="https://www.joomdle.com/wiki/System_health_check">https://www.joomdle.com/wiki/System_health_check</a></center>
</div>

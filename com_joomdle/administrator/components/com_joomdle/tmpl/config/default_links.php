<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<fieldset class="form-horizontal">
    <legend><?php echo Text::_('COM_JOOMDLE_LINKS_BEHAVIOUR'); ?></legend>
    <?php
    foreach ($this->form->getFieldset('links') as $field) :
        ?>
        <div class="control-group">
            <div class="control-label"><?php echo $field->label; ?></div>
            <div class="controls"><?php echo $field->input; ?></div>
        </div>
        <?php
    endforeach;
    ?>
</fieldset>

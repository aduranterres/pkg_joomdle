<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');

?>

<form action="<?php echo Route::_('index.php?option=com_joomdle&view=uploadpasswords'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal" enctype="multipart/form-data">
    <fieldset>
        <ul class="nav nav-tabs">
        <li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('COM_JOOMDLE_UPLOAD_PASSWORDS');?></a></li>
        </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="details">
                    <?php foreach ($this->form->getFieldset('uploadpasswords') as $field) :?>
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $field->label; ?>
                            </div>
                            <div class="controls">
                                <?php echo $field->input; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
        </div>
    </fieldset>
    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

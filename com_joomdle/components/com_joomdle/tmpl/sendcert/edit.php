<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

?>
<script type="text/javascript">
    Joomla.submitbutton = function(pressbutton) {
        var form = document.getElementById('mailtoForm');

        // do field validation
        if (form.mailto.value == "" || form.from.value == "") {
            alert('<?php echo Text::_('COM_JOOMDLE_EMAIL_ERR_NOINFO'); ?>');
            return false;
        }
        form.submit();
    }
</script>
<?php
$data   = $this->get('data');
?>

<div id="mailto-window">
    <h2>
        <?php echo Text::_('COM_JOOMDLE_EMAIL_CERTIFICATE'); ?>
    </h2>
    <form action="<?php echo Route::_('index.php?option=com_joomdle&view=sendcert&layout=edit&tmpl=component'); ?>" method="post" name="adminForm" id="sendcert-form" class="form-validate form-horizontal">

        <div>
            <div class="row">
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-md-6">
                        <?php foreach ($this->form->getFieldset('sendcert') as $field) :?>
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
                </div>
            </div>

            <div class="btn-toolbar">
                <div class="btn-group">
                    <button type="submit" class="btn btn-info validate" name="task" value="sendcert.sendcertificate">
                        <span class="icon-ok"></span> <?php echo Text::_('COM_JOOMDLE_SEND') ?>
                    </button>
                </div>
            </div>

        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>

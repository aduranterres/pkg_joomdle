<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;
use Joomdle\Component\Joomdle\Administrator\Helper\MappingsHelper;

$itemid = ContentHelper::getMenuItem();
?>
<div class="joomdle-userlist">
    <h1>
        <?php echo $this->course['fullname'] . ': '; ?>
        <?php echo Text::_('COM_JOOMDLE_TEACHERS'); ?>
    </h1>

<?php
if (is_array($this->items)) {
    foreach ($this->items as $item) : ?>
        <?php
        $user_info = MappingsHelper::getUserInfoJoomla($item['username']);
        if (!count($user_info)) { //not a Joomla user
            continue;
        }
        ?>
    <div class="joomdle_user_list_item">
        <div class="joomdle_card">
            <div class="joomdle_user_list_item_name">
                <a href="<?php echo Route::_("index.php?option=com_joomdle&view=teacher&username=" . $item['username'] . "&Itemid=$itemid"); ?>"><?php echo $item['firstname'] . " " . $item['lastname']; ?></a>
            </div>
        </div>
    </div>
    <?php endforeach;
}
?>

<?php if ($this->params->get('show_back_links')) : ?>
    <div>
    <P align="center">
    <a href="javascript: history.go(-1)"><?php echo Text::_('COM_JOOMDLE_BACK'); ?></a>
    </P>
    </div>
<?php endif; ?>

</div>

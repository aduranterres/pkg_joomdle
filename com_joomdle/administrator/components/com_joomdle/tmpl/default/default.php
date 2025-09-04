<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

?>
<style>
.cpanel{padding-left:25px;text-align:left}

.cpanel div.icon ,
#cpanel div.icon {
    margin-left: 5px;
    float: right;
}

.cpanel div.icon a ,
#cpanel div.icon a {
    float: right;
}

.cpanel img ,
#cpanel img {
    padding: 10px 0;
    margin: 0 auto;
}

div.cpanel-icons {
    float: right;
}

div.cpanel-component {
    float: left;
}

.panel_btn {
    display: inline-block;
    *display: inline;
    *zoom: 1;
    padding: 4px 12px;
    margin-bottom: 0;
    font-size: 13px;
    line-height: 18px;
    text-align: center;
    vertical-align: middle;
    cursor: pointer;
    background-color: #f3f3f3;
    color: #333;
    border: 1px solid #b3b3b3;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
</style>

       <table class="adminlist">

             <tbody>
             <tr>
            <td width="55%" valign="top">
            <div id="cpanel">
            <table>
            <tr>
                <td>
            <?php
            $link = 'index.php?option=com_joomdle&amp;view=config';
            $this->showButton($link, 'config.png', Text::_('COM_JOOMDLE_CONFIGURATION'));
            ?>
                </td>
                <td>
            <?php
            $link = 'index.php?option=com_joomdle&amp;view=users';
            $this->showButton($link, 'users.png', Text::_('COM_JOOMDLE_USERS'));
            ?>
                </td>
                <td>
            <?php
            $link = 'index.php?option=com_joomdle&amp;view=mappings';
            $this->showButton($link, 'mappings.png', Text::_('COM_JOOMDLE_DATA_MAPPINGS'));
            ?>
                </td>
            </tr>
            <tr>
                <td>
            <?php
            $link = 'index.php?option=com_joomdle&amp;view=shop';
            $this->showButton($link, 'vmart.png', Text::_('COM_JOOMDLE_SHOP_INTEGRATION'));
            ?>
                </td>
                <td>
            <?php
            $link = 'index.php?option=com_joomdle&amp;view=mailinglist';
            $this->showButton($link, 'lists.png', Text::_('COM_JOOMDLE_MAILING_LIST_INTEGRATION'));
            ?>
                </td>
                <td>
            <?php
            $link = 'index.php?option=com_joomdle&amp;view=check';
            $this->showButton($link, 'info.png', Text::_('COM_JOOMDLE_SYSTEM_CHECK'));
            ?>
                </td>
            </tr>
            </table>
            </div>
            </td>
            <td width="45%" valign="top">
            <div style="width: 100%">
<?php
            $title = Text::_("COM_JOOMDLE_ABOUT");
$options = array ('active' => 'about');
echo HTMLHelper :: _('bootstrap.startTabSet', 'myTab', $options);
echo HTMLHelper :: _('bootstrap.addTab', 'myTab', 'about', $title);
$renderer = 'renderAbout';
echo $this->$renderer();
echo HTMLHelper :: _('bootstrap.endTabSet');
?>

            </div>
            </td>
        </tr>
             </tbody>
       </table>
        <input type="hidden" name="option" value="com_joomdle"/>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="hidemainmenu" value="0"/>

        <?php echo HTMLHelper::_('form.token'); ?>


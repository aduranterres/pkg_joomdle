<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomdle\Component\Joomdle\Administrator\Helper\ContentHelper;

$itemid = ContentHelper::getMenuItem();

?>
<div class="joomdle-teacher">
    <h1>
        <?php echo $this->user_info['name']; ?>
    </h1>

    <div class="joomdle_user">
        <?php if ((array_key_exists('pic_url', $this->user_info)) && ($this->user_info['pic_url'] != 'none')) : ?>
        <div class="joomdle_user_pic">
            <?php
            if (array_key_exists('profile_url', $this->user_info)) :
                ?>
                <a href="<?php echo Route::_($this->user_info['profile_url'] . "&Itemid=$itemid"); ?>"><img height='64' width='64' src="<?php echo $this->user_info['pic_url']; ?>"></a>
            <?php else : ?>
                <img height='64' width='64' src="<?php echo $this->user_info['pic_url']; ?>">
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="joomdle_user_details">
            <?php if ((array_key_exists('city', $this->user_info)) && ($this->user_info['city'])) : ?>
            <div class="joomdle_user_city">
                <?php echo '<b>' . Text::_('COM_JOOMDLE_CITY') . ': </b>'; ?>
                <?php echo $this->user_info['city']; ?>
            </div>
            <?php endif; ?>
            <?php if ((array_key_exists('country', $this->user_info)) && ($this->user_info['country'])) : ?>
            <div class="joomdle_user_country">
                <?php echo '<b>' . Text::_('COM_JOOMDLE_COUNTRY') . ': </b>'; ?>
                <?php       echo $this->user_info['country']; ?>
            </div>
            <?php endif; ?>
            <?php if ((array_key_exists('description', $this->user_info)) && ($this->user_info['description'])) : ?>
            <div class="joomdle_user_country">
                <?php echo '<b>' . Text::_('COM_JOOMDLE_ABOUTME') . ': </b>'; ?>
                <?php       echo $this->user_info['description']; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="table">
        <tr>
            <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get('pageclass_sfx'); ?>">
            <?php echo Text::_('COM_JOOMDLE_TEACHED_COURSES'); ?>
            </td>
        </tr>

        <?php
        if (is_array($this->courses)) {
            foreach ($this->courses as $id => $course) :
                $cat_id = $course['cat_id'];
                $course_id = $course['remoteid'];
                $course_slug = ApplicationHelper::stringURLSafe($course['fullname']);
                $cat_slug = ApplicationHelper::stringURLSafe($course['cat_name']);
                ?>
                <tr class="sectiontableentry">
                    <td align="left">
                    <?php
                        $link = Route::_("index.php?option=com_joomdle&view=detail&cat_id=$cat_id-$cat_slug&course_id=$course_id-$course_slug&Itemid=$itemid");
                        echo "<a href=\"$link\">" . $course['fullname'] . "</a>";
                    ?>
                    </td>
                </tr>
            <?php endforeach;
        }
        ?>
</table>
</div>

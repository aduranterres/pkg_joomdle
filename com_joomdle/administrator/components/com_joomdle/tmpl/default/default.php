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
.joomdle-dashboard {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
    align-items: flex-start;
}

.joomdle-dashboard-main {
    flex: 1 1 55%;
    min-width: 300px;
}

.joomdle-dashboard-side {
    flex: 1 1 35%;
    min-width: 280px;
}

.joomdle-cpanel-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    max-width: 576px;
    gap: 1rem;
}

.joomdle-cpanel-grid a {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 1.5rem 1rem;
    background: var(--template-bg-dark-3, #fff);
    border: 1px solid var(--template-bg-dark-10, #dee2e6);
    border-radius: 0.5rem;
    text-decoration: none;
    color: var(--template-text-dark, #495057);
    transition: all 0.2s ease;
    text-align: center;
    min-height: 120px;
}

.joomdle-cpanel-grid a:hover,
.joomdle-cpanel-grid a:focus {
    border-color: var(--template-link-color, #3071a9);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
    text-decoration: none;
    color: var(--template-link-color, #3071a9);
}

.joomdle-cpanel-grid a .icon-fw {
    font-size: 2rem;
    font-weight: 900;
    color: var(--template-link-color, #3071a9);
}

.joomdle-cpanel-grid a:hover .icon-fw {
    color: var(--template-link-hover-color, #234b6e);
}

.joomdle-cpanel-grid a span {
    font-size: 0.875rem;
    font-weight: 500;
    line-height: 1.3;
}
</style>

<div class="joomdle-dashboard">
    <div class="joomdle-dashboard-main">
        <div class="joomdle-cpanel-grid">
            <?php
            $this->showButton('index.php?option=com_joomdle&amp;view=config', 'icon-cog', Text::_('COM_JOOMDLE_CONFIGURATION'));
            $this->showButton('index.php?option=com_joomdle&amp;view=users', 'icon-users', Text::_('COM_JOOMDLE_USERS'));
            $this->showButton('index.php?option=com_joomdle&amp;view=mappings', 'icon-link', Text::_('COM_JOOMDLE_DATA_MAPPINGS'));
            $this->showButton('index.php?option=com_joomdle&amp;view=shop', 'icon-cart', Text::_('COM_JOOMDLE_SHOP_INTEGRATION'));
            $this->showButton('index.php?option=com_joomdle&amp;view=mailinglist', 'icon-envelope', Text::_('COM_JOOMDLE_MAILING_LIST_INTEGRATION'));
            $this->showButton('index.php?option=com_joomdle&amp;view=check', 'icon-info-circle', Text::_('COM_JOOMDLE_SYSTEM_CHECK'));
            ?>
        </div>
    </div>
    <div class="joomdle-dashboard-side">
        <?php
        $title = Text::_("COM_JOOMDLE_ABOUT");
        $options = array('active' => 'about');
        echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', $options);
        echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'about', $title);
        $renderer = 'renderAbout';
        echo $this->$renderer();
        echo HTMLHelper::_('bootstrap.endTabSet');
        ?>
    </div>
</div>
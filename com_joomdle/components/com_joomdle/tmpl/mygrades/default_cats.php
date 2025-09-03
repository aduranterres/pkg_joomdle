<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

?>
<div class="joomdle-gradelist<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

<?php
foreach ($this->items as $this->course) :
    if (count($this->course['grades']['data']) == 0) {
        continue;
    }
        echo $this->loadTemplate('course');
endforeach; ?>

</div>

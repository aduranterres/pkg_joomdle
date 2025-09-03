<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Router\Route;

$unicodeslugs = Factory::getConfig()->get('unicodeslugs');
?>

<div class="joomdle-categorylist<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

    <?php
    if (is_array($this->items)) {
        foreach ($this->items as $cat) : ?>
    <div class="joomdle_category_list_item">
        <div class="joomdle_card">
            <div class="joomdle_category_info">
                <div class="joomdle_category_list_item_title">
                    <?php
                    if ($unicodeslugs == 1) {
                        $slug = OutputFilter::stringURLUnicodeSlug($cat['name']);
                    } else {
                        $slug = OutputFilter::stringURLSafe($cat['name']);
                    }

                    $url = Route::_("index.php?option=com_joomdle&view=coursecategory&cat_id=" . $cat['id'] . '-' . $slug); ?>
                    <?php  echo "<a href=\"$url\">" . $cat['name'] . "</a>"; ?>
                </div>
                    <?php if ($cat['description']) : ?>
                <div class=" joomdle_course_list_item_description">
                        <?php echo $cat['description']; ?>
                </div>
                    <?php endif; ?>
            </div>
        </div>
    </div>
        <?php endforeach;
    }; ?>
</div>

<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Administrator\Service\Html;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseDriver;

/**
 * Joomdle HTML Helper.
 *
 * @since  1.0.0
 */
class JOOMDLE
{
    use DatabaseAwareTrait;

    /**
     * Public constructor.
     *
     * @param   DatabaseDriver  $db  The Joomla DB driver object for the site's database.
     */
    public function __construct(DatabaseDriver $db)
    {
        $this->setDatabase($db);
    }

    public function toggle($value = 0, $view = '', $field = '', $i = '')
    {
        $states = array(
            0 => array('icon-unpublish', Text::_('Toggle'), ''),
            1 => array('icon-publish', Text::_('Toggle'), '')
        );

        $state  = ArrayHelper::getValue($states, (int) $value, $states[0]);
        $text   = '<span aria-hidden="true" class="' . $state[0] . '"></span>';
        $html   = '<a href="javascript:void(0);" class="tbody-icon ' . $state[2] . '"';
        $html  .= 'onclick="return Joomla.toggleField(\'cb' . $i . '\',\'' . $view . '.toggle\',\'' . $field . '\')" title="' . Text::_($state[1]) . '">' . $text . '</a>';

        return $html;
    }
}

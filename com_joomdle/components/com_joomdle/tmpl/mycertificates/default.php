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

$this->moodle_url = $this->params->get('MOODLE_URL');

if (!count($this->items)) {
    echo '<span class="joomdle_nocourses_message">' . Text::_('COM_JOOMDLE_NO_CERTIFICATES_YET') . "</span>";
    return;
}
?>

<div class="joomdle-mycertificates<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
    <h1>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>


    <div class="joomdle_mycertificates">
    <ul>
    <?php
    if (is_array($this->items)) {
        foreach ($this->items as $cert) :  ?>
            <li>
                    <?php
                    $id = $cert['id'];

                    switch ($this->type) {
                        case 'simple':
                            $redirect_url = $this->moodle_url . "/mod/simplecertificate/view.php?id=$id&certificate=1&action=review";
                            break;
                        case 'custom':
                            $redirect_url = $this->moodle_url . "/mod/customcert/view.php?id=$id&downloadown=1";
                            break;
                        case 'coursecertificate':
                            $redirect_url = $this->moodle_url . "/admin/tool/certificate/view.php?code=" . $cert['code'];
                            break;
                        default:
                            $redirect_url = $this->moodle_url . "/mod/certificate/view.php?id=$id&certificate=1&action=review";
                            break;
                    }
                    ?>
                <span>
                    <a target='_blank' href="<?php echo $redirect_url; ?>"><?php echo $cert['name']; ?></a>
                <?php if ($this->show_send_certificate) : ?>
                        <a href="index.php?option=com_joomdle&view=sendcert&tmpl=component&type=<?php echo $this->type; ?>&cert_id=<?php echo $id; ?>" onclick="window.open(this.href,'win2','width=400,height=350,menubar=yes,resizable=yes'); return false;" title="Email"><i class="fa fa-envelope" aria-hidden="true"></i></a>
                <?php endif; ?>
                </span>
            </li>
        <?php endforeach;
    }; ?>
    </ul>
    </div>
</div>

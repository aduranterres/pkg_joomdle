<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;

$wrapperUrl = htmlspecialchars(
    (string) $this->wrapper_url,
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);

?>

<style>
    iframe {
        width: 100%;
        height: 100vh;
    }
</style>
<iframe id="myIframe" src="<?php echo $wrapperUrl; ?>" scrolling="no"></iframe>
<script src="media/com_joomdle/js/parent.umd.js"></script>
<script>
    iframeResize({
        license: 'GPLv3',
    }, '#myIframe')
</script>

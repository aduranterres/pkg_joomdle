<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;

?>
<script src="<?php echo URI::root(); ?>/components/com_joomdle/js/iframeResizer.min.js" type="text/javascript"></script>

<style>iframe{width:100%; border:0;}</style>
<iframe id="myIframe" src="<?php echo $this->wrapper->url; ?>" scrolling="no"></iframe>
<?php $crossdomain_autoheight_calculation_method = $this->params->get('crossdomain_autoheight_calculation_method', 'bodyOffset'); ?>
<script>iFrameResize({log:true, heightCalculationMethod:'<?php echo $crossdomain_autoheight_calculation_method;?>'}, '#myIframe')</script>

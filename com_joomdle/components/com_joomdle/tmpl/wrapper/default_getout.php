<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Uri\Uri;

$root = URI::root();
?>
<script type="text/javascript">
top.location.href = "<?php echo $root; ?>";
</script>

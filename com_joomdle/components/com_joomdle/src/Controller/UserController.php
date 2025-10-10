<?php

/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomdle\Component\Joomdle\Site\Controller;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class UserController extends BaseController
{
    public function login()
    {
        /** @var CMSApplication $app */
        $app = Factory::getApplication();

        $params = $app->getParams();
        $moodle_url = $params->get('MOODLE_URL');

        $login_data = $this->input->get('data', '', 'string');
        $wantsurl = base64_decode($this->input->get('wantsurl', '', 'string'));

        if (!$login_data) {
            echo "Login error";
            exit();
        }

        $data = base64_decode($login_data);

        $fields = explode(':', $data);

        $credentials['username'] = $fields[0];
        $credentials['password'] = $fields[1];

        $options = array ('skip_joomdleuserplugin' => '1');

        $app->login($credentials, $options);

        if (!$wantsurl) {
            $wantsurl = $moodle_url;
        }
        $app->redirect($wantsurl);
    }

    public function logout()
    {
        /** @var CMSApplication $app */
        $app = Factory::getApplication();
        $app->redirect(URI::root() . 'index.php?option=com_joomdle&task=user.getoutlogout');
    }

    public function getout()
    {
        $root = URI::root();
        ?>
        <script type="text/javascript">
        top.location.href = "<?php echo $root; ?>";
        </script>
        <?php
    }

    public function getoutlogin()
    {
        $root = URI::root() . 'index.php?option=com_users&view=login';
        ?>
        <script type="text/javascript">
        top.window.location = "<?php echo $root; ?>";
        </script>
        <?php
    }

    public function getoutlogout()
    {
        $root = URI::root() . 'index.php?option=com_joomdle&task=user.dologout';
        ?>
        <script type="text/javascript">
        top.window.location = "<?php echo $root; ?>";
        </script>
        <?php
    }

    public function dologout()
    {
        /** @var CMSApplication $app */
        $app = Factory::getApplication();
        $app->logout();
        $app->redirect(URI::root());
    }
}

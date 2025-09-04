<?php
/**
 * @package     Joomdle
 * @subpackage  com_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Exception\FilesystemException;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

return new class () implements ServiceProviderInterface {
    public function register(Container $container)
    {
		$container->set(
            InstallerScriptInterface::class,
            new class (
            $container->get(AdministratorApplication::class),
            $container->get(DatabaseInterface::class)
            ) implements InstallerScriptInterface {
                private AdministratorApplication $app;
                private DatabaseInterface $db;

                public function __construct(AdministratorApplication $app, DatabaseInterface $db)
                {
                    $this->app = $app;
                    $this->db    = $db;
                }

                public function install(InstallerAdapter $parent): bool
                {
                    $this->app->enqueueMessage('Successful installed.');

                    return true;
                }

                public function update(InstallerAdapter $parent): bool
                {
                    $this->app->enqueueMessage('Successful updated.');

                    return true;
                }

                public function uninstall(InstallerAdapter $parent): bool
                {
                    $this->app->enqueueMessage('Successful uninstalled.');

                    return true;
                }

                public function preflight(string $type, InstallerAdapter $parent): bool
                {
                    return true;
                }

                public function postflight(string $type, InstallerAdapter $parent): bool
                {
                    $this->deleteUnexistingFiles();

                    return true;
                }

                private function deleteUnexistingFiles()
                {
                    $files = [];    // overwrite this line with your files to delete

                    if (empty($files)) {
                        return;
                    }

                    foreach ($files as $file) {
                        try {
                            File::delete(JPATH_ROOT . $file);
                        } catch (\FilesystemException $e) {
                            echo Text::sprintf('FILES_JOOMLA_ERROR_FILE_FOLDER', $file) . '<br>';
                        }
                    }
                }
            }
        );
    }
};

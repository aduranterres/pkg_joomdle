<?php

/**
 * @package     Joomdle
 * @subpackage  plg_system_joomdlesession
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\System\Joomdle\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Plugin\System\Joomdlesession\Extension\Joomdlesession;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   4.4.0
     */
    public function register(Container $container): void
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $plugin     = new Joomdlesession(
                    $container->get(DispatcherInterface::class),
                    (array) PluginHelper::getPlugin('system', 'joomdlesession')
                );
                $plugin->setApplication(Factory::getApplication());
            //     $plugin->setDatabase($container->get(DatabaseInterface::class));
            //    $plugin->setUserFactory($container->get(UserFactoryInterface::class));

                return $plugin;
            }
        );
    }
};

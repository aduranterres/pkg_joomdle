<?php

/**
 * @package     Joomdle
 * @subpackage  plg_task_joomdle
 *
 * @copyright   Antonio Duran Terres
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\Task\Joomdle\Extension;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Event\SubscriberInterface;
use Joomdle\Component\Joomdle\Administrator\Helper\SsoHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Joomdle scheduled tasks.
 *
 * @since  3.1.2
 */
final class Joomdle extends CMSPlugin implements SubscriberInterface
{
    use TaskPluginTrait;

    /**
     * @var string[]
     *
     * @since  3.1.2
     */
    protected const TASKS_MAP = [
        'joomdle.deleteExpiredSsoTickets' => [
            'langConstPrefix' => 'PLG_TASK_JOOMDLE_DELETE_EXPIRED_SSO_TICKETS',
            'method'          => 'deleteExpiredSsoTickets',
        ],
    ];

    /**
     * Autoload the plugin language.
     *
     * @var boolean
     *
     * @since  3.1.2
     */
    protected $autoloadLanguage = true;

    /**
     * @inheritDoc
     *
     * @return  string[]
     *
     * @since  3.1.2
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onTaskOptionsList'    => 'advertiseRoutines',
            'onExecuteTask'        => 'standardRoutineHandler',
            'onContentPrepareForm' => 'enhanceTaskItemForm',
        ];
    }

    /**
     * Delete SSO tickets created more than 24 hours ago.
     *
     * @param   ExecuteTaskEvent  $event  The onExecuteTask event.
     *
     * @return  int  The task exit code.
     *
     * @since  3.1.2
     */
    private function deleteExpiredSsoTickets(ExecuteTaskEvent $event): int
    {
        try {
            $deleted_tickets = SsoHelper::deleteExpiredTickets();

            $this->logTask(
                \sprintf(
                    'Deleted %d expired Joomdle SSO tickets.',
                    $deleted_tickets
                )
            );

            return Status::OK;
        } catch (\Throwable $exception) {
            $this->logTask(
                'Could not delete expired Joomdle SSO tickets: ' . $exception->getMessage(),
                'error'
            );

            return Status::KNOCKOUT;
        }
    }
}

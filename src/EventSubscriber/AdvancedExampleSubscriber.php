<?php
/*
 * This file is part of the F0ska/AutoGridTest package.
 *
 * (c) Victor Shvets
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace F0ska\AutoGridTestBundle\EventSubscriber;

use F0ska\AutoGridBundle\Event\SaveEvent;
use F0ska\AutoGridTestBundle\Entity\BlogUserExample;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AdvancedExampleSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SaveEvent::EVENT_NAME . '.advanced2' => 'onAdvanced2Save',
        ];
    }

    public function onAdvanced2Save(SaveEvent $event): void
    {
        /** @var BlogUserExample $entity */
        $entity = $event->getEntity();
        $entity->setLastIp($this->requestStack->getCurrentRequest()->getClientIp());
        $entity->setBanned($entity->isBanned() ?? false);
    }
}

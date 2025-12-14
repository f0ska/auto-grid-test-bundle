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

use F0ska\AutoGridBundle\Event\MassEvent;
use F0ska\AutoGridBundle\Event\SaveEvent;
use F0ska\AutoGridTestBundle\Entity\BlogUserExample;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

class ExampleSubscriber implements EventSubscriberInterface
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
            MassEvent::EVENT_NAME => 'onMassAction',
        ];
    }

    public function onAdvanced2Save(SaveEvent $event): void
    {
        /** @var BlogUserExample $entity */
        $entity = $event->getEntity();
        $entity->setLastIp($this->requestStack->getCurrentRequest()->getClientIp());
        $entity->setBanned($entity->isBanned() ?? false);
    }

    public function onMassAction(MassEvent $event): void
    {
        /** @var FlashBagAwareSessionInterface $session */
        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add(
            'warning',
            sprintf('Greetings from the "%s", ids: [%s]', $event->getCode(), implode(', ', $event->getIds()))
        );
    }
}

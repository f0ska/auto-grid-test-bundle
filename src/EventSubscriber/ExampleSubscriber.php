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

use DateTimeInterface;
use F0ska\AutoGridBundle\Event\EntityEvent;
use F0ska\AutoGridBundle\Event\ExportEvent;
use F0ska\AutoGridBundle\Event\MassEvent;
use F0ska\AutoGridBundle\Event\SaveEvent;
use F0ska\AutoGridTestBundle\Entity\BlogUserExample;
use F0ska\AutoGridTestBundle\Entity\CustomFormExample;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExampleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EntityEvent::CREATE_EVENT_NAME                    => 'onCustomFormCreate',
            EntityEvent::EDIT_EVENT_NAME                      => 'onCustomFormEdit',
            EntityEvent::VIEW_EVENT_NAME                      => 'onCustomFormView',
            SaveEvent::EVENT_NAME . '.advanced2'              => 'onAdvanced2Save',
            SaveEvent::EVENT_NAME . '.my-custom-form-example' => 'onMyCustomFormExample',
            MassEvent::EVENT_NAME                             => 'onMassAction',
            MassEvent::EVENT_NAME . '.custom_action_redirect' => ['onCustomRedirectMassAction', 10],
            ExportEvent::EVENT_NAME . '.export_example'       => 'onExportExample',
        ];
    }

    public function onAdvanced2Save(SaveEvent $event): void
    {
        /** @var BlogUserExample $entity */
        $entity = $event->getEntity();
        $entity->setLastIp($this->requestStack->getCurrentRequest()->getClientIp());
        $entity->setBanned($entity->isBanned() ?? false);
    }

    public function onCustomFormCreate(EntityEvent $event): void
    {
        $entity = $event->getEntity();
        if (!$entity instanceof CustomFormExample) {
            return;
        }

        $entity->setNote('Prepared by create event');
    }

    public function onCustomFormEdit(EntityEvent $event): void
    {
        $entity = $event->getEntity();
        if (!$entity instanceof CustomFormExample) {
            return;
        }

        $entity->setNote('Prepared by edit event');
    }

    public function onCustomFormView(EntityEvent $event): void
    {
        $entity = $event->getEntity();
        if (!$entity instanceof CustomFormExample) {
            return;
        }

        /** @var FlashBagAwareSessionInterface $session */
        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add('info', sprintf('Viewed "%s"', $entity->getTitle()));
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

    public function onMyCustomFormExample(SaveEvent $event): void
    {
        /** @var CustomFormExample $entity */
        $entity = $event->getEntity();
        $form = $event->getForm();

        if (null === $entity->getId()) {
            $entity->setNote('Prepared by create event');
        } else {
            $entity->setNote('Prepared by edit event');
        }

        $file = $form->get('file')->getData();
        if ($file instanceof UploadedFile) {
            $entity->setFile($file->getContent());
        }
        if ($form->has('delete') && $form->get('delete')->getData()) {
            $entity->setFile(null);
        }
    }

    public function onCustomRedirectMassAction(MassEvent $event): void
    {
        $url = $this->urlGenerator->generate('auto_grid_test_basic');
        $event->setRedirectUrl($url);
    }

    public function onExportExample(ExportEvent $event): void
    {
        $headerWritten = false;
        $limit = 5;
        $offset = 0;

        $builder = $event->getQueryBuilder();
        $builder->select('customActionExample');
        $builder->setMaxResults($limit);
        $hash = sha1($builder->getDQL() . uniqid());
        $resource = fopen('/tmp/' . $hash, 'w');

        do {
            $result = $builder->setFirstResult($offset)->getQuery()->getScalarResult();
            foreach ($result as $item) {
                if (!$headerWritten) {
                    $headerWritten = true;
                    fputcsv($resource, array_keys($item), escape: '');
                }

                $item = array_map(
                    function ($value) {
                        if ($value instanceof DateTimeInterface) {
                            return $value->format(DateTimeInterface::ATOM);
                        }
                        return $value;
                    },
                    $item
                );

                fputcsv($resource, $item, escape: '');
            }
            $offset += $limit;
        } while (!empty($result));

        fclose($resource);

        $url = $this->urlGenerator->generate('auto_grid_test_custom_action_download', ['hash' => $hash]);
        $event->setRedirectUrl($url);
    }
}

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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ThemeSwitcherSubscriber implements EventSubscriberInterface
{
    private const THEMES = [
        'auto-grid' => [
            'label' => 'Bootstrap 5',
            'theme' => '@F0skaAutoGrid/bootstrap_5',
            'form_theme' => '@F0skaAutoGrid/bootstrap_5/form/layout.html.twig',
        ],
        'auto-grid-bootstrap4' => [
            'label' => 'Bootstrap 4',
            'theme' => '@F0skaAutoGrid/bootstrap_4',
            'form_theme' => '@F0skaAutoGrid/bootstrap_4/form/layout.html.twig',
        ],
        'auto-grid-bulma' => [
            'label' => 'Bulma',
            'theme' => '@F0skaAutoGrid/bulma',
            'form_theme' => '@F0skaAutoGrid/bulma/form/layout.html.twig',
        ],
        'auto-grid-foundation' => [
            'label' => 'Foundation',
            'theme' => '@F0skaAutoGrid/foundation',
            'form_theme' => '@F0skaAutoGrid/foundation/form/layout.html.twig',
        ],
        'auto-grid-flowbite' => [
            'label' => 'Flowbite',
            'theme' => '@F0skaAutoGrid/flowbite',
            'form_theme' => '@F0skaAutoGrid/flowbite/form/layout.html.twig',
        ],
    ];

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 30],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $themeParam = $request->attributes->get('_theme', 'auto-grid');

        if (!isset(self::THEMES[$themeParam])) {
            return;
        }

        $theme = self::THEMES[$themeParam];

        $request->attributes->set('_autogrid_theme', $theme['theme']);
        $request->attributes->set('_autogrid_form_themes', [$theme['form_theme']]);
    }

    public static function getThemes(): array
    {
        return self::THEMES;
    }
}

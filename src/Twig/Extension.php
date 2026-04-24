<?php

declare(strict_types=1);

namespace F0ska\AutoGridTestBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Extension extends AbstractExtension
{
    private RequestStack $requestStack;
    private UrlGeneratorInterface $urlGenerator;

    private const THEMES = [
        'auto-grid' => [
            'label' => 'Bootstrap 5',
            'icon' => 'bootstrap5',
            'css' => [
                ['href' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css'],
            ],
            'js' => [
                ['href' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js'],
            ],
            'infoBlock' => [
                'wrapper' => 'alert alert-secondary border shadow-sm mb-4',
                'title' => 'strong mb-2',
                'list' => 'mb-0',
            ],
            'navbar' => [
                'class' => 'navbar navbar-dark navbar-expand-lg bg-dark fixed-top',
                'toggle' => ['data-bs-toggle', 'data-bs-target'],
                'navClass' => 'navbar-nav me-auto mb-2 mb-lg-0',
                'linkClass' => 'nav-link',
                'containerClass' => 'container',
            ],
        ],
        'auto-grid-bootstrap4' => [
            'label' => 'Bootstrap 4',
            'icon' => 'bootstrap4',
            'css' => [
                ['href' => 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css'],
            ],
            'js' => [
                ['href' => 'https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js'],
                ['href' => 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js'],
            ],
            'infoBlock' => [
                'wrapper' => 'alert alert-secondary border shadow-sm mb-4',
                'title' => 'strong mb-2',
                'list' => 'mb-0',
            ],
            'navbar' => [
                'class' => 'navbar navbar-dark navbar-expand-lg bg-dark fixed-top',
                'toggle' => ['data-toggle', 'data-target'],
                'navClass' => 'navbar-nav mr-auto',
                'linkClass' => 'nav-link',
                'containerClass' => 'container',
            ],
        ],
        'auto-grid-bulma' => [
            'label' => 'Bulma',
            'icon' => 'bulma',
            'css' => [
                ['href' => 'https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css'],
            ],
            'js' => [],
            'infoBlock' => [
                'wrapper' => 'notification is-info is-light mb-4',
                'title' => 'has-text-weight-bold mb-2',
                'list' => 'mb-0',
                'rowClass' => 'columns is-multiline',
                'colClass' => 'column is-6',
            ],
            'navbar' => [
                'class' => 'navbar is-dark is-fixed-top',
                'toggle' => ['data-target', 'data-target'],
                'navClass' => 'navbar-menu',
                'linkClass' => 'navbar-item',
                'containerClass' => 'container',
                'burger' => true,
            ],
        ],
        'auto-grid-foundation' => [
            'label' => 'Foundation',
            'icon' => 'foundation',
            'css' => [
                ['href' => 'https://cdn.jsdelivr.net/npm/foundation-sites@6.9.0/dist/css/foundation.min.css'],
            ],
            'js' => [
                ['href' => 'https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js'],
                ['href' => 'https://cdn.jsdelivr.net/npm/foundation-sites@6.9.0/dist/js/foundation.min.js'],
            ],
            'infoBlock' => [
                'wrapper' => 'callout primary mb-4',
                'title' => 'h6 mb-2',
                'list' => 'mb-0',
                'rowClass' => 'grid-x grid-margin-x',
                'colClass' => 'cell medium-6',
            ],
            'navbar' => [
                'class' => 'title-bar',
                'topBarClass' => 'top-bar',
                'navClass' => 'menu',
                'linkClass' => 'menu-item',
                'containerClass' => 'grid-container',
            ],
        ],
        'auto-grid-flowbite' => [
            'label' => 'Flowbite',
            'icon' => 'flowbite',
            'css' => [
                ['href' => 'https://cdn.jsdelivr.net/npm/tailwindcss@4.0.17/index.min.css'],
                ['href' => 'https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.css'],
            ],
            'js' => [
                ['href' => 'https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js'],
            ],
            'infoBlock' => [
                'wrapper' => 'bg-gray-100 border-l-4 border-blue-500 p-4 mb-4',
                'title' => 'font-bold mb-2',
                'list' => 'mb-0',
                'rowClass' => 'grid grid-cols-1 md:grid-cols-2 gap-4',
                'colClass' => '',
            ],
            'navbar' => [
                'class' => 'bg-gray-900 text-white fixed w-full top-0 z-50',
                'navClass' => 'flex items-center',
                'linkClass' => 'block py-2 px-3 text-white rounded hover:bg-gray-700 md:p-0',
                'containerClass' => 'max-w-screen-xl mx-auto px-4',
                'mobileToggle' => true,
            ],
        ],
    ];

    public function __construct(RequestStack $requestStack, UrlGeneratorInterface $urlGenerator)
    {
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('agt_current_theme', $this->getCurrentTheme(...)),
            new TwigFunction('agt_theme_list', $this->getThemeList(...)),
            new TwigFunction('agt_theme_config', $this->getThemeConfig(...)),
            new TwigFunction('agt_theme_url', $this->getThemeUrl(...)),
            new TwigFunction('agt_is_nav_active', $this->isNavActive(...)),
            new TwigFunction('agt_asset_hash', $this->getAssetHash(...)),
        ];
    }

    public function getCurrentTheme(): string
    {
        return $this->requestStack->getCurrentRequest()->attributes->get('_theme') ?? 'auto-grid';
    }

    public function getThemeList(): array
    {
        return self::THEMES;
    }

    public function getThemeConfig(?string $theme = null): ?array
    {
        $theme = $theme ?? $this->getCurrentTheme();
        return self::THEMES[$theme] ?? null;
    }

    public function getThemeUrl(?string $theme = null, ?string $route = null, array $params = []): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $route = $route ?? $request->attributes->get('_route') ?? 'auto_grid_test_basic';
        $targetTheme = $theme ?? $this->getCurrentTheme();

        $routeParams = $request->attributes->get('_route_params', []);
        $routeParams['_theme'] = $targetTheme;
        $routeParams = array_merge($routeParams, $params);

        return $this->generateUrl($route, $routeParams);
    }

    public function isNavActive(string $route): bool
    {
        return $this->requestStack->getCurrentRequest()->attributes->get('_route') === $route;
    }

    public function getAssetHash(string $theme): string
    {
        return substr(md5($theme . date('Y-m-d')), 0, 8);
    }

    private function generateUrl(string $route, array $params): string
    {
        try {
            return $this->urlGenerator->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_PATH);
        } catch (\Exception $e) {
            return '/' . ($params['_theme'] ?? 'auto-grid') . '/';
        }
    }
}

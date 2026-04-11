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
                ['href' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css', 'integrity' => 'LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr', 'crossorigin' => 'anonymous'],
            ],
            'js' => [
                ['href' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js', 'integrity' => 'ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q', 'crossorigin' => 'anonymous'],
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
                ['href' => 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css', 'integrity' => 'xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N', 'crossorigin' => 'anonymous'],
            ],
            'js' => [
                ['href' => 'https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js', 'integrity' => '1H217gwSVyLSIfaLxHbE7dRb3v4mYCKbpQvzx0cegeju1MVsGrX5xXxAvs/HgeFs', 'crossorigin' => 'anonymous'],
                ['href' => 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js', 'integrity' => 'Fy6S3B9q64WdZWQUjU4+vX1OKdaEAiXoiHU7VzF1eH8rL2cMhctTvVTLkJ13NP7F', 'crossorigin' => 'anonymous'],
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
                ['href' => 'https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/css/flowbite.min.css'],
                ['href' => 'https://cdn.jsdelivr.net/npm/tailwindcss@3.4.15/dist/tailwind.min.css'],
            ],
            'js' => [
                ['href' => 'https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/js/flowbite.min.js'],
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
            new TwigFunction('ag_icon', $this->getIcon(...)),
        ];
    }

    public function getIcon(string $name, int $size = 16): string
    {
        $svgStyle = 'style="width:1em;height:1em;margin-bottom:-0.15em;vertical-align:baseline;display:inline-block;"';
        $icons = [
            'arrow-up' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg></i>',
            'chevron-down' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg></i>',
            'arrow-left' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg></i>',
            'sort-neutral' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 9l7-6 7 6"></path><path d="M5 15l7 6 7-6"></path></svg></i>',
            'sort-asc' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 7l7-5 7 5"></path><path d="M5 16l7-5 7 5"></path></svg></i>',
            'sort-desc' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17l7 5 7-5"></path><path d="M5 8l7 5 7-5"></path></svg></i>',
            'filter' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg></i>',
            'filter-active' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg></i>',
            'filter-reset' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon><line x1="3" y1="12" x2="21" y2="12"></line></svg></i>',
            'view' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></i>',
            'edit' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i>',
            'delete' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></i>',
            'add' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg></i>',
            'search' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></i>',
            'id' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg></i>',
            'save' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg></i>',
            'export' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg></i>',
            'warning' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></i>',
            'boolean-true' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></i>',
            'boolean-false' => '<i><svg '.$svgStyle.' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line></svg></i>',
        ];

        return $icons[$name] ?? '';
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

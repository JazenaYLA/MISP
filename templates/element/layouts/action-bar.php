<?php

use Cake\Routing\Router;
?>

<div class="action-bar d-flex flex-nowrap flex-row mt-2 mb-1 rounded">
    <?php
        if (!empty($actions)) {
            echo '<div>';
            $badgeNumber = 0;
            foreach ($actions as $i => $actionEntry) {
                if (!empty($actionEntry['url_vars'])) {
                    $actionEntry['url'] = $this->DataFromPath->buildStringFromDataPath($actionEntry['url'], $entity, $actionEntry['url_vars']);
                }
                if (!empty($actionEntry['badge'])) {
                    $badgeNumber += 1;
                }
                if (!empty($actionEntry['isPOST'])) {
                    $onclickFunction = sprintf('UI.overlayUntilResolve(this, UI.submissionModalAutoGuess(\'%s\'))', h(Router::url($actionEntry['url'])));
                } else if (!empty($actionEntry['isRedirect'])) {
                    $onclickFunction = sprintf('window.location.replace(\'%s\');', h(Router::url($actionEntry['url'])));
                } else {
                    $onclickFunction = sprintf('UI.overlayUntilResolve(this, UI.modalFromUrl(\'%s\'))', h(Router::url($actionEntry['url'])));
                }
                $buttonBadge = !empty($actionEntry['badge']) ? $this->Bootstrap->badge($actionEntry['badge']) : '';
                echo $this->Bootstrap->button([
                    'text' => h($actionEntry['label']),
                    'icon' => h($actionEntry['icon'] ?? false),
                    'variant' => $actionEntry['variant'] ?? 'primary',
                    'class' => ['text-nowrap'],
                    'onclick' => $onclickFunction,
                ], $buttonBadge);
            }
            echo '</div>';
        }

        if (!empty($links)) {
            $goToLinks = [];
            echo '<div class="ms-auto">';
            echo '<div class="d-flex gap-1">';
            foreach ($links as $i => $linkEntry) {
                if (!empty($linkEntry['is-go-to'])) {
                    if (is_bool($linkEntry['is-go-to'])) {
                        $goToLinks['_root'][] = $linkEntry;
                    } else {
                        $goToLinks[$linkEntry['is-go-to']][] = $linkEntry;
                    }
                    continue;
                }
                if (empty($linkEntry['route_path'])) {
                    $active = false;
                } else {
                    $active = $linkEntry['route_path'] == $route_path;
                }
                if (!empty($linkEntry['url_vars'])) {
                    $linkEntry['url'] = $this->DataFromPath->buildStringFromDataPath($linkEntry['url'], $entity, $linkEntry['url_vars']);
                }
                if (!empty($linkEntry['selfLink'])) {
                    $url = Router::url(null);
                } else {
                    $url = Router::url($linkEntry['url']);
                }
                echo $this->Bootstrap->button([
                    'nodeType' => 'a',
                    'text' => $linkEntry['label'],
                    'icon' => $linkEntry['icon'],
                    'badge' => $linkEntry['badge'] ?? false,
                    'variant' => 'link',
                    'outline' => $active,
                    'class' => ['text-nowrap', 'text-decoration-none', 'btn-link-hover-shadow'],
                    'attrs' => [
                        'href' => $url,
                    ],
                ]);
            }
            echo '</div>';

            if (!empty($goToLinks)) {
                $menu = [];
                foreach ($goToLinks as $menuID => $links) {
                    $jumpToButtons = array_map(function($link) {
                        $url = Router::url($link['url']);
                        return [
                            'nodeType' => 'a',
                            'text' => h($link['label']),
                            'variant' => 'link',
                            'icon' => h($link['icon']),
                            'class' => ['text-nowrap'],
                            'attrs' => [
                                'href' => h($url),
                            ],
                        ];
                    }, $links);
                    if ($menuID === '_root') {
                        $menu = array_merge($menu, $jumpToButtons);
                    } else {
                        $subMenuConfig = $goToMenu[$menuID] ?? [];
                        $subMenu = [
                            'nodeType' => 'a',
                            'text' => h($subMenuConfig['label']),
                            'variant' => h($subMenuConfig['variant'] ?? 'link'),
                            'icon' => h($subMenuConfig['icon']),
                            'class' => ['text-nowrap'],
                            'keepOpen' => true,
                            'menu' => $jumpToButtons
                        ];
                        $menu[] = $subMenu;
                    }
                }
                echo $this->Bootstrap->dropdownMenu([
                    'dropdown-class' => '',
                    'alignment' => 'end',
                    'direction' => 'down',
                    'button' => [
                        'text' => 'Go To',
                        'variant' => 'secondary',
                        'icon' => 'location-arrow',
                    ],
                    'submenu_direction' => 'start',
                    'attrs' => [],
                    'menu' => $menu,
                ]);
            }
            echo '</div>';
        }
    ?>
</div>

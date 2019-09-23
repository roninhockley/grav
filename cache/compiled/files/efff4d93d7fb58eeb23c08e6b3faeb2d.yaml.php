<?php
return [
    '@class' => 'Grav\\Common\\File\\CompiledYamlFile',
    'filename' => '/var/www/html/grav/user/themes/rana/blueprints.yaml',
    'modified' => 1565203950,
    'data' => [
        'name' => 'Rana',
        'version' => '1.0.0',
        'description' => 'A simple app-like mobile first and content-focused Grav theme which is also optimized for large screens.',
        'icon' => 'rebel',
        'author' => [
            'name' => 'Matthias Danzinger',
            'email' => 'matthias.danzinger@gmail.com'
        ],
        'homepage' => 'https://github.com/matthias-danzinger/grav-theme-rana',
        'demo' => 'http://demo.matthiasdanzinger.eu/rana',
        'keywords' => 'grav, theme, mobile, app-like',
        'bugs' => 'https://github.com/matthias-danzinger/grav-theme-rana/issues',
        'readme' => 'https://github.com/matthias-danzinger/grav-theme-rana/blob/master/README.md',
        'license' => 'MIT',
        'form' => [
            'validation' => 'loose',
            'fields' => [
                'sec1' => [
                    'type' => 'section',
                    'title' => 'Basic Settings',
                    'underline' => true,
                    'fields' => [
                        'title_in_menu' => [
                            'type' => 'text',
                            'label' => 'Title in menu',
                            'help' => 'If you do not worry about styling, just enter a short title. But you can also provide a HTML string with inline styles here to easily style the title in the menu.'
                        ]
                    ]
                ],
                'sec2' => [
                    'type' => 'section',
                    'title' => 'Markdown Notices Settings',
                    'text' => '<strong>Note: </strong> These settings are only applicable, if built-in css of markdown notices plugin is not used. You can disable usage of built-in css in the settings of the markdown notices plugin. The Vela theme will then automatically use its own styles for the notices. These include a small text. You can specify here, which text appears above the respective notice.',
                    'underline' => true,
                    'fields' => [
                        'notice.yellow' => [
                            'type' => 'text',
                            'label' => 'Yellow markown notice text'
                        ],
                        'notice.red' => [
                            'type' => 'text',
                            'label' => 'Red markown notice text'
                        ],
                        'notice.blue' => [
                            'type' => 'text',
                            'label' => 'Blue markown notice text'
                        ],
                        'notice.green' => [
                            'type' => 'text',
                            'label' => 'Green markown notice text'
                        ]
                    ]
                ],
                'sec3' => [
                    'type' => 'section',
                    'title' => 'Responsive settings',
                    'text' => 'Read the <a href="https://github.com/danzinger/grav-theme-vela">documentation</a> for infomation on these settings.',
                    'underline' => true,
                    'fields' => [
                        'desktop_mode' => [
                            'type' => 'toggle',
                            'label' => 'Desktop friendly mode.',
                            'help' => 'Off-canvas-menu is always open if screen is wider than value given below.',
                            'highlight' => 1,
                            'default' => 0,
                            'options' => [
                                1 => 'PLUGIN_ADMIN.ENABLED',
                                0 => 'PLUGIN_ADMIN.DISABLED'
                            ]
                        ],
                        'desktop_min_width' => [
                            'type' => 'number',
                            'label' => 'Desktop min width (pixels)',
                            'help' => 'Applies only in desktop friendly mode. If the screen width is wider than the given amount of pixels, the off-canvas menu is always open.',
                            'default' => 1200
                        ],
                        'parents_routable' => [
                            'type' => 'toggle',
                            'label' => 'Parent pages routable in menu/breadcrumbs. Careful, read info!',
                            'help' => 'If you have (routable) pages with subpages (parents) and you want these parents to be routable from the menu and from the breadcrumbs, use this option. Be careful! The menu closes each time you click a parent if you do not use dektop friendly mode, which makes browsing your website hard since the menu closes each time you click a routable parent.',
                            'highlight' => 1,
                            'default' => 0,
                            'options' => [
                                1 => 'PLUGIN_ADMIN.ENABLED',
                                0 => 'PLUGIN_ADMIN.DISABLED'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];

<?php
return [
    '@class' => 'Grav\\Common\\File\\CompiledYamlFile',
    'filename' => '/var/www/html/grav/user/plugins/toc/languages.yaml',
    'modified' => 1558464477,
    'data' => [
        'de' => [
            'PLUGINS' => [
                'TOC' => [
                    'GLOBAL_CONFIG' => 'Globale Einstellungen',
                    'DEFAULT_CONFIG' => 'Standardeinstellungen für External Links',
                    'SPECIFIC_CONFIG' => 'Globale und seitenspezifische Einstellungen',
                    'PLUGIN_STATUS' => 'Plugin Status',
                    'PLUGIN_ACTIVE' => 'Aktiv',
                    'PLUGIN_ACTIVE_HELP' => 'Verwende diese Option zur (De-)Aktivierung des Plugins auf bestimmten Seiten.',
                    'BUILTIN_CSS' => 'Verwende mitgeliefertes CSS',
                    'TITLE' => 'Zeige Titel des Inhaltsverzeichnisses',
                    'ANCHORLINK' => 'Zeige Anker-Links',
                    'ANCHORLINK_HELP' => 'Aktivieren falls alle Überschriften mit Anker-Links versehen werden sollen.',
                    'PERMALINK' => 'Zeige Permalinks für Überschriften',
                    'PERMALINK_HELP' => 'Aktivieren um Permalinks für Überschriften hinzuzufügen.',
                    'PLACEMENT' => 'Platzierung der Anker-Links',
                    'PLACEMENT_LEFT' => 'Links',
                    'PLACEMENT_RIGHT' => 'Rechts',
                    'VISIBLILITY' => 'Sichtbarkeit der Anker-Links',
                    'VISIBLILITY_HOVER' => 'hover - Sichtbar beim Drüberfahren mit der Maus',
                    'VISIBLILITY_ALWAYS' => 'always - Immer sichtbar',
                    'ICON' => 'Text/Bild bevor Anker-Link',
                    'ICON_HELP' => 'Standard oder ein spezifisches Zeichen wie #, ¶, ❡, oder §.',
                    'CLASS' => 'Zusätzliche Klassen',
                    'CLASS_HELP' => 'Hinzufügen von zusätzlichen Klassen zum Anker-Link',
                    'BASELEVEL' => 'Beginne bei Überschrift x. Ordnung',
                    'HEADINGLEVEL' => 'Maximal angezeigte Ordnung von Überschriften in TOC/MINITOC',
                    'PROCESS' => 'Aktiviere <code>Toc</code>-Filter auf Seite',
                    'SLUG' => [
                        'SECTION' => 'Anker-ID Erzeugung',
                        'TRUNCATE' => 'Überschriften kürzen',
                        'LENGTH' => 'Anker-ID Länge',
                        'BREAK' => 'Trennzeichen',
                        'BREAK_HELP' => 'Trennzeichen welches zwischen Wörtern in der Anker-ID eingesetzt wird',
                        'PAD' => 'Addendum',
                        'PAD_HELP' => 'Zusatz, der am Ende der verkürzten Anker-ID angezeigt wird',
                        'GRANULARITY' => [
                            'LABEL' => 'Granularität',
                            'WORDS' => 'Wörter',
                            'CHARACTERS' => 'Zeichen'
                        ]
                    ],
                    'NOT_AVAILABLE' => 'unbekannt',
                    'PATTERNS' => [
                        '~\\&~' => 'und',
                        '~[^\\p{L}\\d]+~u' => '-',
                        '~([A-Z]+)([A-Z][a-z])~' => '\\1-\\2',
                        '~([a-z]{2,})([A-Z])~' => '\\1-\\2'
                    ]
                ]
            ]
        ],
        'en' => [
            'PLUGINS' => [
                'TOC' => [
                    'GLOBAL_CONFIG' => 'Global plugin configurations',
                    'DEFAULT_CONFIG' => 'Default values for External Links configuration',
                    'SPECIFIC_CONFIG' => 'Global and page specific configurations',
                    'PLUGIN_STATUS' => 'Plugin status',
                    'PLUGIN_ACTIVE' => 'Active',
                    'PLUGIN_ACTIVE_HELP' => 'Use this option to (de-)activate this plugin on page.',
                    'BUILTIN_CSS' => 'Use built in CSS',
                    'TITLE' => 'Show title in the Table of Contents',
                    'ANCHORLINK' => 'Show anchor links',
                    'ANCHORLINK_HELP' => 'Set to true to cause all headers to link to themselves.',
                    'PERMALINK' => 'Add permalinks for headings',
                    'PERMALINK_HELP' => 'Set to true to generate permanent links at the beginning of each header',
                    'PLACEMENT' => 'Placement of anchor link',
                    'PLACEMENT_LEFT' => 'Left',
                    'PLACEMENT_RIGHT' => 'Right',
                    'VISIBLILITY' => 'Visibility of anchor link',
                    'VISIBLILITY_HOVER' => 'hover - Visible on hover',
                    'VISIBLILITY_ALWAYS' => 'always - Always visible',
                    'ICON' => 'Icon of the anchor link',
                    'ICON_HELP' => 'Default link or a specific character like: #, ¶, ❡, and §.',
                    'CLASS' => 'Additional classes',
                    'CLASS_HELP' => 'Adds the provided class to the anchor link',
                    'BASELEVEL' => 'Base level for headings',
                    'HEADINGLEVEL' => 'Maximum heading level to show in TOC/MINITOC',
                    'PROCESS' => 'Activate <code>Toc</code> filter on the page',
                    'SLUG' => [
                        'SECTION' => 'Slug generation',
                        'TRUNCATE' => 'Truncate headings for slug generation',
                        'LENGTH' => 'Slug string length',
                        'BREAK' => 'Break delimiter',
                        'BREAK_HELP' => 'The break delimiter to divide the slug into pieces of words',
                        'PAD' => 'Addendum',
                        'PAD_HELP' => 'Added to the end of the truncated slug',
                        'GRANULARITY' => [
                            'LABEL' => 'Granularity',
                            'WORDS' => 'words',
                            'CHARACTERS' => 'characters'
                        ]
                    ],
                    'NOT_AVAILABLE' => 'n-a',
                    'PATTERNS' => [
                        '~\\&~' => 'and',
                        '~[^\\p{L}\\d]+~u' => '-',
                        '~(\\w{4,})y\\-s\\-~i' => '$1ies-',
                        '~(\\w{4,})\\-s\\-~i' => '$1s-',
                        '~([A-Z]+)([A-Z][a-z])~' => '\\1-\\2',
                        '~([a-z]{2,})([A-Z])~' => '\\1-\\2'
                    ]
                ]
            ]
        ],
        'fr' => [
            'PLUGINS' => [
                'TOC' => [
                    'GLOBAL_CONFIG' => 'Configuration générale du plugin',
                    'DEFAULT_CONFIG' => 'Configuration des valeurs par défaut pour les Liens Externes',
                    'SPECIFIC_CONFIG' => 'Configuration générale et spécifique des pages',
                    'PLUGIN_STATUS' => 'Statut du plugin',
                    'BUILTIN_CSS' => 'Utiliser les CSS intégrés',
                    'TITLE' => 'Afficher le titre dans la table des matières',
                    'ANCHORLINK' => 'Afficher les liens d\'ancrage',
                    'ANCHORLINK_HELP' => 'Définir sur Vrai pour créer des liens sur les titres vers eux-mêmes.',
                    'PERMALINK' => 'Ajouter des permaliens aux titres',
                    'PERMALINK_HELP' => 'Définir sur Vrai pour générer des permaliens au début de chaque titre',
                    'PLACEMENT' => 'Position du lien d\'ancrage',
                    'PLACEMENT_LEFT' => 'Gauche',
                    'PLACEMENT_RIGHT' => 'Droite',
                    'VISIBLILITY' => 'Visibilité du lien d\'ancrage',
                    'VISIBLILITY_HOVER' => 'au survol - Visible au survol',
                    'VISIBLILITY_ALWAYS' => 'toujours - Toujours visible',
                    'ICON' => 'Icône du lien d\'ancrage',
                    'ICON_HELP' => 'Le lien par défaut ou un caractère spécifique comme : #, ¶, ❡, et §.',
                    'CLASS' => 'Classes supplémentaires',
                    'CLASS_HELP' => 'Ajoute la classe fournie au lien d\'ancrage',
                    'BASELEVEL' => 'Niveau de base pour les titres',
                    'HEADINGLEVEL' => 'Niveau maximum de la position à afficher dans TOC/MINITOC',
                    'PROCESS' => 'Activer le filtrage <code>Toc</code> sur la page',
                    'SLUG' => [
                        'SECTION' => 'Génération de Slug',
                        'TRUNCATE' => 'Tronquer les en-têtes pour la génération de slug',
                        'LENGTH' => 'Longueur de la chaîne slug',
                        'BREAK' => 'Délimiteur de séparation',
                        'BREAK_HELP' => 'Le délimiteur pour séparer le slug en mots',
                        'PAD' => 'Avenant',
                        'PAD_HELP' => 'Ajouté à la fin du slug tronqué'
                    ],
                    'PATTERNS' => [
                        '~\\&~' => 'and',
                        '~[^\\p{L}\\d]+~u' => '-',
                        '~(\\w{4,})y\\-s\\-~i' => '$1ies-',
                        '~(\\w{4,})\\-s\\-~i' => '$1s-',
                        '~([A-Z]+)([A-Z][a-z])~' => '\\1-\\2',
                        '~([a-z]{2,})([A-Z])~' => '\\1-\\2'
                    ]
                ]
            ]
        ]
    ]
];

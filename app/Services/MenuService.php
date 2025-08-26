<?php

namespace App\Services;

class MenuService
{
    public static function getMenu($role)
    {
        switch ($role) {
            case 'super_admin':
                return [
                            [
                                'label' => 'Home',
                                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1" > 
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0" /> 
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /> 
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                                </svg>',
                                'url' => route('dashboard')
                            ],
                            [
                                'label' => 'Manajemen User',
                                'icon' => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-users-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M3 21v-2a4 4 0 0 1 4 -4h4c.96 0 1.84 .338 2.53 .901" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /><path d="M16 19h6" /><path d="M19 16v6" /></svg>',
                                'url' => route('users.index')
                            ],
                            [
                                'label' => 'Manajemen Instansi',
                                'icon' => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-building-bank"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M3 10l18 0" /><path d="M5 6l7 -3l7 3" /><path d="M4 10l0 11" /><path d="M20 10l0 11" /><path d="M8 14l0 3" /><path d="M12 14l0 3" /><path d="M16 14l0 3" /></svg>',
                                'url' => route('institutions.index')
                            ],
                            [
                                'label' => 'Manajemen Survey',
                                'icon' => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-route-scan"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 8v-2a2 2 0 0 1 2 -2h2" /><path d="M4 16v2a2 2 0 0 0 2 2h2" /><path d="M16 4h2a2 2 0 0 1 2 2v2" /><path d="M16 20h2a2 2 0 0 0 2 -2v-2" /><path d="M7 12v-3h3" /><path d="M14 9h3v3" /><path d="M7 9l4.414 4.414a2 2 0 0 1 .586 1.414v2.172" /><path d="M17 9l-4.414 4.414a2 2 0 0 0 -.586 1.414v2.172" /></svg>',
                                'url' => route('questioner.index')
                            ],
                            [
                                'label' => 'Laporan',
                                'icon' => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-clipboard-text"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 12h6" /><path d="M9 16h6" /></svg>',
                                'children' => [
                                    [
                                    'label' => 'IKM',
                                    'url' => route('reports.index')
                                    ],
                                    [
                                    'label' => 'IKM Perjenis Layanan',
                                    'url' => route('reports.per_layanan')
                                    ],
                                    [
                                    'label' => 'Jumlah Responden',
                                    'url' => route('laporan.responden')
                                    ],
                                    [
                                    'label' => 'Grafik',
                                    'url' => route('laporan.grafik')
                                    ]
                                ]
                            ], 
                        ];
            case 'admin_instansi':
                return [
                            [
                                'label' => 'Home',
                                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1" > 
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0" /> 
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /> 
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                                </svg>',
                                'url' => route('instansi.dashboard')
                            ],
                            [
                                'label' => 'Manajemen Layanan',
                                'icon' => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-mood-smile-beam"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 21a9 9 0 1 1 0 -18a9 9 0 0 1 0 18z" /><path d="M10 10c-.5 -1 -2.5 -1 -3 0" /><path d="M17 10c-.5 -1 -2.5 -1 -3 0" /><path d="M14.5 15a3.5 3.5 0 0 1 -5 0" /></svg>',
                                'url' => route('instansi.services.index')
                            ],
                            [
                                'label' => 'Laporan',
                                'icon' => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-clipboard-text"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 12h6" /><path d="M9 16h6" /></svg>',
                                'children' => [
                                    [
                                    'label' => 'IKM',
                                    'url' => route('instansi.reports.index')
                                    ],
                                    [
                                    'label' => 'IKM Perjenis Layanan',
                                    'url' => route('instansi.reports.per_layanan')
                                    ],
                                    [
                                    'label' => 'Jumlah Responden',
                                    'url' => route('instansi.laporan.responden')
                                    ],
                                    [
                                    'label' => 'Grafik',
                                    'url' => route('instansi.laporan.grafik')
                                    ]
                                ]
                            ], 
                        ];
                default:
                return [];
            }
        }
}
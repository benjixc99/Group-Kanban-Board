<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;

use App\Models\AppConfig;
use App\Models\Language;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;

class Helper
{

        /**
         * @return array
         */

        public static function applClasses(): array
        {


                // default data array
                $DefaultData = [
                        'mainLayoutType'         => 'vertical',
                        'theme'                  => 'light',
                        'sidebarCollapsed'       => false,
                        'navbarColor'            => '',
                        'horizontalMenuType'     => 'floating',
                        'verticalMenuNavbarType' => 'floating',
                        'footerType'             => 'static', //footer
                        'layoutWidth'            => 'boxed',
                        'showMenu'               => true,
                        'bodyClass'              => '',
                        'pageClass'              => '',
                        'pageHeader'             => true,
                        'contentLayout'          => 'default',
                        'blankPage'              => false,
                        'defaultLanguage'        => 'en',
                        'direction'              => env('MIX_CONTENT_DIRECTION', 'ltr'),
                ];

                // if any key missing of array from custom.php file it will be merged and set a default value from dataDefault array and store in data variable
                $data = $DefaultData;



                //layout classes
                $layoutClasses = [
                        'theme'                  => $data['theme'],
                        'layoutTheme'            => $data['theme'],
                        'sidebarCollapsed'       => $data['sidebarCollapsed'],
                        'showMenu'               => $data['showMenu'],
                        'layoutWidth'            => $data['layoutWidth'],
                        'verticalMenuNavbarType' => $data['verticalMenuNavbarType'],
                        'navbarClass'            => $data['verticalMenuNavbarType'],
                        'navbarColor'            => $data['navbarColor'],
                        'horizontalMenuType'     => $data['horizontalMenuType'],
                        'horizontalMenuClass'    => $data['horizontalMenuType'],
                        'footerType'             => $data['footerType'],
                        'sidebarClass'           => '',
                        'bodyClass'              => $data['bodyClass'],
                        'pageClass'              => $data['pageClass'],
                        'pageHeader'             => $data['pageHeader'],
                        'blankPage'              => $data['blankPage'],
                        'blankPageClass'         => '',
                        'contentLayout'          => $data['contentLayout'],
                        'sidebarPositionClass'   => $data['contentLayout'],
                        'contentsidebarClass'    => $data['contentLayout'],
                        'mainLayoutType'         => $data['mainLayoutType'],
                        'defaultLanguage'        => $data['defaultLanguage'],
                        'direction'              => $data['direction'],
                ];


                // sidebar Collapsed
                if ($layoutClasses['sidebarCollapsed'] == 'true') {
                        $layoutClasses['sidebarClass'] = "menu-collapsed";
                }

                // blank page class
                if ($layoutClasses['blankPage'] == 'true') {
                        $layoutClasses['blankPageClass'] = "blank-page";
                }

                return $layoutClasses;
        }

        /**
         * @return string
         */
        public static function home_route(): string
        {
                if (Gate::allows('access backend')) {
                        return url(config('app.admin_path') . "/dashboard");
                }

                return route('user.home');
        }

        /**
         * @param  Request  $request
         *
         * @return bool
         */
        public static function is_admin_route(Request $request): bool
        {
                $action = $request->route()->getAction();

                return 'App\Http\Controllers\Admin' === $action['namespace'];
        }



        /**
         * Check if exec() function is available.
         *
         * @return bool
         */

        public static function exec_enabled(): bool
        {
                try {
                        // make a small test
                        exec('ls');

                        return function_exists('exec') && !in_array('exec', array_map('trim', explode(', ', ini_get('disable_functions'))));
                } catch (Exception $ex) {
                        return false;
                }
        }

        /**
         * application menu
         *
         * @return array[]
         */
        public static function menuData(): array
        {
                return [

                        [
                                'url'    => url("/dashboard"),
                                'slug'   => 'dashboard',
                                'name'   => 'Dashboard',
                                'i18n'   => 'Dashboard',
                                'icon'   => 'home',
                                'access' => 'view_kanban_board',
                        ],
                        [
                                'url'     => '',
                                'name'    => 'Projects',
                                'icon'    => 'folder',
                                'access'  => 'view_kanban_board',
                                'submenu' => [
                                        [
                                                'url'    => url(config('app.admin_path') . "/projects"),
                                                'slug'   => 'projects.index',
                                                'name'   => 'View all',
                                                'i18n'   => 'View all',
                                                'access' => 'view_kanban_board',
                                        ],
                                        [
                                                'url'    => url(config('app.admin_path') . "/projects/create"),
                                                'slug'   => 'projects.create',
                                                'name'   => 'Create new',
                                                'i18n'   => 'Create new',
                                                'access' => 'create_new_project',
                                        ],
                                ],
                        ],
                        [
                                'url'     => url(config('app.admin_path') . "/users"),
                                'name'    => 'Users',
                                'icon'    => 'users',
                                'access'  => 'modify_user_roles',
                        ],

                ];
        }

        public static function greetingMessage()
        {
                /* This sets the $time variable to the current hour in the 24-hour clock format */
                $time = date("H");
                /* If the time is less than 1200 hours, show good morning */
                if ($time < "12") {
                        return __('locale.labels.greeting_message', [
                                'time' => __('locale.labels.good_morning'),
                                'name' => auth()->user()->displayName(),
                        ]);
                } elseif ($time >= "12" && $time < "17") {
                        return __('locale.labels.greeting_message', [
                                'time' => __('locale.labels.good_afternoon'),
                                'name' => auth()->user()->displayName(),
                        ]);
                } elseif ($time >= "17" && $time < "19") {
                        return __('locale.labels.greeting_message', [
                                'time' => __('locale.labels.good_evening'),
                                'name' => auth()->user()->displayName(),
                        ]);
                } else {
                        return __('locale.labels.greeting_message', [
                                'time' => __('locale.labels.good_night'),
                                'name' => auth()->user()->displayName(),
                        ]);
                }
        }
}

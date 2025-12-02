@extends('layouts.app' ,[ 'pageSlug' => 'Statement Of Account' ])


@section('content')
    <head>
        <meta charset="utf-8">
        <title>
            Analytics Dashboard - Application Intel - SmartAdmin v4.0.1
        </title>
        <meta name="description" content="Analytics Dashboard">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
        <!-- Call App Mode on ios devices -->
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <!-- Remove Tap Highlight on Windows Phone IE -->
        <meta name="msapplication-tap-highlight" content="no">
        <!-- base css -->
        <link rel="stylesheet" media="screen, print" href="{{ asset('assets') }}/css/intel-dashboard-css/vendors.bundle.css">
        <link rel="stylesheet" media="screen, print" href="{{ asset('assets') }}/css/intel-dashboard-css/app.bundle.css">
        <!-- Place favicon.ico in the root directory -->
        <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
        <link rel="mask-icon" href="img/favicon/safari-pinned-tab.svg" color="#5bbad5">
        <link rel="stylesheet" media="screen, print" href="{{ asset('assets') }}/css/intel-dashboard-css/miscellaneous/reactions/reactions.css">
        <link rel="stylesheet" media="screen, print" href="{{ asset('assets') }}/css/intel-dashboard-css/miscellaneous/fullcalendar/fullcalendar.bundle.css">
        <link rel="stylesheet" media="screen, print" href="{{ asset('assets') }}/css/intel-dashboard-css/miscellaneous/jqvmap/jqvmap.bundle.css">
        <style>
            td{background-color:white;}
            .pageTitleContainer{
                display:none;
            }
            .page-content-wrapper {
                background-color:transparent;
            }
            #js-page-content{padding:0}
            .white-content .nav-tabs>.nav-item>.nav-link.active {
                border: 0px ;
            }
            .alert.alert-success {
                background-color: #f7fdfc;
            }
            .progress {
                height: 5px;
                box-shadow: 0px 0px 0px 0px rgb(0 0 0);
            }
            .fc-day-top{
                background-color:transparent;
            }
            .dropdown-toggle:after, .dropleft .dropdown-toggle:before {
                content: none !important;
            }
        </style>

    </head>
    <body class="mod-bg-1 ">
    <!-- DOC: script to save and load page settings -->
    <script>
        /**
         *	This script should be placed right after the body tag for fast execution
         *	Note: the script is written in pure javascript and does not depend on thirdparty library
         **/
        'use strict';

        var classHolder = document.getElementsByTagName("BODY")[0],
            /**
             * Load from localstorage
             **/
            themeSettings = (localStorage.getItem('themeSettings')) ? JSON.parse(localStorage.getItem('themeSettings')) :
                {},
            themeURL = themeSettings.themeURL || '',
            themeOptions = themeSettings.themeOptions || '';
        /**
         * Load theme options
         **/
        if (themeSettings.themeOptions)
        {
            classHolder.className = themeSettings.themeOptions;
            console.log("%c✔ Theme settings loaded", "color: #148f32");
        }
        else
        {
            console.log("Heads up! Theme settings is empty or does not exist, loading default settings...");
        }
        if (themeSettings.themeURL && !document.getElementById('mytheme'))
        {
            var cssfile = document.createElement('link');
            cssfile.id = 'mytheme';
            cssfile.rel = 'stylesheet';
            cssfile.href = themeURL;
            document.getElementsByTagName('head')[0].appendChild(cssfile);
        }
        /**
         * Save to localstorage
         **/
        var saveSettings = function()
        {
            themeSettings.themeOptions = String(classHolder.className).split(/[^\w-]+/).filter(function(item)
            {
                return /^(nav|header|mod|display)-/i.test(item);
            }).join(' ');
            if (document.getElementById('mytheme'))
            {
                themeSettings.themeURL = document.getElementById('mytheme').getAttribute("href");
            };
            localStorage.setItem('themeSettings', JSON.stringify(themeSettings));
        }
        /**
         * Reset settings
         **/
        var resetSettings = function()
        {
            localStorage.setItem("themeSettings", "");
        }

    </script>
    <!-- BEGIN Page Wrapper -->
    <div class="page-wrapper">
        <div class="page-inner">

            <div class="page-content-wrapper">

                <!-- BEGIN Page Content -->
                <!-- the #js-page-content id is needed for some plugins to initialize -->
                <main id="js-page-content" role="main" class="page-content">

                    <div class="subheader">
                        <h1 class="subheader-title">
                            <i class="fa-solid fa-chart-area"></i> Analytics <span class='fw-300'>Dashboard</span>
                            <small>
                            </small>
                        </h1>
                        <div class="subheader-block d-lg-flex align-items-center">
                            <div class="d-inline-flex flex-column justify-content-center mr-3">
                                    <span class="fw-300 fs-xs d-block opacity-50">
                                        <small>EXPENSES</small>
                                    </span>
                                <span class="fw-500 fs-xl d-block color-primary-500">
                                        $47,000
                                    </span>
                            </div>
                            <span class="sparklines hidden-lg-down" sparkType="bar" sparkBarColor="#886ab5" sparkHeight="32px" sparkBarWidth="5px" values="3,4,3,6,7,3,3,6,2,6,4"></span>
                        </div>
                        <div class="subheader-block d-lg-flex align-items-center border-faded border-right-0 border-top-0 border-bottom-0 ml-3 pl-3">
                            <div class="d-inline-flex flex-column justify-content-center mr-3">
                                    <span class="fw-300 fs-xs d-block opacity-50">
                                        <small>MY PROFITS</small>
                                    </span>
                                <span class="fw-500 fs-xl d-block color-danger-500">
                                        $38,500
                                    </span>
                            </div>
                            <span class="sparklines hidden-lg-down" sparkType="bar" sparkBarColor="#fe6bb0" sparkHeight="32px" sparkBarWidth="5px" values="1,4,3,6,5,3,9,6,5,9,7"></span>
                        </div>
                    </div>

                    <div class="row" style="
width: auto;
padding: 0;
background-color: transparent">
                        <div class="col-lg-12">
                            <div id="panel-1" class="panel panel-locked" data-panel-lock="false" data-panel-close="false" data-panel-fullscreen="false" data-panel-collapsed="false" data-panel-color="false" data-panel-locked="false" data-panel-refresh="false" data-panel-reset="false">
                                <div class="panel-hdr">
                                    <h2>
                                        Live Feeds
                                    </h2>
                                    <div class="panel-toolbar pr-3 align-self-end">
                                        <ul id="demo_panel-tabs" class="nav nav-tabs border-bottom-0 nav-tabs-clean" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#tab_default-1" role="tab">Live Stats</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#tab_default-2" role="tab">Revenue</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-container show">
                                    <div class="panel-content border-faded border-left-0 border-right-0 border-top-0">
                                        <div class="row no-gutters">
                                            <div class="col-lg-7 col-xl-8">
                                                <div class="position-relative">
                                                    <div class="custom-control custom-switch position-absolute pos-top pos-left ml-5 mt-3 z-index-cloud">
                                                        <input type="checkbox" class="custom-control-input" id="start_interval">
                                                        <label class="custom-control-label" for="start_interval">Live Update</label>
                                                    </div>
                                                    <div id="updating-chart" style="height:242px"></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-5 col-xl-4 pl-lg-3">
                                                <div class="d-flex mt-2">
                                                    My Tasks
                                                    <span class="d-inline-block ml-auto">130 / 500</span>
                                                </div>
                                                <div class="progress progress-sm mb-3">
                                                    <div class="progress-bar bg-fusion-400" role="progressbar" style="width: 65%;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div class="d-flex">
                                                    Transfered
                                                    <span class="d-inline-block ml-auto">440 TB</span>
                                                </div>
                                                <div class="progress progress-sm mb-3">
                                                    <div class="progress-bar bg-success-500" role="progressbar" style="width: 34%;" aria-valuenow="34" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div class="d-flex">
                                                    Bugs Squashed
                                                    <span class="d-inline-block ml-auto">77%</span>
                                                </div>
                                                <div class="progress progress-sm mb-3">
                                                    <div class="progress-bar bg-info-400" role="progressbar" style="width: 77%;" aria-valuenow="77" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div class="d-flex">
                                                    User Testing
                                                    <span class="d-inline-block ml-auto">7 days</span>
                                                </div>
                                                <div class="progress progress-sm mb-g">
                                                    <div class="progress-bar bg-primary-300" role="progressbar" style="width: 84%;" aria-valuenow="84" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div class="row no-gutters">
                                                    <div class="col-6 pr-1">
                                                        <a href="#" class="btn btn-default btn-block">Generate PDF</a>
                                                    </div>
                                                    <div class="col-6 pl-1">
                                                        <a href="#" class="btn btn-default btn-block">Report a Bug</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-content p-0">
                                        <div class="row row-grid no-gutters">
                                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
                                                <div class="px-3 py-2 d-flex align-items-center">
                                                    <div class="js-easy-pie-chart color-primary-300 position-relative d-inline-flex align-items-center justify-content-center" data-percent="75" data-piesize="50" data-linewidth="5" data-linecap="butt" data-scalelength="0">
                                                        <div class="d-flex flex-column align-items-center justify-content-center position-absolute pos-left pos-right pos-top pos-bottom fw-300 fs-lg">
                                                            <span class="js-percent d-block text-dark"></span>
                                                        </div>
                                                    </div>
                                                    <span class="d-inline-block ml-2 text-muted">
                                                            SERVER LOAD
                                                            <i class="fa fa-caret-up color-danger-500 ml-1"></i>
                                                        </span>
                                                    <div class="ml-auto d-inline-flex align-items-center">
                                                        <div class="sparklines d-inline-flex" sparktype="line" sparkheight="30" sparkwidth="70" sparklinecolor="#886ab5" sparkfillcolor="false" sparklinewidth="1" values="5,6,5,3,8,6,9,7,4,2"></div>
                                                        <div class="d-inline-flex flex-column small ml-2">
                                                            <span class="d-inline-block badge badge-success opacity-50 text-center p-1 width-6">97%</span>
                                                            <span class="d-inline-block badge bg-fusion-300 opacity-50 text-center p-1 width-6 mt-1">44%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
                                                <div class="px-3 py-2 d-flex align-items-center">
                                                    <div class="js-easy-pie-chart color-success-500 position-relative d-inline-flex align-items-center justify-content-center" data-percent="79" data-piesize="50" data-linewidth="5" data-linecap="butt">
                                                        <div class="d-flex flex-column align-items-center justify-content-center position-absolute pos-left pos-right pos-top pos-bottom fw-300 fs-lg">
                                                            <span class="js-percent d-block text-dark"></span>
                                                        </div>
                                                    </div>
                                                    <span class="d-inline-block ml-2 text-muted">
                                                            DISK SPACE
                                                            <i class="fa fa-caret-down color-success-500 ml-1"></i>
                                                        </span>
                                                    <div class="ml-auto d-inline-flex align-items-center">
                                                        <div class="sparklines d-inline-flex" sparktype="line" sparkheight="30" sparkwidth="70" sparklinecolor="#1dc9b7" sparkfillcolor="false" sparklinewidth="1" values="5,9,7,3,5,2,5,3,9,6"></div>
                                                        <div class="d-inline-flex flex-column small ml-2">
                                                            <span class="d-inline-block badge badge-info opacity-50 text-center p-1 width-6">76%</span>
                                                            <span class="d-inline-block badge bg-warning-300 opacity-50 text-center p-1 width-6 mt-1">3%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
                                                <div class="px-3 py-2 d-flex align-items-center">
                                                    <div class="js-easy-pie-chart color-info-500 position-relative d-inline-flex align-items-center justify-content-center" data-percent="23" data-piesize="50" data-linewidth="5" data-linecap="butt">
                                                        <div class="d-flex flex-column align-items-center justify-content-center position-absolute pos-left pos-right pos-top pos-bottom fw-300 fs-lg">
                                                            <span class="js-percent d-block text-dark"></span>
                                                        </div>
                                                    </div>
                                                    <span class="d-inline-block ml-2 text-muted">
                                                            DATA TTF
                                                            <i class="fa fa-caret-up color-success-500 ml-1"></i>
                                                        </span>
                                                    <div class="ml-auto d-inline-flex align-items-center">
                                                        <div class="sparklines d-inline-flex" sparktype="line" sparkheight="30" sparkwidth="70" sparklinecolor="#51adf6" sparkfillcolor="false" sparklinewidth="1" values="3,5,2,5,3,9,6,5,9,7"></div>
                                                        <div class="d-inline-flex flex-column small ml-2">
                                                            <span class="d-inline-block badge bg-fusion-500 opacity-50 text-center p-1 width-6">10GB</span>
                                                            <span class="d-inline-block badge bg-fusion-300 opacity-50 text-center p-1 width-6 mt-1">10%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
                                                <div class="px-3 py-2 d-flex align-items-center">
                                                    <div class="js-easy-pie-chart color-fusion-500 position-relative d-inline-flex align-items-center justify-content-center" data-percent="36" data-piesize="50" data-linewidth="5" data-linecap="butt">
                                                        <div class="d-flex flex-column align-items-center justify-content-center position-absolute pos-left pos-right pos-top pos-bottom fw-300 fs-lg">
                                                            <span class="js-percent d-block text-dark"></span>
                                                        </div>
                                                    </div>
                                                    <span class="d-inline-block ml-2 text-muted">
                                                            TEMP.
                                                            <i class="fa fa-caret-down color-success-500 ml-1"></i>
                                                        </span>
                                                    <div class="ml-auto d-inline-flex align-items-center">
                                                        <div class="sparklines d-inline-flex" sparktype="line" sparkheight="30" sparkwidth="70" sparklinecolor="#fd3995" sparkfillcolor="false" sparklinewidth="1" values="5,3,9,6,5,9,7,3,5,2"></div>
                                                        <div class="d-inline-flex flex-column small ml-2">
                                                            <span class="d-inline-block badge badge-danger opacity-50 text-center p-1 width-6">124</span>
                                                            <span class="d-inline-block badge bg-info-300 opacity-50 text-center p-1 width-6 mt-1">40F</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div id="panel-2" class="panel" data-panel-fullscreen="false">
                                <div class="panel-hdr">
                                    <h2>
                                        Smart Chat
                                    </h2>
                                </div>
                                <div class="panel-container show">
                                    <div class="panel-content p-0">
                                        <div class="d-flex flex-column">
                                            <div class="bg-subtlelight-fade custom-scroll" style="height: 244px">
                                                <div class="h-100">
                                                    <!-- message -->
                                                    <div class="d-flex flex-row px-3 pt-3 pb-2">
                                                        <!-- profile photo : lazy loaded -->
                                                        <span class="status status-danger">
                                                                <span class="profile-image rounded-circle d-inline-block" style="background-image:url('img/demo/avatars/avatar-j.png')"></span>
                                                            </span>
                                                        <!-- profile photo end -->
                                                        <div class="ml-3">
                                                            <a href="javascript:void(0);" title="Lisa Hatchensen" class="d-block fw-700 text-dark">Lisa Hatchensen</a>
                                                            Hey did you meet the new board of director? He's a bit of a geek if you ask me...anyway here is the report you requested. I am off to launch with Lisa and Andrew, you wanna join?
                                                            <!-- file download -->
                                                            <div class="d-flex mt-3 flex-wrap">
                                                                <div class="btn-group mr-1 mt-1" role="group" aria-label="Button group with nested dropdown ">
                                                                    <button type="button" class="btn btn-default btn-xs btn-block px-1 py-1 fw-500" data-action="toggle">
                                                                            <span class="d-block text-truncate text-truncate-sm">
                                                                                <i class="fa fa-file-pdf mr-1 color-danger-700"></i> Report-2013-demographic-repo
                                                                            </span>
                                                                    </button>
                                                                    <div class="btn-group" role="group">
                                                                        <button id="btnGroupDrop1" type="button" class="btn btn-default btn-xs dropdown-toggle px-2 js-waves-off" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                                                        <div class="dropdown-menu p-0 fs-xs" aria-labelledby="btnGroupDrop1">
                                                                            <a class="dropdown-item px-3 py-2" href="#">Forward</a>
                                                                            <a class="dropdown-item px-3 py-2" href="#">Open</a>
                                                                            <a class="dropdown-item px-3 py-2" href="#">Delete</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="btn-group mr-1 mt-1" role="group" aria-label="Button group with nested dropdown ">
                                                                    <button type="button" class="btn btn-default btn-xs btn-block px-1 py-1 fw-500" data-action="toggle">
                                                                            <span class="d-block text-truncate text-truncate-sm">
                                                                                <i class="fa fa-file-pdf mr-1 color-danger-700"></i> Bloodworks Patient 34124BA
                                                                            </span>
                                                                    </button>
                                                                    <div class="btn-group" role="group">
                                                                        <button id="btnGroupDrop2" type="button" class="btn btn-default btn-xs dropdown-toggle px-2 js-waves-off" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                                                        <div class="dropdown-menu p-0 fs-xs" aria-labelledby="btnGroupDrop2">
                                                                            <a class="dropdown-item px-3 py-2" href="#">Forward</a>
                                                                            <a class="dropdown-item px-3 py-2" href="#">Open</a>
                                                                            <a class="dropdown-item px-3 py-2" href="#">Delete</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- file download end -->
                                                        </div>
                                                    </div>
                                                    <!-- message end -->
                                                    <!-- message reply -->
                                                    <div class="d-flex flex-row px-3 pt-3 pb-2">
                                                        <!-- profile photo : lazy loaded -->
                                                        <span class="status status-danger">
                                                                <span class="profile-image rounded-circle d-inline-block" style="background-image:url('img/demo/avatars/avatar-admin.png')"></span>
                                                            </span>
                                                        <!-- profile photo end -->
                                                        <div class="ml-3">
                                                            <a href="javascript:void(0);" title="Lisa Hatchensen" class="d-block fw-700 text-dark">Dr. Codex Lantern</a>
                                                            Thanks for the file! You guys go ahead, I have to call some of my patients.
                                                        </div>
                                                    </div>
                                                    <!-- message reply end -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 bg-faded">
                                        <textarea rows="3" class="form-control rounded-top border-bottom-left-radius-0 border-bottom-right-radius-0 border" placeholder="write a reply..."></textarea>
                                        <div class="d-flex align-items-center py-2 px-2 bg-white border border-top-0 rounded-bottom bg-primary">
                                            <div class="btn-group dropup">
                                                <button type="button" class="btn btn-icon fs-lg dropdown-toggle no-arrow" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-smile"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-animated text-center rounded-pill overflow-hidden" style="width: 280px">
                                                    <div class="px-1 py-0">
                                                        <a href="javascript:void(0);" class="emoji emoji--like" data-toggle="tooltip" data-placement="top" title="" data-original-title="Like">
                                                            <div class="emoji__hand">
                                                                <div class="emoji__thumb"></div>
                                                            </div>
                                                        </a>
                                                        <a href="javascript:void(0);" class="emoji emoji--love" data-toggle="tooltip" data-placement="top" title="" data-original-title="Love">
                                                            <div class="emoji__heart"></div>
                                                        </a>
                                                        <a href="javascript:void(0);" class="emoji emoji--haha" data-toggle="tooltip" data-placement="top" title="" data-original-title="Haha">
                                                            <div class="emoji__face">
                                                                <div class="emoji__eyes"></div>
                                                                <div class="emoji__mouth">
                                                                    <div class="emoji__tongue"></div>
                                                                </div>
                                                            </div>
                                                        </a>
                                                        <a href="javascript:void(0);" class="emoji emoji--yay" data-toggle="tooltip" data-placement="top" title="" data-original-title="Yay">
                                                            <div class="emoji__face">
                                                                <div class="emoji__eyebrows"></div>
                                                                <div class="emoji__mouth"></div>
                                                            </div>
                                                        </a>
                                                        <a href="javascript:void(0);" class="emoji emoji--wow" data-toggle="tooltip" data-placement="top" title="" data-original-title="Wow">
                                                            <div class="emoji__face">
                                                                <div class="emoji__eyebrows"></div>
                                                                <div class="emoji__eyes"></div>
                                                                <div class="emoji__mouth"></div>
                                                            </div>
                                                        </a>
                                                        <a href="javascript:void(0);" class="emoji emoji--sad" data-toggle="tooltip" data-placement="top" title="" data-original-title="Sad">
                                                            <div class="emoji__face">
                                                                <div class="emoji__eyebrows"></div>
                                                                <div class="emoji__eyes"></div>
                                                                <div class="emoji__mouth"></div>
                                                            </div>
                                                        </a>
                                                        <a href="javascript:void(0);" class="emoji emoji--angry" data-toggle="tooltip" data-placement="top" title="" data-original-title="Angry">
                                                            <div class="emoji__face">
                                                                <div class="emoji__eyebrows"></div>
                                                                <div class="emoji__eyes"></div>
                                                                <div class="emoji__mouth"></div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-icon fs-lg">
                                                <i class="fa fa-paperclip"></i>
                                            </button>
                                            <div class="custom-control custom-checkbox custom-control-inline ml-auto hidden-sm-down">
                                                <input type="checkbox" class="custom-control-input" id="defaultInline1">
                                                <label class="custom-control-label" for="defaultInline1">Press <strong>ENTER</strong> to send</label>
                                            </div>
                                            <button class="btn btn-primary btn-sm ml-auto ml-sm-0">
                                                Reply
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="panel-3" class="panel">
                                <div class="panel-hdr">
                                    <h2 class="js-get-date"></h2>
                                </div>
                                <div class="panel-container show">
                                    <div class="panel-content">
                                        <div id="calendar"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div id="panel-4" class="panel">
                                <div class="panel-hdr">
                                    <h2>Bird's Eyes</h2>
                                </div>
                                <div class="panel-container show">
                                    <div class="panel-content jqvmap-bg-ocean p-0" style="height: 330px;">
                                        <div id="vector-map" class="w-100 h-100 p-4"></div>
                                    </div>
                                    <div class="panel-content jqvmap-bg-ocean">
                                        <div class="d-flex align-items-center">
                                            <img class="d-inline-block js-jqvmap-flag mr-3 border-faded" alt="flag" src="https://lipis.github.io/flag-icon-css/flags/4x3/us.svg" style="width:55px; height: auto;">
                                            <h4 class="d-inline-block fw-300 m-0 text-muted fs-lg">
                                                Showcasing information:
                                                <small class="js-jqvmap-country mb-0 fw-500 text-dark">United States of America - $3,760,125.00</small>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="panel-5" class="panel">
                                <div class="panel-hdr">
                                    <h2>Subscriptions Hourly</h2>
                                </div>
                                <div class="panel-container show">
                                    <div class="panel-content">
                                        <h5>Subscription Views / hour</h5>
                                        <div id="flotBar1" style="width: 100%; height: 160px;"></div>
                                    </div>
                                </div>
                            </div>
                            <div id="panel-6" class="panel">
                                <div class="panel-hdr">
                                    <h2>Secession Scale </h2>
                                </div>
                                <div class="panel-container show">
                                    <div class="panel-content p-0 mb-g">
                                        <div class="alert alert-success alert-dismissible fade show border-faded border-left-0 border-right-0 border-top-0 rounded-0 m-0" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true"><i class="fa fa-times"></i></span>
                                            </button>
                                            <strong>Last update was on <span class="js-get-date"></span></strong> - Your logs are all up to date.
                                        </div>
                                    </div>
                                    <div class="panel-content">
                                        <div class="row  mb-g">
                                            <div class="col-md-6 d-flex align-items-center">
                                                <div id="flotPie" class="w-100" style="height:250px"></div>
                                            </div>
                                            <div class="col-md-6 col-lg-5 mr-lg-auto">
                                                <div class="d-flex mt-2 mb-1 fs-xs text-primary">
                                                    Current Usage
                                                </div>
                                                <div class="progress progress-xs mb-3">
                                                    <div class="progress-bar" role="progressbar" style="width: 70%;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div class="d-flex mt-2 mb-1 fs-xs text-info">
                                                    Net Usage
                                                </div>
                                                <div class="progress progress-xs mb-3">
                                                    <div class="progress-bar bg-info-500" role="progressbar" style="width: 30%;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div class="d-flex mt-2 mb-1 fs-xs text-warning">
                                                    Users blocked
                                                </div>
                                                <div class="progress progress-xs mb-3">
                                                    <div class="progress-bar bg-warning-500" role="progressbar" style="width: 40%;" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div class="d-flex mt-2 mb-1 fs-xs text-danger">
                                                    Custom cases
                                                </div>
                                                <div class="progress progress-xs mb-3">
                                                    <div class="progress-bar bg-danger-500" role="progressbar" style="width: 15%;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div class="d-flex mt-2 mb-1 fs-xs text-success">
                                                    Test logs
                                                </div>
                                                <div class="progress progress-xs mb-3">
                                                    <div class="progress-bar bg-success-500" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div class="d-flex mt-2 mb-1 fs-xs text-dark">
                                                    Uptime records
                                                </div>
                                                <div class="progress progress-xs mb-3">
                                                    <div class="progress-bar bg-fusion-500" role="progressbar" style="width: 10%;" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
                <!-- this overlay is activated only when mobile menu is triggered -->
                <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div> <!-- END Page Content -->

                <!-- BEGIN Shortcuts -->
                <!-- modal shortcut -->
                <div class="modal fade modal-backdrop-transparent" id="modal-shortcut" tabindex="-1" role="dialog" aria-labelledby="modal-shortcut" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-top modal-transparent" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <ul class="app-list w-auto h-auto p-0 text-left">
                                    <li>
                                        <a href="intel_introduction.html" class="app-list-item text-white border-0 m-0">
                                            <div class="icon-stack">
                                                <i class="base base-7 icon-stack-3x opacity-100 color-primary-500 "></i>
                                                <i class="base base-7 icon-stack-2x opacity-100 color-primary-300 "></i>
                                                <i class="fa fa-home icon-stack-1x opacity-100 color-white"></i>
                                            </div>
                                            <span class="app-list-name">
                                                    Home
                                                </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="page_inbox_general.html" class="app-list-item text-white border-0 m-0">
                                            <div class="icon-stack">
                                                <i class="base base-7 icon-stack-3x opacity-100 color-success-500 "></i>
                                                <i class="base base-7 icon-stack-2x opacity-100 color-success-300 "></i>
                                                <i class="ni ni-envelope icon-stack-1x text-white"></i>
                                            </div>
                                            <span class="app-list-name">
                                                    Inbox
                                                </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="intel_introduction.html" class="app-list-item text-white border-0 m-0">
                                            <div class="icon-stack">
                                                <i class="base base-7 icon-stack-2x opacity-100 color-primary-300 "></i>
                                                <i class="fa fa-plus icon-stack-1x opacity-100 color-white"></i>
                                            </div>
                                            <span class="app-list-name">
                                                    Add More
                                                </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div> <!-- END Shortcuts -->
            </div>
        </div>
    </div>
    <!-- END Page Wrapper -->

    <!-- BEGIN Page Settings -->
    <div class="modal fade js-modal-settings modal-backdrop-transparent" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-right modal-md">
            <div class="modal-content">
                <div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center w-100">
                    <h4 class="m-0 text-center color-white">
                        Layout Settings
                        <small class="mb-0 opacity-80">User Interface Settings</small>
                    </h4>
                    <button type="button" class="close text-white position-absolute pos-top pos-right p-2 m-1 mr-2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="settings-panel">
                        <div class="mt-4 d-table w-100 px-5">
                            <div class="d-table-cell align-middle">
                                <h5 class="p-0">
                                    App Layout
                                </h5>
                            </div>
                        </div>
                        <div class="list" id="fh">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="header-function-fixed"></a>
                            <span class="onoffswitch-title">Fixed Header</span>
                            <span class="onoffswitch-title-desc">header is in a fixed at all times</span>
                        </div>
                        <div class="list" id="nff">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-function-fixed"></a>
                            <span class="onoffswitch-title">Fixed Navigation</span>
                            <span class="onoffswitch-title-desc">left panel is fixed</span>
                        </div>
                        <div class="list" id="nfm">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-function-minify"></a>
                            <span class="onoffswitch-title">Minify Navigation</span>
                            <span class="onoffswitch-title-desc">Skew nav to maximize space</span>
                        </div>
                        <div class="list" id="nfh">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-function-hidden"></a>
                            <span class="onoffswitch-title">Hide Navigation</span>
                            <span class="onoffswitch-title-desc">roll mouse on edge to reveal</span>
                        </div>
                        <div class="list" id="nft">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-function-top"></a>
                            <span class="onoffswitch-title">Top Navigation</span>
                            <span class="onoffswitch-title-desc">Relocate left pane to top</span>
                        </div>
                        <div class="list" id="mmb">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-main-boxed"></a>
                            <span class="onoffswitch-title">Boxed Layout</span>
                            <span class="onoffswitch-title-desc">Encapsulates to a container</span>
                        </div>
                        <div class="expanded">
                            <ul class="">
                                <li>
                                    <div class="bg-fusion-50" data-action="toggle" data-class="mod-bg-1"></div>
                                </li>
                                <li>
                                    <div class="bg-warning-200" data-action="toggle" data-class="mod-bg-2"></div>
                                </li>
                                <li>
                                    <div class="bg-primary-200" data-action="toggle" data-class="mod-bg-3"></div>
                                </li>
                                <li>
                                    <div class="bg-success-300" data-action="toggle" data-class="mod-bg-4"></div>
                                </li>
                            </ul>
                            <div class="list" id="mbgf">
                                <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-fixed-bg"></a>
                                <span class="onoffswitch-title">Fixed Background</span>
                            </div>
                        </div>
                        <div class="mt-4 d-table w-100 px-5">
                            <div class="d-table-cell align-middle">
                                <h5 class="p-0">
                                    Mobile Menu
                                </h5>
                            </div>
                        </div>
                        <div class="list" id="nmp">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-mobile-push"></a>
                            <span class="onoffswitch-title">Push Content</span>
                            <span class="onoffswitch-title-desc">Content pushed on menu reveal</span>
                        </div>
                        <div class="list" id="nmno">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-mobile-no-overlay"></a>
                            <span class="onoffswitch-title">No Overlay</span>
                            <span class="onoffswitch-title-desc">Removes mesh on menu reveal</span>
                        </div>
                        <div class="list" id="sldo">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="nav-mobile-slide-out"></a>
                            <span class="onoffswitch-title">Off-Canvas <sup>(beta)</sup></span>
                            <span class="onoffswitch-title-desc">Content overlaps menu</span>
                        </div>
                        <div class="mt-4 d-table w-100 px-5">
                            <div class="d-table-cell align-middle">
                                <h5 class="p-0">
                                    Accessibility
                                </h5>
                            </div>
                        </div>
                        <div class="list" id="mbf">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-bigger-font"></a>
                            <span class="onoffswitch-title">Bigger Content Font</span>
                            <span class="onoffswitch-title-desc">content fonts are bigger for readability</span>
                        </div>
                        <div class="list" id="mhc">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-high-contrast"></a>
                            <span class="onoffswitch-title">High Contrast Text (WCAG 2 AA)</span>
                            <span class="onoffswitch-title-desc">4.5:1 text contrast ratio</span>
                        </div>
                        <div class="list" id="mcb">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-color-blind"></a>
                            <span class="onoffswitch-title">Daltonism <sup>(beta)</sup> </span>
                            <span class="onoffswitch-title-desc">color vision deficiency</span>
                        </div>
                        <div class="list" id="mpc">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-pace-custom"></a>
                            <span class="onoffswitch-title">Preloader Inside</span>
                            <span class="onoffswitch-title-desc">preloader will be inside content</span>
                        </div>
                        <div class="mt-4 d-table w-100 px-5">
                            <div class="d-table-cell align-middle">
                                <h5 class="p-0">
                                    Global Modifications
                                </h5>
                            </div>
                        </div>
                        <div class="list" id="mcbg">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-clean-page-bg"></a>
                            <span class="onoffswitch-title">Clean Page Background</span>
                            <span class="onoffswitch-title-desc">adds more whitespace</span>
                        </div>
                        <div class="list" id="mhni">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-hide-nav-icons"></a>
                            <span class="onoffswitch-title">Hide Navigation Icons</span>
                            <span class="onoffswitch-title-desc">invisible navigation icons</span>
                        </div>
                        <div class="list" id="dan">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-disable-animation"></a>
                            <span class="onoffswitch-title">Disable CSS Animation</span>
                            <span class="onoffswitch-title-desc">Disables CSS based animations</span>
                        </div>
                        <div class="list" id="mhic">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-hide-info-card"></a>
                            <span class="onoffswitch-title">Hide Info Card</span>
                            <span class="onoffswitch-title-desc">Hides info card from left panel</span>
                        </div>
                        <div class="list" id="mlph">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-lean-subheader"></a>
                            <span class="onoffswitch-title">Lean Subheader</span>
                            <span class="onoffswitch-title-desc">distinguished page header</span>
                        </div>
                        <div class="list" id="mnl">
                            <a href="#" onclick="return false;" class="btn btn-switch" data-action="toggle" data-class="mod-nav-link"></a>
                            <span class="onoffswitch-title">Hierarchical Navigation</span>
                            <span class="onoffswitch-title-desc">Clear breakdown of nav links</span>
                        </div>
                        <div class="list mt-1">
                            <span class="onoffswitch-title">Global Font Size <small>(RESETS ON REFRESH)</small> </span>
                            <div class="btn-group btn-group-sm btn-group-toggle my-2" data-toggle="buttons">
                                <label class="btn btn-default btn-sm" data-action="toggle-swap" data-class="root-text-sm" data-target="html">
                                    <input type="radio" name="changeFrontSize"> SM
                                </label>
                                <label class="btn btn-default btn-sm" data-action="toggle-swap" data-class="root-text" data-target="html">
                                    <input type="radio" name="changeFrontSize" checked=""> MD
                                </label>
                                <label class="btn btn-default btn-sm" data-action="toggle-swap" data-class="root-text-lg" data-target="html">
                                    <input type="radio" name="changeFrontSize"> LG
                                </label>
                                <label class="btn btn-default btn-sm" data-action="toggle-swap" data-class="root-text-xl" data-target="html">
                                    <input type="radio" name="changeFrontSize"> XL
                                </label>
                            </div>
                            <span class="onoffswitch-title-desc d-block mb-g">Change <strong>root</strong> font size to effect rem values</span>
                        </div>
                        <div class="mt-2 d-table w-100 pl-5 pr-3">
                            <div class="d-table-cell align-middle">
                                <h5 class="p-0">
                                    Theme colors <small>(overlays base css)</small>
                                </h5>
                                <div class="fs-xs text-muted p-2 alert alert-warning mt-3 mb-0">
                                    <i class="fa fa-exclamation-triangle text-warning mr-2"></i>Due to network latency and CPU utilization, you may experience a brief flickering effect on page load which may show the intial applied theme for a split second. Setting the prefered style/theme in the header will prevent this from happening.
                                </div>
                            </div>
                        </div>
                        <div class="expanded theme-colors pl-5 pr-3">
                            <ul class="m-0">
                                <li><a href="#" id="myapp-0" data-action="theme-update" data-themesave data-theme="" data-toggle="tooltip" data-placement="top" title="Wisteria (base css)" data-original-title="Wisteria (base css)"></a></li>
                                <li><a href="#" id="myapp-1" data-action="theme-update" data-themesave data-theme="css/themes/cust-theme-1.css" data-toggle="tooltip" data-placement="top" title="Tapestry" data-original-title="Tapestry"></a></li>
                                <li><a href="#" id="myapp-2" data-action="theme-update" data-themesave data-theme="css/themes/cust-theme-2.css" data-toggle="tooltip" data-placement="top" title="Atlantis" data-original-title="Atlantis"></a></li>
                                <li><a href="#" id="myapp-3" data-action="theme-update" data-themesave data-theme="css/themes/cust-theme-3.css" data-toggle="tooltip" data-placement="top" title="Indigo" data-original-title="Indigo"></a></li>
                                <li><a href="#" id="myapp-4" data-action="theme-update" data-themesave data-theme="css/themes/cust-theme-4.css" data-toggle="tooltip" data-placement="top" title="Dodger Blue" data-original-title="Dodger Blue"></a></li>
                                <li><a href="#" id="myapp-5" data-action="theme-update" data-themesave data-theme="css/themes/cust-theme-5.css" data-toggle="tooltip" data-placement="top" title="Tradewind" data-original-title="Tradewind"></a></li>
                                <li><a href="#" id="myapp-6" data-action="theme-update" data-themesave data-theme="css/themes/cust-theme-6.css" data-toggle="tooltip" data-placement="top" title="Cranberry" data-original-title="Cranberry"></a></li>
                                <li><a href="#" id="myapp-7" data-action="theme-update" data-themesave data-theme="css/themes/cust-theme-7.css" data-toggle="tooltip" data-placement="top" title="Oslo Gray" data-original-title="Oslo Gray"></a></li>
                                <li><a href="#" id="myapp-8" data-action="theme-update" data-themesave data-theme="css/themes/cust-theme-8.css" data-toggle="tooltip" data-placement="top" title="Chetwode Blue" data-original-title="Chetwode Blue"></a></li>
                                <li><a href="#" id="myapp-9" data-action="theme-update" data-themesave data-theme="css/themes/cust-theme-9.css" data-toggle="tooltip" data-placement="top" title="Apricot" data-original-title="Apricot"></a></li>
                                <li><a href="#" id="myapp-10" data-action="theme-update" data-themesave data-theme="css/themes/cust-theme-10.css" data-toggle="tooltip" data-placement="top" title="Blue Smoke" data-original-title="Blue Smoke"></a></li>
                                <li><a href="#" id="myapp-11" data-action="theme-update" data-themesave data-theme="css/themes/cust-theme-11.css" data-toggle="tooltip" data-placement="top" title="Green Smoke" data-original-title="Green Smoke"></a></li>
                                <li><a href="#" id="myapp-12" data-action="theme-update" data-themesave data-theme="css/themes/cust-theme-12.css" data-toggle="tooltip" data-placement="top" title="Wild Blue Yonder" data-original-title="Wild Blue Yonder"></a></li>
                                <li><a href="#" id="myapp-13" data-action="theme-update" data-themesave data-theme="css/themes/cust-theme-13.css" data-toggle="tooltip" data-placement="top" title="Emerald" data-original-title="Emerald"></a></li>
                            </ul>
                        </div>
                        <hr class="mb-0 mt-4">
                        <div class="pl-5 pr-3 py-3 bg-faded">
                            <div class="row no-gutters">
                                <div class="col-6 pr-1">
                                    <a href="#" class="btn btn-outline-danger fw-500 btn-block" data-action="app-reset">Reset Settings</a>
                                </div>
                                <div class="col-6 pl-1">
                                    <a href="#" class="btn btn-danger fw-500 btn-block" data-action="factory-reset">Factory Reset</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <span id="saving"></span>
                </div>
            </div>
        </div>
    </div> <!-- END Page Settings -->
    <!-- base vendor bundle:
         DOC: if you remove pace.js from core please note on Internet Explorer some CSS animations may execute before a page is fully loaded, resulting 'jump' animations
                    + pace.js (recommended)
                    + jquery.js (core)
                    + jquery-ui-cust.js (core)
                    + popper.js (core)
                    + bootstrap.js (core)
                    + slimscroll.js (extension)
                    + app.navigation.js (core)
                    + ba-throttle-debounce.js (core)
                    + waves.js (extension)
                    + smartpanels.js (extension)
                    + src/../jquery-snippets.js (core) -->

    </body>
@endsection

@push('js')
<script src="{{ asset('assets') }}/js/intel-dashboard-js/vendors.bundle.js"></script>
<script src="{{ asset('assets') }}/js/intel-dashboard-js/app.bundle.js"></script>
<script type="text/javascript">
    /* Activate smart panels */
    $('#js-page-content').smartPanel();

</script>
<!-- The order of scripts is irrelevant. Please check out the plugin pages for more details about these plugins below: -->
<script src="{{ asset('assets') }}/js/intel-dashboard-js/dependency/moment/moment.js"></script>
<script src="{{ asset('assets') }}/js/intel-dashboard-js/miscellaneous/fullcalendar/fullcalendar.bundle.js"></script>
<script src="{{ asset('assets') }}/js/intel-dashboard-js/statistics/sparkline/sparkline.bundle.js"></script>
<script src="{{ asset('assets') }}/js/intel-dashboard-js/statistics/easypiechart/easypiechart.bundle.js"></script>
<script src="{{ asset('assets') }}/js/intel-dashboard-js/statistics/flot/flot.bundle.js"></script>
<script src="{{ asset('assets') }}/js/intel-dashboard-js/miscellaneous/jqvmap/jqvmap.bundle.js"></script>
<script>
    $(document).ready(function()
    {
        //$('#js-page-content').smartPanel();

        //
        //
        var dataSetPie = [
            {
                label: "Asia",
                data: 4119630000,
                color: myapp_get_color.primary_500
            },
            {
                label: "Latin America",
                data: 590950000,
                color: myapp_get_color.info_500
            },
            {
                label: "Africa",
                data: 1012960000,
                color: myapp_get_color.warning_500
            },
            {
                label: "Oceania",
                data: 95100000,
                color: myapp_get_color.danger_500
            },
            {
                label: "Europe",
                data: 727080000,
                color: myapp_get_color.success_500
            },
            {
                label: "North America",
                data: 344120000,
                color: myapp_get_color.fusion_400
            }];


        $.plot($("#flotPie"), dataSetPie,
            {
                series:
                    {
                        pie:
                            {
                                innerRadius: 0.5,
                                show: true,
                                radius: 1,
                                label:
                                    {
                                        show: true,
                                        radius: 2 / 3,
                                        threshold: 0.1
                                    }
                            }
                    },
                legend:
                    {
                        show: false
                    }
            });


        $.plot('#flotBar1', [
                {
                    data: [
                        [1, 0],
                        [2, 0],
                        [3, 0],
                        [4, 1],
                        [5, 3],
                        [6, 3],
                        [7, 10],
                        [8, 11],
                        [9, 10],
                        [10, 9],
                        [11, 12],
                        [12, 8],
                        [13, 10],
                        [14, 6],
                        [15, 3]
                    ],
                    bars:
                        {
                            show: true,
                            lineWidth: 0,
                            fillColor: myapp_get_color.fusion_50,
                            barWidth: .3,
                            order: 'left'
                        }
                },
                {
                    data: [
                        [1, 0],
                        [2, 0],
                        [3, 1],
                        [4, 2],
                        [5, 2],
                        [6, 5],
                        [7, 8],
                        [8, 12],
                        [9, 10],
                        [10, 11],
                        [11, 3]
                    ],
                    bars:
                        {
                            show: true,
                            lineWidth: 0,
                            fillColor: myapp_get_color.success_500,
                            barWidth: .3,
                            align: 'right'
                        }
                }],
            {
                grid:
                    {
                        borderWidth: 0,
                    },
                yaxis:
                    {
                        min: 0,
                        max: 15,
                        tickColor: '#F0F0F0',
                        ticks: [
                            [0, ''],
                            [5, '$5000'],
                            [10, '$25000'],
                            [15, '$45000']
                        ],
                        font:
                            {
                                color: '#444',
                                size: 10
                            }
                    },
                xaxis:
                    {
                        mode: 'categories',
                        tickColor: '#F0F0F0',
                        ticks: [
                            [0, '3am'],
                            [1, '4am'],
                            [2, '5am'],
                            [3, '6am'],
                            [4, '7am'],
                            [5, '8am'],
                            [6, '9am'],
                            [7, '10am'],
                            [8, '11am'],
                            [9, '12nn'],
                            [10, '1pm'],
                            [11, '2pm'],
                            [12, '3pm'],
                            [13, '4pm'],
                            [14, '5pm']
                        ],
                        font:
                            {
                                color: '#999',
                                size: 9
                            }
                    }
            });


        /*
         * VECTOR MAP
         */

        var data_array = {
            "af": "16.63",
            "al": "0",
            "dz": "158.97",
            "ao": "85.81",
            "ag": "1.1",
            "ar": "351.02",
            "am": "8.83",
            "au": "1219.72",
            "at": "366.26",
            "az": "52.17",
            "bs": "7.54",
            "bh": "21.73",
            "bd": "105.4",
            "bb": "3.96",
            "by": "52.89",
            "be": "461.33",
            "bz": "1.43",
            "bj": "6.49",
            "bt": "1.4",
            "bo": "19.18",
            "ba": "16.2",
            "bw": "12.5",
            "br": "2023.53",
            "bn": "11.96",
            "bg": "44.84",
            "bf": "8.67",
            "bi": "1.47",
            "kh": "11.36",
            "cm": "21.88",
            "ca": "1563.66",
            "cv": "1.57",
            "cf": "2.11",
            "td": "7.59",
            "cl": "199.18",
            "cn": "5745.13",
            "co": "283.11",
            "km": "0.56",
            "cd": "12.6",
            "cg": "11.88",
            "cr": "35.02",
            "ci": "22.38",
            "hr": "59.92",
            "cy": "22.75",
            "cz": "195.23",
            "dk": "304.56",
            "dj": "1.14",
            "dm": "0.38",
            "do": "50.87",
            "ec": "61.49",
            "eg": "216.83",
            "sv": "21.8",
            "gq": "14.55",
            "er": "2.25",
            "ee": "19.22",
            "et": "30.94",
            "fj": "3.15",
            "fi": "231.98",
            "fr": "2555.44",
            "ga": "12.56",
            "gm": "1.04",
            "ge": "11.23",
            "de": "3305.9",
            "gh": "18.06",
            "gr": "305.01",
            "gd": "0.65",
            "gt": "40.77",
            "gn": "4.34",
            "gw": "0.83",
            "gy": "2.2",
            "ht": "6.5",
            "hn": "15.34",
            "hk": "226.49",
            "hu": "132.28",
            "is": "0",
            "in": "1430.02",
            "id": "695.06",
            "ir": "337.9",
            "iq": "84.14",
            "ie": "204.14",
            "il": "201.25",
            "it": "2036.69",
            "jm": "13.74",
            "jp": "5390.9",
            "jo": "27.13",
            "kz": "129.76",
            "ke": "32.42",
            "ki": "0.15",
            "kw": "117.32",
            "kg": "4.44",
            "la": "6.34",
            "lv": "23.39",
            "lb": "39.15",
            "ls": "1.8",
            "lr": "0.98",
            "lt": "35.73",
            "lu": "52.43",
            "mk": "9.58",
            "mg": "8.33",
            "mw": "5.04",
            "my": "218.95",
            "mv": "1.43",
            "ml": "9.08",
            "mt": "7.8",
            "mr": "3.49",
            "mu": "9.43",
            "mx": "1004.04",
            "md": "5.36",
            "rw": "5.69",
            "ws": "0.55",
            "st": "0.19",
            "sa": "434.44",
            "sn": "12.66",
            "rs": "38.92",
            "sc": "0.92",
            "sl": "1.9",
            "sg": "217.38",
            "sk": "86.26",
            "si": "46.44",
            "sb": "0.67",
            "za": "354.41",
            "es": "1374.78",
            "lk": "48.24",
            "kn": "0.56",
            "lc": "1",
            "vc": "0.58",
            "sd": "65.93",
            "sr": "3.3",
            "sz": "3.17",
            "se": "444.59",
            "ch": "522.44",
            "sy": "59.63",
            "tw": "426.98",
            "tj": "5.58",
            "tz": "22.43",
            "th": "312.61",
            "tl": "0.62",
            "tg": "3.07",
            "to": "0.3",
            "tt": "21.2",
            "tn": "43.86",
            "tr": "729.05",
            "tm": "0",
            "ug": "17.12",
            "ua": "136.56",
            "ae": "239.65",
            "gb": "2258.57",
            "us": "14624.18",
            "uy": "40.71",
            "uz": "37.72",
            "vu": "0.72",
            "ve": "285.21",
            "vn": "101.99",
            "ye": "30.02",
            "zm": "15.69",
            "zw": "0"
        };

        $('#vector-map').vectorMap(
            {
                map: 'world_en',
                backgroundColor: 'transparent',
                color: myapp_get_color.warning_50,
                borderOpacity: 0.5,
                borderWidth: 1,
                hoverColor: myapp_get_color.success_300,
                hoverOpacity: null,
                selectedColor: myapp_get_color.success_500,
                selectedRegions: ['US'],
                enableZoom: true,
                showTooltip: true,
                scaleColors: [myapp_get_color.primary_400, myapp_get_color.primary_50],
                values: data_array,
                normalizeFunction: 'polynomial',
                onRegionClick: function(element, code, region)
                {
                    /*var message = 'You clicked "'
                     + region
                     + '" which has the code: '
                     + code.toLowerCase();

                     console.log(message);*/

                    var randomNumber = Math.floor(Math.random() * 10000000);
                    var arrow;

                    if (Math.random() >= 0.5 == true)
                    {
                        arrow = '<div class="ml-2 d-inline-flex"><i class="fa fa-caret-up text-success fs-xs"></i></div>'
                    }
                    else
                    {
                        arrow = '<div class="ml-2 d-inline-flex"><i class="fa fa-caret-down text-danger fs-xs"></i></div>'
                    }

                    $('.js-jqvmap-flag').attr('src', 'https://lipis.github.io/flag-icon-css/flags/4x3/' + code.toLowerCase() + '.svg');
                    $('.js-jqvmap-country').html(region + ' - ' + '$' + randomNumber.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") + arrow);
                }
            });


        /* TAB 1: UPDATING CHART */
        var data = [],
            totalPoints = 200;
        var getRandomData = function()
        {
            if (data.length > 0)
                data = data.slice(1);

            // do a random walk
            while (data.length < totalPoints)
            {
                var prev = data.length > 0 ? data[data.length - 1] : 50;
                var y = prev + Math.random() * 10 - 5;
                if (y < 0)
                    y = 0;
                if (y > 100)
                    y = 100;
                data.push(y);
            }

            // zip the generated y values with the x values
            var res = [];
            for (var i = 0; i < data.length; ++i)
                res.push([i, data[i]])
            return res;
        }
        // setup control widget
        var updateInterval = 1500;
        $("#updating-chart").val(updateInterval).change(function()
        {

            var v = $(this).val();
            if (v && !isNaN(+v))
            {
                updateInterval = +v;
                $(this).val("" + updateInterval);
            }

        });
        // setup plot
        var options = {
            colors: [myapp_get_color.primary_700],
            series:
                {
                    lines:
                        {
                            show: true,
                            lineWidth: 0.5,
                            fill: 0.9,
                            fillColor:
                                {
                                    colors: [
                                        {
                                            opacity: 0.6
                                        },
                                        {
                                            opacity: 0
                                        }]
                                },
                        },

                    shadowSize: 0 // Drawing is faster without shadows
                },
            grid:
                {
                    borderColor: '#F0F0F0',
                    borderWidth: 1,
                    labelMargin: 5
                },
            xaxis:
                {
                    color: '#F0F0F0',
                    font:
                        {
                            size: 10,
                            color: '#999'
                        }
                },
            yaxis:
                {
                    min: 0,
                    max: 100,
                    color: '#F0F0F0',
                    font:
                        {
                            size: 10,
                            color: '#999'
                        }
                }
        };
        var plot = $.plot($("#updating-chart"), [getRandomData()], options);
        /* live switch */
        $('input[type="checkbox"]#start_interval').click(function()
        {
            if ($(this).prop('checked'))
            {
                $on = true;
                updateInterval = 1500;
                update();
            }
            else
            {
                clearInterval(updateInterval);
                $on = false;
            }
        });
        var update = function()
        {
            if ($on == true)
            {
                plot.setData([getRandomData()]);
                plot.draw();
                setTimeout(update, updateInterval);

            }
            else
            {
                clearInterval(updateInterval)
            }

        }



        /*calendar */
        var todayDate = moment().startOf('day');
        var YM = todayDate.format('YYYY-MM');
        var YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
        var TODAY = todayDate.format('YYYY-MM-DD');
        var TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');


        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl,
            {
                plugins: ['dayGrid', 'list', 'timeGrid', 'interaction', 'bootstrap'],
                themeSystem: 'bootstrap',
                timeZone: 'UTC',
                dateAlignment: "month", //week, month
                buttonText:
                    {
                        today: 'today',
                        month: 'month',
                        week: 'week',
                        day: 'day',
                        list: 'list'
                    },
                eventTimeFormat:
                    {
                        hour: 'numeric',
                        minute: '2-digit',
                        meridiem: 'short'
                    },
                navLinks: true,
                header:
                    {
                        left: 'title',
                        center: '',
                        right: 'today prev,next'
                    },
                footer:
                    {
                        left: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
                        center: '',
                        right: ''
                    },
                editable: true,
                eventLimit: true, // allow "more" link when too many events
                events: [
                    {
                        title: 'All Day Event',
                        start: YM + '-01',
                        description: 'This is a test description', //this is currently bugged: https://github.com/fullcalendar/fullcalendar/issues/1795
                        className: "border-warning bg-warning text-dark"
                    },
                    {
                        title: 'Reporting',
                        start: YM + '-14T13:30:00',
                        end: YM + '-14',
                        className: "bg-white border-primary text-primary"
                    },
                    {
                        title: 'Surgery oncall',
                        start: YM + '-02',
                        end: YM + '-03',
                        className: "bg-primary border-primary text-white"
                    },
                    {
                        title: 'NextGen Expo 2019 - Product Release',
                        start: YM + '-03',
                        end: YM + '-05',
                        className: "bg-info border-info text-white"
                    },
                    {
                        title: 'Dinner',
                        start: YM + '-12',
                        end: YM + '-10'
                    },
                    {
                        id: 999,
                        title: 'Repeating Event',
                        start: YM + '-09T16:00:00',
                        className: "bg-danger border-danger text-white"
                    },
                    {
                        id: 1000,
                        title: 'Repeating Event',
                        start: YM + '-16T16:00:00'
                    },
                    {
                        title: 'Conference',
                        start: YESTERDAY,
                        end: TOMORROW,
                        className: "bg-success border-success text-white"
                    },
                    {
                        title: 'Meeting',
                        start: TODAY + 'T10:30:00',
                        end: TODAY + 'T12:30:00',
                        className: "bg-primary text-white border-primary"
                    },
                    {
                        title: 'Lunch',
                        start: TODAY + 'T12:00:00',
                        className: "bg-info border-info"
                    },
                    {
                        title: 'Meeting',
                        start: TODAY + 'T14:30:00',
                        className: "bg-warning text-dark border-warning"
                    },
                    {
                        title: 'Happy Hour',
                        start: TODAY + 'T17:30:00',
                        className: "bg-success border-success text-white"
                    },
                    {
                        title: 'Dinner',
                        start: TODAY + 'T20:00:00',
                        className: "bg-danger border-danger text-white"
                    },
                    {
                        title: 'Birthday Party',
                        start: TOMORROW + 'T07:00:00',
                        className: "bg-primary text-white border-primary text-white"
                    },
                    {
                        title: 'Gotbootstrap Meeting',
                        url: 'http://gotbootstrap.com/',
                        start: YM + '-28',
                        className: "bg-info border-info text-white"
                    }],
                viewSkeletonRender: function()
                {
                    $('.fc-toolbar .btn-default').addClass('btn-sm');
                    $('.fc-header-toolbar h2').addClass('fs-md');
                    $('#calendar').addClass('fc-reset-order')
                },

            });

        calendar.render();
    });

</script>
@endpush
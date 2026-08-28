@extends('layouts.app' ,[ 'pageSlug' => config('site_vars.labWorkFlowLabel')])



@section('content')
    {{--<meta http-equiv="refresh" content="120">--}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body.white-content {
            font-family: 'Cairo', sans-serif;
        }

        body.white-content button,
        body.white-content input,
        body.white-content select,
        body.white-content textarea,
        body.white-content table,
        body.white-content .card,
        body.white-content .modal {
            font-family: inherit;
        }

        .case-checkbox-waiting, .case-checkbox-active {
            transform: scale(1.5);
        }
        .macaw-tabs.macaw-aurora-tabs {
            /*
             --------
             * Color Palette
             --------
             */
            --tab-color-white: #f9f9f9;
            --tab-color-black: #004345;
            --tab-color-cadet: #2b7b7d;
            --tab-color-fighter: #315f5f;
            --tab-color-space: #383961;
            --tab-color-gray: #d7d9d7;
            --tab-color-english: #2b7b7d;
            /*
             --------
             * CSS Vars
             --------
             */
            --tab-bg-color: var(--tab-color-cadet);
            --tab-text-color: var(--tab-color-gray);
            --tab-border-color: var(--tab-color-space);
            --tab-active-bg-color: var(--tab-color-white);
            --tab-active-text-color: var(--tab-color-black);
            --tab-active-border-color: var(--tab-color-english);
            --tab-focus-bg-color: var(--tab-color-fighter);
            --tab-focus-text-color: var(--tab-color-white);
            --tab-focus-text-secondary-color: var(--tab-color-english);
            --tab-focus-border-color: var(--tab-color-fighter);
            /*
             --------
             * Style
             --------
             */
            display: flex;

            width: 100%;

        }
        .site-wrapper {
            margin:0;
            width: 100%;
            max-width: none;
        }
        .alsolent-workflow-modal {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(8, 13, 20, 0.35);
            z-index: 1050;
            padding: 24px;
        }
        .alsolent-workflow-modal.active {
            display: flex;
        }
        .alsolent-workflow-dialog {
            background: #ffffff;
            border-radius: 14px;
            width: min(720px, 100%);
            max-height: 90vh;
            overflow: auto;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        }
        .alsolent-workflow-title {
            font-size: 18px;
            margin: 0;
            color: #1f2a37;
        }
        .alsolent-close-button {
            border: none;
            background: transparent;
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
            color: #6b7280;
        }
        .alsolent-close-button:hover {
            color: #111827;
        }
        .alsolent-workflow-header,
        .alsolent-workflow-body,
        .alsolent-workflow-footer {
            padding: 16px 20px;
        }
        .alsolent-workflow-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(17, 21, 30, 0.08);
        }
        .alsolent-workflow-footer {
            border-top: 1px solid rgba(17, 21, 30, 0.08);
            display: flex;
            justify-content: center;
        }
        .alsolent-workflow-body {
            overflow: auto;
        }
        .alsolent-drivers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 12px;
        }
        .alsolent-driver-card {
            border: 1px solid rgba(17, 21, 30, 0.12);
            border-radius: 12px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            background: #f9fafb;
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        }
        .alsolent-driver-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(17, 21, 30, 0.12);
            border-color: rgba(17, 21, 30, 0.28);
        }
        .alsolent-driver-card.selected {
            border-color: var(--main-orange, #d48b2c);
            box-shadow: 0 10px 20px rgba(212, 139, 44, 0.25);
        }
        .alsolent-driver-image-container {
            width: 100%;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
        }
        .alsolent-driver-image {
            max-width: 110px;
            max-height: 110px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
        .alsolent-driver-name {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }
        .alsolent-button {
            border: none;
            border-radius: 8px;
            padding: 10px 18px;
            color: #ffffff;
            font-weight: 700;
            letter-spacing: 0.02em;
            cursor: pointer;
            background: var(--main-orange, #d48b2c);
        }
        .alsolent-button:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }
        .macaw-tabs.macaw-aurora-tabs > [role="tablist"] {
            position: relative;

            display: flex;
            width: 20%;
            flex-direction: column;
        }
        .macaw-tabs.macaw-aurora-tabs > [role="tablist"] > * {
            border: none;
            border-bottom: 1px solid var(--tab-border-color);
        }
        .macaw-tabs.macaw-aurora-tabs > [role="tablist"] > *:last-child {
            border-bottom: none;
        }
        .macaw-tabs.macaw-aurora-tabs > [role="tablist"] > [role="tab"] {
            position: relative;
            margin: 0;
            overflow: visible;
            word-wrap: break-word;
            font-family: var(--global-heading-font-family);

            font-weight: normal;
            flex-wrap: wrap;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            background-color: #034c4d !important;
            color: var(--tab-text-color);
            fill:  var(--tab-text-color);
            padding: 0.8rem 0.5rem;
        }
        .macaw-tabs.macaw-aurora-tabs > [role="tablist"] > [role="tab"] .label {
            /*display: none;*/
        }
        .macaw-tabs.macaw-aurora-tabs > [role="tablist"] > [role="tab"] > * {
            padding: 0;
        }
        .macaw-tabs.macaw-aurora-tabs > [role="tablist"] > [role="tab"]:hover,
        .macaw-tabs.macaw-aurora-tabs > [role="tablist"] > [role="tab"]:focus {
            outline: 0;
            background-color: var(--tab-focus-bg-color);
            color: var(--tab-focus-text-color);
        }
        .macaw-tabs.macaw-aurora-tabs
        > [role="tablist"]
        > [role="tab"][aria-selected="true"] {
            background-color: var(--tab-active-bg-color);
            color: var(--tab-active-text-color);
            fill: var(--tab-active-text-color);
            border-left: 5px solid var(--tab-active-border-color);
            padding-left:0.2rem;
        }
        .macaw-tabs.macaw-aurora-tabs
        > [role="tablist"]
        > [role="tab"][aria-selected="true"] > svg {
            fill: var(--tab-active-text-color);
        }
        .macaw-tabs.macaw-aurora-tabs
        > [role="tablist"]
        > [role="tab"][aria-selected="true"]
        .icon {
            color: var(--tab-focus-text-secondary-color);
        }
        .macaw-tabs.macaw-aurora-tabs
        > [role="tablist"]
        > [role="tab"][aria-selected="true"]:hover,
        .macaw-tabs.macaw-aurora-tabs
        > [role="tablist"]
        > [role="tab"][aria-selected="true"]:focus {
            outline: 0;
            background-color: var(--tab-active-bg-color);
            color: var(--tab-focus-text-secondary-color);
        }
        .macaw-tabs.macaw-aurora-tabs > [role="tablist"] > [role="tab"]:hover,
        .macaw-tabs.macaw-aurora-tabs > [role="tablist"] > [role="tab"]:focus,
        .macaw-tabs.macaw-aurora-tabs > [role="tablist"] > [role="tab"]:active {
            outline: 0;
        }
        .macaw-tabs.macaw-aurora-tabs > [role="tabpanel"] {
            overflow: hidden;
            position: relative;

            font-family: var(--global-body-font-family);
            font-weight: normal;

            width: 80%;
            padding: 1.25rem 0.9375rem;
            background-color:white;
            color: var(--tab-active-text-color);

        }
        .macaw-tabs.macaw-aurora-tabs > [role="tabpanel"] a,
        .macaw-tabs.macaw-aurora-tabs > [role="tabpanel"] a:visited {
            cursor: pointer;
            color: inherit;
            -webkit-text-decoration-style: dotted;
            text-decoration-style: dotted;
            text-underline-offset: 0.1875rem;
        }
        .macaw-tabs.macaw-aurora-tabs > [role="tabpanel"] a:hover {
            text-decoration: none;
        }
        .macaw-tabs.macaw-aurora-tabs > [role="tabpanel"] > * {
            margin-top: 24px;
            margin-bottom: 24px;
        }
        .macaw-tabs.macaw-aurora-tabs > [role="tabpanel"] > *:first-child {
            margin-top: 0;
        }
        .macaw-tabs.macaw-aurora-tabs > [role="tabpanel"] > *:last-child {
            margin-bottom: 0;
        }
        .macaw-tabs.macaw-aurora-tabs > [role="tabpanel"].active > * {
            /*opacity: 1;*/
            /*animation: zoomIn;*/
            /*animation-duration: 1.5s;*/
        }
        .macaw-tabs.macaw-aurora-tabs > [role="tabpanel"]:focus {
            outline: 0;
            border-left: 6px solid var(--tab-focus-border-color);
        }
        .m-1 {
            margin: 0.1rem 0rem !important;
        }
        /*
        --------
        * Media Queries
        --------
        */
        @media only screen and (max-width: 500px) {
            .stageName{display:none;
            }
            .dotsDiv{display:none}
            .iconSpan > i{
                font-size: 25px;
            }
            .iconSpan > svg{
                width: 1.4rem;
            }
            .millingIcon{
                width:1.4rem;
            }

            .iconSpan{
                margin-bottom:5px;
                justify-content: space-around;
            }
            .sunriseTable thead th {
                font-size: 13px;
            }
            .macaw-tabs.macaw-silk-tabs > [role="tablist"] > [role="tab"] {    padding: 0.625rem 0.7rem !important;}
            table.dataTable thead .sorting {
                background-image: none;
            }
            [role="tabpanel"]{
                overflow-x:scroll;
            }
        }
        @media only screen and (min-width: 768px) {
            .macaw-tabs.macaw-aurora-tabs > [role="tablist"] {
                width: 20%;
            }
            .macaw-tabs.macaw-aurora-tabs > [role="tablist"] > [role="tab"] {

            }

            .macaw-tabs.macaw-aurora-tabs > [role="tablist"] > [role="tab"] .label {
                /*display: flex;*/
            }
            .macaw-tabs.macaw-aurora-tabs > [role="tablist"] > [role="tab"] > * {

            }
            .macaw-tabs.macaw-aurora-tabs
            > [role="tablist"]
            > [role="tab"]
            > *:last-child {
                padding-right: 0;
            }
            .macaw-tabs.macaw-aurora-tabs > [role="tabpanel"] {

                width: 80%;

            }
        }

        @media only screen and (min-width: 1280px) {
            .macaw-tabs.macaw-aurora-tabs {

            }
        }

        .macaw-tabs.macaw-silk-tabs {
            /*
             --------
             * Color Palette
             --------
             */
            --tab-color-white: #fff;
            --tab-color-black: #000;
            --tab-color-metallic: #358491;
            --tab-color-platihum: #e6eced;
            --tab-color-cultured: #f2f9fa;
            --tab-color-isabelline: #f4f4f4;
            /*
             --------
             * CSS Vars
             --------
             */
            --tab-bg-color: var(--tab-color-white);
            --tab-text-color: var(--tab-color-black);
            --tab-border-color: var(--tab-color-platihum);
            --tab-active-bg-color: var(--tab-color-cultured);
            --tab-active-text-color: var(--tab-color-black);
            --tab-focus-bg-color: var(--tab-color-isabelline);
            --tab-focus-text-color: var(--tab-color-black);
            --tab-focus-border-color: var(--tab-color-isabelline);
            --tab-icon-color: var(--tab-color-metallic);
            /*
             --------
             * Style
             --------
             */
            margin-left: auto;
            margin-right: auto;
            width: 100%;

        }
        .macaw-tabs.macaw-silk-tabs > [role="tablist"] {
            position: relative;

            display: flex;
        }
        .macaw-tabs.macaw-silk-tabs > [role="tablist"] > *:not(.innerActiveBtn):not(.innerWaitingBtn) {
            border: none;
            border-right: 1px solid var(--tab-border-color);
        }
        .macaw-tabs.macaw-silk-tabs > [role="tablist"] > *:last-child {

        }
        .macaw-tabs.macaw-silk-tabs > [role="tablist"] > [role="tab"] {
            position: relative;
            margin: 0;
            overflow: visible;
            word-wrap: break-word;
            font-family: var(--global-heading-font-family);
            font-weight: normal;

            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background-color: var(--tab-bg-color);
            color: var(--tab-text-color);
            padding: 0.625rem 1.625rem;

        }
        .macaw-tabs.macaw-silk-tabs > [role="tablist"] > [role="tab"] .icon {
            color: var(--tab-icon-color);
        }
        .macaw-tabs.macaw-silk-tabs > [role="tablist"] > [role="tab"] > * {


        }
        .macaw-tabs.macaw-silk-tabs > [role="tablist"] > [role="tab"] > *:last-child {
            padding-right: 0;
        }
        .macaw-tabs.macaw-silk-tabs > [role="tablist"] > [role="tab"]:hover,
        .macaw-tabs.macaw-silk-tabs > [role="tablist"] > [role="tab"]:focus {
            outline: 0;
            background-color: var(--tab-focus-bg-color);
            color: var(--tab-focus-text-color);
        }
        .macaw-tabs.macaw-silk-tabs
        > [role="tablist"]
        > [role="tab"][aria-selected="true"] {
            background-color: var(--tab-active-bg-color);
            color: var(--tab-active-text-color);
        }
        .macaw-tabs.macaw-silk-tabs
        > [role="tablist"]
        > [role="tab"][aria-selected="true"]:hover,
        .macaw-tabs.macaw-silk-tabs
        > [role="tablist"]
        > [role="tab"][aria-selected="true"]:focus {
            outline: 0;
            background-color: var(--tab-focus-bg-color);
            color: var(--tab-focus-text-color);
        }
        .macaw-tabs.macaw-silk-tabs > [role="tablist"] > [role="tab"]:hover,
        .macaw-tabs.macaw-silk-tabs > [role="tablist"] > [role="tab"]:focus,
        .macaw-tabs.macaw-silk-tabs > [role="tablist"] > [role="tab"]:active {
            outline: 0;
        }
        .macaw-tabs.macaw-silk-tabs > [role="tabpanel"] {
            position: relative;

            font-family: var(--global-body-font-family);
            font-weight: normal;

            background-color: transparent;
            color: var(--tab-active-text-color);
            border-bottom: 6px solid transparent;
        }
        .macaw-tabs.macaw-silk-tabs > [role="tabpanel"] a,
        .macaw-tabs.macaw-silk-tabs > [role="tabpanel"] a:visited {
            cursor: pointer;
            color: inherit;
            -webkit-text-decoration-style: dotted;
            text-decoration-style: dotted;
            text-underline-offset: 0.1875rem;
        }
        .macaw-tabs.macaw-silk-tabs > [role="tabpanel"] a:hover {
            text-decoration: none;
        }
        .macaw-tabs.macaw-silk-tabs > [role="tabpanel"] > * {


            opacity: 0;

            transition: opacity 0.2s, transform 0.2s;
        }
        .macaw-tabs.macaw-silk-tabs > [role="tabpanel"] > *:first-child {
            margin-top: 0;
        }
        .macaw-tabs.macaw-silk-tabs > [role="tabpanel"] > *:last-child {
            margin-bottom: 0;
        }

        .macaw-tabs.macaw-silk-tabs > [role="tabpanel"].active > * {
            opacity: 1;

        }
        .macaw-tabs.macaw-silk-tabs > [role="tabpanel"]:focus {
            outline: 0;
            border-bottom: 6px solid var(--tab-focus-border-color);
        }

        /*
        --------
        * Media Queries
        --------
        */

        @media only screen and (min-width: 768px) {
            .macaw-tabs.macaw-silk-tabs > [role="tablist"] > [role="tab"] {

            }
            .macaw-tabs.macaw-silk-tabs [role="tabpanel"] {

            }
        }

        @media only screen and (min-width: 1280px) {
            .macaw-tabs.macaw-silk-tabs {

            }
        }

        @media only screen and (max-width: 575px) {
            .macaw-tabs.macaw-silk-tabs.vertical {
                display: flex;
                width: 100%;
            }
            .macaw-tabs.macaw-silk-tabs.vertical > [role="tablist"] {
                width: 15%;
                flex-direction: column;
            }
            .macaw-tabs.macaw-silk-tabs.vertical > [role="tablist"] > * {
                border: none;
                border-bottom: 1px solid var(--tab-border-color);
            }
            .macaw-tabs.macaw-silk-tabs.vertical > [role="tablist"] > *:last-child {
                border-bottom: none;
            }
            .macaw-tabs.macaw-silk-tabs.vertical > [role="tablist"] > [role="tab"] .icon {

            }
            .macaw-tabs.macaw-silk-tabs.vertical
            > [role="tablist"]
            > [role="tab"]
            .label {
                display: none;
            }
            .macaw-tabs.macaw-silk-tabs.vertical > [role="tablist"] > [role="tab"] > * {
                padding: 0;
            }
            .macaw-tabs.macaw-silk-tabs.vertical [role="tabpanel"] {
                width: 85%;
                padding: 0 0.9375rem;
            }
        }


        .row{
            background-color: inherit;

        }

        /*Modal titles */
        #doctor {
        }

        #pat {
        }

        .innerSpan4Mobile {
            display: none;
        }

        .waitingtabText {
            color: red;
        }

        .dropdown-menu {
            left: -100px !important;
            transform: translate3d(0px, 28px, 0px) !important;
        }

        .dropdown-menu:before {
            left: 112px;
        }

        .badge {
            color: white;
        }

        * {
            box-sizing: border-box
        }

        .nav-tabs > li {
            width: 25%;
            text-align: center;
        }

        /* Style the tab */
        .tab {
            float: left;
            border: 1px solid #ccc;
            background-color: #f1f1f1;
            width: 20%;
            height: fit-content;
            margin-top: 51px;
        }

        /* Style the buttons that are used to open the tab content */
        .tab button {
            display: flex;
            background-color: inherit;
            color: black;
            padding: 13px 8px;
            width: 100%;
            border: none;
            outline: none;
            text-align: left;
            cursor: pointer;
            transition: 0.3s;
        }

        /* Change background color of buttons on hover */
        .tab button:hover {
            background-color: #ddd;
        }

        /* Create an active/current "tab button" class */
        .tab button.active {
            background-color: #ccc;
        }

        /* Style the tab content */
        .tabcontent {
            float: left;
            padding: 0px 2px;
            /* border: 1px solid #ccc;*/
            width: 80%;
            border-left: none;

            height: -webkit-fill-available;
            max-height: 80vh;
            overflow: auto;
        }

        .waitingBadge {
            background-color: indianred !important;
        }

        .activeBadge {
            background-color: steelblue !important;
        }

        .main-panel > .content {
            /* padding: 78px 30px 30px 265px;*/
        }

        .white-content .table > thead > tr > th, .white-content .table > tbody > tr > th, .white-content .table > tfoot > tr > th, .white-content .table > thead > tr > td, .white-content .table > tbody > tr > td, .white-content .table > tfoot > tr > td {

            border-top-color: transparent;
            border-right-color: rgba(34, 42, 66, 0.2);
            border-bottom-color: transparent;
            border-left-color: rgba(34, 42, 66, 0.2);
        }

        .card {
            padding: 5px;
        }

        .tabbable {
            margin-top: 5px;
        }

        .modal {

            height: auto;
        }

        table.dataTable.compact tbody th, table.dataTable.compact tbody td {
            padding: 1px 0px 0px 5px;
        }

        @media screen and (max-width: 768px) {
            .macaw-tabs.macaw-aurora-tabs > [role="tablist"] > [role="tab"]{
                display:block;
            }
            .main-panel .content {
                padding-left: 4px;
                padding-right: 6px;
            }

            .activeTable tr > *:nth-child(3),
            .activeTable tr > *:nth-child(5),
            .activeTable tr > *:nth-child(6) {
                display: none;
            }

            .waitingTable tr > *:nth-child(4),
            .waitingTable tr > *:nth-child(5) {
                display: none;
            }

            .innerSpan4DeskTop {
                display: none;
            }

            .innerSpan4Mobile {
                display: flex;
            }

            .tab {
                margin-top: 10px;
            }

            .col-3 {
                padding-right: 5px;
                padding-left: 5px;
            }

            .btnsRow {
                padding: 0;
            }
        }
        .notransition {
            -webkit-transition: none !important;
            -moz-transition: none !important;
            -o-transition: none !important;
            transition: none !important;
        }
        /* Active and waiting labels: */
        .phaselabel{
            padding-left:4px !important;
        }
        .stageSidebar{
            border-top: 1px solid #2b7b7d;

            margin-top: 67px;
        }
        waitingtabText.active {

        }
        .innerActiveBtn , .innerWaitingBtn{
            border: 0.0625rem solid transparent;
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
        }
        .innerActiveBtn[aria-selected="true"] {
            background-color: #eef3f8 !important;
            border-color: #4682b4 #4682b4 #4682b4 !important;
        }
        .innerWaitingBtn[aria-selected="true"]{
            background-color: #e8e8e8;
            border-color: #cd5c5c #cd5c5c #cd5c5c;
        }
        svg{
            width: 1rem;
        }
        .millingIcon{
            width:0.8rem;
        }
        .printingIcon{
            width:1.3rem;
        }
        .driverNameBtn{
            margin:4px;
            width: 150px;
            background-color: #ffc107;
            border-color: #ffc107;
        }
        .driverNameBtn:hover,.driverNameBtn:active {
            background-color: #dea700 !important;
            border-color: #dea700 !important;
        }
        .driversContainer{

            display: flex;
            flex-direction: column;
            align-items: center;
            line-height: 0;
        }

/* User requested style for active vertical tab */
.macaw-aurora-tabs > [role="tablist"] > [role="tab"][aria-selected="true"] {
    background-color: #2b7b7d !important; /* Active vertical tab background */
    color: #ffffff !important; /* White text for active vertical tab */
}

/* Adjust badge color on active tab for readability */
.macaw-aurora-tabs > [role="tablist"] > [role="tab"][aria-selected="true"] .badge {
    color: #2b7b7d !important;
    background-color: #ffffff !important;
}

/* Ensure icons retain their original color on active vertical tab */
.macaw-aurora-tabs > [role="tablist"] > [role="tab"][aria-selected="true"] .iconSpan i,
.macaw-aurora-tabs > [role="tablist"] > [role="tab"][aria-selected="true"] .iconSpan svg {
    color: #495057 !important; /* Or a specific icon color */
    fill: #495057 !important; /* For SVG icons */
}

        .ops-dashboard .macaw-tabs.macaw-aurora-tabs {
            display: block !important;
        }

        .ops-dashboard .macaw-tabs.macaw-aurora-tabs > [role="tablist"].stageSidebar {
            width: 100% !important;
            flex-direction: row !important;
            align-items: stretch !important;
            border-top: 0;
        }

        .ops-dashboard .macaw-tabs.macaw-aurora-tabs > [role="tabpanel"] {
            width: 100% !important;
            padding: 1rem 0 !important;
        }

        .ops-dashboard .macaw-tabs.macaw-aurora-tabs > [role="tablist"].stageSidebar {
            display: flex;
            gap: 12px;
            align-items: stretch;
            overflow-x: auto;
            padding: 12px;
            margin-top: 18px;
            margin-bottom: 18px;
            background: rgba(255, 255, 255, 0.58);
            border: 1px solid rgba(134, 177, 196, 0.42);
            border-radius: 22px;
            box-shadow: 0 18px 40px rgba(45, 76, 108, 0.12);
            backdrop-filter: blur(14px);
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }

        .ops-dashboard .macaw-tabs.macaw-aurora-tabs > [role="tablist"].stageSidebar::-webkit-scrollbar {
            display: none;
        }

        .ops-dashboard .macaw-tabs.macaw-aurora-tabs > [role="tablist"].stageSidebar > [role="tab"] {
            display: flex;
            flex: 0 0 74px !important;
            width: 74px !important;
            max-width: 74px !important;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            gap: 7px;
            min-height: 96px;
            padding: 10px 6px !important;
            margin: 0;
            border-radius: 16px !important;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(237, 246, 250, 0.96));
            border: 1px solid #cae0ea;
            color: #32425a;
            box-shadow: 0 10px 22px rgba(75, 110, 145, 0.12);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease;
        }

        .ops-dashboard .macaw-tabs.macaw-aurora-tabs > [role="tablist"].stageSidebar > [role="tab"]:hover,
        .ops-dashboard .macaw-tabs.macaw-aurora-tabs > [role="tablist"].stageSidebar > [role="tab"][aria-selected="true"] {
            transform: translateY(-2px);
            border-color: #9ac9de !important;
            background: linear-gradient(180deg, rgba(255, 255, 255, 1), rgba(226, 242, 248, 1)) !important;
            color: #32425a !important;
            box-shadow: 0 16px 28px rgba(75, 110, 145, 0.18) !important;
            border-left: 1px solid #9ac9de !important;
            padding-left: 6px !important;
        }

        .stageSidebar__icon {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 24px;
            color: #3a4658;
        }

        .stageSidebar__icon i,
        .stageSidebar__icon svg {
            font-size: 18px;
        }

        .stageSidebar__name {
            font-size: 12px;
            font-weight: 700;
            line-height: 1.1;
            text-align: center;
            color: #34445b;
        }

        .stageSidebar__counts {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .stageSidebar .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 22px;
            padding: 0 5px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid transparent;
            box-shadow: none;
        }

        .stageSidebar .activeBadge {
            background: #eaf5ff !important;
            color: #2f7fd9;
            border-color: #bddcff;
        }

        .stageSidebar .waitingBadge {
            background: #fff0f0 !important;
            color: #eb5757;
            border-color: #ffc7c7;
        }

        .ops-dashboard .macaw-tabs.macaw-aurora-tabs > [role="tablist"].stageSidebar > [role="tab"][aria-selected="true"] .activeBadge {
            background: #eaf5ff !important;
            color: #2f7fd9 !important;
            border-color: #bddcff !important;
        }

        .ops-dashboard .macaw-tabs.macaw-aurora-tabs > [role="tablist"].stageSidebar > [role="tab"][aria-selected="true"] .waitingBadge {
            background: #fff0f0 !important;
            color: #eb5757 !important;
            border-color: #ffc7c7 !important;
        }

        .YSH-slide-overlay {
            display: none !important;
            position: fixed;
            inset: 0;
            z-index: 99991;
            background: rgba(8, 13, 20, 0.35);
            padding: 16px;
            overflow-y: auto;
        }

        .YSH-slide-overlay.YSH-active,
        .YSH-slide-overlay.YSH-closing {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        .YSH-slide-panel {
            position: relative;
            top: auto;
            right: auto;
            width: min(680px, calc(100vw - 32px));
            max-width: min(680px, calc(100vw - 32px));
            min-width: 0;
            height: auto;
            max-height: calc(100vh - 32px);
            margin: auto;
            overflow: hidden;
            background: #ffffff;
            color: #11151E;
            border-radius: 24px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.22);
            padding: 0;
            animation: none !important;
        }

        .YSH-slide-grid {
            display: block;
        }

        .YSH-slide-header {
            padding: 22px 24px 18px;
            border-bottom: 1px solid #dfe7ef;
            background: #ffffff;
        }

        .YSH-slide-body {
            padding: 18px 24px 22px;
            height: auto;
            max-height: calc(100vh - 240px);
            overflow-y: auto;
            background: #ffffff;
            color: #11151E;
        }

        .YSH-close-slide {
            color: #6b7280;
        }

        .YSH-close-slide:hover {
            color: #111827;
        }

        .YSH-slide-panel.YSH-operations-case-sheet {
            --case-sheet-text: #111827;
            --case-sheet-muted: #64748b;
            --case-sheet-border: #d8dce3;
            --case-sheet-subtle: #f8fafc;
            --case-sheet-accent: #6366f1;
            --case-sheet-accent-soft: #eef2ff;
            --case-sheet-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);

            display: grid;
            grid-template-rows: auto minmax(0, 1fr) auto;
            width: min(760px, calc(100vw - 32px));
            max-width: min(760px, calc(100vw - 32px));
            height: min(760px, calc(100dvh - 32px));
            max-height: calc(100dvh - 32px);
            margin: auto;
            padding: 0;
            overflow: hidden;
            color: var(--case-sheet-text);
            background: #ffffff;
            border: 1px solid var(--case-sheet-border);
            border-radius: 16px;
            box-shadow: var(--case-sheet-shadow);
        }

        .YSH-operations-case-sheet__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 22px 24px 18px;
            color: #ffffff;
            background: #151c27;
            border-bottom: 3px solid var(--case-sheet-accent);
        }

        .YSH-operations-case-sheet__header h2,
        .YSH-operations-case-sheet__section-heading h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0;
        }

        .YSH-operations-case-sheet__eyebrow,
        .YSH-operations-case-sheet__label {
            display: block;
            color: var(--case-sheet-muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0;
            line-height: 1.3;
            text-transform: uppercase;
        }

        .YSH-operations-case-sheet__header .YSH-operations-case-sheet__eyebrow {
            margin-bottom: 5px;
            color: #c7d2fe;
        }

        .YSH-operations-case-sheet__close {
            display: inline-grid;
            width: 40px;
            min-width: 40px;
            height: 40px;
            place-items: center;
            padding: 0;
            color: #ffffff;
            font-size: 28px;
            line-height: 1;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 8px;
            cursor: pointer;
        }

        .YSH-operations-case-sheet__close:hover,
        .YSH-operations-case-sheet__close:focus-visible {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.7);
            outline: none;
        }

        .YSH-operations-case-sheet__content {
            min-height: 0;
            padding: 22px 24px 24px;
            overflow-y: auto;
            overscroll-behavior: contain;
            background: #ffffff;
        }

        .YSH-operations-case-sheet__summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1px;
            margin-bottom: 24px;
            overflow: hidden;
            background: var(--case-sheet-border);
            border: 1px solid var(--case-sheet-border);
            border-radius: 10px;
        }

        .YSH-operations-case-sheet__summary-item {
            min-width: 0;
            padding: 14px 16px;
            background: var(--case-sheet-subtle);
        }

        .YSH-operations-case-sheet__summary-item .YSH-operations-case-sheet__label {
            margin-bottom: 6px;
        }

        .YSH-operations-case-sheet__summary-item strong {
            display: block;
            overflow-wrap: anywhere;
            color: var(--case-sheet-text);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.45;
        }

        .YSH-operations-case-sheet__section + .YSH-operations-case-sheet__section {
            margin-top: 26px;
            padding-top: 24px;
            border-top: 1px solid var(--case-sheet-border);
        }

        .YSH-operations-case-sheet__section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 12px;
        }

        .YSH-operations-case-sheet__section-heading .YSH-operations-case-sheet__eyebrow {
            margin-bottom: 4px;
        }

        .YSH-operations-case-sheet__section-heading h3 {
            color: var(--case-sheet-text);
            font-size: 16px;
        }

        .YSH-operations-case-sheet__count {
            display: inline-grid;
            width: 28px;
            min-width: 28px;
            height: 28px;
            place-items: center;
            color: var(--case-sheet-accent);
            font-size: 12px;
            font-weight: 700;
            background: var(--case-sheet-accent-soft);
            border-radius: 999px;
        }

        .YSH-operations-case-sheet__jobs,
        .YSH-operations-case-sheet__notes {
            display: grid;
            gap: 10px;
        }

        .YSH-operations-case-sheet__job,
        .YSH-operations-case-sheet__note {
            padding: 14px 16px;
            background: #ffffff;
            border: 1px solid var(--case-sheet-border);
            border-radius: 10px;
        }

        .YSH-operations-case-sheet__job-topline,
        .YSH-operations-case-sheet__note-meta {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 12px;
        }

        .YSH-operations-case-sheet__job-topline strong {
            min-width: 0;
            overflow-wrap: anywhere;
            color: var(--case-sheet-text);
            font-size: 14px;
            font-weight: 700;
        }

        .YSH-operations-case-sheet__job-topline > span {
            flex: 0 1 auto;
            color: var(--case-sheet-muted);
            font-size: 12px;
            font-weight: 600;
            text-align: end;
        }

        .YSH-operations-case-sheet__job-details {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 8px;
            margin-top: 9px;
        }

        .YSH-operations-case-sheet__job-details span {
            max-width: 100%;
            overflow-wrap: anywhere;
            color: var(--case-sheet-muted);
            font-size: 12px;
            line-height: 1.4;
        }

        .YSH-operations-case-sheet__job-details span + span::before {
            margin-inline-end: 8px;
            color: #cbd5e1;
            content: '\2022';
        }

        .YSH-operations-case-sheet__note-meta {
            color: var(--case-sheet-muted);
            font-size: 12px;
            font-weight: 600;
        }

        .YSH-operations-case-sheet__note-meta time {
            text-align: end;
        }

        .YSH-operations-case-sheet__note p,
        .YSH-operations-case-sheet__empty {
            margin: 8px 0 0;
            overflow-wrap: anywhere;
            color: var(--case-sheet-text);
            font-size: 13px;
            line-height: 1.6;
        }

        .YSH-operations-case-sheet__empty {
            margin: 0;
            padding: 16px;
            color: var(--case-sheet-muted);
            background: var(--case-sheet-subtle);
            border: 1px dashed var(--case-sheet-border);
            border-radius: 10px;
        }

        .YSH-operations-case-sheet__actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            padding: 16px 24px;
            background: #ffffff;
            border-top: 1px solid var(--case-sheet-border);
        }

        .YSH-operations-case-sheet__action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 10px 14px;
            color: var(--case-sheet-text);
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
            text-align: center;
            text-decoration: none;
            background: #ffffff;
            border: 1px solid var(--case-sheet-border);
            border-radius: 8px;
            cursor: pointer;
        }

        .YSH-operations-case-sheet__action:hover,
        .YSH-operations-case-sheet__action:focus-visible {
            color: var(--case-sheet-text);
            text-decoration: none;
            border-color: #a5b4fc;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.14);
            outline: none;
        }

        .YSH-operations-case-sheet__action--primary {
            color: #ffffff;
            background: var(--case-sheet-accent);
            border-color: var(--case-sheet-accent);
        }

        .YSH-operations-case-sheet__action--primary:hover,
        .YSH-operations-case-sheet__action--primary:focus-visible {
            color: #ffffff;
            background: #4f46e5;
            border-color: #4f46e5;
        }

        .YSH-operations-case-sheet__action--quiet {
            color: var(--case-sheet-muted);
            background: var(--case-sheet-subtle);
        }

        @media (max-width: 575.98px) {
            .YSH-slide-panel.YSH-operations-case-sheet {
                width: calc(100vw - 24px);
                max-width: calc(100vw - 24px);
                height: calc(100dvh - 24px);
                max-height: calc(100dvh - 24px);
                border-radius: 14px;
            }

            .YSH-operations-case-sheet__header,
            .YSH-operations-case-sheet__content,
            .YSH-operations-case-sheet__actions {
                padding-inline: 18px;
            }

            .YSH-operations-case-sheet__summary {
                grid-template-columns: 1fr;
            }

            .YSH-operations-case-sheet__job-topline,
            .YSH-operations-case-sheet__note-meta {
                align-items: flex-start;
                flex-direction: column;
                gap: 4px;
            }

            .YSH-operations-case-sheet__job-topline > span,
            .YSH-operations-case-sheet__note-meta time {
                text-align: start;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .YSH-operations-case-sheet__action,
            .YSH-operations-case-sheet__close {
                transition: none;
            }
        }

        .modal {
            padding: 16px !important;
        }

        .modal-dialog {
            width: 100%;
            max-width: min(680px, calc(100vw - 32px));
            margin: auto;
        }

        .modal-content {
            border-radius: 24px;
            overflow: hidden;
        }

        .modal-header,
        .modal-body,
        .modal-footer {
            padding-left: 24px;
            padding-right: 24px;
        }

        .modal-header {
            padding-top: 22px;
            padding-bottom: 18px;
        }

        .modal-body {
            padding-top: 18px;
            padding-bottom: 22px;
        }

        .modal-footer {
            padding-top: 12px;
            padding-bottom: 18px;
        }

        @media (max-width: 575.98px) {
            .ops-dashboard .macaw-tabs.macaw-aurora-tabs > [role="tablist"].stageSidebar > [role="tab"] {
                flex-basis: 68px !important;
                width: 68px !important;
                max-width: 68px !important;
                min-height: 90px;
                padding: 9px 5px !important;
            }

            .stageSidebar__name {
                font-size: 11px;
            }

            .stageSidebar .badge {
                min-width: 20px;
                height: 20px;
                font-size: 10px;
            }

            .YSH-slide-overlay {
                padding: 12px;
            }

            .YSH-slide-panel {
                width: calc(100vw - 24px);
                max-width: calc(100vw - 24px);
                max-height: calc(100vh - 24px);
            }

            .YSH-slide-header,
            .YSH-slide-body,
            .YSH-slide-panel .modal-footer {
                padding-left: 18px;
                padding-right: 18px;
            }

            .modal {
                padding: 12px !important;
            }

            .modal-dialog {
                max-width: calc(100vw - 24px);
            }

            .modal-header,
            .modal-body,
            .modal-footer {
                padding-left: 18px;
                padding-right: 18px;
            }
        }

        .ops-dashboard .macaw-tabs.macaw-aurora-tabs > [role="tabpanel"],
        .ops-dashboard .macaw-tabs.macaw-silk-tabs > [role="tabpanel"],
        .ops-dashboard .dataTables_wrapper {
            background: var(--surface) !important;
            border: 1px solid var(--border) !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 28px var(--shadow-2) !important;
        }

        .ops-dashboard .dataTables_wrapper {
            padding: 10px !important;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .ops-dashboard .YSH-operations-table-tools {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 10px 16px;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .ops-dashboard .YSH-operations-table-tools .dt-buttons,
        .ops-dashboard .YSH-operations-table-tools .dataTables_filter,
        .ops-dashboard .YSH-operations-table-tools .dataTables_length {
            float: none;
            margin: 0;
        }

        .ops-dashboard .dataTables_wrapper .dataTables_filter {
            float: none;
            margin: 0;
            text-align: start;
        }

        .ops-dashboard .dataTables_wrapper .dataTables_filter label {
            display: block;
            margin: 0;
        }

        .ops-dashboard .dataTables_wrapper .dataTables_filter input {
            width: min(280px, 100%);
            height: 40px;
            margin: 0;
            padding: 8px 12px;
            color: var(--text-1);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
        }

        .ops-dashboard .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-bg);
            outline: none;
        }

        .ops-dashboard .YSH-operations-table-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-top: 12px;
        }

        .ops-dashboard .YSH-operations-table-footer .dataTables_info,
        .ops-dashboard .YSH-operations-table-footer .dataTables_paginate {
            float: none;
            margin: 0;
            padding: 0;
        }

        @media (max-width: 575.98px) {
            .ops-dashboard .YSH-operations-table-tools {
                align-items: stretch;
                flex-direction: column;
            }

            .ops-dashboard .YSH-operations-table-tools .dt-buttons,
            .ops-dashboard .YSH-operations-table-tools .dataTables_length,
            .ops-dashboard .dataTables_wrapper .dataTables_filter,
            .ops-dashboard .dataTables_wrapper .dataTables_filter label,
            .ops-dashboard .dataTables_wrapper .dataTables_filter input {
                width: 100%;
            }

            .ops-dashboard .YSH-operations-table-footer {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        .ops-dashboard table.sunriseTable,
        .ops-dashboard table.dataTable.sunriseTable {
            min-width: 640px;
            background: var(--surface) !important;
            border: 1px solid var(--border) !important;
            border-radius: 12px !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            overflow: hidden;
        }

        .ops-dashboard .sunriseTable thead,
        .ops-dashboard .sunriseTable thead tr,
        .ops-dashboard .sunriseTable thead th,
        .ops-dashboard .sunriseTable thead td {
            background: var(--text-1) !important;
            color: var(--surface) !important;
            border-color: var(--text-1) !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            letter-spacing: 0.02em;
            padding: 12px 14px !important;
            text-transform: uppercase;
        }

        .ops-dashboard .sunriseTable tbody tr {
            background: var(--surface) !important;
        }

        .ops-dashboard .sunriseTable tbody tr:nth-child(even) {
            background: var(--surface-raised) !important;
        }

        .ops-dashboard .sunriseTable tbody tr:hover {
            background: var(--accent-bg) !important;
        }

        .ops-dashboard .sunriseTable tbody td {
            border-color: var(--border) !important;
            color: var(--text-1) !important;
            font-size: 13px !important;
            padding: 12px 14px !important;
            vertical-align: middle !important;
        }

        .ops-dashboard .stageSidebar .badge,
        .ops-dashboard .activeBadge,
        .ops-dashboard .waitingBadge,
        .ops-dashboard .macaw-tabs.macaw-aurora-tabs > [role="tablist"].stageSidebar > [role="tab"][aria-selected="true"] .activeBadge,
        .ops-dashboard .macaw-tabs.macaw-aurora-tabs > [role="tablist"].stageSidebar > [role="tab"][aria-selected="true"] .waitingBadge {
            align-items: center !important;
            border-radius: 999px !important;
            color: var(--surface) !important;
            display: inline-flex !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            justify-content: center !important;
            min-height: 24px !important;
            min-width: 28px !important;
            padding: 6px 6px !important;
        }

        .ops-dashboard .activeBadge,
        .ops-dashboard .stageSidebar .activeBadge,
        .ops-dashboard .macaw-tabs.macaw-aurora-tabs > [role="tablist"].stageSidebar > [role="tab"][aria-selected="true"] .activeBadge {
            background: var(--accent) !important;
            border-color: var(--accent) !important;
        }

        .ops-dashboard .waitingBadge,
        .ops-dashboard .stageSidebar .waitingBadge,
        .ops-dashboard .macaw-tabs.macaw-aurora-tabs > [role="tablist"].stageSidebar > [role="tab"][aria-selected="true"] .waitingBadge {
            background: var(--warning) !important;
            border-color: var(--warning) !important;
        }

        .ops-dashboard .macaw-silk-tabs > [role="tablist"] > .innerBtn {
            align-items: center;
            direction: ltr;
            display: inline-flex;
            gap: 6px;
        }

        .ops-dashboard .macaw-silk-tabs > [role="tablist"] > .innerBtn .badge {
            margin: 0 !important;
        }

        .ops-dashboard .macaw-silk-tabs > [role="tablist"] > .innerBtn .phaselabel {
            direction: rtl;
            padding: 0 !important;
            unicode-bidi: isolate;
        }

    </style>

    @php $color = "#01292b"; @endphp

    @php
        $permissions = Cache::get('user'.Auth()->user()->id);
        $canEditCase = false;
        if(Auth()->user()->is_admin || ($permissions && ($permissions->contains('permission_id', 102))))
        $canEditCase = true;
        $canAssignEmployees = Auth()->user()->is_admin || ($permissions && $permissions->contains('permission_id', 129));

    @endphp
    @php
        $stages = array(
        'Design'=> array('activeCases' => $aDesign, "waitingCases" => $wDesign, "numericStage" => 1,'icon' => "<i class='fa-solid fa-desktop'></i>") ,
        '3DPrinting' => array('activeCases' => $aPrinting, "waitingCases" => $wPrinting, "numericStage" => 3, 'icon' => "
<svg version='1.1' class='printingIcon' id='Layer_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px'
	 viewBox='0 0 367.579 213.624' style='enable-background:new 0 0 367.579 213.624;' xml:space='preserve'>
<g id='XMLID_80_'>
	<path id='XMLID_81_' d='M54.962,85.176h21.863c12.45,0,20.9-2.581,25.355-7.743c4.453-5.162,6.681-10.678,6.681-16.549
		c0-6.579-2.456-12.424-7.364-17.537c-4.911-5.11-11.767-7.667-20.573-7.667c-16.803,0-27.382,8.858-31.732,26.57L6.225,55.417
		C9.767,39.426,18.345,26.19,31.96,15.714C45.573,5.238,62.35,0,82.292,0c20.851,0,38.387,4.965,52.609,14.891
		c14.22,9.926,21.332,23.5,21.332,40.719c0,22.589-11.843,38.038-35.528,46.344c27.834,7.385,41.753,23.771,41.753,49.159
		c0,18.208-7.112,33.177-21.332,44.911c-14.222,11.734-33.834,17.601-58.834,17.601c-23.989,0-42.994-6.033-57.012-18.099
		C11.259,183.46,2.833,168.488,0,150.615l44.031-6.377c4.251,21.358,16.599,32.036,37.046,32.036c9.513,0,17.232-2.522,23.154-7.57
		c5.921-5.046,8.882-11.809,8.882-20.288c0-8.984-2.709-15.698-8.123-20.139c-5.416-4.441-15.767-6.662-31.049-6.662H54.962V85.176z
		'/>
	<path id='XMLID_83_' d='M197.682,3.188h63.256c25.788,0,45.002,3.568,57.643,10.704c12.641,7.136,23.967,18.423,33.979,33.858
		c10.012,15.437,15.02,34.947,15.02,58.53c0,29.659-8.75,54.431-26.242,74.32c-17.496,19.89-41.615,29.834-72.359,29.834h-71.295
		V3.188z M245.356,41.297v130.118h19.999c17.677,0,30.808-6.604,39.392-19.814c8.586-13.209,12.881-28.719,12.881-46.536
		c0-12.55-2.451-24.165-7.35-34.845c-4.9-10.678-10.984-18.167-18.258-22.471c-7.273-4.301-16.009-6.453-26.21-6.453H245.356z'/>
</g>
</svg>
"),
        'Milling' => array('activeCases' => $aMilling, "waitingCases" => $wMilling, "numericStage" => 2,'icon' =>"<svg class='millingIcon' version='1.1' id='Layer_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px'
	 viewBox='0 0 219.296 416.891' style='enable-background:new 0 0 219.296 416.891;' xml:space='preserve'>
<path id='XMLID_96_'  d='M83.523,285.071
	c-8.936,0-17.387-0.009-25.838,0.002c-18.806,0.023-29.419-10.595-29.401-29.402c0.014-14.833-0.005-29.665,0.01-44.498
	c0.016-15.216,6.395-23.871,20.709-28.584c1.27-0.418,2.911-2.281,2.937-3.503c0.228-10.983,0.133-21.974,0.133-33.931
	c-6.659,0-13.105,0.414-19.467-0.14c-4.52-0.393-9.315-1.333-13.297-3.374c-6.953-3.564-10.608-9.93-11.158-17.792
	c-0.267-3.816-0.089-51.173-0.126-55.005c-0.039-4.011,1.898-6.506,5.923-6.479c4.169,0.028,5.652,2.911,5.665,6.735
	c0.008,2.333-0.048,48.178-0.011,50.511c0.152,9.463,4.396,13.745,13.798,13.749c50.664,0.021,101.328,0.018,151.992,0.001
	c9.887-0.003,14.027-4.111,14.03-13.927c0.015-47.664,0.002-54.688,0.006-102.352c0-1.496-0.504-3.464,0.245-4.389
	c1.598-1.976,3.891-4.731,5.849-4.692c1.897,0.038,3.856,3.071,5.52,5.018c0.499,0.584,0.113,1.935,0.113,2.934
	c-0.001,48.164,0.019,55.688-0.021,103.852c-0.013,16.046-9.434,25.329-25.55,25.365c-5.95,0.013-11.901,0.002-18.142,0.002
	c0,12.556,0,24.485,0,36.328c3.596,1.459,7.364,2.457,10.568,4.39c8.888,5.365,12.844,13.639,12.863,23.894
	c0.03,15.999,0.112,31.998,0.031,47.997c-0.081,16.03-11.432,27.205-27.559,27.28c-8.982,0.042-17.965,0.008-27.537,0.008
	c-0.12,2.405-0.316,4.492-0.315,6.579c0.007,28.831,0.009,57.661,0.106,86.491c0.014,4.045-1.001,7.294-4.025,10.249
	c-5.715,5.585-11.001,11.606-16.559,17.356c-4.076,4.216-6.825,4.203-10.946-0.073c-6.011-6.239-12.218-12.334-17.687-19.025
	c-1.956-2.393-2.785-6.337-2.807-9.581c-0.186-28.664-0.075-57.329-0.052-85.994C83.524,289.277,83.523,287.485,83.523,285.071z
	 M96.692,294.324c-0.469,0.283-0.937,0.567-1.406,0.85c0,3.786,0.243,7.591-0.068,11.351c-0.316,3.831,1.056,6.487,3.816,9.093
	c7.003,6.614,13.707,13.545,20.548,20.331c1.076,1.067,2.241,2.045,4.245,3.863c0-5.612,0.096-9.891-0.05-14.162
	c-0.054-1.586-0.161-3.641-1.12-4.651C114.113,312.002,105.374,303.19,96.692,294.324z M96.59,329.452
	c-0.428,0.236-0.857,0.472-1.285,0.708c0,1.957,0.271,3.96-0.048,5.864c-1.276,7.614,1.425,13.266,7.321,18.203
	c5.594,4.684,10.458,10.239,15.648,15.406c1.543,1.536,3.099,3.06,5.565,5.493c0-6.143,0.101-10.776-0.062-15.399
	c-0.049-1.384-0.441-3.121-1.342-4.054C113.871,346.854,105.21,338.173,96.59,329.452z M116.706,386.799
	c-7.345-7.452-14.241-14.449-20.966-21.272c-2.12,11.971,1.972,20.231,14.419,28.222
	C112.486,391.28,114.851,388.769,116.706,386.799z M106.012,285.297c5.899,5.949,11.817,11.916,17.377,17.523
	c0-5.319,0-11.342,0-17.523C117.143,285.297,111.367,285.297,106.012,285.297z'/>
<path id='XMLID_93_' d='M163.2,66.867c0.003-13.2-0.054-6.892,0.028-20.092c0.04-6.383,2.412-9.25,7.259-9.047
	c5.681,0.237,6.988,4.26,6.997,8.851c0.057,26.603-0.012,33.698-0.026,60.301c-0.003,4.996-2.164,8.607-7.374,8.444
	c-5.177-0.162-6.987-3.838-6.935-8.856C163.289,93.269,163.197,80.068,163.2,66.867z'/>
<path id='XMLID_83_' style='fill:#FFFFFF;' d='M96.692,294.324c8.682,8.866,17.422,17.678,25.965,26.676
	c0.959,1.01,1.066,3.065,1.12,4.651c0.146,4.271,0.05,14.833,0.05,20.445c-2.004-1.818-3.169-2.796-4.245-3.863
	c-6.841-6.787-13.545-13.717-20.548-20.331c-2.76-2.607-4.132-5.262-3.816-9.093c0.311-3.76,0.068-13.849,0.068-17.634
	C95.754,294.89,96.223,294.607,96.692,294.324z'/>
<path id='XMLID_82_' style='fill:#FFFFFF;' d='M116.706,375.126c-1.855,1.97-4.22,16.153-6.547,18.624
	c-12.447-7.991-16.539-27.924-14.419-39.895C102.465,360.678,109.361,367.675,116.706,375.126z'/>
<path id='XMLID_81_' style='fill:#FFFFFF;' d='M123.895,244.73c10.552,0,21.104-0.043,31.655,0.017
	c6.287,0.036,9.415,2.403,9.209,7.09c-0.26,5.919-4.436,7.163-9.32,7.172c-21.302,0.037-42.604,0.06-63.906,0.008
	c-4.776-0.012-8.647-1.797-8.584-7.259c0.062-5.416,3.757-7.081,8.685-7.049C102.387,244.777,113.141,244.729,123.895,244.73z'/>
<path id='XMLID_80_' style='fill:#FFFFFF;' d='M96.462,165.605c-5.129,0-10.259,0.075-15.387-0.024
	c-4.105-0.079-6.799-2.359-6.76-6.389c0.038-3.964,2.628-6.336,6.784-6.353c10.259-0.043,20.519-0.042,30.778,0.017
	c4.142,0.024,6.756,2.289,6.733,6.364c-0.022,4.051-2.594,6.308-6.766,6.342c-5.127,0.041-10.255,0.01-15.383,0.01
	C96.462,165.583,96.462,165.594,96.462,165.605z'/>
</svg>
"),
        'Sintering' => array('activeCases' => $aSintering, "waitingCases" => $wSintering, "numericStage" => 4,'icon' => "<i class='fa-solid fa-fire-flame-curved'></i>"),
        'Pressing'=> array('activeCases' => $aPressing, "waitingCases" => $wPressing, "numericStage" => 5,'icon' => "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 384 512'>
<defs><style>.fa-secondary{opacity:.4}</style>
</defs><path class='fa-primary' d='M350 206.6c3.781 8.803 1.984 19.03-4.594 26l-136 144.1c-9.062 9.601-25.84 9.601-34.91 0l-136-144.1C31.97 225.7 30.17 215.4 33.95 206.6C37.75 197.8 46.42 192.1 56 192.1L128 192.1V64.03c0-17.69 14.33-32.02 32-32.02h64c17.67 0 32 14.34 32 32.02v128.1l72 .0314C337.6 192.1 346.3 197.8 350 206.6z'/>
<path class='fa-secondary' d='M352 416H31.1C14.33 416 0 430.3 0 447.1S14.33 480 31.1 480H352C369.7 480 384 465.7 384 448S369.7 416 352 416z'/></svg>"),
        'MetalWork' => array('activeCases' => $aMetalWork, "waitingCases" => $wMetalWork, "numericStage" => 9,'icon' => "<i class='fa-solid fa-hammer'></i>"),
        'Finishing' => array('activeCases' => $aFinishing, "waitingCases" => $wFinishing, "numericStage" => 6,'icon' =>"<i class='fa-solid fa-broom'></i>"),
        'QC' => array('activeCases' => $aQC, "waitingCases" => $wQC, "numericStage" => 7,"icon" => "<i class='fa-solid fa-magnifying-glass'></i>"),
        'Delivery' => array('activeCases' => $aDelivery, "waitingCases" => $wDelivery, "numericStage" => 8,'icon' => "<i class='fa-solid fa-truck'></i>")
        );
        if (!Auth()->user()->is_admin){
        if(!$permissions->contains('permission_id', 1))
           unset($stages['Design']);
           if(!$permissions->contains('permission_id', 2))
           unset($stages['Milling']);
           if(!$permissions->contains('permission_id', 3))
           unset($stages['3DPrinting']);
             if(!$permissions->contains('permission_id', 4))
           unset($stages['Sintering']);
              if(!$permissions->contains('permission_id', 5))
            unset($stages['Pressing']);
              if(!$permissions->contains('permission_id', 5))
            unset($stages['MetalWork']);
              if(!$permissions->contains('permission_id', 6))
            unset($stages['Finishing']);
             if(!$permissions->contains('permission_id', 7))
           unset($stages['QC']);
             if(!$permissions->contains('permission_id', 8))
           unset($stages['Delivery']);
        }


          if (isset($stages['Finishing']))
          // Removing Models only cases from finishing stage (until case has units and not only models)
          foreach ($stages['Finishing']['waitingCases'] as $key => $case)
          // checks if case has only models ready at the finishing stage
          if (!$case->shouldShowForFinishing())
          unset($stages['Finishing']['waitingCases'][$key]);

    @endphp
        <!-- Begin .site-wrapper -->
    <div class="site-wrapper ops-dashboard">
        <!-- Begin Main -->
        <main class="ops-main">
            <!-- Begin .macaw-tabs -->
            <div class="macaw-tabs macaw-aurora-tabs notransition">
                <div role="tablist" class="stageSidebar" aria-orientation="vertical">
                    @foreach($stages as $key => $stage)
                        @php
                            $displayKey = strtolower($key) === '3dprinting' ? 'Printing' : (strtolower($key) === 'qc' ? 'QC' : $key);
                        @endphp
                        <button role="tab" aria-selected="false" aria-controls="{{$key.'label'}}" id="{{$key}}" onclick="setOuterTab(this)">
                            <span class="stageSidebar__icon">{!! $stage['icon'] !!}</span>
                            <span class="stageSidebar__name">{{ $displayKey }}</span>
                            <span class="stageSidebar__counts">
                                <span class="badge activeBadge">{{ count($stage['activeCases']) }}</span>
                                <span class="badge waitingBadge">{{ count($stage['waitingCases']) }}</span>
                            </span>
                        </button>
                    @endforeach
                </div>
                @foreach($stages as $key => $stage)
                    <div class="notransition" tabindex="0" role="tabpanel" aria-labelledby="{{$key}}" id="{{$key.'label'}}" hidden>
                        <!-- Begin .macaw-tabs -->
                        <div class="macaw-tabs macaw-silk-tabs notransition" >
                            <div role="tablist" aria-label="Fashion Trends" style="margin-left: 1%;">
                                <button href="{{$stage['numericStage']}}" role="tab" class="innerActiveBtn innerBtn" aria-selected="false" aria-controls="{{'active-'.$key}}" id="{{'active-'.$key .'label'}}" tabindex="-1" onclick="setInnerTab(this)"><span class="badge bg-info m-1 activeBadge">{{count($stage['activeCases'])}} </span> <span class="phaselabel activeTabText"> Active</span></button>
                                <button href="{{$stage['numericStage']}}" role="tab" class="innerWaitingBtn innerBtn" aria-selected="false" aria-controls="{{'waiting-'.$key}}" id="{{'waiting-'.$key .'label'}}" tabindex="-1"  onclick="setInnerTab(this)"><span class="badge bg-info m-1 waitingBadge">{{count($stage['waitingCases'])}} </span> <span class="phaselabel waitingtabText"> Waiting</span> </button>
                            </div>

                            <div tabindex="0" role="tabpanel" hidden aria-labelledby="{{'waiting-'.$key .'label'}}" id="{{'waiting-'.$key}}">
                                @if($key == '3DPrinting')
                                <div class="d-flex" id="assign-3dprinting-actions" style="display: none; gap: 8px; flex-wrap: wrap; margin: 5px 0;">
                                    <button type="button" class="btn btn-secondary" id="assign-3dprinting-to-me-btn" onclick="assign3dPrintingToMe()">{{ trans('ui.dom')['Assign to me'] ?? 'Assign to me' }}</button>
                                    @if($canAssignEmployees)
                                        <button type="button" class="btn btn-primary" id="assign-3dprinting-btn" onclick="openEmployeeModal('3dprinting')">Assign Selected</button>
                                    @endif
                                </div>

                                <table class="waitingTable sunriseTable" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all-waiting-printing"></th>
                                        <th>Doctor</th>
                                        <th>Patient  </th>
                                        <th class="deliveryDateHeader"><span class="innerSpan4Mobile">D.Date</span><span
                                                class="innerSpan4DeskTop">Delivery Date</span></th>
                                        <th>#</th>
                                        @if ($key == "Delivery")
                                            <th> Assigned To</th>
                                        @endif
                                        <th>Tags</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($stage['waitingCases'] as $case)

                                        <tr style="color:{{$color}}">
                                            <td><input type="checkbox" value="{{ $case->id }}" class="case-checkbox-waiting" data-stage-type="3dprinting" data-case-checkbox data-case-id="{{ $case->id }}"></td>
                                                <td class="clickable" onclick="YSH_openSlidePanel({{ $case->id }}, '3dprinting', 'dashboard')"><p class="">{{$case->client->name}}</p></td>
                                                <td class="clickable" onclick="YSH_openSlidePanel({{ $case->id }}, '3dprinting', 'dashboard')"><p class="">{{$case->patient_name}}</p></td>
                                                <td class="clickable" onclick="YSH_openSlidePanel({{ $case->id }}, '3dprinting', 'dashboard')">
                                                    <p class="">{{date_format(date_create($case->initDeliveryDate()),"d-M")}}</p>
                                                </td>
                                                <td class="clickable" onclick="YSH_openSlidePanel({{ $case->id }}, '3dprinting', 'dashboard')"><p class="">{{$case->unitsAmount($stage['numericStage'])}}</p></td>
                                                @if ($key == "Delivery")
                                                    <td class="clickable" onclick="YSH_openSlidePanel({{ $case->id }}, '3dprinting', 'dashboard')"><p class="">{{$case->jobs->where('stage',$stage['numericStage'])->first()->assignedTo ?
                                                         $case->jobs->where('stage',$stage['numericStage'])->first()->assignedTo->name_initials : "None"}}</p>
                                                    </td>
                                                @endif
                                                <td class="clickable" onclick="YSH_openSlidePanel({{ $case->id }}, '3dprinting', 'dashboard')">

                                                    @foreach($case->tags as $tag)
                                                        <i title="{{$tag->originalTagRecord?->text}}"
                                                           style="color:{{$tag->originalTagRecord?->color}}"
                                                           class="{{ preg_replace('/[^a-z0-9\-\s]/i', '', (string) ($tag->originalTagRecord?->icon)) }} fa-lg"></i>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                    @foreach ($stage['waitingCases'] as $case)
                                        <x-partiels.operationsCaseSlidePanel
                                            :case="$case"
                                            stageType="3dprinting"
                                            panelScope="dashboard"
                                            :stageName="$key"
                                            :stageNumber="$stage['numericStage']"
                                            caseState="waiting" />
                                    @endforeach

                                    <!-- Employee Assignment Dialog for 3D Printing -->
                                    <x-waiting-employee-dialog
                                        title="Assign 3D Printing Cases"
                                        btnText="ASSIGN"
                                        type="3dprinting"
                                        :employees="$printers"
                                        stageId="{{ $stage['numericStage'] }}"
                                        stageName="3D Printing"
                                    />
                                @else
                                <table class="waitingTable sunriseTable" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Doctor</th>
                                        <th>Patient  </th>
                                        <th class="deliveryDateHeader"><span class="innerSpan4Mobile">D.Date</span><span
                                                class="innerSpan4DeskTop">Delivery Date</span></th>
                                        <th>#</th>
                                        @if ($key == "Delivery")
                                            <th> Assigned To</th>
                                        @endif
                                        <th>Tags</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($stage['waitingCases'] as $case)

                                        <tr style="color:{{$color}}" class="clickable"
                                            onclick="YSH_openSlidePanel({{ $case->id }}, '{{ strtolower($key) }}', 'dashboard')">
                                            @if ($key == "Finishing")
                                                @php
                                                    $notReadyA=false;
                                                    $abutmentsReceived = $case->abutmentsReceived();
                                                    if(!$case->allUnitsAtFinishing())
                                                    $notReadyA=true;
                                                @endphp
                                            @endif
                                            <td><p class="">{{$case->client->name}}</p></td>
                                            <td><p class="">{{$case->patient_name}} @if ($key == "Finishing")
                                                        @if($notReadyA) <span style="margin: 4px 16px 1px 1px;float:right; line-height: 1;color:#ffa400;font-size: 10px;">
                                                                            Not <br>
                                                                            Ready
                                                                            </span>  @endif
                                                        @if(!$abutmentsReceived) <span style="margin: 4px 16px 1px 1px;float:right; line-height: 1;color:#ffa400;font-size: 10px;">
                                                                            Abutment <br>
                                                                            Missing
                                                                            </span>  @endif
                                                    @endif
                                                </p></td>
                                            <td>
                                                <p class="">{{date_format(date_create($case->initDeliveryDate()),"d-M")}}</p>
                                            </td>
                                            <td><p class="">{{$case->unitsAmount($stage['numericStage'])}}</p></td>
                                            <!-- Assigned to for delivery stage -->
                                            @if ($key == "Delivery")
                                                <td><p class="">{{$case->jobs->where('stage',$stage['numericStage'])->first()->assignedTo ?
                                                     $case->jobs->where('stage',$stage['numericStage'])->first()->assignedTo->name_initials : "None"}}</p>
                                                </td>
                                            @endif
                                            <td>

                                                @foreach($case->tags as $tag)
                                                    <i title="{{$tag->originalTagRecord?->text}}"
                                                       style="color:{{$tag->originalTagRecord?->color}}"
                                                       class="{{ preg_replace('/[^a-z0-9\-\s]/i', '', (string) ($tag->originalTagRecord?->icon)) }} fa-lg"></i>
                                                @endforeach
                                            </td>
                                        </tr>
                                        @if($key == "Delivery")
                                            <div class="modal fade" tabindex="-1" role="dialog"
                                                 id="myModal{{$case->id}}">
                                                <form action="{{route('assign-to-delivery-person')}}"
                                                      method="POST">
                                                    @csrf
                                                    <input type="hidden" name="case_id"
                                                           value="{{$case->id}}">
                                                    <div class="modal-dialog modal-dialog-centered"
                                                         role="document" style="width: 30%">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    Assign case to driver</h5>
                                                                <button type="button"
                                                                        class="close"
                                                                        data-dismiss="modal"
                                                                        aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">

                                                                <div>


                                                                    <div class="kt-form__control" style="    display: flex;
                                                                                                flex-direction: column;
                                                                                                align-items: center;">
                                                                        <label style="margin-bottom:10px !important" >Delivery
                                                                            Driver:</label>
                                                                        <nav class="driversContainer">
                                                                            @foreach($drivers as $driver)
                                                                                <br/>
                                                                                {{--<a class="driverName" href="{{route('assign-to-delivery-person',["driver_user" => $driver->id,"case_id" => $case->id])}}"><button class="btn btn-info driverNameBtn">--}}
                                                                                {{--{{$driver->name_initials}}--}}
                                                                                {{--</button></a>--}}
                                                                                <a class="btn btn-info driverNameBtn driverName" href="{{route('assign-to-delivery-person',["driver_user" => $driver->id,"case_id" => $case->id])}}">
                                                                                    {{$driver->name_initials}}
                                                                                </a>
                                                                            @endforeach
                                                                        </nav>
                                                                    </div>


                                                                </div>

                                                            </div>
                                                            <div class="modal-footer fullBtnsWidth" style="padding: 0px 10px 3px 10px !important">
                                                                {{--<button type="submit"--}}
                                                                {{--class="btn btn-primary">--}}
                                                                {{--Assign--}}
                                                                {{--</button>--}}
                                                                <button type="button"
                                                                        class="btn btn-secondary"
                                                                        data-dismiss="modal">
                                                                    Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                        {{-- Add assignment modals for all non-delivery stages --}}
                                        @if($key != "Delivery")
                                            <div class="modal fade" tabindex="-1" role="dialog"
                                                 id="assignModal{{$key}}{{$case->id}}">
                                                <form action="{{route('assign-to-stage-employee')}}"
                                                      method="POST">
                                                    @csrf
                                                    <input type="hidden" name="case_id" value="{{$case->id}}">
                                                    <input type="hidden" name="stage" value="{{$stage['numericStage']}}">
                                                    <input type="hidden" name="stage_name" value="{{$key}}">
                                                    <div class="modal-dialog modal-dialog-centered"
                                                         role="document" style="width: 30%">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">
                                                                    Assign case to {{$key}} employee</h5>
                                                                <button type="button"
                                                                        class="close"
                                                                        data-dismiss="modal"
                                                                        aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="kt-form__control" style="display: flex; flex-direction: column; align-items: center;">
                                                                    <label style="margin-bottom:10px !important">{{$key}} Employee:</label>
                                                                    <nav class="driversContainer">
                                                                        @php
                                                                            $stageUsers = [];
                                                                            switch($key) {
                                                                                case 'Design': $stageUsers = $designers ?? []; break;
                                                                                case 'Milling': $stageUsers = $millers ?? []; break;
                                                                                case '3dprinting': $stageUsers = $printers ?? []; break;
                                                                                case 'Sintering': $stageUsers = $sinteringUsers ?? []; break;
                                                                                case 'Pressing': $stageUsers = $pressingUsers ?? []; break;
                                                                                case 'MetalWork': $stageUsers = $pressingUsers ?? []; break;
                                                                                case 'Finishing': $stageUsers = $finishingUsers ?? []; break;
                                                                                case 'QC': $stageUsers = $qcUsers ?? []; break;
                                                                                default: $stageUsers = [];
                                                                            }
                                                                        @endphp
                                                                        @foreach($stageUsers as $employee)
                                                                            <br/>
                                                                            <a class="btn btn-info driverNameBtn driverName"
                                                                               href="javascript:void(0)"
                                                                               onclick="selectStageEmployee('{{$employee->id}}', '{{$key}}', '{{$case->id}}')">
                                                                                {{$employee->name_initials ?? $employee->first_name}}
                                                                            </a>
                                                                        @endforeach
                                                                    </nav>
                                                                    <input type="hidden" name="employee_id" id="employee_id_{{$key}}_{{$case->id}}" value="">
                                                                    <div style="margin-top: 15px; text-align: center;">
                                                                        <small class="text-muted">Click on an employee name to assign immediately</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif

                                    @endforeach

                                    </tbody>
                                </table>
                                @foreach ($stage['waitingCases'] as $case)
                                    <x-partiels.operationsCaseSlidePanel
                                        :case="$case"
                                        :stageType="strtolower($key)"
                                        panelScope="dashboard"
                                        :stageName="$key"
                                        :stageNumber="$stage['numericStage']"
                                        caseState="waiting" />
                                @endforeach
                                @endif
                            </div>
                            <div tabindex="0" role="tabpanel" aria-labelledby="{{'active-'.$key .'label'}}" id="{{'active-'.$key}}" hidden>
                                @if($key == '3DPrinting')
                                <form action="{{ route('bulk-complete-printing') }}" method="POST" id="bulk-complete-printing-form">
                                    @csrf
                                    <input type="hidden" name="case_ids" id="bulk-complete-case-ids">
                                    <button type="submit" class="btn btn-primary" style="display: none; margin: 5px 0">Complete Selected</button>
                                    <table class="activeTable sunriseTable" style="width:100%;">
                                        <thead>
                                        <tr>
                                            <th><input type="checkbox" id="select-all-active-printing"></th>
                                            <th>Doctor</th>
                                            <th>Patient</th>
                                            <th class="deliveryToHeader">Delivery Date</th>

                                            <th class="assignedToHeader">Assigned To</th>
                                            <th class="">#</th>
                                            <th class="">Tags</th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($stage['activeCases'] as $case)
                                            <tr style="color:{{$color}}">
                                                <td><input type="checkbox" value="{{ $case->id }}" class="case-checkbox-active"></td>
                                                <td class="clickable" onclick="YSH_openSlidePanel({{ $case->id }}, '3dprinting', 'dashboard')"><p class="">{{$case->client->name}}</p></td>
                                                <td class="clickable" onclick="YSH_openSlidePanel({{ $case->id }}, '3dprinting', 'dashboard')"><p class="">{{$case->patient_name}}</p></td>
                                                <td class="clickable" onclick="YSH_openSlidePanel({{ $case->id }}, '3dprinting', 'dashboard')">
                                                    <p class="">{{date_format(date_create($case->initDeliveryDate()),"d-M")}}</p>
                                                </td>
                                                <td class="clickable" onclick="YSH_openSlidePanel({{ $case->id }}, '3dprinting', 'dashboard')">
                                                    <p class="">{{$case->jobs->where('stage',$stage["numericStage"])->first() ? ($case->jobs->where('stage',$stage["numericStage"])->first()->assignedTo? $case->jobs->where('stage',$stage["numericStage"])->first()->assignedTo->name_initials : "None") : "None"}}</p>
                                                </td>
                                                <td class="clickable" onclick="YSH_openSlidePanel({{ $case->id }}, '3dprinting', 'dashboard')"><p class="">{{$case->unitsAmount($stage["numericStage"])}}</p>
                                                </td>
                                                <td class="clickable" onclick="YSH_openSlidePanel({{ $case->id }}, '3dprinting', 'dashboard')">

                                                    @foreach($case->tags as $tag)
                                                        <i title="{{$tag->originalTagRecord?->text}}"
                                                           style="color:{{$tag->originalTagRecord?->color}}"
                                                           class="{{ preg_replace('/[^a-z0-9\-\s]/i', '', (string) ($tag->originalTagRecord?->icon)) }} fa-lg"></i>
                                                    @endforeach
                                                </td>

                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </form>
                                @foreach ($stage['activeCases'] as $case)
                                    <x-partiels.operationsCaseSlidePanel
                                        :case="$case"
                                        stageType="3dprinting"
                                        panelScope="dashboard"
                                        :stageName="$key"
                                        :stageNumber="$stage['numericStage']"
                                        caseState="active" />
                                @endforeach
                                @else
                                <table class="activeTable sunriseTable" style="width:100%;">
                                    <thead>
                                    <tr>

                                        <th>Doctor</th>
                                        <th>Patient</th>
                                        <th class="deliveryToHeader">Delivery Date</th>

                                        <th class="assignedToHeader">Assigned To</th>
                                        <th class="">#</th>
                                        <th class="">Tags</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($stage['activeCases'] as $case)
                                        <tr class="clickable" style="color:{{$color}}"
                                            onclick="YSH_openSlidePanel({{ $case->id }}, '{{ strtolower($key) }}', 'dashboard')">
                                            @if ($key == "Finishing")
                                                @php
                                                    $notReadyA=false;
                                                    $abutmentsReceived = $case->abutmentsReceived();
                                                    if(!$case->allUnitsAtFinishing())
                                                    $notReadyA=true;
                                                @endphp
                                            @endif
                                            <td><p class="">{{$case->client->name}}</p></td>
                                            <td><p class="">{{$case->patient_name}} @if ($key == "Finishing")
                                                        @if($notReadyA) <span
                                                            style="float:right;margin-left: 5px; line-height: 1;color:#ffa400;font-size: 9px;">
                    Not <br>
                    Ready
                    </span>  @endif

                                                        @if(!$abutmentsReceived) <span
                                                            style="float:right; line-height: 1;color:#ffa400;font-size: 9px;">
                    Abutment <br>
                    Missing
                    </span>  @endif
                                                    @endif

                                                </p></td>
                                            <td class="">
                                                <p class="">{{date_format(date_create($case->initDeliveryDate()),"d-M")}}</p>
                                            </td>
                                            <td>
                                                <p class="">{{$case->jobs->where('stage',$stage["numericStage"])->first() ? ($case->jobs->where('stage',$stage["numericStage"])->first()->assignedTo? $case->jobs->where('stage',$stage["numericStage"])->first()->assignedTo->name_initials : "None") : "None"}}</p>
                                            </td>
                                            <td class=""><p class="">{{$case->unitsAmount($stage["numericStage"])}}</p>
                                            </td>
                                            <td class="">

                                                @foreach($case->tags as $tag)
                                                    <i title="{{$tag->originalTagRecord?->text}}"
                                                       style="color:{{$tag->originalTagRecord?->color}}"
                                                       class="{{ preg_replace('/[^a-z0-9\-\s]/i', '', (string) ($tag->originalTagRecord?->icon)) }} fa-lg"></i>
                                                @endforeach
                                            </td>

                                        </tr>


                                        <!-- External Milling Dialog -->
                                        @if ($key == "Milling")
                                            <div class="modal fade" tabindex="-1" role="dialog"
                                                 id="MEX{{$case->id}}">
                                                <form action="{{route('externally-milled')}}"
                                                      method="POST">
                                                    @csrf
                                                    <input type="hidden" name="case_id"
                                                           value="{{$case->id}}">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Case milling
                                                                    information</h5>
                                                                <button type="button" class="close"
                                                                        data-dismiss="modal"
                                                                        aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group row">
                                                                    <div class="form-group col-6 lab_id">
                                                                        <label for="lab_id">Lab
                                                                            name: </label>
                                                                        <select class="form-control"
                                                                                id="lab_id"
                                                                                name="lab_id">
                                                                            <option selected>Select
                                                                                your lab
                                                                            </option>
                                                                            @foreach($labs as $lab)
                                                                                <option value="{{$lab->id}}">{{$lab->name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer fullBtnsWidth">
                                                                <button type="submit"
                                                                        class="btn btn-primary">Save
                                                                    changes
                                                                </button>
                                                                <button type="button"
                                                                        class="btn btn-secondary"
                                                                        data-dismiss="modal">Close
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endif
                                    @endforeach
                                </tbody>
                                </table>
                                @foreach ($stage['activeCases'] as $case)
                                    <x-partiels.operationsCaseSlidePanel
                                        :case="$case"
                                        :stageType="strtolower($key)"
                                        panelScope="dashboard"
                                        :stageName="$key"
                                        :stageNumber="$stage['numericStage']"
                                        caseState="active" />
                                @endforeach
                                @endif
                            </div>
                        </div>
                        <!-- End .macaw-tabs -->
                    </div>
                @endforeach
            </div>
            <!-- End .macaw-tabs -->
        </main>
        <!-- End Main -->



    </div>
    <!-- End .site-wrapper -->


@endsection
@push('js')
    <script src="{{asset('https://cdn.jsdelivr.net/gh/htmlcssfreebies/macaw-tabs@v1.0.4/dist/js/macaw-tabs.js')}}"></script>

    <script>
        function YSH_getOperationsTableInputs(selector) {
            var $inputs = $();

            $('.ops-dashboard table.sunriseTable').each(function () {
                if ($.fn.DataTable && $.fn.DataTable.isDataTable(this)) {
                    $inputs = $inputs.add($(this).DataTable().$(selector));
                    return;
                }

                $inputs = $inputs.add($(this).find(selector));
            });

            return $inputs;
        }

        function YSH_adjustOperationsDataTables() {
            if (!$.fn.DataTable) {
                return;
            }

            $('.ops-dashboard table.sunriseTable:visible').each(function () {
                if (!$.fn.DataTable.isDataTable(this)) {
                    return;
                }

                $(this).DataTable().columns.adjust();
            });
        }

        $(document).ready(function () {
            var tables = $('.ops-dashboard table.sunriseTable');

            if ($.fn.DataTable && tables.length) {
                tables.each(function () {
                    if ($.fn.DataTable.isDataTable(this)) {
                        return;
                    }

                    var $table = $(this);
                    var nonOrderableColumns = [$table.find('thead th').length - 1];

                    if ($table.find('thead input[type="checkbox"]').length) {
                        nonOrderableColumns.unshift(0);
                    }

                    $table.DataTable({
                        autoWidth: false,
                        buttons: window.solentDataTableButtons ? window.solentDataTableButtons(true) : [],
                        pageLength: 10,
                        lengthChange: true,
                        paging: true,
                        searching: true,
                        info: true,
                        ordering: true,
                        columnDefs: [{
                            targets: nonOrderableColumns,
                            orderable: false,
                            searchable: false
                        }],
                        dom: "<'YSH-operations-table-tools solent-datatable-toolbar'Bfl>rt<'YSH-operations-table-footer'ip>",
                        language: Object.assign({}, (window.SolentI18n && window.SolentI18n.dataTables) || {}, {
                            search: '',
                            searchPlaceholder: @json(trans('ui.dom')['Search cases...'] ?? 'Search cases...')
                        })
                    });
                });

                $(document).on('click', '.ops-dashboard [role="tab"]', function () {
                    window.setTimeout(YSH_adjustOperationsDataTables, 20);
                });

                window.setTimeout(YSH_adjustOperationsDataTables, 20);
            }


// Main Tabs
            $(".macaw-aurora-tabs").macawTabs({
                autoVerticalOrientation: true,
                tabPanelTransitionLogic:true,
                tabPanelTransitionTimeoutDuration: 10
            });

// Nested Tabs
            $(".macaw-silk-tabs").macawTabs({
                autoVerticalOrientation: false,
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }

// activate single outer tab =>
            function activateOuterTab(tabId) {
                var btn = document.getElementById(tabId);
                var panel = document.getElementById(tabId + 'label');

                if (!btn || !panel) {
                    return false;
                }

                $(btn).attr('aria-selected', true);
                $(btn).removeAttr('tabindex');
                $(panel).addClass('active');
                $(panel).removeAttr('hidden');

                return true;
            }

            function activateInnerTab(tabId) {
                var innerTabBtn = document.getElementById(tabId);

                if (!innerTabBtn) {
                    return false;
                }

                var innerTab = document.getElementById(innerTabBtn.getAttribute('aria-controls'));

                if (!innerTab) {
                    return false;
                }

                $(innerTab).addClass('active');
                $(innerTab).removeAttr('hidden');
                $(innerTabBtn).attr('aria-selected', true);
                $(innerTabBtn).removeAttr('tabindex');

                return true;
            }

            var activeOuter = Cookies.get("activeOuterTab");

            if (!activateOuterTab(activeOuter)) {
                activeOuter = $('.macaw-aurora-tabs > [role="tablist"] > [role="tab"]').first().attr('id');
                if (activeOuter && activateOuterTab(activeOuter)) {
                    Cookies.set('activeOuterTab', activeOuter);
                }
            }


// activate multiple inner tabs =>
            for (let i = 1; i < 11; i++) {
                var activeInnerTab = Cookies.get('inner' + i);
                console.log("active inner : " + i +" => "+ activeInnerTab);
                if (activeInnerTab == undefined) {
                    continue;
                }
                else {
                    activateInnerTab(activeInnerTab);
                }
            }

            if (activeOuter) {
                var activeOuterBtn = document.getElementById(activeOuter);
                var activeStage = activeOuterBtn ? activeOuterBtn.getAttribute('aria-controls') : '';
                var activeStagePanel = activeStage ? $(document.getElementById(activeStage)) : $();
                var waitingBtn = activeStagePanel.find('.innerWaitingBtn').first();
                var hasSelectedInnerTab = activeStagePanel.find('.innerBtn[aria-selected="true"]').length > 0;

                if (waitingBtn.length && !hasSelectedInnerTab) {
                    activateInnerTab(waitingBtn.attr('id'));
                    Cookies.set('inner' + waitingBtn.attr('href'), waitingBtn.attr('id'));
                }
            }
        });

        $("[id^='active']").click(function (e) {

            Cookies.set('inner' + $(this).attr('href'), $(this).attr('id'));
            console.log("set cookie for : " + 'inner' + $(this).attr('href') + ' => ' +'inner' + $(this).attr('id'));
        });
        function setInnerTab(btnElement){
            Cookies.set('inner' + $(btnElement).attr('href'), $(btnElement).attr('id'));
            console.log("set cookie for : " + 'inner' + $(btnElement).attr('href') + ' => ' +'inner' + $(btnElement).attr('id'));

        }
        function setOuterTab(btnElement) {
            Cookies.set('activeOuterTab', btnElement.id);

            // Macaw reveals the outer stage panel after this inline click handler.
            // Once it has done so, default an untouched stage to its Waiting pane.
            // A stage with an explicitly selected inner tab keeps that selection.
            window.setTimeout(function () {
                var stagePanelId = btnElement.getAttribute('aria-controls');
                var stagePanel = stagePanelId ? document.getElementById(stagePanelId) : null;

                if (!stagePanel || stagePanel.querySelector('.innerBtn[aria-selected="true"]')) {
                    return;
                }

                var waitingTab = stagePanel.querySelector('.innerWaitingBtn');

                if (waitingTab) {
                    waitingTab.click();
                }
            }, 0);
        }

        function YSH_findSlideOverlay(caseId, stageType = '', panelScope = '') {
            const candidateIds = [
                panelScope && stageType ? `YSH-slide-overlay-${panelScope}-${stageType}-${caseId}` : null,
                stageType ? `YSH-slide-overlay-${stageType}-${caseId}` : null,
                `YSH-slide-overlay-${caseId}`
            ].filter(Boolean);

            for (const id of candidateIds) {
                const overlay = document.getElementById(id);
                if (overlay) {
                    return overlay;
                }
            }

            return null;
        }

        function YSH_findCaseActionsModal(caseId, stageType = '', panelScope = '') {
            const candidateIds = [
                panelScope && stageType ? `caseActionsModal-${panelScope}-${stageType}-${caseId}` : null,
                stageType ? `caseActionsModal-${stageType}-${caseId}` : null,
                `caseActionsModal-${caseId}`,
                `caseActionsModal${caseId}`
            ].filter(Boolean);

            for (const id of candidateIds) {
                const modal = document.getElementById(id);
                if (modal) {
                    return modal;
                }
            }

            return null;
        }

        function YSH_closeAllSlidePanels() {
            document.querySelectorAll('.YSH-slide-overlay.YSH-active, .YSH-slide-overlay.YSH-closing').forEach(function (overlay) {
                overlay.classList.remove('YSH-active', 'YSH-closing');
                overlay.style.display = 'none';
            });
            document.body.classList.remove('YSH-no-scroll');
        }

        function YSH_openSlidePanel(caseId, stageType = '', panelScope = '') {
            const caseActionsModal = YSH_findCaseActionsModal(caseId, stageType, panelScope);
            if (caseActionsModal && window.jQuery && typeof window.jQuery(caseActionsModal).modal === 'function') {
                window.jQuery(caseActionsModal).modal('show');
                return;
            }

            const overlay = YSH_findSlideOverlay(caseId, stageType, panelScope);
            if (!overlay) {
                return;
            }

            YSH_closeAllSlidePanels();
            overlay.classList.remove('YSH-closing');
            overlay.style.display = 'flex';
            overlay.classList.add('YSH-active');
            document.body.classList.add('YSH-no-scroll');
        }

        function YSH_closeSlidePanel(caseId, stageType = '', panelScope = '') {
            const overlay = YSH_findSlideOverlay(caseId, stageType, panelScope);
            if (!overlay) {
                YSH_closeAllSlidePanels();
                return;
            }

            overlay.classList.remove('YSH-active', 'YSH-closing');
            overlay.style.display = 'none';

            if (!document.querySelector('.YSH-slide-overlay.YSH-active')) {
                document.body.classList.remove('YSH-no-scroll');
            }
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                YSH_closeAllSlidePanels();
            }
        });

        function assign3dPrintingToMe() {
            const checkboxes = YSH_getOperationsTableInputs('input[data-stage-type="3dprinting"][data-case-checkbox]').filter(':checked');
            if (!checkboxes.length) {
                alert('Please select at least one case');
                return;
            }

            const form = document.getElementById('employee-form-3dprinting');
            if (!form) {
                console.error('Employee form not found for 3dprinting');
                return;
            }

            const caseIds = checkboxes.map(function () {
                return this.getAttribute('data-case-id');
            }).get().join(',');
            const employeeInput = document.getElementById('employee-id-input-3dprinting');
            const caseIdsInput = document.getElementById('case-ids-input-3dprinting');

            if (employeeInput) {
                employeeInput.value = {{ Auth()->user()->id }};
            }
            if (caseIdsInput) {
                caseIdsInput.value = caseIds;
            }

            form.submit();
        }

        function update3dPrintingAssignButtons() {
            const anyChecked = YSH_getOperationsTableInputs('input[data-stage-type="3dprinting"][data-case-checkbox]').filter(':checked').length > 0;
            const actions = document.getElementById('assign-3dprinting-actions');
            if (actions) {
                actions.style.display = anyChecked ? 'flex' : 'none';
            }
        }

        // Function to handle stage employee selection and immediate form submission
        function selectStageEmployee(employeeId, stageName, caseId) {
            const modalId = `assignModal${stageName}${caseId}`;
            const modal = document.getElementById(modalId);

            if (modal) {
                // Set the hidden input value
                const employeeInput = document.getElementById(`employee_id_${stageName}_${caseId}`);
                if (employeeInput) {
                    employeeInput.value = employeeId;
                }

                // Find and submit the form immediately
                const form = modal.querySelector('form');
                if (form) {
                    // Add visual feedback - highlight selected employee briefly
                    event.target.classList.remove('btn-info');
                    event.target.classList.add('btn-success');

                    // Submit the form after a brief delay for visual feedback
                    setTimeout(() => {
                        form.submit();
                    }, 200);
                }
            }
        }

        $(document).on('change', '#select-all-waiting-printing', function () {
            const boxes = YSH_getOperationsTableInputs('input[data-stage-type="3dprinting"][data-case-checkbox]');
            boxes.prop('checked', this.checked);
            update3dPrintingAssignButtons();
        });

        // Old bulk assign form removed - now using employee dialog

        $(document).on('change', '#select-all-active-printing', function () {
            YSH_getOperationsTableInputs('input.case-checkbox-active')
                .prop('checked', this.checked)
                .trigger('change');
        });

        $('#bulk-complete-printing-form').submit(function(e) {
            var ids = [];
            YSH_getOperationsTableInputs('input.case-checkbox-active').filter(':checked').each(function() {
                ids.push($(this).val());
            });
            if (ids.length > 0) {
                $('#bulk-complete-case-ids').val(ids.join(','));
            } else {
                e.preventDefault();
                alert('Please select at least one case.');
            }
        });

        // Show/hide assignment actions for 3D Printing
        $(document).on('change', 'input[data-stage-type="3dprinting"][data-case-checkbox]', function () {
            update3dPrintingAssignButtons();
        });

        // Show/hide "Complete Selected" button
        $(document).on('change', '.case-checkbox-active', function () {
            if (YSH_getOperationsTableInputs('input.case-checkbox-active').filter(':checked').length > 0) {
                $('#bulk-complete-printing-form button[type="submit"]').show();
            } else {
                $('#bulk-complete-printing-form button[type="submit"]').hide();
            }
        });
    </script>
@endpush

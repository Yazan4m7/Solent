<!--<!---->
<!--@extends('layouts.app' ,[ 'pageSlug' => config('site_vars.labWorkFlowLabel')])-->
<!---->
<!---->
<!---->
<!--@section('content')-->
<!--{{&#45;&#45;-->
<!--<meta http-equiv="refresh" content="120">&#45;&#45;}}-->
<!--&lt;!&ndash;suppress ALL &ndash;&gt;-->
<!--<link rel="stylesheet"-->
<!--    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />-->
<!--<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">-->
<!--<link href="{{ asset('assets') }}/css/dialog.css" rel="stylesheet" />-->
<!---->
<!---->
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">-->
<!--</link>-->
<!--<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">-->
<!--{{&#45;&#45;BEGIN black box dialog button &#45;&#45;}}-->
<!--<style>-->
<!--    .main-panel>.content {-->
<!--        /*margin: 78px 0px 30px 0px;*/-->
<!--    }-->
<!---->
<!--    body {-->
<!--        /*display: flex;*/-->
<!--        /*justify-content: center;*/-->
<!--        /*align-items: center;*/-->
<!--        /*height: 100vh;*/-->
<!---->
<!--    }-->
<!--</style>-->
<!--{{&#45;&#45;End black box dialog button &#45;&#45;}}-->
<!--<style>-->
<!--    .receiveSelectBtn {-->
<!--        padding: 7px 40px 7px 40px;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs {-->
<!--        /*-->
<!--             &#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;-->
<!--             * Color Palette-->
<!--             &#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;-->
<!--             */-->
<!--        &#45;&#45;tab-color-white: #f9f9f9;-->
<!--        &#45;&#45;tab-color-black: #004345;-->
<!--        &#45;&#45;tab-color-cadet: #2b7b7d;-->
<!--        &#45;&#45;tab-color-fighter: #315f5f;-->
<!--        &#45;&#45;tab-color-space: #383961;-->
<!--        &#45;&#45;tab-color-gray: #d7d9d7;-->
<!--        &#45;&#45;tab-color-english: #2b7b7d;-->
<!--        /*-->
<!--             &#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;-->
<!--             * CSS Vars-->
<!--             &#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;-->
<!--             */-->
<!--        &#45;&#45;tab-bg-color: var(&#45;&#45;tab-color-cadet);-->
<!--        &#45;&#45;tab-text-color: var(&#45;&#45;tab-color-gray);-->
<!--        &#45;&#45;tab-border-color: var(&#45;&#45;tab-color-space);-->
<!--        &#45;&#45;tab-active-bg-color: var(&#45;&#45;tab-color-white);-->
<!--        &#45;&#45;tab-active-text-color: var(&#45;&#45;tab-color-black);-->
<!--        &#45;&#45;tab-active-border-color: var(&#45;&#45;tab-color-english);-->
<!--        &#45;&#45;tab-focus-bg-color: var(&#45;&#45;tab-color-fighter);-->
<!--        &#45;&#45;tab-focus-text-color: var(&#45;&#45;tab-color-white);-->
<!--        &#45;&#45;tab-focus-text-secondary-color: var(&#45;&#45;tab-color-english);-->
<!--        &#45;&#45;tab-focus-border-color: var(&#45;&#45;tab-color-fighter);-->
<!--        /*-->
<!--             &#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;-->
<!--             * Style-->
<!--             &#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;-->
<!--             */-->
<!--        display: flex;-->
<!---->
<!--        width: 100%;-->
<!---->
<!--    }-->
<!---->
<!--    .site-wrapper {-->
<!--        margin: 0;-->
<!--        width: 100%;-->
<!--        max-width: none;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tablist"] {-->
<!--        position: relative;-->
<!---->
<!--        display: flex;-->
<!--        width: 20%;-->
<!--        flex-direction: column;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>* {-->
<!--        border: none;-->
<!--        border-bottom: 1px solid var(&#45;&#45;tab-border-color);-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>*:last-child {-->
<!--        border-bottom: none;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"] {-->
<!--        position: relative;-->
<!--        margin: 0;-->
<!--        overflow: visible;-->
<!--        word-wrap: break-word;-->
<!---->
<!---->
<!--        font-weight: normal;-->
<!--        flex-wrap: wrap;-->
<!--        display: flex;-->
<!--        align-items: center;-->
<!--        justify-content: space-between;-->
<!--        text-align: center;-->
<!--        background-color: var(&#45;&#45;tab-bg-color);-->
<!--        color: var(&#45;&#45;tab-text-color);-->
<!--        fill: var(&#45;&#45;tab-text-color);-->
<!--        padding: 0.8rem 0.5rem;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"] .label {-->
<!--        /*display: none;*/-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"]>* {-->
<!--        padding: 0;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"]:hover,-->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"]:focus {-->
<!--        outline: 0;-->
<!--        background-color: var(&#45;&#45;tab-focus-bg-color);-->
<!--        color: var(&#45;&#45;tab-focus-text-color);-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"][aria-selected="true"] {-->
<!--        background-color: var(&#45;&#45;tab-active-bg-color);-->
<!--        color: var(&#45;&#45;tab-active-text-color);-->
<!--        fill: var(&#45;&#45;tab-active-text-color);-->
<!--        border-left: 5px solid var(&#45;&#45;tab-active-border-color);-->
<!--        padding-left: 0.2rem;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"][aria-selected="true"]>svg {-->
<!--        fill: var(&#45;&#45;tab-active-text-color);-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"][aria-selected="true"] .icon {-->
<!--        color: var(&#45;&#45;tab-focus-text-secondary-color);-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"][aria-selected="true"]:hover,-->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"][aria-selected="true"]:focus {-->
<!--        outline: 0;-->
<!--        background-color: var(&#45;&#45;tab-active-bg-color);-->
<!--        color: var(&#45;&#45;tab-focus-text-secondary-color);-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"]:hover,-->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"]:focus,-->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"]:active {-->
<!--        outline: 0;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tabpanel"] {-->
<!--        overflow: hidden;-->
<!--        position: relative;-->
<!---->
<!--        font-family: var(&#45;&#45;global-body-font-family);-->
<!--        font-weight: normal;-->
<!---->
<!--        width: 80%;-->
<!--        padding: 1.25rem 0.9375rem;-->
<!--        background-color: white;-->
<!--        color: var(&#45;&#45;tab-active-text-color);-->
<!---->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tabpanel"] a,-->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tabpanel"] a:visited {-->
<!--        cursor: pointer;-->
<!--        color: inherit;-->
<!--        -webkit-text-decoration-style: dotted;-->
<!--        text-decoration-style: dotted;-->
<!--        text-underline-offset: 0.1875rem;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tabpanel"] a:hover {-->
<!--        text-decoration: none;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tabpanel"]>* {-->
<!--        margin-top: 24px;-->
<!--        margin-bottom: 24px;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tabpanel"]>*:first-child {-->
<!--        margin-top: 0;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tabpanel"]>*:last-child {-->
<!--        margin-bottom: 0;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tabpanel"].active>* {-->
<!--        /*opacity: 1;*/-->
<!--        /*animation: zoomIn;*/-->
<!--        /*animation-duration: 1.5s;*/-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-aurora-tabs>[role="tabpanel"]:focus {-->
<!--        outline: 0;-->
<!--        border-left: 6px solid var(&#45;&#45;tab-focus-border-color);-->
<!--    }-->
<!---->
<!--    .m-1 {-->
<!--        margin: 0.1rem 0rem !important;-->
<!--    }-->
<!---->
<!--    /*-->
<!--        &#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;-->
<!--        * Media Queries-->
<!--        &#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;-->
<!--        */-->
<!--    @media only screen and (max-width: 500px) {-->
<!--        .bb-inline-img {-->
<!--            width: 40%;-->
<!---->
<!--        }-->
<!---->
<!--        .bb-inline-img img {-->
<!--            max-width: 100%;-->
<!--            max-height: 100%;-->
<!--            object-fit: revert;-->
<!--            width: 100%;-->
<!--            height: 100%;-->
<!--            margin-bottom: 0;-->
<!--        }-->
<!---->
<!--        .stageName {-->
<!--            display: none;-->
<!--        }-->
<!---->
<!--        .dotsDiv {-->
<!--            display: none-->
<!--        }-->
<!---->
<!--        .iconSpan>i {-->
<!--            font-size: 25px;-->
<!--        }-->
<!---->
<!--        .iconSpan>svg {-->
<!--            width: 1.4rem;-->
<!--        }-->
<!---->
<!--        .millingIcon {-->
<!--            width: 1.4rem;-->
<!--        }-->
<!---->
<!--        .iconSpan {-->
<!--            margin-bottom: 5px;-->
<!--            justify-content: space-around;-->
<!--        }-->
<!---->
<!--        .sunriseTable thead th {-->
<!--            font-size: 13px;-->
<!--        }-->
<!---->
<!--        .macaw-tabs.macaw-silk-tabs>[role="tablist"]>[role="tab"] {-->
<!--            padding: 0.625rem 0.7rem !important;-->
<!--        }-->
<!---->
<!--        table.dataTable thead .sorting {-->
<!--            background-image: none;-->
<!--        }-->
<!---->
<!--        [role="tabpanel"] {-->
<!--            overflow-x: scroll;-->
<!--        }-->
<!--    }-->
<!---->
<!--    @media only screen and (min-width: 768px) {-->
<!--        .macaw-tabs.macaw-aurora-tabs>[role="tablist"] {-->
<!--            width: 20%;-->
<!--        }-->
<!---->
<!--        .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"] {}-->
<!---->
<!--        .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"] .label {-->
<!--            /*display: flex;*/-->
<!--        }-->
<!---->
<!--        .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"]>* {}-->
<!---->
<!--        .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"]>*:last-child {-->
<!--            padding-right: 0;-->
<!--        }-->
<!---->
<!--        .macaw-tabs.macaw-aurora-tabs>[role="tabpanel"] {-->
<!---->
<!--            width: 80%;-->
<!---->
<!--        }-->
<!--    }-->
<!---->
<!--    @media only screen and (min-width: 1280px) {-->
<!--        .macaw-tabs.macaw-aurora-tabs {}-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs {-->
<!--        /*-->
<!--             &#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;-->
<!--             * Color Palette-->
<!--             &#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;-->
<!--             */-->
<!--        &#45;&#45;tab-color-white: #fff;-->
<!--        &#45;&#45;tab-color-black: #000;-->
<!--        &#45;&#45;tab-color-metallic: #358491;-->
<!--        &#45;&#45;tab-color-platihum: #e6eced;-->
<!--        &#45;&#45;tab-color-cultured: #f2f9fa;-->
<!--        &#45;&#45;tab-color-isabelline: #f4f4f4;-->
<!--        /*-->
<!--             &#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;-->
<!--             * CSS Vars-->
<!--             &#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;-->
<!--             */-->
<!--        &#45;&#45;tab-bg-color: var(&#45;&#45;tab-color-white);-->
<!--        &#45;&#45;tab-text-color: var(&#45;&#45;tab-color-black);-->
<!--        &#45;&#45;tab-border-color: var(&#45;&#45;tab-color-platihum);-->
<!--        &#45;&#45;tab-active-bg-color: var(&#45;&#45;tab-color-cultured);-->
<!--        &#45;&#45;tab-active-text-color: var(&#45;&#45;tab-color-black);-->
<!--        &#45;&#45;tab-focus-bg-color: var(&#45;&#45;tab-color-isabelline);-->
<!--        &#45;&#45;tab-focus-text-color: var(&#45;&#45;tab-color-black);-->
<!--        &#45;&#45;tab-focus-border-color: var(&#45;&#45;tab-color-isabelline);-->
<!--        &#45;&#45;tab-icon-color: var(&#45;&#45;tab-color-metallic);-->
<!--        /*-->
<!--             &#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;-->
<!--             * Style-->
<!--             &#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;-->
<!--             */-->
<!--        margin-left: auto;-->
<!--        margin-right: auto;-->
<!--        width: 100%;-->
<!---->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tablist"] {-->
<!--        position: relative;-->
<!---->
<!--        display: flex;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tablist"]>*:not(.innerActiveBtn):not(.innerWaitingBtn) {-->
<!--        border: none;-->
<!--        border-right: 1px solid var(&#45;&#45;tab-border-color);-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tablist"]>*:last-child {}-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tablist"]>[role="tab"] {-->
<!--        position: relative;-->
<!--        margin: 0;-->
<!--        overflow: visible;-->
<!--        word-wrap: break-word;-->
<!---->
<!--        font-weight: normal;-->
<!---->
<!--        display: flex;-->
<!--        align-items: center;-->
<!--        justify-content: center;-->
<!--        text-align: center;-->
<!--        background-color: var(&#45;&#45;tab-bg-color);-->
<!--        color: var(&#45;&#45;tab-text-color);-->
<!--        padding: 0.625rem 1.625rem;-->
<!---->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tablist"]>[role="tab"] .icon {-->
<!--        color: var(&#45;&#45;tab-icon-color);-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tablist"]>[role="tab"]>* {}-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tablist"]>[role="tab"]>*:last-child {-->
<!--        padding-right: 0;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tablist"]>[role="tab"]:hover,-->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tablist"]>[role="tab"]:focus {-->
<!--        outline: 0;-->
<!--        background-color: var(&#45;&#45;tab-focus-bg-color);-->
<!--        color: var(&#45;&#45;tab-focus-text-color);-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tablist"]>[role="tab"][aria-selected="true"] {-->
<!--        background-color: var(&#45;&#45;tab-active-bg-color);-->
<!--        color: var(&#45;&#45;tab-active-text-color);-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tablist"]>[role="tab"][aria-selected="true"]:hover,-->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tablist"]>[role="tab"][aria-selected="true"]:focus {-->
<!--        outline: 0;-->
<!--        background-color: var(&#45;&#45;tab-focus-bg-color);-->
<!--        color: var(&#45;&#45;tab-focus-text-color);-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tablist"]>[role="tab"]:hover,-->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tablist"]>[role="tab"]:focus,-->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tablist"]>[role="tab"]:active {-->
<!--        outline: 0;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tabpanel"] {-->
<!--        position: relative;-->
<!---->
<!--        font-family: var(&#45;&#45;global-body-font-family);-->
<!--        font-weight: normal;-->
<!---->
<!--        background-color: transparent;-->
<!--        color: var(&#45;&#45;tab-active-text-color);-->
<!--        border-bottom: 6px solid transparent;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tabpanel"] a,-->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tabpanel"] a:visited {-->
<!--        cursor: pointer;-->
<!--        color: inherit;-->
<!--        -webkit-text-decoration-style: dotted;-->
<!--        text-decoration-style: dotted;-->
<!--        text-underline-offset: 0.1875rem;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tabpanel"] a:hover {-->
<!--        text-decoration: none;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tabpanel"]>* {-->
<!---->
<!---->
<!--        opacity: 0;-->
<!---->
<!--        transition: opacity 0.2s, transform 0.2s;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tabpanel"]>*:first-child {-->
<!--        margin-top: 0;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tabpanel"]>*:last-child {-->
<!--        margin-bottom: 0;-->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tabpanel"].active>* {-->
<!--        opacity: 1;-->
<!---->
<!--    }-->
<!---->
<!--    .macaw-tabs.macaw-silk-tabs>[role="tabpanel"]:focus {-->
<!--        outline: 0;-->
<!--        border-bottom: 6px solid var(&#45;&#45;tab-focus-border-color);-->
<!--    }-->
<!---->
<!--    /*-->
<!--        &#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;-->
<!--        * Media Queries-->
<!--        &#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;-->
<!--        */-->
<!---->
<!--    @media only screen and (min-width: 768px) {-->
<!--        .macaw-tabs.macaw-silk-tabs>[role="tablist"]>[role="tab"] {}-->
<!---->
<!--        .macaw-tabs.macaw-silk-tabs [role="tabpanel"] {}-->
<!--    }-->
<!---->
<!--    @media only screen and (min-width: 1280px) {-->
<!--        .macaw-tabs.macaw-silk-tabs {}-->
<!--    }-->
<!---->
<!--    @media only screen and (max-width: 575px) {-->
<!--        .macaw-tabs.macaw-silk-tabs.vertical {-->
<!--            display: flex;-->
<!--            width: 100%;-->
<!--        }-->
<!---->
<!--        .macaw-tabs.macaw-silk-tabs.vertical>[role="tablist"] {-->
<!--            width: 15%;-->
<!--            flex-direction: column;-->
<!--        }-->
<!---->
<!--        .macaw-tabs.macaw-silk-tabs.vertical>[role="tablist"]>* {-->
<!--            border: none;-->
<!--            border-bottom: 1px solid var(&#45;&#45;tab-border-color);-->
<!--        }-->
<!---->
<!--        .macaw-tabs.macaw-silk-tabs.vertical>[role="tablist"]>*:last-child {-->
<!--            border-bottom: none;-->
<!--        }-->
<!---->
<!--        .macaw-tabs.macaw-silk-tabs.vertical>[role="tablist"]>[role="tab"] .icon {}-->
<!---->
<!--        .macaw-tabs.macaw-silk-tabs.vertical>[role="tablist"]>[role="tab"] .label {-->
<!--            display: none;-->
<!--        }-->
<!---->
<!--        .macaw-tabs.macaw-silk-tabs.vertical>[role="tablist"]>[role="tab"]>* {-->
<!--            padding: 0;-->
<!--        }-->
<!---->
<!--        .macaw-tabs.macaw-silk-tabs.vertical [role="tabpanel"] {-->
<!--            width: 85%;-->
<!--            padding: 0 0.9375rem;-->
<!--        }-->
<!--    }-->
<!---->
<!---->
<!--    .row {-->
<!--        background-color: inherit;-->
<!---->
<!--    }-->
<!---->
<!--    /*Modal titles */-->
<!--    #doctor {}-->
<!---->
<!--    #pat {}-->
<!---->
<!--    .innerSpan4Mobile {-->
<!--        display: none;-->
<!--    }-->
<!---->
<!--    .waitingtabText {-->
<!--        color: red;-->
<!--    }-->
<!---->
<!--    .dropdown-menu {-->
<!--        left: -100px !important;-->
<!--        transform: translate3d(0px, 28px, 0px) !important;-->
<!--    }-->
<!---->
<!--    .dropdown-menu:before {-->
<!--        left: 112px;-->
<!--    }-->
<!---->
<!--    .badge {-->
<!--        color: white;-->
<!--    }-->
<!---->
<!--    * {-->
<!--        box-sizing: border-box-->
<!--    }-->
<!---->
<!--    .nav-tabs>li {-->
<!--        width: 25%;-->
<!--        text-align: center;-->
<!--    }-->
<!---->
<!--    /* Style the tab */-->
<!--    .tab {-->
<!--        float: left;-->
<!--        border: 1px solid #ccc;-->
<!--        background-color: #f1f1f1;-->
<!--        width: 20%;-->
<!--        height: fit-content;-->
<!--        margin-top: 51px;-->
<!--    }-->
<!---->
<!--    /* Style the buttons that are used to open the tab content */-->
<!--    .tab button {-->
<!--        display: flex;-->
<!--        background-color: inherit;-->
<!--        color: black;-->
<!--        padding: 13px 8px;-->
<!--        width: 100%;-->
<!--        border: none;-->
<!--        outline: none;-->
<!--        text-align: left;-->
<!--        cursor: pointer;-->
<!--        transition: 0.3s;-->
<!--    }-->
<!---->
<!--    /* Change background color of buttons on hover */-->
<!--    .tab button:hover {-->
<!--        background-color: #ddd;-->
<!--    }-->
<!---->
<!--    /* Create an active/current "tab button" class */-->
<!--    .tab button.active {-->
<!--        background-color: #ccc;-->
<!--    }-->
<!---->
<!--    /* Style the tab content */-->
<!--    .tabcontent {-->
<!--        float: left;-->
<!--        padding: 0px 2px;-->
<!--        /* border: 1px solid #ccc;*/-->
<!--        width: 80%;-->
<!--        border-left: none;-->
<!---->
<!--        height: -webkit-fill-available;-->
<!--        max-height: 80vh;-->
<!--        overflow: auto;-->
<!--    }-->
<!---->
<!--    .waitingBadge {-->
<!--        background-color: indianred !important;-->
<!--    }-->
<!---->
<!--    .activeBadge {-->
<!--        background-color: steelblue !important;-->
<!--    }-->
<!---->
<!--    .main-panel>.content {-->
<!--        padding: 0px;-->
<!--    }-->
<!---->
<!--    .white-content .table>thead>tr>th,-->
<!--    .white-content .table>tbody>tr>th,-->
<!--    .white-content .table>tfoot>tr>th,-->
<!--    .white-content .table>thead>tr>td,-->
<!--    .white-content .table>tbody>tr>td,-->
<!--    .white-content .table>tfoot>tr>td {-->
<!---->
<!--        border-top-color: transparent;-->
<!--        border-right-color: rgba(34, 42, 66, 0.2);-->
<!--        border-bottom-color: transparent;-->
<!--        border-left-color: rgba(34, 42, 66, 0.2);-->
<!--    }-->
<!---->
<!--    .card {-->
<!--        padding: 5px;-->
<!--    }-->
<!---->
<!--    .tabbable {-->
<!--        margin-top: 5px;-->
<!--    }-->
<!---->
<!--    .modal {-->
<!---->
<!--        height: auto;-->
<!--    }-->
<!---->
<!--    table.dataTable.compact tbody th,-->
<!--    table.dataTable.compact tbody td {-->
<!--        padding: 1px 0px 0px 5px;-->
<!--    }-->
<!---->
<!--    @media screen and (max-width: 768px) {-->
<!--        .macaw-tabs.macaw-aurora-tabs>[role="tablist"]>[role="tab"] {-->
<!--            display: block;-->
<!--        }-->
<!---->
<!--        .main-panel .content {-->
<!--            padding-left: 4px;-->
<!--            padding-right: 6px;-->
<!--        }-->
<!---->
<!--        .activeTable tr>*:nth-child(3),-->
<!--        .activeTable tr>*:nth-child(5),-->
<!--        .activeTable tr>*:nth-child(6) {-->
<!--            display: none;-->
<!--        }-->
<!---->
<!--        .waitingTable tr>*:nth-child(4),-->
<!--        .waitingTable tr>*:nth-child(5) {-->
<!--            display: none;-->
<!--        }-->
<!---->
<!--        .innerSpan4DeskTop {-->
<!--            display: none;-->
<!--        }-->
<!---->
<!--        .innerSpan4Mobile {-->
<!--            display: flex;-->
<!--        }-->
<!---->
<!--        .tab {-->
<!--            margin-top: 10px;-->
<!--        }-->
<!---->
<!--        .col-3 {-->
<!--            padding-right: 5px;-->
<!--            padding-left: 5px;-->
<!--        }-->
<!---->
<!--        .btnsRow {-->
<!--            padding: 0;-->
<!--        }-->
<!--    }-->
<!---->
<!--    .notransition {-->
<!--        -webkit-transition: none !important;-->
<!--        -moz-transition: none !important;-->
<!--        -o-transition: none !important;-->
<!--        transition: none !important;-->
<!--    }-->
<!---->
<!--    /* Active and waiting labels: */-->
<!--    .phaselabel {-->
<!--        padding-left: 4px !important;-->
<!--    }-->
<!---->
<!--    .stageSidebar {-->
<!--        /*border-top: 1px solid #2b7b7d;*/-->
<!--        /*border-bottom: 1px solid #2b7b7d;*/-->
<!--        margin-top: 67px;-->
<!--    }-->
<!---->
<!--    waitingtabText.active {}-->
<!---->
<!--    .innerActiveBtn,-->
<!--    .innerWaitingBtn {-->
<!--        border: 0.0625rem solid transparent;-->
<!--        border-top-left-radius: 0.25rem;-->
<!--        border-top-right-radius: 0.25rem;-->
<!--    }-->
<!---->
<!--    .innerActiveBtn[aria-selected="true"] {-->
<!--        background-color: #eef3f8 !important;-->
<!--        border-color: #4682b4 #4682b4 #4682b4 !important;-->
<!--    }-->
<!---->
<!--    .innerWaitingBtn[aria-selected="true"] {-->
<!--        background-color: #e8e8e8;-->
<!--        border-color: #cd5c5c #cd5c5c #cd5c5c;-->
<!--    }-->
<!---->
<!--    svg {-->
<!--        width: 1rem;-->
<!--    }-->
<!---->
<!--    .millingIcon {-->
<!--        width: 0.8rem;-->
<!--    }-->
<!---->
<!--    .printingIcon {-->
<!--        width: 1.3rem;-->
<!--    }-->
<!---->
<!--    .driverNameBtn {-->
<!--        margin: 4px;-->
<!--        width: 150px;-->
<!--        background-color: #ffc107;-->
<!--        border-color: #ffc107;-->
<!--    }-->
<!---->
<!--    .driverNameBtn:hover,-->
<!--    .driverNameBtn:active {-->
<!--        background-color: #dea700 !important;-->
<!--        border-color: #dea700 !important;-->
<!--    }-->
<!---->
<!--    .driversContainer {-->
<!---->
<!--        display: flex;-->
<!--        flex-direction: column;-->
<!--        align-items: center;-->
<!--        line-height: 0;-->
<!--    }-->
<!--</style>-->
<!---->
<!--{{&#45;&#45; DELIVERY DIALOG (FACES) STYLE &#45;&#45;}}-->
<!--<style>-->
<!--    .silicon-valley-container {-->
<!--        display: flex;-->
<!--        /* justify-content: center; */-->
<!--        align-items: center;-->
<!--        position: relative;-->
<!--        /* height: 100vh; */-->
<!--        padding: 30px 40px 40px 40px;-->
<!--        border-radius: 10px;-->
<!--        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);-->
<!--        max-width: 90%;-->
<!--        max-height: 90%;-->
<!--        width: 600px;-->
<!--        overflow: visible;-->
<!--        margin: 10px;-->
<!--        justify-content: space-around;-->
<!--    }-->
<!---->
<!--    .silicon-valley-face {-->
<!--        margin: 10px;-->
<!--        cursor: pointer;-->
<!--        position: relative;-->
<!--        transition: transform 0.2s, filter 0.2s;-->
<!--    }-->
<!---->
<!--    .silicon-valley-face img {-->
<!--        max-width: 100%;-->
<!--        height: auto;-->
<!--    }-->
<!---->
<!--    .silicon-valley-face.silicon-valley-selected img {-->
<!--        filter: brightness(1.2) saturate(1.5);-->
<!--        transform: scale(1.1);-->
<!--    }-->
<!---->
<!--    .silicon-valley-face.silicon-valley-disabled:not(.silicon-valley-selected) img {-->
<!--        filter: grayscale(100%);-->
<!--        pointer-events: none;-->
<!--    }-->
<!---->
<!--    .silicon-valley-overlay {-->
<!--        position: absolute;-->
<!--        top: 0;-->
<!--        left: 0;-->
<!--        width: 100%;-->
<!--        height: 100%;-->
<!--        background: rgba(0, 0, 0, 0.5);-->
<!--        display: flex;-->
<!--        justify-content: center;-->
<!--        align-items: center;-->
<!--        color: #fff;-->
<!--        font-size: 1.5em;-->
<!--        display: none;-->
<!--    }-->
<!---->
<!--    .silicon-valley-overlay.silicon-valley-active {-->
<!--        display: flex;-->
<!--    }-->
<!--</style>-->
<!--@php $color = "#01292b";-->
<!--$permissions = Cache::get('user'.Auth()->user()->id);-->
<!--$canEditCase = false;-->
<!--if(Auth()->user()->is_admin || ($permissions && ($permissions->contains('permission_id', 102))))-->
<!--$canEditCase = true;-->
<!--@endphp-->
<!---->
<!---->
<!--@php-->
<!--$stages = array(-->
<!--'Design'=> array('activeCases' => $aDesign, "waitingCases" => $wDesign, "numericStage" => 1,'icon' => "<i-->
<!--    class='fa-solid fa-desktop'></i>") ,-->
<!--'3DPrinting' => array('activeCases' => $aPrinting, "waitingCases" => $wPrinting, "numericStage" => 3, 'icon' => "-->
<!--<svg version='1.1' class='printingIcon' id='Layer_1' xmlns='http://www.w3.org/2000/svg'-->
<!--    xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 367.579 213.624'-->
<!--    style='enable-background:new 0 0 367.579 213.624;' xml:space='preserve'>-->
<!--    <g id='XMLID_80_'>-->
<!--        <path id='XMLID_81_' d='M54.962,85.176h21.863c12.45,0,20.9-2.581,25.355-7.743c4.453-5.162,6.681-10.678,6.681-16.549-->
<!--		c0-6.579-2.456-12.424-7.364-17.537c-4.911-5.11-11.767-7.667-20.573-7.667c-16.803,0-27.382,8.858-31.732,26.57L6.225,55.417-->
<!--		C9.767,39.426,18.345,26.19,31.96,15.714C45.573,5.238,62.35,0,82.292,0c20.851,0,38.387,4.965,52.609,14.891-->
<!--		c14.22,9.926,21.332,23.5,21.332,40.719c0,22.589-11.843,38.038-35.528,46.344c27.834,7.385,41.753,23.771,41.753,49.159-->
<!--		c0,18.208-7.112,33.177-21.332,44.911c-14.222,11.734-33.834,17.601-58.834,17.601c-23.989,0-42.994-6.033-57.012-18.099-->
<!--		C11.259,183.46,2.833,168.488,0,150.615l44.031-6.377c4.251,21.358,16.599,32.036,37.046,32.036c9.513,0,17.232-2.522,23.154-7.57-->
<!--		c5.921-5.046,8.882-11.809,8.882-20.288c0-8.984-2.709-15.698-8.123-20.139c-5.416-4.441-15.767-6.662-31.049-6.662H54.962V85.176z-->
<!--		' />-->
<!--        <path id='XMLID_83_'-->
<!--            d='M197.682,3.188h63.256c25.788,0,45.002,3.568,57.643,10.704c12.641,7.136,23.967,18.423,33.979,33.858-->
<!--		c10.012,15.437,15.02,34.947,15.02,58.53c0,29.659-8.75,54.431-26.242,74.32c-17.496,19.89-41.615,29.834-72.359,29.834h-71.295-->
<!--		V3.188z M245.356,41.297v130.118h19.999c17.677,0,30.808-6.604,39.392-19.814c8.586-13.209,12.881-28.719,12.881-46.536-->
<!--		c0-12.55-2.451-24.165-7.35-34.845c-4.9-10.678-10.984-18.167-18.258-22.471c-7.273-4.301-16.009-6.453-26.21-6.453H245.356z' />-->
<!--    </g>-->
<!--</svg>-->
<!--"),-->
<!--'Milling' => array('activeCases' => $aMilling, "waitingCases" => $wMilling, "numericStage" => 2,'icon' =>"<svg-->
<!--    class='millingIcon' version='1.1' id='Layer_1' xmlns='http://www.w3.org/2000/svg'-->
<!--    xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 219.296 416.891'-->
<!--    style='enable-background:new 0 0 219.296 416.891;' xml:space='preserve'>-->
<!--    <path id='XMLID_96_' d='M83.523,285.071-->
<!--	c-8.936,0-17.387-0.009-25.838,0.002c-18.806,0.023-29.419-10.595-29.401-29.402c0.014-14.833-0.005-29.665,0.01-44.498-->
<!--	c0.016-15.216,6.395-23.871,20.709-28.584c1.27-0.418,2.911-2.281,2.937-3.503c0.228-10.983,0.133-21.974,0.133-33.931-->
<!--	c-6.659,0-13.105,0.414-19.467-0.14c-4.52-0.393-9.315-1.333-13.297-3.374c-6.953-3.564-10.608-9.93-11.158-17.792-->
<!--	c-0.267-3.816-0.089-51.173-0.126-55.005c-0.039-4.011,1.898-6.506,5.923-6.479c4.169,0.028,5.652,2.911,5.665,6.735-->
<!--	c0.008,2.333-0.048,48.178-0.011,50.511c0.152,9.463,4.396,13.745,13.798,13.749c50.664,0.021,101.328,0.018,151.992,0.001-->
<!--	c9.887-0.003,14.027-4.111,14.03-13.927c0.015-47.664,0.002-54.688,0.006-102.352c0-1.496-0.504-3.464,0.245-4.389-->
<!--	c1.598-1.976,3.891-4.731,5.849-4.692c1.897,0.038,3.856,3.071,5.52,5.018c0.499,0.584,0.113,1.935,0.113,2.934-->
<!--	c-0.001,48.164,0.019,55.688-0.021,103.852c-0.013,16.046-9.434,25.329-25.55,25.365c-5.95,0.013-11.901,0.002-18.142,0.002-->
<!--	c0,12.556,0,24.485,0,36.328c3.596,1.459,7.364,2.457,10.568,4.39c8.888,5.365,12.844,13.639,12.863,23.894-->
<!--	c0.03,15.999,0.112,31.998,0.031,47.997c-0.081,16.03-11.432,27.205-27.559,27.28c-8.982,0.042-17.965,0.008-27.537,0.008-->
<!--	c-0.12,2.405-0.316,4.492-0.315,6.579c0.007,28.831,0.009,57.661,0.106,86.491c0.014,4.045-1.001,7.294-4.025,10.249-->
<!--	c-5.715,5.585-11.001,11.606-16.559,17.356c-4.076,4.216-6.825,4.203-10.946-0.073c-6.011-6.239-12.218-12.334-17.687-19.025-->
<!--	c-1.956-2.393-2.785-6.337-2.807-9.581c-0.186-28.664-0.075-57.329-0.052-85.994C83.524,289.277,83.523,287.485,83.523,285.071z-->
<!--	 M96.692,294.324c-0.469,0.283-0.937,0.567-1.406,0.85c0,3.786,0.243,7.591-0.068,11.351c-0.316,3.831,1.056,6.487,3.816,9.093-->
<!--	c7.003,6.614,13.707,13.545,20.548,20.331c1.076,1.067,2.241,2.045,4.245,3.863c0-5.612,0.096-9.891-0.05-14.162-->
<!--	c-0.054-1.586-0.161-3.641-1.12-4.651C114.113,312.002,105.374,303.19,96.692,294.324z M96.59,329.452-->
<!--	c-0.428,0.236-0.857,0.472-1.285,0.708c0,1.957,0.271,3.96-0.048,5.864c-1.276,7.614,1.425,13.266,7.321,18.203-->
<!--	c5.594,4.684,10.458,10.239,15.648,15.406c1.543,1.536,3.099,3.06,5.565,5.493c0-6.143,0.101-10.776-0.062-15.399-->
<!--	c-0.049-1.384-0.441-3.121-1.342-4.054C113.871,346.854,105.21,338.173,96.59,329.452z M116.706,386.799-->
<!--	c-7.345-7.452-14.241-14.449-20.966-21.272c-2.12,11.971,1.972,20.231,14.419,28.222-->
<!--	C112.486,391.28,114.851,388.769,116.706,386.799z M106.012,285.297c5.899,5.949,11.817,11.916,17.377,17.523-->
<!--	c0-5.319,0-11.342,0-17.523C117.143,285.297,111.367,285.297,106.012,285.297z' />-->
<!--    <path id='XMLID_93_' d='M163.2,66.867c0.003-13.2-0.054-6.892,0.028-20.092c0.04-6.383,2.412-9.25,7.259-9.047-->
<!--	c5.681,0.237,6.988,4.26,6.997,8.851c0.057,26.603-0.012,33.698-0.026,60.301c-0.003,4.996-2.164,8.607-7.374,8.444-->
<!--	c-5.177-0.162-6.987-3.838-6.935-8.856C163.289,93.269,163.197,80.068,163.2,66.867z' />-->
<!--    <path id='XMLID_83_' style='fill:#FFFFFF;' d='M96.692,294.324c8.682,8.866,17.422,17.678,25.965,26.676-->
<!--	c0.959,1.01,1.066,3.065,1.12,4.651c0.146,4.271,0.05,14.833,0.05,20.445c-2.004-1.818-3.169-2.796-4.245-3.863-->
<!--	c-6.841-6.787-13.545-13.717-20.548-20.331c-2.76-2.607-4.132-5.262-3.816-9.093c0.311-3.76,0.068-13.849,0.068-17.634-->
<!--	C95.754,294.89,96.223,294.607,96.692,294.324z' />-->
<!--    <path id='XMLID_82_' style='fill:#FFFFFF;' d='M116.706,375.126c-1.855,1.97-4.22,16.153-6.547,18.624-->
<!--	c-12.447-7.991-16.539-27.924-14.419-39.895C102.465,360.678,109.361,367.675,116.706,375.126z' />-->
<!--    <path id='XMLID_81_' style='fill:#FFFFFF;'-->
<!--        d='M123.895,244.73c10.552,0,21.104-0.043,31.655,0.017-->
<!--	c6.287,0.036,9.415,2.403,9.209,7.09c-0.26,5.919-4.436,7.163-9.32,7.172c-21.302,0.037-42.604,0.06-63.906,0.008-->
<!--	c-4.776-0.012-8.647-1.797-8.584-7.259c0.062-5.416,3.757-7.081,8.685-7.049C102.387,244.777,113.141,244.729,123.895,244.73z' />-->
<!--    <path id='XMLID_80_' style='fill:#FFFFFF;' d='M96.462,165.605c-5.129,0-10.259,0.075-15.387-0.024-->
<!--	c-4.105-0.079-6.799-2.359-6.76-6.389c0.038-3.964,2.628-6.336,6.784-6.353c10.259-0.043,20.519-0.042,30.778,0.017-->
<!--	c4.142,0.024,6.756,2.289,6.733,6.364c-0.022,4.051-2.594,6.308-6.766,6.342c-5.127,0.041-10.255,0.01-15.383,0.01-->
<!--	C96.462,165.583,96.462,165.594,96.462,165.605z' />-->
<!--</svg>-->
<!--"),-->
<!--'Sintering' => array('activeCases' => $aSintering, "waitingCases" => $wSintering, "numericStage" => 4,'icon' => "<i-->
<!--    class='fa-solid fa-fire-flame-curved'></i>"),-->
<!--'Pressing'=> array('activeCases' => $aPressing, "waitingCases" => $wPressing, "numericStage" => 5,'icon' => "<svg-->
<!--    xmlns='http://www.w3.org/2000/svg' viewBox='0 0 384 512'>-->
<!--    <defs>-->
<!--        <style>-->
<!--            .fa-secondary {-->
<!--                opacity: .4-->
<!--            }-->
<!--        </style>-->
<!--    </defs>-->
<!--    <path class='fa-primary'-->
<!--        d='M350 206.6c3.781 8.803 1.984 19.03-4.594 26l-136 144.1c-9.062 9.601-25.84 9.601-34.91 0l-136-144.1C31.97 225.7 30.17 215.4 33.95 206.6C37.75 197.8 46.42 192.1 56 192.1L128 192.1V64.03c0-17.69 14.33-32.02 32-32.02h64c17.67 0 32 14.34 32 32.02v128.1l72 .0314C337.6 192.1 346.3 197.8 350 206.6z' />-->
<!--    <path class='fa-secondary'-->
<!--        d='M352 416H31.1C14.33 416 0 430.3 0 447.1S14.33 480 31.1 480H352C369.7 480 384 465.7 384 448S369.7 416 352 416z' />-->
<!--</svg>"),-->
<!--'Finishing' => array('activeCases' => $aFinishing, "waitingCases" => $wFinishing, "numericStage" => 6,'icon' =>"<i-->
<!--    class='fa-solid fa-broom'></i>"),-->
<!--'QC' => array('activeCases' => $aQC, "waitingCases" => $wQC, "numericStage" => 7,"icon" => "<i-->
<!--    class='fa-solid fa-magnifying-glass'></i>"),-->
<!--'Delivery' => array('activeCases' => $aDelivery, "waitingCases" => $wDelivery, "numericStage" => 8,'icon' => "<i-->
<!--    class='fa-solid fa-truck'></i>")-->
<!--);-->
<!---->
<!---->
<!--if (!Auth()->user()->is_admin){-->
<!--if(!$permissions->contains('permission_id', 1))-->
<!--unset($stages['Design']);-->
<!--if(!$permissions->contains('permission_id', 2))-->
<!--unset($stages['Milling']);-->
<!--if(!$permissions->contains('permission_id', 3))-->
<!--unset($stages['3DPrinting']);-->
<!--if(!$permissions->contains('permission_id', 4))-->
<!--unset($stages['Sintering']);-->
<!--if(!$permissions->contains('permission_id', 5))-->
<!--unset($stages['Pressing']);-->
<!--if(!$permissions->contains('permission_id', 6))-->
<!--unset($stages['Finishing']);-->
<!--if(!$permissions->contains('permission_id', 7))-->
<!--unset($stages['QC']);-->
<!--if(!$permissions->contains('permission_id', 8))-->
<!--unset($stages['Delivery']);-->
<!--}-->
<!---->
<!---->
<!--if (isset($stages['Finishing']))-->
<!--// Removing Models only cases from finishing stage (until case has units and not only models)-->
<!--foreach ($stages['Finishing']['waitingCases'] as $key => $case)-->
<!--// checks if case has only models ready at the finishing stage-->
<!--if (!$case->shouldShowForFinishing())-->
<!--unset($stages['Finishing']['waitingCases'][$key]);-->
<!---->
<!--@endphp-->
<!---->
<!---->
<!--&lt;!&ndash; Begin .site-wrapper &ndash;&gt;-->
<!--<div class="site-wrapper">-->
<!---->
<!---->
<!--    &lt;!&ndash; Begin waiting milling dialog &ndash;&gt;-->
<!--    &lt;!&ndash; Begin Main &ndash;&gt;-->
<!--    <main style="background-color: white">-->
<!--        &lt;!&ndash; Begin .macaw-tabs &ndash;&gt;-->
<!--        <div class="macaw-tabs macaw-aurora-tabs notransition">-->
<!--            <div role="tablist" class="stageSidebar" aria-orientation="vertical">-->
<!--                @foreach($stages as $key => $stage)-->
<!--                <button  role="tab" aria-selected="false" aria-controls="{{$key.'label'}}" id="{{$key}}" style=""  onclick="setOuterTab(this)">-->
<!--                    <span class="iconSpan" style="display: flex;align-items: center;">{!!$stage['icon'] !!}-->
<!--                    <span style=" padding-left:6px" class="stageName"> {{$key}}</span></span>-->
<!--                    <div> <span class="badge bg-info m-1 activeBadge" style="padding: 0.25em 0.4em;">{{count($stage['activeCases'])}}</span>-->
<!--                    <span class="badge bg-info m-1 waitingBadge" style="padding: 0.25em 0.4em;">{{count($stage['waitingCases'])}} </span>-->
<!--            </div>-->
<!--                </button>-->
<!--                @endforeach-->
<!--            </div>-->
<!--            @foreach($stages as $key => $stage)-->
<!--            <div class="notransition" tabindex="0" role="tabpanel" aria-labelledby="{{$key}}" id="{{$key.'label'}}"-->
<!--                hidden>-->
<!---->
<!--                &lt;!&ndash; Begin .macaw-tabs &ndash;&gt;-->
<!--                <div class="macaw-tabs macaw-silk-tabs notransition">-->
<!--                    <div role="tablist" aria-label="Fashion Trends" style="margin-left: 1%;">-->
<!--                        <button href="{{$stage['numericStage']}}" role="tab" class="innerActiveBtn innerBtn"-->
<!--                            aria-selected="false" aria-controls="{{'active-'.$key}}" id="{{'active-'.$key .'label'}}"-->
<!--                            tabindex="-1" onclick="setInnerTab(this)"><span-->
<!--                                class="badge bg-info m-1 activeBadge">{{count($stage['activeCases'])}} </span>-->
<!--                            <span class="phaselabel activeTabText"> Active</span>-->
<!--                        </button>-->
<!--                        <button href="{{$stage['numericStage']}}" role="tab" class="innerWaitingBtn innerBtn"-->
<!--                            aria-selected="false" aria-controls="{{'waiting-'.$key}}" id="{{'waiting-'.$key .'label'}}"-->
<!--                            tabindex="-1" onclick="setInnerTab(this)">-->
<!--                            <span class="badge bg-info m-1 waitingBadge">{{count($stage['waitingCases'])}} </span> <span-->
<!--                                class="phaselabel waitingtabText"> Waiting</span> </button>-->
<!--                    </div>-->
<!--&lt;!&ndash;                        {{&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;waiting TABLE-&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45; &#45;&#45;}}&ndash;&gt;-->
<!--&lt;!&ndash;                        {{&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;waiting TABLE-&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45; &#45;&#45;}}&ndash;&gt;-->
<!--&lt;!&ndash;                        {{&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;waiting TABLE-&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45; &#45;&#45;}}&ndash;&gt;-->
<!--&lt;!&ndash;                        {{&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;waiting TABLE-&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45; &#45;&#45;}}&ndash;&gt;-->
<!--                        <div tabindex="0" role="tabpanel" hidden aria-labelledby="{{'waiting-'.$key .'label'}}"-->
<!--                             id="{{'waiting-'.$key}}">-->
<!---->
<!--                            <table class=" waitingTable sunriseTable" style="width:100%">-->
<!--                                <thead>-->
<!--                                <tr>-->
<!--                                    <td class="no-sort">-->
<!--                                        @if ($key == "Milling" || $key == "Sintering" ||$key == "3DPrinting" || $key == "Pressing" || $key == "Delivery")-->
<!--                                            <input type="checkbox" class="selectAllCases" value="0" name="selectAllCases" onchange="selectAll(this)" />-->
<!--                                        @endif-->
<!--                                    </td>-->
<!--                                    <th>Doctor</th>-->
<!--                                    <th>Patient</th>-->
<!--                                    <th class="deliveryDateHeader"><span class="innerSpan4Mobile">D.Date</span><span-->
<!--                                            class="innerSpan4DeskTop">Delivery Date</span></th>-->
<!--                                    <th>#</th>-->
<!--                                    @if ($key == "Delivery")-->
<!--                                        <th> Assigned To</th>-->
<!--                                    @endif-->
<!--                                    <th>Tags</th>-->
<!--                                </tr>-->
<!--                                </thead>-->
<!--                                <tbody>-->
<!--                                @foreach ($stage['waitingCases'] as $case)-->
<!---->
<!--                                    <tr style="color:{{$color}}">-->
<!--                                        @if ($key == "Finishing")-->
<!--                                            @php-->
<!--                                                $notReadyA=false;-->
<!--                                                $abutmentsReceived = $case->abutmentsReceived();-->
<!--                                                if(!$case->allUnitsAtFinishing())-->
<!--                                                $notReadyA=true;-->
<!--                                            @endphp-->
<!--                                        @endif-->
<!--                                        <td>-->
<!--                                            @if ($key == "Milling" || $key == "3DPrinting" || $key == "Sintering" || $key == "Pressing" || $key == "Delivery" )-->
<!--                                                <input type="checkbox" class="custom-control-input multipleCB" value="{{$case->id}}" name="casesCheckBoxes[]" onchange="multiCBChanged()" />-->
<!--                                            @endif-->
<!--                                        </td>-->
<!--                                        <td class="clickable" data-toggle="modal"-->
<!--                                            data-target="#waitingDialog{{$key. $case->id}}">-->
<!--                                            <p class="">{{$case->client->name}}</p>-->
<!--                                        </td>-->
<!--                                        <td class="clickable" data-toggle="modal"-->
<!--                                            data-target="#waitingDialog{{$key. $case->id}}">-->
<!--                                            <p class="">{{$case->patient_name}} @if ($key == "Finishing")-->
<!--                                                    @if($notReadyA) <span style="margin: 4px 16px 1px 1px;float:right; line-height: 1;color:#ffa400;font-size: 10px;">-->
<!--                                                Not <br>-->
<!--                                                Ready-->
<!--                                            </span> @endif-->
<!--                                                    @if(!$abutmentsReceived) <span style="margin: 4px 16px 1px 1px;float:right; line-height: 1;color:#ffa400;font-size: 10px;">-->
<!--                                                Abutment <br>-->
<!--                                                Missing-->
<!--                                            </span> @endif-->
<!--                                                @endif-->
<!--                                            </p>-->
<!--                                        </td>-->
<!--                                        <td class="clickable" data-toggle="modal"-->
<!--                                            data-target="#waitingDialog{{$key. $case->id}}">-->
<!--                                            <p class="">{{date_format(date_create($case->initDeliveryDate()),"d-M")}}</p>-->
<!--                                        </td>-->
<!--                                        <td class="clickable" data-toggle="modal"-->
<!--                                            data-target="#waitingDialog{{$key. $case->id}}">-->
<!--                                            <p class="">{{$case->unitsAmount($stage['numericStage'])}}</p>-->
<!--                                        </td>-->
<!--                                        &lt;!&ndash; Assigned to for delivery stage &ndash;&gt;-->
<!--                                        @if ($key == "Delivery")-->
<!--                                            <td class="clickable" data-toggle="modal"-->
<!--                                                data-target="#waitingDialog{{$key. $case->id}}">-->
<!--                                                <p class="">{{$case->jobs->where('stage',$stage['numericStage'])->first()->assignedTo ?-->
<!--                                                     $case->jobs->where('stage',$stage['numericStage'])->first()->assignedTo->name_initials : "None"}}</p>-->
<!--                                            </td>-->
<!--                                        @endif-->
<!--                                        <td class="clickable" data-toggle="modal"-->
<!--                                            data-target="#waitingDialog{{$key. $case->id}}">-->
<!---->
<!--                                            @foreach($case->tags as $tag)-->
<!--                                                <i title="{{$tag->originalTagRecord->text}}"-->
<!--                                                   style="color:{{$tag->originalTagRecord->color}}"-->
<!--                                                   class="{{$tag->originalTagRecord->icon}}  fa-lg"></i>-->
<!--                                            @endforeach-->
<!--                                        </td>-->
<!--                                    </tr>-->
<!--                                    @if($key == "Delivery")-->
<!--                                        <div class="modal fade" tabindex="-1" role="dialog"-->
<!--                                             id="myModal{{$case->id}}">-->
<!--                                            <form action="{{route('assign-to-delivery-person')}}"-->
<!--                                                  method="POST">-->
<!--                                                @csrf-->
<!--                                                <input type="hidden" name="case_id"-->
<!--                                                       value="{{$case->id}}">-->
<!--                                                <div class="modal-dialog modal-dialog-centered"-->
<!--                                                     role="document" style="width: 30%">-->
<!--                                                    <div class="modal-content">-->
<!--                                                        <div class="modal-header">-->
<!--                                                            <h5 class="modal-title">-->
<!--                                                                Assign case to driver</h5>-->
<!--                                                            <button type="button"-->
<!--                                                                    class="close"-->
<!--                                                                    data-dismiss="modal"-->
<!--                                                                    aria-label="Close">-->
<!--                                                                <span aria-hidden="true">&times;</span>-->
<!--                                                            </button>-->
<!--                                                        </div>-->
<!--                                                        <div class="modal-body">-->
<!---->
<!--                                                            <div>-->
<!---->
<!---->
<!--                                                                <div class="kt-form__control" style="    display: flex;-->
<!--                                                                                                flex-direction: column;-->
<!--                                                                                                align-items: center;">-->
<!--                                                                    <label style="margin-bottom:10px !important">Delivery-->
<!--                                                                        Driver:</label>-->
<!--                                                                    <nav class="driversContainer">-->
<!--                                                                        @foreach($drivers as $driver)-->
<!--                                                                            <br />-->
<!--                                                                            {{&#45;&#45;<a class="driverName" href="{{route('assign-to-delivery-person',["driver_user" => $driver->id,"case_id" => $case->id])}}"><button class="btn btn-info driverNameBtn">&#45;&#45;}}-->
<!--                                                                            {{&#45;&#45;{{$driver->name_initials}}&#45;&#45;}}-->
<!--                                                                            {{&#45;&#45;</button></a>&#45;&#45;}}-->
<!--                                                                            <a class="btn btn-info driverNameBtn driverName" href="{{route('assign-to-delivery-person',["driver_user" => $driver->id,"case_id" => $case->id])}}">-->
<!--                                                                                {{$driver->name_initials}}-->
<!--                                                                            </a>-->
<!--                                                                        @endforeach-->
<!--                                                                    </nav>-->
<!--                                                                </div>-->
<!---->
<!---->
<!--                                                            </div>-->
<!---->
<!--                                                        </div>-->
<!--                                                        <div class="modal-footer fullBtnsWidth" style="padding: 0px 10px 3px 10px !important">-->
<!--                                                            {{&#45;&#45;<button type="submit"&#45;&#45;}}-->
<!--                                                            {{&#45;&#45;class="btn btn-primary">&#45;&#45;}}-->
<!--                                                            {{&#45;&#45;Assign&#45;&#45;}}-->
<!--                                                            {{&#45;&#45;</button>&#45;&#45;}}-->
<!--                                                            <button type="button"-->
<!--                                                                    class="btn btn-secondary"-->
<!--                                                                    data-dismiss="modal">-->
<!--                                                                Close-->
<!--                                                            </button>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </form>-->
<!--                                        </div>-->
<!--                                    @endif-->
<!---->
<!---->
<!--                                    {{&#45;&#45;BEGIN WAITING DIALOG &#45;&#45;}}-->
<!--                                    <div class="modal fade" tabindex="-1" role="dialog" id="waitingDialog{{$key.$case->id}}">-->
<!--                                        <form action="{{$key=="Delivery" ? route('delivery-accept', $case->id) : route('assign-to-me',['caseId'=> $case->id,'stage'=>$stage["numericStage"]] )}}"-->
<!--                                              method="GET">-->
<!--                                            @csrf-->
<!--                                            <input type="hidden" name="case_id" value="{{$case->id}}">-->
<!--                                            <div class="modal-dialog modal-dialog-centered" role="document">-->
<!--                                                <div class="modal-content">-->
<!--                                                    <div class="modal-header">-->
<!--                                                        <h5 class="modal-title">Case Completion</h5>-->
<!--                                                        @if(Auth()->user()->is_admin )-->
<!--                                                            <div class="tooltipY">-->
<!--                                                                <a href="{{route('finish-case-completely',['caseId' => $case->id])}}">-->
<!--                                                                    <i class="fa-solid fa-forward-fast close "></i>-->
<!--                                                                </a>-->
<!--                                                                <span class="tooltiptextY">Send To Delivery</span>-->
<!--                                                            </div>-->
<!--                                                        @endif-->
<!--                                                        <button type="button" class="close" data-dismiss="modal"-->
<!--                                                                aria-label="Close">-->
<!--                                                            <span aria-hidden="true">&times;</span>-->
<!--                                                        </button>-->
<!---->
<!--                                                    </div>-->
<!--                                                    <div class="modal-body">-->
<!---->
<!--                                                        <div class="form-group row" style="margin-bottom: 0px">-->
<!--                                                            <div class="form-group col-6 " style="margin-bottom: 0px">-->
<!--                                                                <label for="doctor">Doctor: </label>-->
<!--                                                                <h5 id="doctor"><b>{{$case->client->name}}</b></h5>-->
<!--                                                            </div>-->
<!--                                                            <div class="form-group col-6 " style="margin-bottom: 0px">-->
<!--                                                                <label for="pat">Patient: </label>-->
<!--                                                                <h5 id="pat"><b>{{$case->patient_name}}</b></h5>-->
<!--                                                            </div>-->
<!--                                                        </div>-->
<!--                                                        <hr>-->
<!--                                                        <div class="form-group row">-->
<!--                                                            <div class=" col-12 ">-->
<!--                                                                <label><b>Jobs:</b></label><br>-->
<!---->
<!---->
<!--                                                                @foreach( $case->jobs->where('stage',$stage["numericStage"]) as $job)-->
<!---->
<!--                                                                    @php-->
<!--                                                                        $unit = explode(', ',$job->unit_num);-->
<!--                                                                    @endphp-->
<!---->
<!--                                                                    <span>{{$job->unit_num}}-->
<!--                                                                - {{$job->jobType->name ?? "No Job Type"}}-->
<!--                                                                - {{$job->material->name ?? "no material"}} {{$job->color =='0' ? "":" - " .$job->color}}-->
<!--                                                                        {{$job->style == 'None' ? "":" - " .$job->style}} {{isset($job->implantR) && $job->jobType->id ==6  ?( " - Implant Type: " . $job->implantR->name): "" }}-->
<!--                                                                <br>-->
<!--                                                                {{isset($job->abutmentR) && $job->jobType->id ==6  ?( " Abutment Type: " . $job->abutmentR->name): "" }} </span>-->
<!--                                                                @endforeach-->
<!--                                                            </div>-->
<!--                                                        </div>-->
<!--                                                        @if(count($case->notes)>0)-->
<!--                                                            <hr>-->
<!--                                                            <label><b>Notes:</b></label><br>-->
<!--                                                            @foreach($case->notes as $note)-->
<!--                                                                <div class="form-control"-->
<!--                                                                     style="height:fit-content;width:80%;background-color: #dcecfd59;margin-bottom: 5px; color:black;font-size:12px"-->
<!--                                                                     disabled>-->
<!---->
<!--                                                                    <span class="noteHeader">{{'['. substr( $note->created_at,0,16) . '] [' . $note->writtenBy->name_initials . '] : ' }}</span><br>-->
<!--                                                                    <span class="noteText">{{$note->note}}</span>-->
<!--                                                                </div>-->
<!--                                                            @endforeach-->
<!--                                                        @endif-->
<!--                                                    </div>-->
<!--                                                    <div class="modal-footer fullBtnsWidth">-->
<!--                                                        <div class="row btnsRow"-->
<!--                                                             style=" margin-right: 0px; margin-left: 0px;width:100%">-->
<!--                                                            <div class="col-md-3 col-sm-12 padding5px">-->
<!--                                                                <a href="{{route('view-case', ['id' => $case->id, 'stage' => $stage["numericStage"]  ])}}">-->
<!--                                                                    <button type="button" class="btn btn-info ">View-->
<!--                                                                    </button>-->
<!--                                                                </a>-->
<!--                                                            </div>-->
<!--                                                            @if ($key == "Milling")-->
<!--                                                                <div class="col-md-6 col-sm-12 padding5px">-->
<!--                                                                    <a >-->
<!--                                                                        <button type="button" data-toggle="modal"  class="btn btn-success" data-dismiss="modal"  onclick="openModal('MillingDialog')"><i class="fa-solid fa-hexagon-nodes"></i> Nest </button>-->
<!--                                                                    </a>-->
<!--                                                                </div>-->
<!---->
<!--                                                            @else-->
<!--                                                                <div class="col-md-6 col-sm-12 padding5px">-->
<!--                                                                    <button type="submit" class="btn btn-success"-->
<!--                                                                            style="width:100%">{{$key == "Delivery" ? 'Take' : 'Assign To Me'}}</button>-->
<!--                                                                </div>-->
<!--                                                            @endif-->
<!--                                                            <div class="col-md-3 col-sm-12 padding5px"><a-->
<!--                                                                    href="{{route('edit-case-view',$case->id)}}">-->
<!--                                                                    <button type="button"-->
<!--                                                                            class="btn btn-warning " {{$canEditCase ? '' : 'disabled'}}>-->
<!--                                                                        Edit Case-->
<!--                                                                    </button>-->
<!--                                                                </a></div>-->
<!--                                                            @if ($key == "QC")-->
<!--                                                                <div class="col-12 padding5px">-->
<!--                                                                    <a href="{{route('assign-and-finish',['caseId'=> $case->id,'stage'=>$stage["numericStage"]])}}">-->
<!--                                                                        <button type="button" class="btn btn-info "><i class="fa-solid fa-arrow-trend-up"></i>Nest </button>-->
<!--                                                                    </a>-->
<!--                                                                </div>-->
<!--                                                            @endif-->
<!---->
<!---->
<!--                                                            @if ($key == "Delivery")-->
<!--                                                                @if(Auth()->user()->is_admin || ($permissions && ($permissions->contains('permission_id', 129))))-->
<!--                                                                    @if($case->jobs[0]->assignee == null)-->
<!--                                                                        <div class="col-12 padding5px">-->
<!--                                                                            <a data-toggle="modal"-->
<!--                                                                               data-target="#myModal{{$case->id}} ">-->
<!--                                                                                <button type="button" data-dismiss="modal" class="btn btn-warning"> Assign to.. </button>-->
<!--                                                                            </a>-->
<!--                                                                        </div>-->
<!--                                                                    @else-->
<!--                                                                        <div class="col-12 padding5px">-->
<!--                                                                            <a data-toggle="modal"-->
<!--                                                                               data-target="#myModal{{$case->id}}">-->
<!--                                                                                <button type="button" data-dismiss="modal" class="btn btn-warning">Re-Assign.. </button>-->
<!--                                                                            </a>-->
<!--                                                                        </div>-->
<!--                                                                    @endif-->
<!--                                                                @endif-->
<!--                                                            @endif-->
<!--                                                            @if ($key == "Delivery")-->
<!--                                                                <div class="col-12 padding5px">-->
<!--                                                                    <a href="{{route('view-voucher',$case->id)}}">-->
<!--                                                                        <button type="button" class="btn btn-info "><i-->
<!--                                                                                class="fas fa-print"></i> Print Voucher </button>-->
<!--                                                                    </a>-->
<!--                                                                </div>-->
<!--                                                            @endif-->
<!--                                                            <div class="col-12 padding5px">-->
<!--                                                                <button type="button" class="btn btn-secondary "-->
<!--                                                                        data-dismiss="modal" style="width:100%">Cancel-->
<!--                                                                </button>-->
<!--                                                            </div>-->
<!--                                                        </div>-->
<!---->
<!---->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </form>-->
<!--                                    </div>-->
<!---->
<!--                                @endforeach-->
<!--                                &lt;!&ndash; Begin Active tab &ndash;&gt;-->
<!---->
<!---->
<!--                                </tbody>-->
<!--                            </table>-->
<!---->
<!---->
<!---->
<!--                        </div>-->
<!--                        {{&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;ACTIVE TABLE-&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45; &#45;&#45;}}-->
<!--                        {{&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;ACTIVE TABLE-&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45; &#45;&#45;}}-->
<!--                        {{&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;ACTIVE TABLE-&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45; &#45;&#45;}}-->
<!--                        {{&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;ACTIVE TABLE-&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45; &#45;&#45;}}-->
<!--                        {{&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;ACTIVE TABLE-&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45; &#45;&#45;}}-->
<!--                        {{&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;ACTIVE TABLE-&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45;&#45; &#45;&#45;}}-->
<!--                        <div tabindex="0" role="tabpanel" aria-labelledby="{{'active-'.$key .'label'}}"-->
<!--                             id="{{'active-'.$key}}" hidden>-->
<!---->
<!---->
<!--                                <table class=" activeTable sunriseTable" style="width:100%;">-->
<!--                                    <thead>-->
<!--                                    <tr>-->
<!--                                        <th>Doctor</th>-->
<!--                                        <th>Patient</th>-->
<!--                                        <th class="deliveryToHeader">Delivery Date</th>-->
<!--                                        <th class="assignedToHeader">Assigned To</th>-->
<!--                                        <th class="">#</th>-->
<!--                                        <th class="">Tags</th>-->
<!--                                    </tr>-->
<!--                                    </thead>-->
<!--                                    <tbody>-->
<!--                                    @foreach ($stage['activeCases'] as $case)-->
<!--                                        <tr class="clickable" style="color:{{$color}}" data-toggle="modal"-->
<!--                                            data-target="#confirmCompletion{{$key.$case->id}}">-->
<!--                                            @if ($key == "Finishing")-->
<!--                                                @php-->
<!--                                                    $notReadyA=false;-->
<!--                                                    $abutmentsReceived = $case->abutmentsReceived();-->
<!--                                                    if(!$case->allUnitsAtFinishing())-->
<!--                                                    $notReadyA=true;-->
<!--                                                @endphp-->
<!--                                            @endif-->
<!--                                            <td>-->
<!--                                                <p class="">{{$case->client->name}}</p>-->
<!--                                            </td>-->
<!--                                            <td>-->
<!--                                                <p class="">{{$case->patient_name}} @if ($key == "Finishing")-->
<!--                                                        @if($notReadyA) <span-->
<!--                                                            style="float:right;margin-left: 5px; line-height: 1;color:#ffa400;font-size: 9px;">-->
<!--                                                Not <br>-->
<!--                                                Ready-->
<!--                                            </span> @endif-->
<!---->
<!--                                                        @if(!$abutmentsReceived) <span-->
<!--                                                            style="float:right; line-height: 1;color:#ffa400;font-size: 9px;">-->
<!--                                                Abutment <br>-->
<!--                                                Missing-->
<!--                                            </span> @endif-->
<!--                                                    @endif-->
<!---->
<!--                                                </p>-->
<!--                                            </td>-->
<!--                                            <td class="">-->
<!--                                                <p class="">{{date_format(date_create($case->initDeliveryDate()),"d-M")}}</p>-->
<!--                                            </td>-->
<!--                                            <td>-->
<!--                                                <p class="">{{$case->jobs->where('stage',$stage["numericStage"])->first() ? ($case->jobs->where('stage',$stage["numericStage"])->first()->assignedTo? $case->jobs->where('stage',$stage["numericStage"])->first()->assignedTo->name_initials : "None") : "None"}}</p>-->
<!--                                            </td>-->
<!--                                            <td class="">-->
<!--                                                <p class="">{{$case->unitsAmount($stage["numericStage"])}}</p>-->
<!--                                            </td>-->
<!--                                            <td class="">-->
<!---->
<!--                                                @foreach($case->tags as $tag)-->
<!--                                                    <i title="{{$tag->originalTagRecord->text}}"-->
<!--                                                       style="color:{{$tag->originalTagRecord->color}}"-->
<!--                                                       class="{{$tag->originalTagRecord->icon}}  fa-lg"></i>-->
<!--                                                @endforeach-->
<!--                                            </td>-->
<!--                                        </tr>-->
<!--                                        &lt;!&ndash; End Active tab &ndash;&gt;-->
<!---->
<!--                                        &lt;!&ndash; External Milling Dialog &ndash;&gt;-->
<!--                                        @if ($key == "Milling")-->
<!--                                            <div class="modal fade" tabindex="-1" role="dialog"-->
<!--                                                 id="MEX{{$case->id}}">-->
<!--                                                <form action="{{route('externally-milled')}}"-->
<!--                                                      method="POST">-->
<!--                                                    @csrf-->
<!--                                                    <input type="hidden" name="case_id"-->
<!--                                                           value="{{$case->id}}">-->
<!--                                                    <div class="modal-dialog modal-dialog-centered" role="document">-->
<!--                                                        <div class="modal-content">-->
<!--                                                            <div class="modal-header">-->
<!--                                                                <h5 class="modal-title">Case milling-->
<!--                                                                    information</h5>-->
<!--                                                                <button type="button" class="close"-->
<!--                                                                        data-dismiss="modal"-->
<!--                                                                        aria-label="Close">-->
<!--                                                                    <span aria-hidden="true">&times;</span>-->
<!--                                                                </button>-->
<!--                                                            </div>-->
<!--                                                            <div class="modal-body">-->
<!--                                                                <div class="form-group row">-->
<!--                                                                    <div class="form-group col-6 lab_id">-->
<!--                                                                        <label for="lab_id">Lab-->
<!--                                                                            name: </label>-->
<!--                                                                        <select class="form-control"-->
<!--                                                                                id="lab_id"-->
<!--                                                                                name="lab_id">-->
<!--                                                                            <option selected>Select-->
<!--                                                                                your lab-->
<!--                                                                            </option>-->
<!--                                                                            @foreach($labs as $lab)-->
<!--                                                                                <option value="{{$lab->id}}">{{$lab->name}}</option>-->
<!--                                                                            @endforeach-->
<!--                                                                        </select>-->
<!--                                                                    </div>-->
<!--                                                                </div>-->
<!--                                                            </div>-->
<!--                                                            <div class="modal-footer fullBtnsWidth">-->
<!--                                                                <button type="submit"-->
<!--                                                                        class="btn btn-primary">Save-->
<!--                                                                    changes-->
<!--                                                                </button>-->
<!--                                                                <button type="button"-->
<!--                                                                        class="btn btn-secondary"-->
<!--                                                                        data-dismiss="modal">Close-->
<!--                                                                </button>-->
<!--                                                            </div>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                </form>-->
<!--                                            </div>-->
<!--                                        @endif-->
<!--                                        &lt;!&ndash; Active case actions Dialog &ndash;&gt;-->
<!--                                        <div class="modal fade" tabindex="-1" role="dialog"-->
<!--                                             id="confirmCompletion{{$key.$case->id}}">-->
<!--                                            <form action="{{$key == "delivery" ? route('delivery-accept', $case->id) : route('finish-case',['caseId'=> $case->id,'stage'=>$stage["numericStage"]] )}}"-->
<!--                                                  method="GET">-->
<!--                                                @csrf-->
<!--                                                <input type="hidden" name="case_id" value="{{$case->id}}">-->
<!--                                                <div class="modal-dialog modal-dialog-centered" role="document">-->
<!--                                                    <div class="modal-content">-->
<!--                                                        <div class="modal-header">-->
<!--                                                            <h5 class="modal-title">Case Completion</h5>-->
<!---->
<!--                                                            <button type="button" class="close" data-dismiss="modal"-->
<!--                                                                    aria-label="Close">-->
<!--                                                                <span aria-hidden="true">&times;</span>-->
<!--                                                            </button>-->
<!--                                                        </div>-->
<!--                                                        <div class="modal-body">-->
<!---->
<!--                                                            <div class="form-group row" style="margin-bottom: 0px">-->
<!--                                                                <div class="form-group col-6 "-->
<!--                                                                     style="margin-bottom: 0px">-->
<!--                                                                    <label for="doctor">Doctor: </label>-->
<!--                                                                    <h5 id="doctor"><b>{{$case->client->name}}</b></h5>-->
<!--                                                                </div>-->
<!--                                                                <div class="form-group col-6 "-->
<!--                                                                     style="margin-bottom: 0px">-->
<!--                                                                    <label for="pat">Patient: </label>-->
<!--                                                                    <h5 id="pat"><b>{{$case->patient_name}}</b></h5>-->
<!--                                                                </div>-->
<!--                                                            </div>-->
<!--                                                            <hr>-->
<!--                                                            <div class="form-group row">-->
<!--                                                                <div class=" col-12 ">-->
<!--                                                                    <label><b>Jobs:</b></label><br>-->
<!---->
<!---->
<!--                                                                    @foreach( $case->jobs->where('stage',$stage["numericStage"]) as $job)-->
<!---->
<!--                                                                        @php-->
<!--                                                                            $unit = explode(', ',$job->unit_num);-->
<!--                                                                        @endphp-->
<!---->
<!--                                                                        <span>{{$job->unit_num}}-->
<!--                                                                - {{$job->jobType->name ?? "No Job Type"}}-->
<!--                                                                - {{$job->material->name ?? "no material"}} {{$job->color =='0' ? "":" - " .$job->color}}-->
<!--                                                                            {{$job->style == 'None' ? "":" - " .$job->style}} {{isset($job->implantR) && $job->jobType->id ==6  ?( " - Implant Type: " . $job->implantR->name): "" }}-->
<!--                                                                <br>-->
<!--                                                                {{isset($job->abutmentR) && $job->jobType->id ==6  ?( " Abutment Type: " . $job->abutmentR->name): "" }} </span>-->
<!--                                                                    @endforeach-->
<!--                                                                </div>-->
<!--                                                            </div>-->
<!--                                                            @if(count($case->notes)>0)-->
<!--                                                                <hr>-->
<!--                                                                <label><b>Notes:</b></label><br>-->
<!--                                                                @foreach($case->notes as $note)-->
<!--                                                                    <div class="form-control"-->
<!--                                                                         style="height:fit-content;width:80%;background-color: #dcecfd59;margin-bottom: 5px; color:black;font-size:12px"-->
<!--                                                                         disabled>-->
<!---->
<!--                                                                        <span class="noteHeader">{{'['. substr( $note->created_at,0,16) . '] [' . $note->writtenBy->name_initials . '] : ' }}</span><br>-->
<!--                                                                        <span class="noteText">{{$note->note}}</span>-->
<!--                                                                    </div>-->
<!--                                                                @endforeach-->
<!--                                                            @endif-->
<!---->
<!--                                                        </div>-->
<!--                                                        <div class="modal-footer fullBtnsWidth">-->
<!--                                                            <div class="row btnsRow"-->
<!--                                                                 style=" margin-right: 0px; margin-left: 0px;width:100%">-->
<!--                                                                @if($key == "Delivery")-->
<!--                                                                    <div class="col-12 padding5px">-->
<!---->
<!--                                                                        <a class="dropdown-item" href="{{route('delivered-in-box',$case->id)}}">-->
<!--                                                                            <button type="button" class="btn btn-outline-info" style="width:100%">Delivered In Box</button></a>-->
<!--                                                                    </div>-->
<!--                                                                @endif-->
<!--                                                                <div class="col-3 padding5px">-->
<!--                                                                    <a href="{{route('view-case', ['id' => $case->id, 'stage' =>$stage["numericStage"]])}}">-->
<!--                                                                        <button type="button" class="btn btn-info ">-->
<!--                                                                            View-->
<!--                                                                        </button>-->
<!--                                                                    </a>-->
<!--                                                                </div>-->
<!---->
<!--                                                                <div class="col-6 padding5px">-->
<!--                                                                    @php-->
<!--                                                                        $isAdmin = Auth()->user()->is_admin;-->
<!--                                                                        $canBeFinished= true;-->
<!--                                                                        $isUserCase = false;-->
<!--                                                                        $canComplete = false;-->
<!--                                                                        if($case->jobs->where('stage',$stage["numericStage"])->first() && $case->jobs->where('stage',$stage["numericStage"])->first()->assignee == Auth()->user()->id)-->
<!--                                                                        {$canComplete = true;-->
<!--                                                                        $isUserCase= true; }-->
<!--                                                                        if($key == "Finishing")-->
<!--                                                                        if ($notReadyA || !$abutmentsReceived){-->
<!--                                                                        $canComplete= false;-->
<!--                                                                        $canBeFinished = false;-->
<!--                                                                        }-->
<!--                                                                    @endphp-->
<!--                                                                    @if ($isAdmin && $canBeFinished && !$isUserCase)-->
<!---->
<!--                                                                        <a class=""-->
<!--                                                                           href="{{route('complete-by-admin', ['id'=>$case->id,'stage'=>$stage["numericStage"]] )}}">-->
<!--                                                                            <button type="button" class="btn btn-success">Override Complete</button>-->
<!--                                                                        </a>-->
<!---->
<!---->
<!--                                                                    @else-->
<!--                                                                        <button type="submit" class="btn btn-success"-->
<!--                                                                                style="width:100%" {{$canComplete ? '' : 'disabled'}}>{{$canComplete ? 'Complete' : 'Case cannot be completed'}}</button>-->
<!--                                                                    @endif-->
<!--                                                                </div>-->
<!--                                                                <div class="col-3 padding5px"><a-->
<!--                                                                        href="{{route('edit-case-view',$case->id)}}">-->
<!--                                                                        <button type="button"-->
<!--                                                                                class="btn btn-warning " {{$canEditCase ? '' : 'disabled'}}>-->
<!--                                                                            Edit Case-->
<!--                                                                        </button>-->
<!--                                                                    </a></div>-->
<!---->
<!--                                                                @if ($key == "Milling")-->
<!--                                                                    <div class="col-12 padding5px">-->
<!--                                                                        <button type="button" class="btn btn-dark "-->
<!--                                                                                data-toggle="modal"-->
<!--                                                                                data-target="#MEX{{$case->id}}"-->
<!--                                                                                data-dismiss="modal" style="width:100%">-->
<!--                                                                            Externally Milled-->
<!--                                                                        </button>-->
<!--                                                                    </div>-->
<!--                                                            </div>-->
<!--                                                            @endif-->
<!--                                                            @if ($key == "Delivery")-->
<!---->
<!--                                                                <div class="col-12 padding5px">-->
<!---->
<!--                                                                    <a class="dropdown-item" href="{{route('view-voucher',$case->id)}}"> <button type="button" class="btn btn-outline-info">Print voucher</button></a>-->
<!--                                                                </div>-->
<!--                                                                @if($case->delivered_to_client == 1)-->
<!--                                                                    @if (Auth()->user()->is_admin || ($permissions && $permissions->contains('permission_id', 9)))-->
<!--                                                                        <div class="col-12 padding5px">-->
<!--                                                                            <a class="dropdown-item"-->
<!--                                                                               href="{{route('receive-voucher', $case->id )}}">-->
<!--                                                                                <button type="button" class="btn btn-outline-secondary">Receive Voucher</button>-->
<!--                                                                            </a>-->
<!---->
<!--                                                                        </div>-->
<!--                                                                    @endif-->
<!--                                                                @endif-->
<!--                                                            @endif-->
<!---->
<!--                                                            <div class="col-12 padding5px">-->
<!--                                                                <a class=""-->
<!--                                                                   href="{{route('reset-to-waiting', ['id'=>$case->id,'stage'=>$stage["numericStage"]] )}}">-->
<!--                                                                    <button type="button" class="btn btn-outline-danger">Reset To Waiting</button>-->
<!--                                                                </a>-->
<!--                                                            </div>-->
<!--                                                            <div class="col-12 padding5px">-->
<!--                                                                <button type="button" class="btn btn-secondary "-->
<!--                                                                        data-dismiss="modal" style="width:100%">Cancel-->
<!--                                                                </button>-->
<!--                                                            </div>-->
<!--                                                        </div>-->
<!---->
<!---->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                        </div>-->
<!--                                        </form>-->
<!--                                            /////////// v2 DIALOG-->
<!---->
<!--                                    @endforeach-->
<!--                                    </tbody>-->
<!--                                </table>-->
<!---->
<!---->
<!--                        </div>-->
<!--                    </div>-->
<!---->
<!---->
<!--                </div>-->
<!---->
<!---->
<!---->
<!--            @endforeach-->
<!--        </div>-->
<!---->
<!--        </main>-->
<!---->
<!--    </div>-->
<!---->
<!---->
<!--        @endsection-->
<!---->
<!---->
<!--        @push('js')-->
<!---->
<!--        <script>-->
<!--                $(document).ready(function() {-->
<!--                    // Listen for changes to the 'selected' class on the images-->
<!--                    $('.bb-outer-img').on('click', function() {-->
<!--                        // Check if any image has the 'selected' class-->
<!---->
<!--                        if ($('.bb-outer-img.selected').length === 1) {-->
<!--                            // Enable the button with animation-->
<!--                            $('.outer-devices-popup.blue').removeClass('disabled').addClass('enabled');-->
<!--                            $('.outer-devices-popup.blue').removeClass('disabled').addClass('enabled');-->
<!--                        } else {-->
<!--                            // Disable the button with animation-->
<!--                            $('.outer-devices-popup.blue').removeClass('enabled').addClass('disabled');-->
<!--                            $('.outer-devices-popup.blue').removeClass('enabled').addClass('disabled');-->
<!--                        }-->
<!--                    });-->
<!--                });-->
<!--            </script>-->
<!--        <script>-->
<!--            //para-->
<!--            function multiCBChang ed() {-->
<!--                console.log('multiCBChanged');-->
<!--                if ($('.multipleCB:checkbox:checked').length > 0) {-->
<!--                    if (!$('.receiveSelectBtn').is(":visible")) {-->
<!--                        $('.receiveSelectBtn').css({-->
<!--                            "opacity": "0",-->
<!--                            "display": "flex"-->
<!--                        }).show().animate({-->
<!--                            opacity: 1-->
<!--                        }, 500);-->
<!--                    }-->
<!--                } else {-->
<!--                    console.log('multiCBChanged else');-->
<!--                    $('.receiveSelectBtn').css({-->
<!--                        "opacity": "1",-->
<!--                        "display": "flex"-->
<!--                    }).animate({-->
<!--                        opacity: 0-->
<!--                    }, 500, function () {-->
<!--                        $('.receiveSelectBtn').css({-->
<!--                            "display": "none"-->
<!--                        });-->
<!--                    });-->
<!--                }-->
<!--            }-->
<!---->
<!--            function selectAll(ele) {-->
<!--                if ($(ele).prop('checked')) {-->
<!--                    $('.multipleCB').prop('checked', true);-->
<!--                } else {-->
<!--                    $('.multipleCB').prop('checked', false);-->
<!--                }-->
<!--                if ($('.multipleCB:checkbox').length > 0) {-->
<!--                    multiCBChanged();-->
<!--                }-->
<!--                else{-->
<!--                    console.log('select all didnt call multipleCheckboxChanged');-->
<!--                }-->
<!--            }-->
<!--        </script>-->
<!--        <script src="{{asset('https://cdn.jsdelivr.net/gh/htmlcssfreebies/macaw-tabs@v1.0.4/dist/js/macaw-tabs.js')}}"></script>-->
<!---->
<!--        <script>-->
<!--            $(document).ready(function() {-->
<!--                var tables2 = $('.sunriseTable');-->
<!--                if (tables2) {-->
<!--                    tables2.DataTable({-->
<!--                        "pageLength": 25,-->
<!--                        "searching": false,-->
<!--                        "lengthChange": false,-->
<!--                        "fixedHeader": true-->
<!---->
<!--                    });-->
<!--                    tables2.addClass("nowrap hover compact  stripe");-->
<!--                }-->
<!---->
<!---->
<!--                // Main Tabs-->
<!--                $(".macaw-aurora-tabs").macawTabs({-->
<!--                    autoVerticalOrientation: true,-->
<!--                    tabPanelTransitionLogic: true,-->
<!--                    tabPanelTransitionTimeoutDuration: 10-->
<!--                });-->
<!---->
<!--                // Nested Tabs-->
<!--                $(".macaw-silk-tabs").macawTabs({-->
<!--                    autoVerticalOrientation: false,-->
<!--                });-->
<!--            });-->
<!--        </script>-->
<!--        <script>-->
<!--            $(document).ready(function() {-->
<!--                tabcontent = document.getElementsByClassName("tabcontent");-->
<!--                for (i = 0; i < tabcontent.length; i++) {-->
<!--                    tabcontent[i].style.display = "none";-->
<!--                }-->
<!---->
<!--                // activate single outer tab =>-->
<!--                var activeOuter = Cookies.get("activeOuterTab");-->
<!--                var btn = $('#' + activeOuter);-->
<!--                btn.attr('aria-selected', true);-->
<!--                btn.removeAttr('tabindex');-->
<!--                $('#' + activeOuter + "label").addClass('active');-->
<!--                $('#' + activeOuter + "label").removeAttr('hidden');-->
<!---->
<!---->
<!--                // activate multiple inner tabs =>-->
<!--                for (let i = 1; i < 11; i++) {-->
<!--                    var activeInnerTab = Cookies.get('inner' + i);-->
<!--                    console.log("active inner : " + i + " => " + activeInnerTab);-->
<!--                    if (activeInnerTab == undefined) {-->
<!--                        continue;-->
<!--                    } else {-->
<!--                        var innerTabBtn = $("[id='" + activeInnerTab + "']");-->
<!--                        var innerTab = $("[aria-labelledby='" + activeInnerTab + "']");-->
<!--                        innerTab.addClass('active');-->
<!--                        innerTab.removeAttr('hidden');-->
<!--                        innerTabBtn.attr('aria-selected', true);-->
<!--                        innerTabBtn.removeAttr('tabindex');-->
<!--                    }-->
<!--                }-->
<!--            });-->
<!---->
<!--            $("[id^='active']").click(function(e) {-->
<!---->
<!--                Cookies.set('inner' + $(this).attr('href'), $(this).attr('id'));-->
<!--                console.log("set cookie for : " + 'inner' + $(this).attr('href') + ' => ' + 'inner' + $(this).attr('id'));-->
<!--            });-->
<!---->
<!--            function setInnerTab(btnElement) {-->
<!--                Cookies.set('inner' + $(btnElement).attr('href'), $(btnElement).attr('id'));-->
<!--                console.log("set cookie for : " + 'inner' + $(btnElement).attr('href') + ' => ' + 'inner' + $(btnElement).attr('id'));-->
<!---->
<!--            }-->
<!---->
<!--            function setOuterTab(btnElement) {-->
<!--                Cookies.set('activeOuterTab', btnElement.id);-->
<!--                console.log("set outer cookie for : " + 'activeOuterTab' + ' =>'+ btnElement.id);-->
<!---->
<!--            }-->
<!--        </script>-->
<!---->
<!--        <script>-->
<!--            function selectImage(element) {-->
<!--                console.log('selectImage called ');-->
<!--                if (element.classList.contains('selected')) {-->
<!--                    console.log('selected image');-->
<!--                    element.classList.remove('selected');-->
<!--                } else {-->
<!--                    console.log('else');-->
<!--                    const images = document.querySelectorAll('.blackbox-image-container');-->
<!--                    images.forEach(img => img.classList.remove('selected'));-->
<!--                    element.classList.add('selected');-->
<!--                }-->
<!--            }-->
<!---->
<!---->
<!--        </script>-->
<!--        @endpush-->
<!---->
<!---->-->

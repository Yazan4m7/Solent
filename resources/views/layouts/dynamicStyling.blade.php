<style>
    body,
    html, .wrapper {
        height: 100%;
    }

    <!-- tooltop -->
     .tooltipY {
         position: relative;
         display: inline-block;
         border-bottom: 1px dotted black;
     }

    .tooltipY .tooltiptextY {
        visibility: hidden;
        width: auto;
        background-color: #0e3539;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        bottom: 100%;
        left: 50%;
        margin-left: -60px;

        /* Fade in tooltip - takes 1 second to go from 0% to 100% opac: */
        opacity: 0;
        transition: opacity 1s;
    }

    .tooltipY:hover .tooltiptextY {
        visibility: visible;
        opacity: 1;
    }
    <!-- End tooltop -->


    <!-- Sidebar -->
    a {
        color: {{config('site_vars.sidebarFirstColor')}} ;
    }
    a:hover, a:focus {
        color: {{config('site_vars.sidebarSecondColor')}};
    }
    .sidebar, .off-canvas-sidebar {

        linear-gradient(318deg, #0d71ac 0%, #002829 100%);
       }
    .main-panel {
        border-top: 2px solid {{config('site_vars.sidebarFirstColor')}};
    }

    <!-- Buttons -->
    .deleteBtn{
        padding:{{config('site_vars.delBtnHorizPadding')}}px , {{config('site_vars.delBtnVertPadding')}}px;
        background-color:{{config('site_vars.delBtnVertPadding')}};
    }
    .btn-primary {
    {{--background: #37b44a;--}}
    {{--background-image: -webkit-linear-gradient(0deg,  {{config('site_vars.primaryBtnColor1')}} 0%, {{config('site_vars.primaryBtnColor2')}} 100%);--}}
    {{--background-image: -o-linear-gradient(0deg,  {{config('site_vars.primaryBtnColor1')}} 0%, {{config('site_vars.primaryBtnColor2')}} 100%);--}}
    {{--background-image: -moz-linear-gradient(0deg,  {{config('site_vars.primaryBtnColor1')}} 0%, {{config('site_vars.primaryBtnColor2')}} 100%);--}}
    {{--background-image: linear-gradient(0deg,  {{config('site_vars.primaryBtnColor1')}} 0%, {{config('site_vars.primaryBtnColor2')}} 100%);--}}
{{--}--}}
}
    .btn-secondary {
        {{--background-color: {{config('site_vars.secondryBtnColor')}};--}}
    }
    /*.dropdown-toggle {*/
        /*-webkit-text-size-adjust: 100%;*/
        /*-webkit-tap-highlight-color: transparent;*/
        /*--blue: #886ab5;*/
        /*--indigo: #6610f2;*/
        /*--purple: #6f42c1;*/
        /*--pink: #e83e8c;*/
        /*--red: #fd3995;*/
        /*--orange: #ffc241;*/
        /*--yellow: #ffc241;*/
        /*--green: #1dc9b7;*/
        /*--teal: #20c997;*/
        /*--cyan: #17a2b8;*/
        /*--white: #fff;*/
        /*--gray: #868e96;*/
        /*--gray-dark: #495057;*/
        /*--primary: #886ab5;*/
        /*--secondary: #868e96;*/
        /*--success: #1dc9b7;*/
        /*--info: #2196F3;*/
        /*--warning: #ffc241;*/
        /*--danger: #fd3995;*/
        /*--light: #fff;*/
        /*--dark: #505050;*/
        /*--font-family-sans-serif: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";*/
        /*--font-family-monospace: SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;*/
        /*--theme-primary: #886ab5;*/
        /*--theme-secondary: #868e96;*/
        /*--theme-success: #1dc9b7;*/
        /*--theme-info: #2196F3;*/
        /*--theme-warning: #ffc241;*/
        /*--theme-danger: #fd3995;*/
        /*--theme-light: #fff;*/
        /*--theme-dark: #505050;*/
        /*--theme-primary-50: #ccbfdf;*/
        /*--theme-primary-100: #beaed7;*/
        /*--theme-primary-200: #b19dce;*/
        /*--theme-primary-300: #a38cc6;*/
        /*--theme-primary-400: #967bbd;*/
        /*--theme-primary-500: #886ab5;*/
        /*--theme-primary-600: #7a59ad;*/
        /*--theme-primary-700: #6e4e9e;*/
        /*--theme-primary-800: #62468d;*/
        /*--theme-primary-900: #563d7c;*/
        /*--theme-success-50: #7aece0;*/
        /*--theme-success-100: #63e9db;*/
        /*--theme-success-200: #4de5d5;*/
        /*--theme-success-300: #37e2d0;*/
        /*--theme-success-400: #21dfcb;*/
        /*--theme-success-500: #1dc9b7;*/
        /*--theme-success-600: #1ab3a3;*/
        /*--theme-success-700: #179c8e;*/
        /*--theme-success-800: #13867a;*/
        /*--theme-success-900: #107066;*/
        /*--theme-info-50: #9acffa;*/
        /*--theme-info-100: #82c4f8;*/
        /*--theme-info-200: #6ab8f7;*/
        /*--theme-info-300: #51adf6;*/
        /*--theme-info-400: #39a1f4;*/
        /*--theme-info-500: #2196F3;*/
        /*--theme-info-600: #0d8aee;*/
        /*--theme-info-700: #0c7cd5;*/
        /*--theme-info-800: #0a6ebd;*/
        /*--theme-info-900: #0960a5;*/
        /*--theme-warning-50: #ffebc1;*/
        /*--theme-warning-100: #ffe3a7;*/
        /*--theme-warning-200: #ffdb8e;*/
        /*--theme-warning-300: #ffd274;*/
        /*--theme-warning-400: #ffca5b;*/
        /*--theme-warning-500: #ffc241;*/
        /*--theme-warning-600: #ffba28;*/
        /*--theme-warning-700: #ffb20e;*/
        /*--theme-warning-800: #f4a500;*/
        /*--theme-warning-900: #da9400;*/
        /*--theme-danger-50: #feb7d9;*/
        /*--theme-danger-100: #fe9ecb;*/
        /*--theme-danger-200: #fe85be;*/
        /*--theme-danger-300: #fe6bb0;*/
        /*--theme-danger-400: #fd52a3;*/
        /*--theme-danger-500: #fd3995;*/
        /*--theme-danger-600: #fd2087;*/
        /*--theme-danger-700: #fc077a;*/
        /*--theme-danger-800: #e7026e;*/
        /*--theme-danger-900: #ce0262;*/
        /*--theme-fusion-50: #909090;*/
        /*--theme-fusion-100: #838383;*/
        /*--theme-fusion-200: #767676;*/
        /*--theme-fusion-300: dimgray;*/
        /*--theme-fusion-400: #5d5d5d;*/
        /*--theme-fusion-500: #505050;*/
        /*--theme-fusion-600: #434343;*/
        /*--theme-fusion-700: #363636;*/
        /*--theme-fusion-800: #2a2a2a;*/
        /*--theme-fusion-900: #1d1d1d;*/
        /*--breakpoint-xs: 0;*/
        /*--breakpoint-sm: 576px;*/
        /*--breakpoint-md: 768px;*/
        /*--breakpoint-lg: 992px;*/
        /*--breakpoint-xl: 1399px;*/
        /*direction: ltr;*/
        /*-webkit-box-direction: normal;*/
        /*box-sizing: border-box;*/
        /*font-family: inherit;*/
        /*overflow: visible;*/
        /*text-transform: none;*/
        /*touch-action: manipulation;*/
        /*-webkit-appearance: button;*/
        /*display: inline-block;*/
        /*font-weight: 400;*/
        /*text-align: center;*/
        /*vertical-align: middle;*/
        /*user-select: none;*/
        /*border: 1px solid transparent;*/
        /*padding: .5rem 1.125rem;*/
        /*font-size: .8125rem;*/
        /*line-height: 1.47;*/
        /*border-radius: 4px;*/
        /*transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out,-webkit-box-shadow .15s ease-in-out;*/
        /*color: #fff;*/
        /*background-color: #868e96;*/
        /*border-color: #868e96;*/
        /*outline: 0;*/
        /*box-shadow: 0 2px 6px 0 rgba(134,142,150,.5);*/
        /*margin: 0 .375rem 1rem 0!important;*/
        /*cursor: pointer;*/
    /*}*/
    .btn:hover, .btn:focus, .btn-primary:hover, .btn-primary:focus {
        {{--background-color: {{config('site_vars.btnOnHoverColor')}}  !important;--}}
        {{--background-image: linear-gradient(0deg, {{config('site_vars.btnOnHoverColor')}} 0%, {{config('site_vars.btnOnHoverColor')}} 100%);--}}
    }

</style>

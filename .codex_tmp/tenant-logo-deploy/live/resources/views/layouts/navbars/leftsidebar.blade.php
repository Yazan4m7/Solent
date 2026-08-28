<div class="sidebar">
    <style>
        body.white-content .wrapper > .sidebar,
        .wrapper > .sidebar,
        .sidebar {
            display: flex !important;
            flex-direction: column !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 280px !important;
            height: 100vh !important;
            margin-left: 0 !important;
            margin-top: 0 !important;
            background: linear-gradient(180deg, #0c1426 0%, #111c32 58%, #0a1221 100%) !important;
            box-shadow: 18px 0 48px rgba(15, 23, 42, 0.08) !important;
            border-right: 1px solid var(--sidebar-border) !important;
            border-radius: 0 !important;
        }

        body.white-content .wrapper > .sidebar::before,
        body.white-content .wrapper > .sidebar::after,
        .wrapper > .sidebar::before,
        .wrapper > .sidebar::after,
        .sidebar::before,
        .sidebar::after {
            display: none !important;
        }

        body.white-content .wrapper > .sidebar .sidebar-wrapper,
        .wrapper > .sidebar .sidebar-wrapper,
        .sidebar .sidebar-wrapper {
            background: transparent !important;
            flex: 1 1 auto !important;
            height: auto !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding: 10px 12px 18px !important;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .sidebar .sidebar-wrapper::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        body.white-content .wrapper > .sidebar .korvion-sidebar-brand,
        .wrapper > .sidebar .korvion-sidebar-brand,
        .korvion-sidebar-brand {
            min-height: 82px;
            padding: 0 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(8, 15, 29, 0.38) !important;
            border-bottom: 1px solid var(--sidebar-brand-border);
        }

        .sidebar .nav {
            display: flex;
            flex-direction: column;
            gap: 3px;
            margin: 0 !important;
            padding: 0 !important;
        }

        .sidebar .nav > li,
        .sidebar .nav > div {
            margin: 0 !important;
            width: 100%;
        }

        .korvion-sidebar-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            max-width: 100%;
            margin: 0 auto;
            text-decoration: none !important;
        }

        .korvion-sidebar-logo-full {
            display: block;
            width: min(154px, 100%);
            height: auto;
            max-height: 50px;
            object-fit: contain;
            filter: none !important;
        }

        .solent-sidebar-close {
            align-items: center;
            justify-content: center;
            display: none;
            width: 40px;
            height: 40px;
            padding: 0;
            border: 1px solid rgba(255, 255, 255, 0.11);
            border-radius: 13px;
            background: rgba(255, 255, 255, 0.06);
            color: #ffffff;
        }

        .solent-sidebar-close svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-width: 2;
        }

        .solent-sidebar-link-copy {
            min-width: 0;
        }

        .solent-sidebar-quick-action {
            margin: 5px 2px 13px;
        }

        .solent-sidebar-quick-action > a {
            align-items: center;
            display: grid;
            grid-template-columns: 36px minmax(0, 1fr);
            gap: 10px;
            min-height: 52px;
            padding: 8px 10px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.045);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.035);
            color: #ffffff !important;
            text-decoration: none !important;
            transition: background-color 0.16s ease, border-color 0.16s ease, transform 0.16s ease;
        }

        .solent-sidebar-quick-action > a:hover,
        .solent-sidebar-quick-action > a:focus {
            border-color: rgba(129, 140, 248, 0.28);
            background: rgba(99, 102, 241, 0.09);
            transform: translateY(-1px);
        }

        .solent-sidebar-quick-action.active > a {
            border-color: rgba(129, 140, 248, 0.34);
            background: rgba(99, 102, 241, 0.12);
            box-shadow: inset 3px 0 0 #818cf8;
        }

        .solent-sidebar-quick-action__icon {
            align-items: center;
            justify-content: center;
            display: inline-flex;
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: rgba(129, 140, 248, 0.12);
        }

        .solent-sidebar-quick-action__icon svg {
            width: 19px !important;
            height: 19px !important;
            color: #ffffff !important;
            fill: none !important;
            stroke: currentColor !important;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.9;
        }

        .solent-sidebar-quick-action__copy strong,
        .solent-sidebar-quick-action__copy small {
            display: block;
        }

        .solent-sidebar-quick-action__copy strong {
            color: #ffffff;
            font-size: 13px;
            font-weight: 800;
        }

        .solent-sidebar-quick-action__copy small {
            margin-top: 2px;
            color: rgba(226, 232, 240, 0.5);
            font-size: 9px;
            font-weight: 700;
        }

        html[dir="rtl"] .solent-sidebar-quick-action.active > a {
            box-shadow: inset -3px 0 0 #818cf8;
        }

        .sidebar .nav li a i,
        .sidebar .nav li svg {
            width: 28px !important;
            text-align: center !important;
            flex-shrink: 0 !important;
            margin-right: 0 !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 20px !important;
        }

        .sidebar .nav li svg.solent-sidebar-icon {
            fill: none;
            height: 22px !important;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.8;
        }

        body.white-content .wrapper > .sidebar .nav li > a,
        .wrapper > .sidebar .nav li > a,
        .sidebar .nav li > a {
            display: flex;
            align-items: center;
            position: relative;
            gap: 11px;
            min-height: 50px;
            margin: 0 !important;
            padding: 10px 14px !important;
            border: 1px solid transparent !important;
            border-radius: 15px !important;
            color: var(--sidebar-link) !important;
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease, transform 0.15s ease;
        }

        body.white-content .wrapper > .sidebar .nav li > a span,
        body.white-content .wrapper > .sidebar .nav li > a p,
        .wrapper > .sidebar .nav li > a span,
        .wrapper > .sidebar .nav li > a p,
        .sidebar .nav li > a span,
        .sidebar .nav li > a p {
            margin: 0;
            display: inline;
            color: var(--sidebar-link) !important;
            font-weight: 600;
            transition: color 0.15s ease;
        }

        body.white-content .wrapper > .sidebar .nav li > a,
        body.white-content .wrapper > .sidebar .nav li > a i,
        body.white-content .wrapper > .sidebar .nav li > a svg,
        .wrapper > .sidebar .nav li > a,
        .wrapper > .sidebar .nav li > a i,
        .wrapper > .sidebar .nav li > a svg,
        .sidebar .nav li > a,
        .sidebar .nav li > a i,
        .sidebar .nav li > a svg {
            color: var(--sidebar-link) !important;
            transition: color 0.15s ease;
        }

        .sidebar .nav li.active > a,
        .sidebar .nav li.active > a:hover,
        .sidebar .nav li.active > a:focus {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.23), rgba(99, 102, 241, 0.08)) !important;
            border-color: rgba(129, 140, 248, 0.27) !important;
            color: var(--surface) !important;
            font-weight: 700 !important;
        }

        .sidebar .solent-sidebar-nav > li.active > a::before {
            position: absolute;
            top: 12px;
            bottom: 12px;
            left: -13px;
            width: 3px;
            border-radius: 0 999px 999px 0;
            background: linear-gradient(180deg, #818cf8, #38bdf8);
            box-shadow: 0 0 14px rgba(99, 102, 241, 0.7);
            content: "";
        }

        .sidebar .nav li.active > a span,
        .sidebar .nav li.active > a p {
            color: var(--surface) !important;
            font-weight: 500 !important;
        }

        .sidebar .nav li.active > a i,
        .sidebar .nav li.active > a svg {
            color: var(--accent-lt) !important;
            font-weight: 500 !important;
        }

        .sidebar .nav li:not(.active):hover > a {
            background: var(--sidebar-hover) !important;
            color: var(--surface) !important;
            transform: translateX(2px);
        }

        .sidebar .solent-sidebar-icon-shell {
            align-items: center;
            justify-content: center;
            display: inline-flex !important;
            flex: 0 0 34px;
            width: 34px;
            height: 34px;
            border: 1px solid rgba(255, 255, 255, 0.055);
            border-radius: 11px;
            background: rgba(255, 255, 255, 0.045);
            color: var(--sidebar-link) !important;
        }

        .sidebar .nav li.active > a .solent-sidebar-icon-shell {
            border-color: rgba(129, 140, 248, 0.2);
            background: rgba(99, 102, 241, 0.19);
            color: #a5b4fc !important;
        }

        .sidebar .solent-sidebar-icon-shell svg.solent-sidebar-icon {
            width: 19px !important;
            height: 19px !important;
        }

        .sidebar .solent-sidebar-link-copy {
            display: flex !important;
            flex: 1 1 auto;
            flex-direction: column;
            justify-content: center;
            gap: 2px;
        }

        .sidebar .solent-sidebar-link-copy > span,
        .sidebar .solent-sidebar-link-copy > small {
            display: block !important;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sidebar .solent-sidebar-link-copy > span {
            color: inherit !important;
            font-size: 12px;
            font-weight: 700 !important;
            line-height: 1.2;
        }

        .sidebar .solent-sidebar-link-copy > small {
            color: rgba(226, 232, 240, 0.43);
            font-size: 9px;
            font-weight: 600;
            line-height: 1.2;
        }

        .sidebar .nav li.active > a .solent-sidebar-link-copy > small {
            color: rgba(224, 231, 255, 0.62);
        }

        .sidebar .solent-sidebar-section-label {
            color: var(--sidebar-link);
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.12em;
            opacity: 0.62;
            padding: 11px 12px 5px;
            text-transform: uppercase;
        }

        .sidebar .solent-sidebar-divider {
            border-top: 1px solid var(--sidebar-border);
            height: 1px;
            margin: 8px 8px 4px !important;
        }

        .sidebar .solent-sidebar-collapse-toggle .caret {
            margin-left: auto;
            transition: transform 0.18s ease;
        }

        .sidebar .solent-sidebar-collapse-toggle[aria-expanded="true"] .caret {
            transform: rotate(180deg);
        }

        .sidebar .solent-sidebar-submenu {
            gap: 2px;
            position: relative;
            padding: 5px 0 7px 25px !important;
        }

        .sidebar .solent-sidebar-submenu::before {
            position: absolute;
            top: 7px;
            bottom: 9px;
            left: 16px;
            width: 1px;
            background: rgba(148, 163, 184, 0.18);
            content: "";
        }

        .sidebar .solent-sidebar-submenu li > a {
            min-height: 34px;
            padding: 7px 10px !important;
            border-radius: 11px !important;
        }

        .sidebar .solent-sidebar-submenu li > a span {
            font-size: 11px;
            font-weight: 600 !important;
        }

        .sidebar .solent-sidebar-submenu li > .solent-sidebar-disabled-link {
            align-items: center;
            display: flex;
            min-height: 34px;
            padding: 7px 10px;
            position: relative;
            border-radius: 11px;
            color: var(--sidebar-link);
            cursor: not-allowed;
            opacity: 0.42;
            user-select: none;
        }

        .sidebar .solent-sidebar-submenu li > .solent-sidebar-disabled-link span {
            font-size: 11px;
            font-weight: 600;
        }

        .sidebar .solent-sidebar-submenu li > .solent-sidebar-disabled-link::before {
            position: absolute;
            top: 50%;
            left: -12px;
            width: 7px;
            height: 7px;
            border: 2px solid #111c32;
            border-radius: 999px;
            background: #475569;
            content: '';
            transform: translateY(-50%);
        }

        .sidebar .solent-sidebar-submenu li > a::before {
            position: absolute;
            top: 50%;
            left: -12px;
            width: 7px;
            height: 7px;
            border: 2px solid #111c32;
            border-radius: 999px;
            background: #64748b;
            content: "";
            transform: translateY(-50%);
        }

        .sidebar .solent-sidebar-submenu li.active > a::before {
            background: #818cf8;
            box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.12);
        }

        .sidebar .solent-sidebar-submenu li > a svg.solent-sidebar-icon {
            height: 18px !important;
            width: 22px !important;
        }

        .sidebar hr {
            border-color: var(--sidebar-border) !important;
        }

        .solent-sidebar-account {
            flex: 0 0 auto;
            margin-top: auto;
            padding: 8px 12px 10px;
            border-top: 1px solid var(--sidebar-border);
            background: linear-gradient(180deg, rgba(15, 23, 42, 0), rgba(15, 23, 42, 0.72));
        }

        .solent-sidebar-account .dropdown,
        .solent-sidebar-account .dropup {
            width: 100%;
        }

        .solent-sidebar-profile {
            align-items: center;
            display: grid !important;
            grid-template-columns: 34px minmax(0, 1fr) 14px;
            gap: 8px;
            width: 100%;
            min-height: 48px;
            padding: 6px 8px !important;
            border: 1px solid var(--sidebar-border);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.055);
            color: var(--surface) !important;
            text-decoration: none !important;
        }

        .solent-sidebar-profile:hover,
        .solent-sidebar-profile:focus {
            background: rgba(255, 255, 255, 0.09);
            color: var(--surface) !important;
        }

        .solent-sidebar-profile::after {
            display: none;
        }

        .solent-sidebar-profile__avatar {
            align-items: center;
            display: inline-flex;
            justify-content: center;
            width: 34px;
            height: 34px;
            border: 2px solid rgba(255, 255, 255, 0.12);
            border-radius: 11px;
            background: linear-gradient(135deg, var(--accent), var(--accent-lt));
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.03em;
        }

        .solent-sidebar-profile__meta {
            min-width: 0;
        }

        .solent-sidebar-profile__name,
        .solent-sidebar-profile__role {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .solent-sidebar-profile__name {
            color: var(--surface);
            font-size: 13px;
            font-weight: 900;
            line-height: 1.2;
        }

        .solent-sidebar-profile__role {
            color: var(--sidebar-link);
            font-size: 11px;
            font-weight: 700;
            margin-top: 2px;
        }

        .solent-sidebar-profile__chevron {
            color: var(--sidebar-link);
            fill: none;
            height: 14px;
            justify-self: end;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 2.2;
            width: 14px;
        }

        .solent-sidebar-account .dropdown-menu {
            width: 100%;
            min-width: 0;
            margin-bottom: 8px;
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 20px 44px rgba(15, 23, 42, 0.22);
            overflow: hidden;
        }

        .solent-sidebar-account .solent-sidebar-language-menu-item {
            padding: 6px;
            border-bottom: 1px solid var(--border);
        }

        .solent-sidebar-account .dropdown-menu .solent-sidebar-language {
            justify-content: flex-start;
            width: 100%;
            min-height: 42px;
            margin: 0;
            border: 0;
            border-radius: 9px;
            background: transparent;
            color: var(--text-1) !important;
        }

        .solent-sidebar-account .dropdown-menu .solent-sidebar-language:hover,
        .solent-sidebar-account .dropdown-menu .solent-sidebar-language:focus {
            background: var(--surface-raised);
        }

        html[dir="rtl"] .solent-sidebar-account .dropdown-menu,
        html[dir="rtl"] .solent-sidebar-account .dropdown-menu .dropdown-item,
        html[dir="rtl"] .solent-sidebar-account .dropdown-menu .solent-sidebar-language {
            direction: rtl !important;
            text-align: right !important;
        }

        html[dir="ltr"] .solent-sidebar-account .dropdown-menu,
        html[dir="ltr"] .solent-sidebar-account .dropdown-menu .dropdown-item,
        html[dir="ltr"] .solent-sidebar-account .dropdown-menu .solent-sidebar-language {
            direction: ltr !important;
            text-align: left !important;
        }

        .solent-floating-topbar {
            align-items: center;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            position: absolute;
            top: 18px;
            right: 24px;
            left: 24px;
            z-index: 1000;
            pointer-events: none;
        }

        .solent-floating-search,
        .solent-mobile-sidebar-toggle {
            pointer-events: auto;
        }

        .solent-floating-search {
            display: flex;
            justify-content: flex-end;
            margin: 0;
            width: min(336px, 48vw);
        }

        .solent-floating-topbar #wrapp {
            align-items: center;
            display: flex;
            height: 44px;
            margin: 0;
            padding: 0;
            position: relative;
            width: 100%;
        }

        .solent-floating-topbar #wrapp .SBF2 {
            align-items: center;
            display: flex;
            gap: 10px;
            width: 100%;
            height: 44px;
            padding: 0 16px;
            border: 1px solid var(--border);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.94);

            backdrop-filter: blur(14px);
            box-sizing: border-box;
        }

        .solent-floating-topbar #wrapp input[type="text"] {
            flex: 1 1 auto;
            min-width: 0;
            height: 100%;
            padding: 0;
            border: 0;
            outline: 0;
            background: transparent;
            color: var(--text-1);
            font-family: var(--font-family-sans-serif, "Cairo", sans-serif);
            font-size: 14px;
            line-height: 1;
        }

        .solent-floating-topbar #wrapp input[type="text"]::placeholder {
            color: var(--text-2);
        }

        .solent-floating-topbar #wrapp #search_submit {
            order: -1;
            flex: 0 0 16px;
            width: 16px;
            height: 16px;
            border: 0;
            background: url("data:image/svg+xml,%3Csvg width='18' height='18' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10.75 18.5C6.47 18.5 3 15.03 3 10.75S6.47 3 10.75 3s7.75 3.47 7.75 7.75a7.72 7.72 0 0 1-1.68 4.82l3.31 3.31-1.25 1.25-3.31-3.31a7.72 7.72 0 0 1-4.82 1.68Zm0-1.8a5.95 5.95 0 1 0 0-11.9 5.95 5.95 0 0 0 0 11.9Z' fill='%2364748B'/%3E%3C/svg%3E") center center / 16px auto no-repeat;
            opacity: 0.72;
            pointer-events: none;
            text-indent: -10000px;
        }

        .solent-mobile-sidebar-toggle {
            align-items: center;
            justify-content: center;
            display: none;
            width: 44px;
            height: 44px;
            padding: 0 !important;
            border: 1px solid var(--border) !important;
            border-radius: 14px !important;
            background: rgba(255, 255, 255, 0.94) !important;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.1);
        }

        .solent-mobile-sidebar-toggle .navbar-toggler-bar {
            display: block;
            width: 18px;
            height: 2px;
            margin: 2px 0;
            border-radius: 999px;
            background: var(--text-1);
        }

        .main-panel {
            margin-left: 280px !important;
            width: calc(100% - 280px) !important;
            min-height: 100vh;
            background: var(--surface-raised) !important;
            border-top: 0 !important;
        }

        body.white-content,
        body.white-content .main-panel,
        body.white-content .content,
        body.white-content .main-panel > .content {
            background: var(--surface-raised) !important;
        }

        .main-panel > .content {
            min-height: 100vh;
            padding: 86px 24px 24px !important;
            background: var(--surface-raised) !important;
        }

        /* Keep the default navigation fully visible on standard laptop screens. */
        @media screen and (min-width: 992px) and (max-height: 850px) {
            body.white-content .wrapper > .sidebar .korvion-sidebar-brand,
            .wrapper > .sidebar .korvion-sidebar-brand,
            .korvion-sidebar-brand {
                min-height: 68px;
            }

            .korvion-sidebar-logo-full {
                max-height: 42px;
            }

            body.white-content .wrapper > .sidebar .sidebar-wrapper,
            .wrapper > .sidebar .sidebar-wrapper,
            .sidebar .sidebar-wrapper {
                padding: 6px 10px 8px !important;
            }

            .solent-sidebar-quick-action {
                margin: 2px 0 7px;
            }

            .solent-sidebar-quick-action > a {
                min-height: 44px;
                padding: 5px 8px;
            }

            .solent-sidebar-quick-action__icon {
                width: 32px;
                height: 32px;
                flex-basis: 32px;
            }

            .sidebar .nav {
                gap: 1px;
            }

            body.white-content .wrapper > .sidebar .nav li > a,
            .wrapper > .sidebar .nav li > a,
            .sidebar .nav li > a {
                min-height: 42px;
                padding: 5px 10px !important;
            }

            .sidebar .solent-sidebar-section-label {
                padding: 6px 10px 2px;
            }

            .solent-sidebar-account {
                padding: 5px 10px 7px;
            }

            .solent-sidebar-account > .dropdown > a,
            .solent-sidebar-account > .dropup > a {
                min-height: 44px;
                padding: 4px 7px !important;
            }
        }

        @media screen and (min-width: 992px) and (max-height: 700px) {
            body.white-content .wrapper > .sidebar .korvion-sidebar-brand,
            .wrapper > .sidebar .korvion-sidebar-brand,
            .korvion-sidebar-brand {
                min-height: 60px;
            }

            .korvion-sidebar-logo-full {
                max-height: 38px;
            }

            .solent-sidebar-quick-action > a {
                min-height: 40px;
            }

            .solent-sidebar-quick-action__copy small,
            .sidebar .solent-sidebar-link-copy small {
                display: none;
            }

            body.white-content .wrapper > .sidebar .nav li > a,
            .wrapper > .sidebar .nav li > a,
            .sidebar .nav li > a {
                min-height: 38px;
                padding-top: 3px !important;
                padding-bottom: 3px !important;
            }

            .solent-sidebar-account {
                padding-top: 3px;
                padding-bottom: 5px;
            }

            .solent-sidebar-account > .dropdown > a,
            .solent-sidebar-account > .dropup > a {
                min-height: 40px;
            }
        }

        @media screen and (max-width: 991px) {
            .overlay.active {
                left: 280px;
                width: calc(100% - 280px);
                z-index: 1030;
            }

            .sidebar {
                z-index: 1031 !important;
                transform: translate3d(-290px, 0, 0) !important;
                transition: transform 0.35s ease !important;
            }

            .nav-open .sidebar {
                transform: translate3d(0, 0, 0) !important;
                box-shadow: 0 10px 35px var(--shadow-1) !important;
            }

            .solent-sidebar-close {
                display: inline-flex;
            }

            .sidebar .sidebar-wrapper {
                height: auto !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch;
            }

            .main-panel {
                margin-left: 0 !important;
                width: 100% !important;
            }

            .main-panel > .content {
                padding: 82px 14px 18px !important;
            }

            .solent-floating-topbar {
                left: 12px;
                right: 12px;
                top: 12px;
                justify-content: space-between;
            }

            .solent-mobile-sidebar-toggle {
                display: inline-flex;
                flex-direction: column;
                flex: 0 0 44px;
            }

            .solent-floating-search {
                width: min(336px, calc(100vw - 72px));
            }
        }

        @media screen and (max-width: 575.98px) {
            .solent-floating-topbar {
                gap: 8px;
            }

            .solent-floating-topbar #wrapp,
            .solent-floating-topbar #wrapp .SBF2 {
                height: 40px;
            }

            .solent-mobile-sidebar-toggle {
                width: 40px;
                height: 40px;
                flex-basis: 40px;
            }

            .solent-floating-search {
                width: calc(100vw - 68px);
            }
        }

        html[dir="rtl"] body.white-content .wrapper > .sidebar,
        html[dir="rtl"] .wrapper > .sidebar,
        html[dir="rtl"] .sidebar {
            right: 0 !important;
            left: auto !important;
            margin-right: 0 !important;
            box-shadow: -18px 0 48px rgba(15, 23, 42, 0.08) !important;
            border-right: 0 !important;
            border-left: 1px solid var(--sidebar-border) !important;
        }

        html[dir="rtl"] .main-panel {
            margin-right: 280px !important;
            margin-left: 0 !important;
        }

        html[dir="rtl"] .solent-floating-topbar {
            right: 24px;
            left: 24px;
        }

        html[dir="rtl"] .sidebar .solent-sidebar-nav > li.active > a::before {
            right: -12px;
            left: auto;
        }

        html[dir="rtl"] .sidebar .solent-sidebar-submenu {
            margin-right: 24px !important;
            margin-left: 0 !important;
            padding-right: 19px !important;
            padding-left: 0 !important;
        }

        html[dir="rtl"] .sidebar .solent-sidebar-submenu::before {
            right: 0;
            left: auto;
        }

        @media screen and (max-width: 991px) {
            html[dir="rtl"] .overlay.active {
                right: 280px;
                left: auto;
            }

            html[dir="rtl"] .sidebar {
                transform: translate3d(290px, 0, 0) !important;
            }

            html[dir="rtl"].nav-open .sidebar {
                transform: translate3d(0, 0, 0) !important;
            }

            html[dir="rtl"] .main-panel {
                margin-right: 0 !important;
            }

            html[dir="rtl"] .solent-floating-topbar {
                right: 12px;
                left: 12px;
            }
        }
    </style>
    @php
        $permissions = Cache::get('user' . Auth()->user()->id);
        $isPlatformAdminPage = !empty($platformAdminPage);
        $sidebarBrandName = $brandingName ?? config('branding.defaults.name', 'Solent');
        $sidebarBrandMark = asset($brandingSidebarMarkPath ?? config('branding.defaults.sidebar_mark_path'));
        $sidebarUser = Auth()->user();
        $sidebarProfileName = trim(implode(' ', array_filter([
            $sidebarUser->first_name ?? null,
            $sidebarUser->last_name ?? null,
        ])));
        $sidebarProfileName = $sidebarProfileName !== '' ? $sidebarProfileName : ($sidebarUser->name ?? $sidebarUser->email ?? __('User'));
        $sidebarProfileInitials = mb_strtoupper(mb_substr(preg_replace('/\s+/', '', $sidebarProfileName), 0, 2));
        $sidebarProfileInitials = $sidebarProfileInitials !== '' ? $sidebarProfileInitials : 'U';
        $sidebarProfileRole = ($sidebarUser->is_admin ?? false) ? __('Administrator') : __('Regular User');
        $currentRoute = Route::currentRouteName();
        $canAccessSidebar = static function (int $permissionId) use ($permissions, $sidebarUser): bool {
            return (bool) ($sidebarUser->is_admin ?? false)
                || ($permissions && $permissions->contains('permission_id', $permissionId));
        };
        $reportsRoutes = ['num-of-units-report', 'job-types-report', 'QC-report', 'repeats-report', 'materials-report'];
        $billingRoutes = ['invoices-index', 'payments-index'];
        $settingsRoutes = ['material-index', 'job-type-index', 'users-index', 'implants-index', 'tags-index', 'f-causes-index'];
        $reportsExpanded = in_array($currentRoute, $reportsRoutes, true);
        $billingExpanded = in_array($currentRoute, $billingRoutes, true);
        $settingsExpanded = in_array($currentRoute, $settingsRoutes, true);
        $showWorkspace = $canAccessSidebar(103)
            || $canAccessSidebar(113)
            || $canAccessSidebar(109)
            || $canAccessSidebar(107);
        $showOverview = $canAccessSidebar(123) || $canAccessSidebar(106);
        $showBilling = $canAccessSidebar(104) || $canAccessSidebar(121);
        $showManagement = $canAccessSidebar(120) || $showBilling || (bool) ($sidebarUser->is_admin ?? false);
        $ui = trans('ui.dom');
    @endphp
    <div class="korvion-sidebar-brand">
        <a class="korvion-sidebar-logo" href="{{ $isPlatformAdminPage ? route('system.tenants.index') : route('home') }}"
            aria-label="{{ $isPlatformAdminPage ? 'Platform administration' : $sidebarBrandName . ' home' }}">
            @if ($isPlatformAdminPage)
                <strong style="color:#fff;font-size:15px;line-height:1.25;">Platform<br>Administration</strong>
            @else
                <img class="korvion-sidebar-logo-full" src="{{ $sidebarBrandMark }}" alt="{{ $sidebarBrandName }} logo">
            @endif
        </a>
        <button type="button" class="solent-sidebar-close" aria-label="{{ $ui['Close menu'] ?? 'Close menu' }}">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"></path></svg>
        </button>
    </div>
    <div class="sidebar-wrapper">
        @if ($isPlatformAdminPage)
            <ul class="nav solent-sidebar-nav">
                <li class="solent-sidebar-section-label"><span>Platform</span></li>
                <li class="active">
                    <a href="{{ route('system.tenants.index') }}">
                        <span class="solent-sidebar-icon-shell">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'users'])</span>
                        <span class="solent-sidebar-link-copy"><span>Tenants</span><small>Workspaces and provisioning</small></span>
                    </a>
                </li>
            </ul>
        @else
        @if ($canAccessSidebar(100))
            <div class="solent-sidebar-quick-action{{ $currentRoute === 'new-case-view' ? ' active' : '' }}">
                <a href="{{ route('new-case-view') }}">
                    <span class="solent-sidebar-quick-action__icon">
                        @include('layouts.navbars.partials.sidebar-icon', ['name' => 'plus-square'])
                    </span>
                    <span class="solent-sidebar-quick-action__copy">
                        <strong>{{ $ui['Create new case'] ?? 'Create new case' }}</strong>
                        <small>{{ $ui['Start a production order'] ?? 'Start a production order' }}</small>
                    </span>
                </a>
            </div>
        @endif
        <ul class="nav solent-sidebar-nav">
            @if ($showOverview)
                <li class="solent-sidebar-section-label"><span>{{ $ui['Overview'] ?? 'Overview' }}</span></li>
            @endif

            @if ($canAccessSidebar(123))
                <li class="{{ $currentRoute === 'home' ? 'active' : '' }}">
                    <a href="{{ route('home') }}">
                        <span class="solent-sidebar-icon-shell">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'home'])</span>
                        <span class="solent-sidebar-link-copy"><span>{{ $ui['Home'] ?? 'Home' }}</span><small>{{ $ui['Business snapshot'] ?? 'Business snapshot' }}</small></span>
                    </a>
                </li>
            @endif

            @if ($canAccessSidebar(106))
                <li class="{{ $currentRoute === 'admin-dashboard-v2' ? 'active' : '' }}">
                    <a href="{{ route('admin-dashboard-v2') }}">
                        <span class="solent-sidebar-icon-shell">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'dashboard'])</span>
                        <span class="solent-sidebar-link-copy"><span>{{ $ui['Operations'] ?? 'Operations' }}</span><small>{{ $ui['Live production overview'] ?? 'Live production overview' }}</small></span>
                    </a>
                </li>
            @endif

            @if ($showWorkspace)
                <li class="solent-sidebar-divider" role="separator"></li>
                <li class="solent-sidebar-section-label"><span>{{ $ui['Workspace'] ?? 'Workspace' }}</span></li>
            @endif

            @if ($canAccessSidebar(103))
                <li class="{{ $currentRoute === 'cases-index' ? 'active' : '' }}">
                    <a href="{{ route('cases-index') }}">
                        <span class="solent-sidebar-icon-shell">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'case'])</span>
                        <span class="solent-sidebar-link-copy"><span>{{ $ui['Cases'] ?? 'Cases' }}</span><small>{{ $ui['Search and manage orders'] ?? 'Search and manage orders' }}</small></span>
                    </a>
                </li>
            @endif

            @if ($canAccessSidebar(113))
                <li class="{{ $currentRoute === 'view-cases-monitor' ? 'active' : '' }}">
                    <a href="{{ route('view-cases-monitor') }}">
                        <span class="solent-sidebar-icon-shell">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'monitor'])</span>
                        <span class="solent-sidebar-link-copy"><span>{{ $ui['Live monitor'] ?? 'Live monitor' }}</span><small>{{ $ui['Track active workflow'] ?? 'Track active workflow' }}</small></span>
                    </a>
                </li>
            @endif

            @if ($canAccessSidebar(109))
                <li class="{{ $currentRoute === 'delivery-schedule' ? 'active' : '' }}">
                    <a href="{{ route('delivery-schedule') }}">
                        <span class="solent-sidebar-icon-shell">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'clock'])</span>
                        <span class="solent-sidebar-link-copy"><span>{{ $ui['Deliveries'] ?? 'Deliveries' }}</span><small>{{ $ui['Schedule and dispatch'] ?? 'Schedule and dispatch' }}</small></span>
                    </a>
                </li>
            @endif

            @if ($canAccessSidebar(107))
                <li class="{{ $currentRoute === 'clients-index' ? 'active' : '' }}">
                    <a href="{{ route('clients-index') }}">
                        <span class="solent-sidebar-icon-shell">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'users'])</span>
                        <span class="solent-sidebar-link-copy"><span>{{ $ui['Clients'] ?? 'Clients' }}</span><small>{{ $ui['Doctors and clinics'] ?? 'Doctors and clinics' }}</small></span>
                    </a>
                </li>
            @endif

            @if ($showManagement)
                <li class="solent-sidebar-divider" role="separator"></li>
                <li class="solent-sidebar-section-label"><span>{{ $ui['Management'] ?? 'Management' }}</span></li>
            @endif

            @if ($canAccessSidebar(120))
                <li class="{{ $reportsExpanded ? 'active' : '' }}">
                    <a class="solent-sidebar-collapse-toggle" data-toggle="collapse" href="#sidebarReports"
                        aria-controls="sidebarReports" aria-expanded="{{ $reportsExpanded ? 'true' : 'false' }}">
                        <span class="solent-sidebar-icon-shell">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'chart'])</span>
                        <span class="solent-sidebar-link-copy"><span>{{ $ui['Reports'] ?? 'Reports' }}</span><small>{{ $ui['Performance insights'] ?? 'Performance insights' }}</small></span>
                        <b class="caret"></b>
                    </a>
                    <div class="collapse{{ $reportsExpanded ? ' show' : '' }}" id="sidebarReports">
                        <ul class="nav solent-sidebar-submenu">
                            <li class="{{ $currentRoute === 'num-of-units-report' ? 'active' : '' }}">
                                <a href="{{ route('num-of-units-report') }}">
                                    <span>{{ $ui['Units Summary'] ?? 'Units Summary' }}</span>
                                </a>
                            </li>
                            <li class="{{ $currentRoute === 'job-types-report' ? 'active' : '' }}">
                                <a href="{{ route('job-types-report') }}">
                                    <span>Job Mix</span>
                                </a>
                            </li>
                            <li class="{{ $currentRoute === 'QC-report' ? 'active' : '' }}">
                                <a href="{{ route('QC-report') }}">
                                    <span>{{ $ui['QC Summary'] ?? 'QC Summary' }}</span>
                                </a>
                            </li>
                            <li class="{{ $currentRoute === 'repeats-report' ? 'active' : '' }}">
                                <a href="{{ route('repeats-report') }}">
                                    <span>{{ $ui['Remakes'] ?? 'Remakes' }}</span>
                                </a>
                            </li>
                            <li class="{{ $currentRoute === 'materials-report' ? 'active' : '' }}">
                                <a href="{{ route('materials-report') }}">
                                    <span>Materials Usage</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            @if ($showBilling)
                <li class="{{ $billingExpanded ? 'active' : '' }}">
                    <a class="solent-sidebar-collapse-toggle" data-toggle="collapse" href="#sidebarBilling"
                        aria-controls="sidebarBilling" aria-expanded="{{ $billingExpanded ? 'true' : 'false' }}">
                        <span class="solent-sidebar-icon-shell">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'billing'])</span>
                        <span class="solent-sidebar-link-copy"><span>{{ $ui['Billing'] ?? 'Billing' }}</span><small>{{ $ui['Invoices and payments'] ?? 'Invoices and payments' }}</small></span>
                        <b class="caret"></b>
                    </a>
                    <div class="collapse{{ $billingExpanded ? ' show' : '' }}" id="sidebarBilling">
                        <ul class="nav solent-sidebar-submenu">
                            @if ($canAccessSidebar(104))
                                <li class="{{ $currentRoute === 'invoices-index' ? 'active' : '' }}">
                                    <a href="{{ route('invoices-index') }}">
                                        <span>{{ $ui['Invoices'] ?? 'Invoices' }}</span>
                                    </a>
                                </li>
                            @endif
                            @if ($canAccessSidebar(121))
                                <li class="{{ $currentRoute === 'payments-index' ? 'active' : '' }}">
                                    <a href="{{ route('payments-index') }}">
                                        <span>{{ $ui['Payments'] ?? 'Payments' }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            @if ($sidebarUser->is_admin)
                <li class="{{ $settingsExpanded ? 'active' : '' }}">
                    <a class="solent-sidebar-collapse-toggle" data-toggle="collapse" href="#sidebarSettings"
                        aria-controls="sidebarSettings" aria-expanded="{{ $settingsExpanded ? 'true' : 'false' }}">
                        <span class="solent-sidebar-icon-shell">@include('layouts.navbars.partials.sidebar-icon', ['name' => 'settings'])</span>
                        <span class="solent-sidebar-link-copy"><span>{{ $ui['Configuration'] ?? 'Configuration' }}</span><small>{{ $ui['Lab setup and access'] ?? 'Lab setup and access' }}</small></span>
                        <b class="caret"></b>
                    </a>
                    <div class="collapse{{ $settingsExpanded ? ' show' : '' }}" id="sidebarSettings">
                        <ul class="nav solent-sidebar-submenu">
                            <li class="{{ $currentRoute === 'material-index' ? 'active' : '' }}">
                                <a href="{{ route('material-index') }}">
                                    <span>Materials</span>
                                </a>
                            </li>
                            <li class="{{ $currentRoute === 'job-type-index' ? 'active' : '' }}">
                                <a href="{{ route('job-type-index') }}">
                                    <span>Job Types</span>
                                </a>
                            </li>
                            <li class="{{ $currentRoute === 'users-index' ? 'active' : '' }}">
                                <a href="{{ route('users-index') }}">
                                    <span>{{ $ui['Users'] ?? 'Users' }}</span>
                                </a>
                            </li>
                            <li class="{{ $currentRoute === 'implants-index' ? 'active' : '' }}">
                                <a href="{{ route('implants-index') }}">
                                    <span>{{ $ui['Implants'] ?? 'Implants' }}</span>
                                </a>
                            </li>
                            <li class="{{ $currentRoute === 'tags-index' ? 'active' : '' }}">
                                <a href="{{ route('tags-index') }}">
                                    <span>{{ $ui['Tags'] ?? 'Tags' }}</span>
                                </a>
                            </li>
                            <li class="{{ $currentRoute === 'f-causes-index' ? 'active' : '' }}">
                                <a href="{{ route('f-causes-index') }}">
                                    <span>{{ $ui['Failure Reasons'] ?? 'Failure Reasons' }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif
        </ul>
        @endif
    </div>
    <div class="solent-sidebar-account">
        <div class="dropup">
            <a href="#" class="dropdown-toggle solent-sidebar-profile" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="solent-sidebar-profile__avatar" aria-hidden="true">{{ $sidebarProfileInitials }}</span>
                <span class="solent-sidebar-profile__meta">
                    <span class="solent-sidebar-profile__name">{{ $sidebarProfileName }}</span>
                    <span class="solent-sidebar-profile__role">{{ $sidebarProfileRole }}</span>
                </span>

            </a>
            <ul class="dropdown-menu">
                <li class="solent-sidebar-language-menu-item">
                    <x-language-switcher class="solent-sidebar-language" />
                </li>
                <li>
                    <a href="{{ route('logout') }}" class="dropdown-item"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ $ui['Log out'] ?? 'Log out' }}</a>
                </li>
            </ul>
        </div>
    </div>
</div>

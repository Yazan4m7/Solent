@extends('layouts.app', ['pageSlug' => 'New Case'])

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.imagesloader.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/elegant-dashboard.css') }}" />
    <style>
        .create-shell {
            --cc-accent: #6366f1;
            --cc-accent-dark: #4f46e5;
            --cc-accent-soft: #eef2ff;
            --cc-border: #dfe4ec;
            --cc-muted: #64748b;
            --cc-text: #0f172a;
            --cc-surface: #ffffff;
            --cc-surface-soft: #f8fafc;
            max-width: 1500px;
            margin: 0 auto;
            padding: 20px 0 40px;
        }

        .cc-page-intro {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 24px;
            margin-bottom: 18px;
            padding: 24px 26px;
            color: #ffffff;
            background:
                radial-gradient(circle at 88% 12%, rgba(99, 102, 241, 0.34), transparent 30%),
                linear-gradient(135deg, #0f172a 0%, #172033 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            box-shadow: 0 16px 38px rgba(15, 23, 42, 0.16);
        }

        .cc-page-intro__eyebrow,
        .cc-section-kicker {
            display: block;
            margin-bottom: 5px;
            color: var(--cc-accent);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .cc-page-intro__eyebrow {
            color: #c7d2fe;
        }

        body.white-content .cc-page-intro h1 {
            margin: 0;
            color: #ffffff !important;
            font-size: clamp(24px, 3vw, 34px);
            font-weight: 800;
            letter-spacing: -0.035em;
            line-height: 1.12;
        }

        .cc-page-intro p {
            max-width: 680px;
            margin: 8px 0 0;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            line-height: 1.55;
        }

        .cc-case-reference {
            display: grid;
            min-width: 190px;
            gap: 2px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.09);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 14px;
        }

        .cc-page-intro__meta {
            display: grid;
            gap: 10px;
            min-width: 230px;
        }

        .cc-page-actions {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 8px;
        }

        .cc-page-actions .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 42px;
            margin: 0;
            padding: 9px 14px;
            border-radius: 11px;
            font-size: 13px;
            font-weight: 800;
        }

        .cc-page-actions .btn-light {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.08) !important;
            border-color: rgba(255, 255, 255, 0.18) !important;
        }

        .cc-page-actions .btn-light:hover {
            background: rgba(255, 255, 255, 0.16) !important;
        }

        .cc-case-reference span,
        .cc-case-reference small {
            color: rgba(255, 255, 255, 0.62);
            font-size: 11px;
        }

        .cc-case-reference strong {
            color: #ffffff;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.02em;
        }

        .cc-workspace {
            display: grid;
            grid-template-columns: minmax(0, 1.8fr) minmax(300px, 0.72fr);
            align-items: start;
            gap: 18px;
        }

        .cc-primary-column,
        .cc-secondary-column {
            display: grid;
            min-width: 0;
            gap: 18px;
        }

        .cc-secondary-column {
            position: sticky;
            top: 82px;
        }

        .cc-card {
            min-width: 0;
            margin: 0;
            padding: 0;
            background: var(--cc-surface);
            border: 1px solid var(--cc-border);
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            overflow: visible;
        }

        .cc-section-header {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: start;
            gap: 13px;
            padding: 18px 20px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
            border-bottom: 1px solid var(--cc-border);
            border-radius: 16px 16px 0 0;
        }

        .cc-section-header--compact {
            grid-template-columns: auto minmax(0, 1fr);
            padding: 16px 18px;
        }

        .cc-section-header h2 {
            margin: 0;
            color: var(--cc-text);
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1.25;
        }

        .cc-section-header p {
            margin: 5px 0 0;
            color: var(--cc-muted);
            font-size: 12px;
            line-height: 1.5;
        }

        .cc-step-number,
        .cc-section-icon {
            display: inline-grid;
            width: 34px;
            height: 34px;
            place-items: center;
            color: var(--cc-accent-dark);
            font-size: 12px;
            font-weight: 800;
            background: var(--cc-accent-soft);
            border: 1px solid #d9ddff;
            border-radius: 10px;
        }

        .cc-required-badge {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 5px 9px;
            color: #166534;
            font-size: 11px;
            font-weight: 800;
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            border-radius: 999px;
        }

        .cc-card-body {
            padding: 20px;
        }

        #unitsDialog .modal-dialog {
            max-width: 400px;
            width: calc(100% - 24px);
            min-height: calc(100% - 1rem);
            display: flex;
            align-items: center;
            margin: 0.5rem auto;
        }

        #unitsDialog .modal-content {

            overflow: hidden;
            background: linear-gradient(145deg, #ffffff 0%, #fbfcff 100%);
            border: 1px solid rgba(255, 255, 255, 0.94);
            border-radius: 14px;
            box-shadow: 0 18px 44px rgba(17, 21, 30, 0.18);
        }

        #unitsDialog .modal-body {
            padding: 0px 0px 80px 0 !important;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        #unitsDialog .main-body {

            width: 380px !important;
            max-width: 100%;
            margin: 0 auto;
        }

        @media screen and (max-width: 576px) {
            #unitsDialog .modal-dialog {
                max-width: 420px;
                width: calc(100% - 12px);
                margin: 0.75rem auto;
            }

            #unitsDialog .main-body {
                transform: scale(0.9);
                transform-origin: top center;
            }
        }

        .cc-field-grid {
            display: grid;
            gap: 16px;
        }

        .cc-field-grid--details {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .cc-field {
            display: flex;
            min-width: 0;
            flex-direction: column;
        }

        .cc-field--wide {
            grid-column: 1 / -1;
        }

        .cc-label,
        .cc-field label {
            display: block;
            margin-bottom: 6px;
            color: #475569;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .cc-label span {
            color: #dc2626;
            display: inline-block;
            font-size: 2em;
            line-height: 0.5;
            vertical-align: -0.08em;
        }

        .cc-field-help {
            margin-top: 6px;
            color: var(--cc-muted);
            font-size: 11px;
            line-height: 1.4;
        }

        .cc-input {
            width: 100%;
            margin: 0;
            padding: 10px 12px;
            color: var(--cc-text);
            background: var(--cc-surface);
            border: 1px solid #cfd6e1;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
        }

        .cc-input:focus,
        .cc-input:active,
        .cc-input:focus-visible {
            border-color: var(--cc-accent);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.16);
            outline: none;
        }

        .slctUnitsBtn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 44px !important;
            min-height: 44px;
            margin: 0;
            padding: 10px 12px !important;
            overflow: hidden;
            line-height: 1.2;
            white-space: nowrap !important;
        }

        .slctUnitsBtn:focus,
        .slctUnitsBtn:focus-visible {
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.18) !important;
        }

        .cc-ghost-btn {
            color: var(--cc-accent-dark) !important;
            background: var(--cc-accent-soft) !important;
            border: 1px dashed #a5b4fc !important;
            border-radius: 10px;
        }

        .cc-ghost-btn:hover {
            color: #ffffff !important;
            background: var(--cc-accent-dark) !important;
            border-color: var(--cc-accent-dark) !important;
        }

        .create-shell .bootstrap-select>.dropdown-toggle,
        .create-shell .bootstrap-select>.dropdown-toggle:focus,
        .create-shell .bootstrap-select>.dropdown-toggle:active {
            height: 44px;
            padding: 10px 12px;
            color: var(--cc-text);
            background: var(--cc-surface);
            border: 1px solid #cfd6e1;
            border-radius: 10px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
            outline: none !important;
        }

        .create-shell .bootstrap-select {
            width: 100% !important;
        }

        .create-shell .bootstrap-select.cc-input {
            padding: 0;
            border: 0;
            background: transparent;
            box-shadow: none;
        }

        .create-shell .bootstrap-select.show>.dropdown-toggle,
        .create-shell .bootstrap-select>.dropdown-toggle:focus-visible {
            background: var(--cc-surface);
            border-color: var(--cc-accent);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.16);
        }

        .create-shell .bootstrap-select .filter-option-inner-inner {
            font-weight: 600;
        }

        .create-shell .form-control.cc-input:not(textarea),
        .create-shell .form-control:not(textarea),
        .create-shell select.cc-input {
            height: 44px;
        }

        .cc-card-body--jobs {
            padding: 18px;
            background: var(--cc-surface-soft);
            border-radius: 0 0 16px 16px;
        }

        .cc-job-list {
            display: flex;
            counter-reset: cc-job;
            flex-direction: column;
            gap: 12px;
        }

        .cc-job-block {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            align-items: end;
            gap: 12px;
            position: relative;
            width: 100%;
            margin: 0 !important;
            padding: 42px 16px 16px;
            background: var(--cc-surface);
            border: 1px solid var(--cc-border);
            border-radius: 14px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
            counter-increment: cc-job;
            overflow: visible;
        }

        .cc-job-block::before {
            position: absolute;
            top: 14px;
            left: 16px;
            color: var(--cc-text);
            font-size: 12px;
            font-weight: 800;
            content: "Job " counter(cc-job);
        }

        .cc-job-grid,
        .cc-job-field {
            width: 100%;
            min-width: 0;
        }

        .cc-job-grid .col-md-2,
        .cc-job-grid .col-md-3,
        .cc-job-grid .col-md-12,
        .cc-abutment-row [class*="col-md-"] {
            width: auto;
            max-width: none;
            padding: 0;
            flex: none;
        }

        /* Adapted from Uiverse.io by reglobby for the existing Single/Bridge contract. */
        .cc-style-toggle.toggle-container {
            position: relative;
            display: flex;
            width: min(150px, 100%);
            flex-direction: column;
            align-items: center;
            perspective: 800px;
            z-index: 5;
        }

        .cc-style-toggle .toggle-wrap {
            position: relative;
            width: 100%;
            height: 52px;
            margin: 0;
            transform-style: preserve-3d;
        }

        .cc-style-toggle .toggle-input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
        }

        .cc-style-toggle .toggle-track {
            position: absolute;
            inset: 0;
            overflow: hidden;
            cursor: pointer;
            background: rgba(30, 64, 175, 0.09);
            border: 1px solid rgba(79, 70, 229, 0.32);
            border-radius: 999px;
            box-shadow: inset 0 0 10px rgba(15, 23, 42, 0.08), 0 4px 12px rgba(79, 70, 229, 0.08);
            transition: border-color 0.3s ease, background 0.3s ease, box-shadow 0.3s ease;
        }

        .cc-style-toggle .toggle-track::after {
            position: absolute;
            top: 2px;
            right: 8px;
            left: 8px;
            height: 7px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.18), rgba(56, 189, 248, 0.08));
            content: "";
            filter: blur(1px);
        }

        .cc-style-toggle .track-lines {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 1px;
            overflow: hidden;
            transform: translateY(-50%);
        }

        .cc-style-toggle .track-line {
            width: 100%;
            height: 100%;
            background: repeating-linear-gradient(90deg, rgba(99, 102, 241, 0.2) 0 5px, transparent 5px 15px);
            animation: cc-track-line-move 3s linear infinite;
        }

        @keyframes cc-track-line-move {
            to { transform: translateX(20px); }
        }

        .cc-style-toggle .toggle-thumb,
        .cc-style-toggle .energy-rings {
            position: absolute;
            top: 4px;
            left: 4px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            transition: left 0.38s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .cc-style-toggle .toggle-thumb {
            z-index: 2;
            overflow: hidden;
            background: radial-gradient(circle, #818cf8 0%, #4f46e5 72%);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.28), inset 0 0 10px rgba(255, 255, 255, 0.28);
        }

        .cc-style-toggle .thumb-core,
        .cc-style-toggle .thumb-inner {
            position: absolute;
            top: 50%;
            left: 50%;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }

        .cc-style-toggle .thumb-core {
            width: 31px;
            height: 31px;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 14px rgba(129, 140, 248, 0.72);
        }

        .cc-style-toggle .thumb-inner {
            width: 18px;
            height: 18px;
            background: rgba(255, 255, 255, 0.82);
            box-shadow: 0 0 9px rgba(255, 255, 255, 0.7);
            animation: cc-toggle-pulse 2s infinite alternate;
        }

        @keyframes cc-toggle-pulse {
            from { transform: translate(-50%, -50%) scale(0.9); opacity: 0.68; }
            to { transform: translate(-50%, -50%) scale(1.08); opacity: 1; }
        }

        .cc-style-toggle .thumb-scan {
            position: absolute;
            top: -4px;
            left: 0;
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.72);
            filter: blur(1px);
            animation: cc-thumb-scan 2s linear infinite;
        }

        @keyframes cc-thumb-scan {
            to { top: 48px; opacity: 0; }
        }

        .cc-style-toggle .toggle-data {
            position: absolute;
            inset: 0;
            z-index: 1;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .cc-style-toggle .data-text {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            transition: opacity 0.25s ease;
        }

        .cc-style-toggle .data-text.off { right: 13px; color: #4338ca; }
        .cc-style-toggle .data-text.on { left: 13px; color: #047857; opacity: 0; }

        .cc-style-toggle .status-indicator {
            position: absolute;
            top: 22px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #6366f1;
            box-shadow: 0 0 8px rgba(99, 102, 241, 0.65);
        }

        .cc-style-toggle .status-indicator.off { right: 8px; }
        .cc-style-toggle .status-indicator.on { left: 8px; background: #10b981; opacity: 0; }

        .cc-style-toggle .energy-rings { pointer-events: none; z-index: 1; }
        .cc-style-toggle .energy-ring {
            position: absolute;
            inset: 4px;
            border: 1px solid transparent;
            border-top-color: rgba(255, 255, 255, 0.55);
            border-radius: 50%;
            animation: cc-toggle-spin 2.5s linear infinite;
        }
        .cc-style-toggle .energy-ring:nth-child(2) { inset: 9px; animation-direction: reverse; }
        .cc-style-toggle .energy-ring:nth-child(3) { inset: 14px; animation-duration: 1.4s; }

        @keyframes cc-toggle-spin { to { transform: rotate(360deg); } }

        .cc-style-toggle .toggle-reflection,
        .cc-style-toggle .holo-glow {
            position: absolute;
            inset: 0;
            pointer-events: none;
            border-radius: 999px;
        }

        .cc-style-toggle .toggle-reflection {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.18), transparent 42%);
        }

        .cc-style-toggle .holo-glow {
            background: radial-gradient(ellipse at center, rgba(99, 102, 241, 0.12), transparent 70%);
            filter: blur(8px);
        }

        .cc-style-toggle .toggle-input:focus-visible + .toggle-track {
            outline: 3px solid rgba(99, 102, 241, 0.24);
            outline-offset: 2px;
        }

        .cc-style-toggle .toggle-input:checked + .toggle-track {
            background: rgba(16, 185, 129, 0.09);
            border-color: rgba(5, 150, 105, 0.35);
            box-shadow: inset 0 0 10px rgba(15, 23, 42, 0.08), 0 4px 12px rgba(16, 185, 129, 0.1);
        }

        .cc-style-toggle .toggle-input:checked + .toggle-track .toggle-thumb,
        .cc-style-toggle .toggle-input:checked + .toggle-track .energy-rings {
            left: calc(100% - 48px);
        }

        .cc-style-toggle .toggle-input:checked + .toggle-track .toggle-thumb {
            background: radial-gradient(circle, #34d399 0%, #059669 72%);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.28), inset 0 0 10px rgba(255, 255, 255, 0.28);
        }

        .cc-style-toggle .toggle-input:checked + .toggle-track .data-text.off,
        .cc-style-toggle .toggle-input:checked + .toggle-track .status-indicator.off { opacity: 0; }
        .cc-style-toggle .toggle-input:checked + .toggle-track .data-text.on,
        .cc-style-toggle .toggle-input:checked + .toggle-track .status-indicator.on { opacity: 1; }

        .cc-style-toggle .thumb-particles,
        .cc-style-toggle .interface-lines {
            display: none;
        }

        /* The original animated switch was visually oversized while its labels were only 10px. */
        .cc-style-toggle.toggle-container {
            width: min(136px, 100%);
            direction: ltr;
            perspective: none;
        }

        .cc-style-toggle .toggle-wrap {
            height: 44px;
            transform-style: flat;
        }

        .cc-style-toggle .toggle-track {
            background: #eef2f7;
            border-color: #d8dee9;
            border-radius: 12px;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.05);
        }

        .cc-style-toggle .toggle-track::after,
        .cc-style-toggle .track-lines,
        .cc-style-toggle .energy-rings,
        .cc-style-toggle .status-indicator,
        .cc-style-toggle .toggle-reflection,
        .cc-style-toggle .holo-glow,
        .cc-style-toggle .thumb-core,
        .cc-style-toggle .thumb-inner,
        .cc-style-toggle .thumb-scan {
            display: none;
        }

        .cc-style-toggle .toggle-thumb {
            top: 3px;
            bottom: 3px;
            left: 3px;
            width: calc(50% - 3px);
            height: auto;
            background: var(--cc-accent-dark);
            border: 0;
            border-radius: 9px;
            box-shadow: 0 3px 8px rgba(79, 70, 229, 0.24);
            transition: left 0.22s ease, background 0.22s ease, box-shadow 0.22s ease;
        }

        .cc-style-toggle .toggle-data {
            z-index: 3;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: center;
            pointer-events: none;
            font-size: 13px;
            letter-spacing: 0;
            text-transform: none;
        }

        .cc-style-toggle .data-text {
            position: static;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            transform: none;
            opacity: 1;
            transition: color 0.2s ease;
        }

        .cc-style-toggle .data-text.off {
            color: #ffffff;
        }

        .cc-style-toggle .data-text.on {
            color: #64748b;
        }

        .cc-style-toggle .toggle-input:checked + .toggle-track {
            background: #eef2f7;
            border-color: #cfd8e3;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.05);
        }

        .cc-style-toggle .toggle-input:checked + .toggle-track .toggle-thumb {
            left: 50%;
            background: #059669;
            box-shadow: 0 3px 8px rgba(5, 150, 105, 0.24);
        }

        .cc-style-toggle .toggle-input:checked + .toggle-track .data-text.off {
            color: #64748b;
            opacity: 1;
        }

        .cc-style-toggle .toggle-input:checked + .toggle-track .data-text.on {
            color: #ffffff;
            opacity: 1;
        }

        .cc-job-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 0;
            max-width: 100%;
            min-height: 44px;
        }

        .cc-delete-job,
        .deleteBtn2 {
            display: inline-grid;
            width: 40px;
            height: 40px;
            place-items: center;
            margin: 0 !important;
            padding: 0 !important;
            color: #b91c1c !important;
            background: #fff1f2 !important;
            border: 1px solid #fecdd3 !important;
            border-radius: 10px !important;
            box-sizing: border-box;
            flex: 0 0 40px;
        }

        .cc-delete-job:hover,
        .deleteBtn2:hover {
            color: #ffffff !important;
            background: #b91c1c !important;
            border-color: #b91c1c !important;
        }

        .cc-delete-job .fa,
        .deleteBtn2 .fa {
            color: inherit !important;
        }

        .cc-add-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 38px;
            padding: 8px 13px;
            border: 0;
            border-radius: 10px;
            box-shadow: none;
        }

        .cc-abutment-card {
            grid-column: 1 / -1;
            margin-top: 2px;
            padding: 14px !important;
            background: #fafaff;
            border: 1px solid #d9ddff;
            border-radius: 12px;
        }

        .cc-subsection-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .cc-subsection-heading strong,
        .cc-subsection-heading span {
            display: block;
        }

        .cc-subsection-heading strong {
            color: var(--cc-text);
            font-size: 13px;
        }

        .cc-subsection-heading span {
            margin-top: 2px;
            color: var(--cc-muted);
            font-size: 11px;
        }

        .cc-abutment-row {
            display: grid;
            grid-template-columns: minmax(130px, 1fr) repeat(2, minmax(130px, 1fr)) minmax(110px, 0.7fr) 40px;
            align-items: end;
            gap: 10px;
            margin: 0 0 10px !important;
            padding: 12px !important;
            background: #ffffff;
            border: 1px solid #e0e4ff;
            border-radius: 10px;
        }

        .purpleBorder {
            width: 100%;
            min-height: 42px;
            color: var(--cc-text);
            background: #ffffff;
            border: 1px solid #cfd6e1 !important;
            border-radius: 9px;
        }

        .cc-note-area textarea {
            min-height: 150px;
            padding: 12px 14px;
            resize: vertical;
        }

        .cc-upload {
            display: grid;
            gap: 12px;
            min-height: 0;
            padding: 14px;
            background: var(--cc-surface-soft);
            border: 1px dashed #b8c1cf;
            border-radius: 12px;
        }

        .cc-upload__intro {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--cc-text);
        }

        .cc-upload__intro>i {
            display: inline-grid;
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            place-items: center;
            color: var(--cc-accent-dark);
            background: var(--cc-accent-soft);
            border-radius: 10px;
        }

        .cc-upload__intro strong,
        .cc-upload__intro span {
            display: block;
        }

        .cc-upload__intro strong {
            font-size: 13px;
        }

        .cc-upload__intro span {
            margin-top: 2px;
            color: var(--cc-muted);
            font-size: 11px;
        }

        .cc-upload input[type="file"] {
            height: auto !important;
            padding: 10px 12px;
            background: #ffffff;
        }

        .cc-upload__status {
            margin: 0;
            color: var(--cc-muted);
            font-size: 12px;
        }

        .cc-file-preview {
            min-width: 0;
            padding-top: 2px;
        }

        .cc-file-preview__list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(150px, 100%), 1fr));
            gap: 12px;
            min-width: 0;
        }

        .cc-file-preview__entry {
            min-width: 0;
        }

        .cc-file-preview .file-preview-item {
            position: relative;
            min-width: 0;
            height: 100%;
            padding: 10px;
            overflow: hidden;
            border: 1px solid var(--cc-border) !important;
            border-radius: 9px !important;
            background: #ffffff !important;
            box-shadow: none !important;
        }

        .cc-file-preview .file-preview-item>img {
            display: block;
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }

        .cc-file-preview .file-info {
            min-width: 0;
            margin-top: 7px;
            padding-inline-end: 32px;
            font-size: 11px;
        }

        .cc-file-preview .file-info>div:first-child {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .cc-file-preview .remove-file {
            position: absolute;
            top: 7px;
            inset-inline-end: 7px;
            display: inline-grid;
            width: 30px;
            min-width: 30px;
            height: 30px;
            min-height: 30px;
            margin: 0;
            padding: 0 !important;
            place-items: center;
            border-radius: 8px;
        }

        .cc-toggle-row {
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin: 0 !important;
            padding: 12px;
            color: var(--cc-text) !important;
            background: var(--cc-surface-soft);
            border: 1px solid var(--cc-border);
            border-radius: 11px;
            cursor: pointer;
            text-transform: none !important;
            letter-spacing: 0 !important;
        }

        .cc-toggle-row strong,
        .cc-toggle-row small {
            display: block;
        }

        .cc-toggle-row strong {
            font-size: 13px;
        }

        .cc-toggle-row small {
            margin-top: 2px;
            color: var(--cc-muted);
            font-size: 11px;
            font-weight: 500;
        }

        .cc-toggle-row input {
            width: 20px;
            height: 20px;
            flex: 0 0 20px;
            accent-color: var(--cc-accent);
        }

        .cc-discount-fields {
            display: grid;
            grid-template-columns: minmax(110px, 0.65fr) minmax(0, 1.35fr);
            gap: 12px;
            margin-top: 12px;
        }

        .cc-testing-helper {
            margin-top: 12px;
            padding: 12px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 11px;
        }

        .checked {
            filter: invert(26%) sepia(73%) saturate(492%) hue-rotate(133deg) brightness(94%) contrast(86%);
        }

        .hidden {
            display: none;
        }

        #unitsDialog.modal.show .modal-dialog {
            -webkit-transform: translate(0, 0%);
            transform: translate(0, 0%);
        }

        .xdsoft_time_box {
            width: 100px !important;
        }

        .xdsoft_datetimepicker {
            padding-right: 50px;
        }

        #addJobBtn2 {
            background-color: var(--cc-accent);
            border-color: var(--cc-accent-dark);
        }

        #unitsDialog img {
            max-width: none;
        }

        @media (min-width: 1500px) {
            .cc-job-block {
                grid-template-columns: minmax(112px, 0.8fr) minmax(150px, 1fr) minmax(150px, 1fr) minmax(96px, 0.62fr) minmax(148px, 0.9fr) 42px;
            }
        }

        @media (max-width: 1199.98px) {
            .cc-workspace {
                grid-template-columns: minmax(0, 1.5fr) minmax(280px, 0.72fr);
            }

            .cc-job-block {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .cc-job-actions {
                position: absolute;
                top: 10px;
                right: auto;
                inset-inline-end: 12px;
                min-height: 0;
                z-index: 6;
            }
        }

        @media screen and (max-width: 991.98px) {
            .cc-workspace {
                grid-template-columns: 1fr;
            }

            .cc-secondary-column {
                position: static;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            #unitsDialog .modal-content .modal-footer button {
                margin: 0;
                width: auto;
                white-space: break-spaces;
            }
        }

        @media (max-width: 575.98px) {
            .create-shell {
                padding: 10px 0 28px;
            }

            .cc-page-intro {
                grid-template-columns: 1fr;
                gap: 16px;
                margin-bottom: 12px;
                padding: 20px 18px;
                border-radius: 14px;
            }

            body.white-content .cc-page-intro h1 {
                font-size: 25px;
            }

            .cc-case-reference {
                min-width: 0;
                width: 100%;
            }

            .cc-page-intro__meta {
                min-width: 0;
                width: 100%;
            }

            .cc-page-actions {
                grid-template-columns: 1fr 1fr;
            }

            .cc-workspace,
            .cc-primary-column,
            .cc-secondary-column {
                gap: 12px;
            }

            .cc-secondary-column {
                grid-template-columns: 1fr;
            }

            .cc-card,
            .cc-section-header {
                border-radius: 13px;
            }

            .cc-section-header {
                grid-template-columns: auto minmax(0, 1fr);
                padding: 15px 14px;
            }

            .cc-section-header .cc-required-badge,
            .cc-jobs-header .cc-add-btn {
                grid-column: 2;
                justify-self: start;
            }

            .cc-card-body,
            .cc-card-body--jobs {
                padding: 14px;
            }

            .cc-field-grid--details,
            .cc-discount-fields {
                grid-template-columns: 1fr;
            }

            .cc-field--wide {
                grid-column: auto;
            }

            .cc-job-block {
                grid-template-columns: 1fr;
                gap: 13px;
                padding: 44px 13px 13px;
            }

            .cc-style-options {
                max-width: none;
            }

            .cc-job-actions {
                top: 10px;
                right: auto;
                inset-inline-end: 10px;
            }

            .cc-subsection-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .cc-abutment-row {
                grid-template-columns: 1fr;
            }

            .cc-abutment-actions {
                justify-self: end;
            }

            .cc-page-actions .btn {
                min-width: 0;
                padding-inline: 10px;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $color = '#212529';
        $permissions = Cache::get('user' . Auth()->user()->id);
        $currencyLabel = $currencyLabel ?? (string) ($currencyContext['display'] ?? $currencyContext['code'] ?? 'JOD');
    @endphp
    <div class="ed-shell container-fluid px-0 create-shell">
        @if (config('site_vars.environment') == 'testing')
            <form class="kt-form create-case-form" method="POST" enctype="multipart/form-data"
                action="{{ route('create-and-send-case-to') }}">
            @else
                <form class="kt-form create-case-form" method="POST" enctype="multipart/form-data"
                    action="{{ route('new-case-post') }}">
        @endif
        @csrf
        <input type="hidden" name="temp_case_id" value="{{ $tempCaseId }}">

        <header class="cc-page-intro">
            <div class="cc-page-intro__copy">
                <span class="cc-page-intro__eyebrow">New laboratory case</span>
                <h1>Create a production-ready case</h1>
                <p>Capture the patient, delivery plan, and laboratory work in one clear workflow.</p>
            </div>
            <div class="cc-page-intro__meta">
                <div class="cc-case-reference" aria-label="Auto-generated case reference">
                    <span>Case reference</span>
                    <strong>{{ $tempCaseId }}</strong>
                    <small>Generated automatically</small>
                </div>
                <div class="cc-page-actions" aria-label="Case form actions">
                    <button type="submit" class="btn btn-primary extraPadding">
                        <i class="fa-solid fa-check" aria-hidden="true"></i>
                        Create case
                    </button>
                    <a class="btn btn-light" href="{{ route('cases-index') }}">Cancel</a>
                </div>
            </div>
        </header>

        <div class="cc-workspace">
            <main class="cc-primary-column">
                <section class="cc-card cc-card--details" aria-labelledby="cc-case-details-title">
                    <div class="cc-section-header">
                        <span class="cc-step-number">01</span>
                        <div>
                            <span class="cc-section-kicker">Case setup</span>
                            <h2 id="cc-case-details-title">Patient and delivery details</h2>
                            <p>Start with the information production needs to identify and schedule the case.</p>
                        </div>
                        <span class="cc-required-badge">Required</span>
                    </div>

                    <div class="cc-card-body">
                        @php
                            $time = new DateTime('tomorrow 13:00');
                            $time = $time->format('d M, Y h:i a');
                        @endphp
                        <div class="cc-field-grid cc-field-grid--details">
                            <div class="cc-field cc-field--patient">
                                <label class="cc-label" for="cc-patient-name">Patient name <span aria-hidden="true">*</span></label>
                                <input class="form-control cc-input" id="cc-patient-name" type="text"
                                    name="patient_name" value="{{ old('patient_name') }}" placeholder="Enter patient name"
                                    autocomplete="off" required />
                            </div>

                            <div class="cc-field cc-field--doctor">
                                <label class="cc-label" for="cc-doctor">Doctor <span aria-hidden="true">*</span></label>
                                <select class="selectpicker cc-input" id="cc-doctor" name="doctor" data-live-search="true"
                                    required title="Select a doctor" data-tap-disabled="true">
                                    @foreach ($doctors as $doctor)
                                        <option value="{{ $doctor->id }}"
                                            {{ (string) old('doctor') === (string) $doctor->id ? 'selected' : '' }}>
                                            {{ $doctor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="cc-field">
                                <label class="cc-label" for="cc-delivery-date">Delivery date <span aria-hidden="true">*</span></label>
                                <input class="form-control SDTP cc-input" id="cc-delivery-date" name="delivery_date"
                                    type="text" value="{{ old('delivery_date', $time) }}" required readonly />
                                <small class="cc-field-help">Tap to adjust the production deadline.</small>
                            </div>

                            <div class="cc-field">
                                <label class="cc-label" for="cc-impression-type">Impression type</label>
                                <select class="form-control cc-input" id="cc-impression-type" name="impression_type"
                                    data-container="body" data-live-search="true" title="Select impression"
                                    data-hide-disabled="true">
                                    @foreach ($impressionTypes as $impression)
                                        <option value="{{ $impression->id }}"
                                            {{ (string) old('impression_type') === (string) $impression->id ? 'selected' : '' }}>
                                            {{ $impression->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="cc-field cc-field--wide">
                                <label class="cc-label" for="cc-tags">Tags</label>
                                <select class="select selectpicker cc-input" id="cc-tags" name="tags[]" multiple
                                    data-mdb-placeholder="Tags">
                                    @php
                                        $selectedTags = array_map('strval', (array) old('tags', []));
                                    @endphp
                                    @foreach ($tags as $tag)
                                        <option style="color:{{ $tag->color }}" value="{{ $tag->id }}"
                                            {{ in_array((string) $tag->id, $selectedTags, true) ? 'selected' : '' }}>
                                            {{ $tag->text }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="cc-field-help">Add only tags that help the production team act quickly.</small>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="cc-card cc-jobs-card" aria-labelledby="cc-jobs-title">
                    <div class="repeater jobsRepeater">
                        <div class="cc-section-header cc-jobs-header">
                            <span class="cc-step-number">02</span>
                            <div>
                                <span class="cc-section-kicker">Production plan</span>
                                <h2 id="cc-jobs-title">Laboratory jobs</h2>
                                <p>Group units that share the same restoration, material, shade, and style.</p>
                            </div>
                            <a href="javascript:" data-repeater-create="" class="btn btn-primary cc-add-btn"
                                id="addJobBtn">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                <span>Add job</span>
                            </a>
                        </div>

                        <div class="cc-card-body cc-card-body--jobs">
                            <div data-repeater-list="repeat" class="cc-job-list">
                                <div data-repeater-item class="jobRow">
                                    <div class="form-group mb-0">
                                        <div data-repeater-list="repeat" class="cc-job-inner">
                                            <div data-repeater-item
                                                class="form-group row align-items-start row-item cc-job-block cc-job-grid">
                                                <div class="col-md-2 cc-job-field cc-job-field--units">
                                                    <label class="cc-label">Units</label>
                                                    <input type="hidden" name="units" id="units"
                                                        class="hiddenUnitsInput" required>
                                                    <button type="button"
                                                        class="btn btn-secondary slctUnitsBtn cc-ghost-btn"
                                                        data-toggle="modal" data-target="#unitsDialog"
                                                        name="openDialogBtn" onclick="preOpenDialog(this)">
                                                        Select units
                                                    </button>
                                                </div>

                                                <div class="col-md-2 cc-job-field">
                                                    <label class="cc-label">Job type</label>
                                                    <select class="form-control cc-input" id="jobType" name="jobType"
                                                        onchange="jobTypeChanged(this)">
                                                        @foreach ($types as $type)
                                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-2 cc-job-field">
                                                    <label class="cc-label">Material</label>
                                                    <select class="form-control cc-input" id="material_id"
                                                        name="material_id">
                                                        @foreach ($materials as $m)
                                                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-2 cc-job-field cc-job-field--shade">
                                                    <label class="cc-label">Shade</label>
                                                    <select class="form-control cc-input" id="color" name="color">
                                                        <option value="0" selected>None</option>
                                                        <option value="A1">A1</option>
                                                        <option value="A2">A2</option>
                                                        <option value="A3">A3</option>
                                                        <option value="A3.5">A3.5</option>
                                                        <option value="A4">A4</option>
                                                        <option value="B1">B1</option>
                                                        <option value="B2">B2</option>
                                                        <option value="B3">B3</option>
                                                        <option value="B4">B4</option>
                                                        <option value="C1">C1</option>
                                                        <option value="C2">C2</option>
                                                        <option value="C3">C3</option>
                                                        <option value="C4">C4</option>
                                                        <option value="D2">D2</option>
                                                        <option value="D3">D3</option>
                                                        <option value="D4">D4</option>
                                                        <option value="BL1">BL1</option>
                                                        <option value="BL2">BL2</option>
                                                        <option value="BL3">BL3</option>
                                                        <option value="BL4">BL4</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-2 cc-job-field cc-job-field--style">
                                                    <label class="cc-label">Style</label>
                                                    <div class="toggle-container cc-style-toggle">
                                                        <input type="hidden" class="cc-style-value" name="style" value="Single">
                                                        <label class="toggle-wrap">
                                                            <input class="toggle-input cc-style-toggle-input" type="checkbox"
                                                                aria-label="Single or Bridge" aria-checked="false">
                                                            <span class="toggle-track">
                                                                <span class="track-lines"><span class="track-line"></span></span>
                                                                <span class="toggle-thumb">
                                                                    <span class="thumb-core"></span>
                                                                    <span class="thumb-inner"></span>
                                                                    <span class="thumb-scan"></span>
                                                                    <span class="thumb-particles" aria-hidden="true"></span>
                                                                </span>
                                                                <span class="toggle-data" aria-hidden="true">
                                                                    <span class="data-text off">Single</span>
                                                                    <span class="data-text on">Bridge</span>
                                                                    <span class="status-indicator off"></span>
                                                                    <span class="status-indicator on"></span>
                                                                </span>
                                                                <span class="energy-rings" aria-hidden="true">
                                                                    <span class="energy-ring"></span>
                                                                    <span class="energy-ring"></span>
                                                                    <span class="energy-ring"></span>
                                                                </span>
                                                                <span class="interface-lines" aria-hidden="true"></span>
                                                                <span class="toggle-reflection"></span>
                                                                <span class="holo-glow"></span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-md-2 cc-job-actions">
                                                    <button data-repeater-delete
                                                        class="btn deleteBtn btn-sm cc-delete-job" type="button"
                                                        value="Delete" aria-label="Delete job">
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </button>
                                                </div>

                                                <div class="col-md-12 abutment abutmentsArea cc-abutment-card"
                                                    style="display:none;">
                                                    <div class="abutments-repeater abutmentsRepeater">
                                                        <div class="cc-subsection-heading">
                                                            <div>
                                                                <strong>Implant components</strong>
                                                                <span>Assign component details to the selected units.</span>
                                                            </div>
                                                            <a href="javascript:" data-repeater-create=""
                                                                class="btn btn-success btn-sm" id="addJobBtn2"
                                                                onClick="addAbutmentJob(this)">
                                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                                Add component
                                                            </a>
                                                        </div>
                                                        <div data-repeater-list="abutments" class="dataRepeaterList">
                                                            <div data-repeater-item class="abutmentsRow">
                                                                <div class="row cc-abutment-row">
                                                                    <div class="col-md-3 cc-abutment-field">
                                                                        <label class="cc-label">Units</label>
                                                                        <select
                                                                            class="select abutmentsUnitsPicker greyBG purpleBorder"
                                                                            name="abutmentUnits[]" multiple
                                                                            data-mdb-placeholder="Units">
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-2 cc-abutment-field">
                                                                        <label class="cc-label">Implant type</label>
                                                                        <select class="form-control purpleBorder"
                                                                            id="implant" name="implant">
                                                                            <option value="0" selected>None</option>
                                                                            @foreach ($implants as $implant)
                                                                                <option value="{{ $implant->id }}">
                                                                                    {{ $implant->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-2 cc-abutment-field">
                                                                        <label class="cc-label">Abutment type</label>
                                                                        <select class="form-control purpleBorder"
                                                                            id="abutment" name="abutment">
                                                                            <option value="0" selected>None</option>
                                                                            @foreach ($abutments as $abutment)
                                                                                <option value="{{ $abutment->id }}">
                                                                                    {{ $abutment->name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-2 cc-abutment-field">
                                                                        <label class="cc-label">Code</label>
                                                                        <input type="text" name="abutmentCode"
                                                                            class="form-control purpleBorder">
                                                                    </div>
                                                                    <div class="col-md-1 cc-abutment-actions">
                                                                        <button data-repeater-delete
                                                                            class="btn deleteBtn2 btn-sm" type="button"
                                                                            value="Delete"
                                                                            aria-label="Delete component">
                                                                            <i class="fa fa-trash"
                                                                                aria-hidden="true"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <aside class="cc-secondary-column" aria-label="Case supporting information and submission">
                <section class="cc-card cc-note-area" aria-labelledby="cc-notes-title">
                    <div class="cc-section-header cc-section-header--compact">
                        <span class="cc-step-number">03</span>
                        <div>
                            <span class="cc-section-kicker">Clinical context</span>
                            <h2 id="cc-notes-title">Notes</h2>
                            <p>Add instructions that cannot be expressed by the job fields.</p>
                        </div>
                    </div>
                    <div class="cc-card-body">
                        <label class="cc-label" for="exampleTextarea">Case notes</label>
                        <textarea class="form-control cc-input" name="note" id="exampleTextarea" rows="5"
                            placeholder="Special instructions, contacts, or clinical considerations">{{ old('note') }}</textarea>
                    </div>
                </section>

                <section class="cc-card" aria-labelledby="cc-attachments-title">
                    <div class="cc-section-header cc-section-header--compact">
                        <span class="cc-section-icon"><i class="fa-regular fa-image" aria-hidden="true"></i></span>
                        <div>
                            <span class="cc-section-kicker">Files</span>
                            <h2 id="cc-attachments-title">Attachments</h2>
                            <p>Add photos, scans, or supporting documents.</p>
                        </div>
                    </div>
                    <div class="cc-card-body">
                        <div class="form-group form-group-last mb-0 cc-upload">
                            <div class="cc-upload__intro">
                                <i class="fa-solid fa-cloud-arrow-up" aria-hidden="true"></i>
                                <div>
                                    <strong>Choose case files</strong>
                                    <span>You can select multiple files.</span>
                                </div>
                            </div>
                            <input type="file" id="images" class="form-control cc-input" name="images[]" multiple>
                            <p id="file-selection-status" class="cc-upload__status" role="status" aria-live="polite">No files selected.</p>
                            <div id="file-preview-container" class="cc-file-preview" style="display: none;">
                                <div id="file-preview-list" class="cc-file-preview__list"></div>
                            </div>
                        </div>

                        @if (config('site_vars.environment') == 'testing')
                            <div class="cc-testing-helper">
                                <label class="cc-label" for="stageToSendTo">Testing destination</label>
                                <select class="form-control cc-input" id="stageToSendTo" name="stageToSendTo">
                                    <option value="1">Design</option>
                                    <option value="6">Finishing</option>
                                    <option value="7">QC</option>
                                    <option value="8">Delivery</option>
                                    <option value="10">Completed</option>
                                </select>
                            </div>
                        @endif
                    </div>
                </section>

                @if (Auth()->user()->is_admin || ($permissions && $permissions->contains('permission_id', 114)))
                    <section class="cc-card cc-discount-card" aria-labelledby="cc-discount-title">
                        <div class="cc-section-header cc-section-header--compact">
                            <span class="cc-section-icon"><i class="fa-solid fa-tag" aria-hidden="true"></i></span>
                            <div>
                                <span class="cc-section-kicker">Optional</span>
                                <h2 id="cc-discount-title">Discount</h2>
                                <p>Apply a documented case-level adjustment.</p>
                            </div>
                        </div>
                        <div class="cc-card-body">
                            <label class="cc-toggle-row">
                                <span>
                                    <strong>Apply discount</strong>
                                    <small>Requires an amount and reason.</small>
                                </span>
                                <input type="checkbox" class="discountCB" name="discountCB"
                                    onclick="toggleDiscountPortion(this)" />
                            </label>
                            <div class="discountPortion cc-discount-fields" style="display:none">
                                <div class="cc-field">
                                    <label class="cc-label" for="cc-discount-amount">Amount ({{ $currencyLabel }})</label>
                                    <input class="form-control cc-input" id="cc-discount-amount" type="number"
                                        min="0" step="0.01" name="discount_amount" placeholder="0.00" />
                                </div>
                                <div class="cc-field">
                                    <label class="cc-label" for="cc-discount-reason">Reason</label>
                                    <input class="form-control cc-input" id="cc-discount-reason" type="text"
                                        name="discount_reason" placeholder="Why is this discount applied?">
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

            </aside>
        </div>
        </form>
    </div>

    <!-- TEETH PICK DIALOG -->

    <div data-repeater-item class="modal fade" id="unitsDialog" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLongTitle" style="display: none;" aria-hidden="true" name="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-body">

                    <input type="hidden" value="success" name="dialogNum" class="dialogTag">
                    @php
                        $startingPosition = 290;
                        $imageSize = 50;
                        $decrement = 45;
                        $teeth = 0;
                        $imageSizeL = 49;
                        $imageSizeM = 35;
                        $leftPadding = 66;
                    @endphp
                    <div class="main-body" style="padding-top: 30px;width:200px;/*height:500px*/">

                        {{-- <img class="jaw lowerJaw" alt="lower" src="/assets/teethPics/lower-jaw.png" width=180px --}}
                        {{-- style="position: absolute; top: 330px;left: 150px;"> --}}

                        <img class="jaw upperJaw" alt="upper" src="/assets/teethPics/v2/upper_jaw.png" height=265px
                            style="position: absolute; top: 17px;left: 0px;">
                        <img class="jaw lowerJaw" alt="lower" src="/assets/teethPics/v2/lower_jaw.png" height=280px
                            style="position: absolute; top: 295px;left: 17px;">

                        <img class="teeth" alt="18" src="/assets/teethPics/v2/18.png"
                            height={{ $imageSizeM + 8 }}px style="  position: absolute; top: 226px;left: 55px;">
                        @php $teeth = 1; @endphp
                        <img class="teeth" alt="17" src="/assets/teethPics/v2/17.png"
                            height={{ $imageSizeL }}px style="  position: absolute; top:183px;left:59px;">
                        @php $teeth = 2; @endphp
                        <img class="teeth" alt="16" src="/assets/teethPics/v2/16.png"
                            height={{ $imageSizeL + 3 }}px style="  position: absolute; top: 139px;left:67px;">
                        @php
                            $teeth = 3;
                            $decrement = $decrement - 1.5;
                        @endphp
                        <img class="teeth" alt="15" src="/assets/teethPics/v2/15.png"
                            height={{ $imageSizeM + 1 }}px style="  position: absolute; top: 111px;left:79px;">
                        @php $teeth = 4; @endphp
                        <img class="teeth" alt="14" src="/assets/teethPics/v2/14.png"
                            height={{ $imageSizeM + 2 }}px style="  position: absolute; top:82px;left:92px;">
                        @php $teeth = 5; @endphp
                        <img class="teeth" alt="13" src="/assets/teethPics/v2/13.png"
                            height={{ $imageSizeM + 6 }}px style="  position: absolute; top:53px;left:110px;">
                        @php $teeth = 6; @endphp
                        <img class="teeth" alt="12" src="/assets/teethPics/v2/12.png"
                            height={{ $imageSizeM + 4 }}px style="  position: absolute; top: 36px;left: 135px;">
                        @php $teeth = 7; @endphp
                        <img class="teeth" alt="11" src="/assets/teethPics/v2/11.png"
                            height={{ $imageSizeM + 5 }}px style="  position: absolute; top: 23.5px;left: 162px;">
                        @php $teeth = 8; @endphp
                        <img class="teeth" alt="21" src="/assets/teethPics/v2/21.png"
                            height={{ $imageSizeM + 5 }}px style="  position: absolute; top: 23px;left:200px;">
                        @php $teeth = 9; @endphp
                        <img class="teeth" alt="22" src="/assets/teethPics/v2/22.png"
                            height={{ $imageSizeM + 5 }}px style="  position: absolute; top:35px;left: 231px;">
                        @php $teeth = 5; @endphp
                        <img class="teeth" alt="23" src="/assets/teethPics/v2/23.png"
                            height={{ $imageSizeM + 3 }}px style="  position: absolute; top: 55px;left: 254px;">
                        @php $teeth = 4; @endphp
                        <img class="teeth" alt="24" src="/assets/teethPics/v2/24.png"
                            height={{ $imageSizeM }}px style="  position: absolute; top: 84px;left: 266px;">
                        @php $teeth = 3; @endphp
                        <img class="teeth" alt="25" src="/assets/teethPics/v2/25.png"
                            height={{ $imageSizeM }}px style="  position: absolute; top:112px;left:272px;">
                        @php $teeth = 2; @endphp
                        <img class="teeth" alt="26" src="/assets/teethPics/v2/26.png"
                            height={{ $imageSizeL + 1 }}px style="  position: absolute; top: 141px;left: 280px;">
                        @php $teeth = 1; @endphp
                        <img class="teeth" alt="27" src="/assets/teethPics/v2/27.png"
                            height={{ $imageSizeL }}px style="  position: absolute; top:182px;left: 291px;">
                        @php $teeth = 0; @endphp
                        <img class="teeth" alt="28" src="/assets/teethPics/v2/28.png"
                            height={{ $imageSizeL }}px style="  position: absolute; top:227px;left: 291px;">
                        @php $teeth = 16; @endphp


                        @php
                            $startingPosition = 330;
                            $imageSize = 50;
                            $decrement = 45;
                            $teeth = 0;
                            $imageSizeL = 43;
                            $imageSizeM = 35;
                            $leftPadding = 70;
                        @endphp
                        <div class="main-body" style="padding-top: 50px;width:200px;height:500px">
                            <h2 style="padding-left:300%" id="teethSelectedH2"></h2>

                            <img class="teeth" alt="38" src="/assets/teethPics/v2/38.png"
                                height={{ $imageSizeL + 1 }}px style="  position: absolute; top:326px;left: 309px;">
                            @php $teeth = 1; @endphp
                            <img class="teeth" alt="37" src="/assets/teethPics/v2/37.png"
                                height={{ $imageSizeL + 6 }}px style="  position: absolute; top:367px;left:299px;">
                            @php $teeth = 2; @endphp
                            <img class="teeth" alt="36" src="/assets/teethPics/v2/36.png"
                                height={{ $imageSizeL + 5 }}px style="  position: absolute; top:412px;left:285px;">
                            @php
                                $teeth = 3;
                                $decrement = $decrement - 1.5;
                            @endphp
                            <img class="teeth" alt="35" src="/assets/teethPics/v2/35.png"
                                height={{ $imageSizeM }}px style="  position: absolute; top: 454px;left:275px;">
                            @php $teeth = 4; @endphp
                            <img class="teeth" alt="34" src="/assets/teethPics/v2/34.png"
                                height={{ $imageSizeM }}px style="  position: absolute; top: 484px;left:263px;">
                            @php $teeth = 5; @endphp
                            <img class="teeth" alt="33" src="/assets/teethPics/v2/33.png"
                                height={{ $imageSizeM + 1 }}px style="  position: absolute; top: 508px;left:247px;">
                            @php $teeth = 6; @endphp
                            <img class="teeth" alt="32" src="/assets/teethPics/v2/32.png"
                                height={{ $imageSizeM }}px style="  position: absolute; top: 527px;left: 229px;">
                            @php $teeth = 7; @endphp
                            <img class="teeth" alt="31" src="/assets/teethPics/v2/31.png"
                                height={{ $imageSizeM - 3 }}px style="position: absolute; top:538px;left: 203px;">
                            @php $teeth = 8; @endphp
                            <img class="teeth" alt="41" src="/assets/teethPics/v2/41.png"
                                height={{ $imageSizeM - 2 }}px style="position: absolute; top: 534px;left:176px;">
                            @php $teeth = 9; @endphp
                            <img class="teeth" alt="42" src="/assets/teethPics/v2/42.png"
                                height={{ $imageSizeM }}px style="  position: absolute; top:524px;left: 150px;">
                            @php $teeth = 5; @endphp
                            <img class="teeth" alt="43" src="/assets/teethPics/v2/43.png"
                                height={{ $imageSizeM }}px style="  position: absolute; top: 510px;left: 127px;">
                            @php $teeth = 4; @endphp
                            <img class="teeth" alt="44" src="/assets/teethPics/v2/44.png"
                                height={{ $imageSizeM }}px style="  position: absolute; top: 485px;left: 108px;">
                            @php $teeth = 3; @endphp
                            <img class="teeth" alt="45" src="/assets/teethPics/v2/45.png"
                                height={{ $imageSizeM + 2 }}px style="  position: absolute; top: 455px;left: 88px;">
                            @php $teeth = 2; @endphp
                            <img class="teeth" alt="46" src="/assets/teethPics/v2/46.png"
                                height={{ $imageSizeL + 4.5 }}px style="  position: absolute; top: 415px;left: 68px;">
                            @php $teeth = 1; @endphp
                            <img class="teeth" alt="47" src="/assets/teethPics/v2/47.png"
                                height={{ $imageSizeL + 5 }}px style="  position: absolute; top: 371px;left: 55px;">
                            @php $teeth = 0; @endphp
                            <img class="teeth" alt="48" src="/assets/teethPics/v2/48.png"
                                height={{ $imageSizeL + 1 }}px style="  position: absolute; top: 331px;left:44px;">
                            @php $teeth = 16; @endphp


                        </div>
                    </div>

                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
                        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>


                </div>
                <div class="modal-footer" name="model-footer" style="padding-top:25px">
                    <button type="button" class="btn btn-primary" id="submitDialog" onclick="" style="padding: 7px 50px;">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>


    <!-- FILES DIALOG -->

    <div class="modal fade" id="filesDialog" tabindex="-1" role="dialog" aria-labelledby="fileDialog"
        style="display: none;" aria-hidden="true" name="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle-1">Upload files </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">


                </div>
                <div class="modal-footer" name="model-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitDialog" onclick="">Save
                        changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ asset('assets/js/jquery.repeater3.min.js') }}" defer></script>
    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();
            $('.selectpicker').selectpicker('refresh');
            $('.repeater').repeater({
                // (Required if there is a nested repeater)
                // Specify the configuration of the nested repeaters.
                // Nested configuration follows the same format as the base configuration,
                // supporting options "defaultValues", "show", "hide", etc.
                // Nested repeaters additionally require a "selector" field.
                repeaters: [{
                    // (Required)
                    // Specify the jQuery selector for this nested repeater
                    selector: '.abutments-repeater',
                    show: function() {
                        $(this).slideDown();
                    },

                    hide: function(deleteElement) {
                        $(this).slideUp(deleteElement);
                    }
                }],


                defaultValues: {
                    style: 'Single'
                },

                show: function() {
                    setJobDefaults(this);
                    $(this).slideDown();
                },
                initEmpty: false,
                hide: function(deleteElement) {
                    $(this).slideUp(deleteElement);
                }
            });

            // removing first job because it causes UI errors with the repeater
            $(".jobsRepeater").find(".jobRow").first().html("");
            $("#addJobBtn").click();
            setJobDefaults($(".jobsRepeater").find(".row-item").last());
            //        $(".abutmentsRepeater").find(".abutmentsRow").first().html("");
            //        $("#addJobBtn2").click();


        });
    </script>
    <script>
        function toggleDiscountPortion(ele) {

            var discountPortion = $(".discountPortion");
            if (ele.checked) {
                discountPortion.show(200);
            } else {
                discountPortion.hide(200);
            }
        }

        var teethSelected = [];
        var lstSelectedJobUNName = "";
        var currentJobBlock = null;

        function getJobBlock(element) {
            var jobRow = $(element).closest('.cc-job-block');
            if (!jobRow.length && currentJobBlock && currentJobBlock.length) {
                jobRow = currentJobBlock;
            }
            return jobRow;
        }

        function getSelectInJob(row, fieldName) {
            return $(row).find("select[name$='[" + fieldName + "]'], select[name='" + fieldName + "']").first();
        }

        function getInputInJob(row, fieldName) {
            return $(row).find("input[name$='[" + fieldName + "]'], input[name='" + fieldName + "']").first();
        }

        function getButtonInJob(row, fieldName) {
            return $(row).find("button[name$='[" + fieldName + "]'], button[name='" + fieldName + "']").first();
        }

        function getSelectedUnitsForJob(row) {
            var unitsValue = getInputInJob(row, 'units').val() || '';
            return unitsValue.split(',').filter(function(value) {
                return value !== '';
            });
        }

        function replaceSelectOptions(selectBox, rows, valueKey, textKey) {
            selectBox.empty();
            $.each(rows, function(key, value) {
                selectBox.append($("<option></option>")
                    .attr("value", value[valueKey])
                    .text(value[textKey]));
            });
            selectBox.val(selectBox.find('option:first').val());
        }

        function setJobDefaults(row) {
            var jobRow = $(row);
            var jobTypeBox = getSelectInJob(jobRow, 'jobType');
            var materialBox = getSelectInJob(jobRow, 'material_id');
            var colorBox = getSelectInJob(jobRow, 'color');

            jobTypeBox.val(jobTypeBox.find('option:first').val());
            materialBox.val(materialBox.find('option:first').val());
            colorBox.val(colorBox.find('option:first').val());
            setStyleValue(jobRow, 'Single');
        }

        function setStyleValue(row, value) {
            var jobRow = $(row);
            var styleValue = jobRow.find("input.cc-style-value[name$='[style]']");
            var styleToggle = jobRow.find('.cc-style-toggle-input');
            var isBridge = value === 'Bridge';

            styleValue.val(value);
            styleToggle.prop('checked', isBridge).attr('aria-checked', isBridge ? 'true' : 'false');
        }

        $(document).on('change', '.cc-style-toggle-input', function() {
            setStyleValue($(this).closest('.cc-job-block'), this.checked ? 'Bridge' : 'Single');
        });

        function jobTypeChanged(jobTypeDD) {
            var jobTypeBox = $(jobTypeDD).first();
            var jobRow = getJobBlock(jobTypeBox);
            if (!jobRow.length) return;

            var materials = {!! json_encode($materials->toArray()) !!};
            var materialJobTypeRelations = {!! json_encode($jobTypeMaterials->toArray()) !!};

            var materialBox = getSelectInJob(jobRow, 'material_id');
            var openDialogBtn = getButtonInJob(jobRow, 'openDialogBtn');
            var implantBox = jobRow.find("select[name$='[implant]'], select[name='implant']").first();
            var abutmentBox = jobRow.find("select[name$='[abutment]'], select[name='abutment']").first();
            var jobTypeSelectedId = jobTypeBox.val();
            var jobTypeMaterials = materialJobTypeRelations.filter(element => element.jobtype_id == jobTypeSelectedId);

            materialBox.empty();

            const options = jobTypeMaterials
                .map(v => {
                    const mat = materials.find(x => x.id == v.material_id);
                    if (!mat) return ''; // skip if missing
                    return `<option value="${v.material_id}">${mat.name}</option>`;
                })
                .join('');

            materialBox.append(options);
            materialBox.val(materialBox.find('option:first').val());
            var abutmentsArea = jobRow.find(".abutmentsArea").first();
            var abutmentUnitsBox = $(abutmentsArea).find(".abutmentsUnitsPicker");
            var currentlySelectedUnits = getSelectedUnitsForJob(jobRow);
            if (jobTypeBox.find(":selected").val() == 6) {

                // get to parent of the main repeater and find abutment units box

                $(abutmentBox).attr('required', '');
                $(implantBox).attr('required', '');

                $(abutmentsArea).css("display", "block");
                abutmentUnitsBox.empty();
                // show the 6th parent of the box which has display none property
                // $(found).parent().parent().parent().parent().parent().parent().css("display","block");

                $.each(currentlySelectedUnits, function(index, value) {
                    abutmentUnitsBox.append($("<option></option>")
                        .attr("value", value)
                        .text(value));
                });
                abutmentUnitsBox.selectpicker();
                abutmentUnitsBox.selectpicker('refresh');
                jobTypeBox.attr("readonly", "true");
                $(openDialogBtn).attr("disabled", "true");
            } else {
                $(abutmentBox).removeAttr('required');
                $(implantBox).removeAttr('required');
                $(abutmentsArea).css("display", "none");
                abutmentUnitsBox.val(0);
                if (abutmentUnitsBox.hasClass('selectpicker')) {
                    abutmentUnitsBox.selectpicker('refresh');
                }
                //            implantBox.val(0);
                // $(found).parent().parent().parent().parent().parent().parent().css("display","none");
            }
        }

        function addAbutmentJob(ele) {
            // get units selected originally in the job
            var teethSelectedAsArr = getSelectedUnitsForJob(getJobBlock(ele));
            // wait for new repeater row to populate then add unit selected to abutment units box
            setTimeout(function() {
                var lastAbutmentUnitsBox = $("select[name$='[abutmentUnits][]']").last();


                $.each(teethSelectedAsArr, function(index, value) {
                    $(lastAbutmentUnitsBox).last().append($("<option></option>")
                        .attr("value", value)
                        .text(value));
                });
                lastAbutmentUnitsBox.selectpicker();
            }, 500);

        }

        $("#submitDialog").click(function() {

            var jobRow = currentJobBlock && currentJobBlock.length
                ? currentJobBlock
                : $("[name='" + lstSelectedJobUNName + "']").closest('.cc-job-block');

            if (!jobRow.length) return;

            var teethSelectedAsArr = getSelectedUnitsForJob(jobRow);
            var jobTypeBox = getSelectInJob(jobRow, 'jobType');
            var jobTypes = {!! json_encode($types->toArray()) !!};
            var colorBox = getSelectInJob(jobRow, 'color');
            var selectBtn = getButtonInJob(jobRow, 'openDialogBtn');
            /* Updating dropdowns according to teeth selection
             * First if is for jaws, second is for teeth
             * @Yazan -
             */
            if (jQuery.inArray("lower", teethSelectedAsArr) !== -1 || jQuery.inArray("upper",
                teethSelectedAsArr) !== -1) {
                // filter all job types to only jaws.
                var jawOnlyTypes = jobTypes.filter(element => element.teeth_or_jaw == 1);
                // fill up the options with the array above.
                replaceSelectOptions(jobTypeBox, jawOnlyTypes, 'id', 'name');
                // Notify Job type changed function to update materials with which box changed
                jobTypeChanged(jobTypeBox);

                // Hide style/color columns for this row only (jaw selections)
                colorBox.closest('.col-md-2').hide();

                var styleCol = getInputInJob(jobRow, 'style').closest('.col-md-2').first();
                setStyleValue(jobRow, 'None');
                styleCol.hide();

            }

            // No jaws selected
            else {
                // Restore style/color inputs for teeth selections (jaw selection may have hidden/disabled them)
                colorBox.closest('.col-md-2').show();

                var styleCol = getInputInJob(jobRow, 'style').closest('.col-md-2').first();
                styleCol.show();

                const jawOnlyTypes = jobTypes.filter(element => element.teeth_or_jaw == 0);
                replaceSelectOptions(jobTypeBox, jawOnlyTypes, 'id', 'name');
                setStyleValue(jobRow, teethSelectedAsArr.length > 1 ? 'Bridge' : 'Single');
                // Notify Job type changed function to update materials with which box changed
                jobTypeChanged(jobTypeBox);

            }

            // Change button label with selected teeth
            if (teethSelectedAsArr.length > 0)
                selectBtn.html(teethSelectedAsArr.join(","));
            else
                selectBtn.html("Select Units");


            colorBox.val(colorBox.find('option:first').val());

            // close dialog
            $(".modal").modal('hide');

        });


        $(".teeth").click(function() {

            // Check if any jaws is selected, if any remove them from array
            if (jQuery.inArray("upper", teethSelected) !== -1) {
                const jawIndex = teethSelected.indexOf("upper");
                teethSelected.splice(jawIndex, 1);
            }
            if (jQuery.inArray("lower", teethSelected) !== -1) {
                const jawIndex = teethSelected.indexOf("lower");
                teethSelected.splice(jawIndex, 1);
            }

            // remove the light of the jaws buttons
            var list = $('.jaw');
            list.removeClass("checked");


            //if not pre selected light up the teeth and add it to array
            if ($(this).hasClass("checked")) {
                $(this).removeClass("checked");
                var teethNumber = $(this).attr("alt");
                const index = teethSelected.indexOf(teethNumber);

                if (index > -1) {
                    teethSelected.splice(index, 1);
                }

                // remove the selection if previously selected
            } else {
                var teethNumber = $(this).attr("alt");
                teethSelected.push(teethNumber);
                $(this).addClass("checked");
                // console.log("Added a teeth" + teethSelected);
            }

            //console.log("Updating units input : "  + teethSelected);

            var activeUnitsInput = currentJobBlock && currentJobBlock.length
                ? getInputInJob(currentJobBlock, 'units')
                : $("[name='" + lstSelectedJobUNName + "']");
            activeUnitsInput.val(teethSelected);
        });
        $(".jaw").click(function() {

            var jaw = $(this).attr("alt");

            if ($(this).hasClass("checked")) {
                $(this).removeClass("checked");
                teethSelected = teethSelected.filter(function(value) {
                    return value !== jaw;
                });
            } else {
                // add visual selection to the jaw
                $(this).addClass("checked");

                // remove visual selection of all teeth if a jaw is selected
                $('.teeth').removeClass("checked");

                // keep only jaws in selection, then add the selected jaw
                teethSelected = teethSelected.filter(function(value) {
                    return value === "lower" || value === "upper";
                });
                if (jQuery.inArray(jaw, teethSelected) === -1) {
                    teethSelected.push(jaw);
                }
            }

            var activeUnitsInput = currentJobBlock && currentJobBlock.length
                ? getInputInJob(currentJobBlock, 'units')
                : $("[name='" + lstSelectedJobUNName + "']");
            activeUnitsInput.val(teethSelected);
        });

        function preOpenDialog(element) {
            currentJobBlock = getJobBlock(element);
            var currentJobUnits = getInputInJob(currentJobBlock, 'units');
            lstSelectedJobUNName = currentJobUnits.attr('name') || '';
            if (typeof currentJobUnits !== "undefined" && currentJobUnits.val()) {
                teethSelected = currentJobUnits.val().split(',');
                // console.log("is defined and its now : " + teethSelected);
            } else {
                // console.log("NOT defined,cleared");
                teethSelected = [];
            }
            if (teethSelected.length !== 0) {
                var teethPreSelected = currentJobUnits.val().split(',');
                // console.log("Lighting up : " + teethPreSelected);
                // light on and off according to the pre selected
                $(".teeth").each(function() {
                    if (jQuery.inArray($(this).attr("alt"), teethPreSelected) !== -1) {
                        // console.log("true");
                        $(this).addClass("checked");
                    } else
                        $(this).removeClass("checked");
                });
                $(".jaw").each(function() {
                    if (jQuery.inArray($(this).attr("alt"), teethPreSelected) !== -1)
                        $(this).addClass("checked");
                    else
                        $(this).removeClass("checked");
                });
            } else {
                $(".teeth").removeClass("checked");
                $(".jaw").removeClass("checked");
            }
        }
    </script>
    <script src="{{ asset('assets/js/jquery.imagesloader-1.0.1.js') }}"></script>
    {{-- <script src="{{asset('assets/js/jquery.repeater.js')}}" defer></script> --}}
    {{-- <script src="{{asset('assets/js/jquery.repeater.min.js')}}" defer></script> --}}
    {{-- <script src="{{asset('assets/js/jquery.repeater3.min.js')}}" defer></script> --}}

    <script src="{{ asset('assets/js/lightgallery.js') }}"></script>

    <script>
        // File upload preview functionality
        $(document).ready(function() {
            $('#images').on('change', function(e) {
                const files = e.target.files;
                const previewContainer = $('#file-preview-container');
                const previewList = $('#file-preview-list');

                // Clear previous previews
                previewList.empty();

                if (files.length > 0) {
                    previewContainer.show();
                    $('#file-selection-status').text(files.length === 1 ? '1 file selected.' : files.length + ' files selected.');

                    Array.from(files).forEach(function(file, index) {
                        const fileName = file.name.length > 15 ? file.name.substring(0, 15) +
                            '...' : file.name;
                        const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                        const isImage = file.type.startsWith('image/');

                        if (isImage) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const imagePreview = `
                            <div class="cc-file-preview__entry">
                                <div class="file-preview-item">
                                    <img src="${e.target.result}" alt="">
                                    <div class="file-info">
                                        <div title="${file.name}">${fileName}</div>
                                        <div class="text-muted">${fileSize}</div>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm remove-file" data-index="${index}" aria-label="Remove file">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                                previewList.append(imagePreview);
                            };
                            reader.readAsDataURL(file);
                        } else {
                            const filePreview = `
                        <div class="cc-file-preview__entry">
                            <div class="file-preview-item" style="text-align: center;">
                                <i class="fa fa-file" style="font-size: 40px; color: #6c757d;"></i>
                                <div class="file-info">
                                    <div title="${file.name}">${fileName}</div>
                                    <div class="text-muted">${fileSize}</div>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-file" data-index="${index}" aria-label="Remove file">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                    `;
                            previewList.append(filePreview);
                        }
                    });
                } else {
                    previewContainer.hide();
                    $('#file-selection-status').text('No files selected.');
                }
            });

            // Handle file removal
            $(document).on('click', '.remove-file', function() {
                const indexToRemove = $(this).data('index');
                const fileInput = document.getElementById('images');
                const dt = new DataTransfer();

                Array.from(fileInput.files).forEach(function(file, index) {
                    if (index !== indexToRemove) {
                        dt.items.add(file);
                    }
                });

                fileInput.files = dt.files;
                $('#images').trigger('change');
            });
        });
    </script>
    <script>
        // Track selected employees per stage type
        window.selectedEmployeeIds = window.selectedEmployeeIds || {};

        /**
         * Select an employee for assignment
         */
        function selectEmployee(type, cardElement, employeeId) {
            // Remove selection from all cards in this dialog
            const dialog = document.getElementById('EmployeeDialog' + type);
            if (!dialog) return;

            dialog.querySelectorAll('.alsolent-driver-card').forEach(card => {
                card.classList.remove('selected');
                const img = card.querySelector('.alsolent-driver-image');
                if (img) {
                    img.classList.add('grayscale');
                }
            });

            // Select the clicked card
            cardElement.classList.add('selected');
            const img = cardElement.querySelector('.alsolent-driver-image');
            if (img) {
                img.classList.remove('grayscale');
            }

            // Store selected employee ID
            window.selectedEmployeeIds[type] = employeeId;

            // Update hidden input
            const employeeInput = document.getElementById('employee-id-input-' + type);
            if (employeeInput) {
                employeeInput.value = employeeId;
            }

            // Enable assign button
            const assignButton = document.getElementById('action-button-' + type + '-employee');
            if (assignButton) {
                assignButton.disabled = false;
            }

            console.log('Selected employee ' + employeeId + ' for ' + type);
        }

        /**
         * Submit employee assignment form
         */
        function submitEmployeeAssignment(type) {
            const form = document.getElementById('employee-form-' + type);
            const assignButton = document.getElementById('action-button-' + type + '-employee');

            if (!form) {
                console.error('Employee form not found for type: ' + type);
                return;
            }

            // Check if employee is selected
            const employeeIdInput = document.getElementById('employee-id-input-' + type);
            if (!employeeIdInput || !employeeIdInput.value) {
                alert('Please select an employee');
                return;
            }

            // Get selected case IDs from checkboxes
            const checkboxes = document.querySelectorAll(`input[data-stage-type="${type}"][data-case-checkbox]:checked`);
            if (checkboxes.length === 0) {
                alert('No cases selected');
                return;
            }

            const caseIds = Array.from(checkboxes).map(cb => cb.getAttribute('data-case-id')).join(',');
            const caseIdsInput = document.getElementById('case-ids-input-' + type);
            if (caseIdsInput) {
                caseIdsInput.value = caseIds;
            }

            // Show loading state
            if (assignButton) {
                assignButton.disabled = true;
                assignButton.classList.add('btn-loading');
                assignButton.innerText = 'ASSIGNING...';
            }

            console.log('Submitting employee assignment:', {
                type: type,
                employeeId: employeeIdInput.value,
                caseIds: caseIds
            });

            // Submit the form
            form.submit();
        }

        /**
         * Close employee modal
         */
        function closeEmployeeModal(type) {
            const modalId = 'EmployeeDialog' + type;
            const modal = document.getElementById(modalId);

            if (!modal) {
                console.error(`Modal not found: ${modalId}`);
                return;
            }

            console.log(`Closing employee modal: ${modalId}`);

            // Remove focus if modal contains active element
            if (modal.contains(document.activeElement)) {
                document.activeElement.blur();
            }

            // Clear any pending animations
            const dialogContent = modal.querySelector('.alsolent-workflow-dialog');
            if (dialogContent) {
                dialogContent.classList.remove('fade-in');
                dialogContent.classList.add('fade-out');
            }

            // Reset employee selection
            modal.querySelectorAll('.alsolent-driver-card').forEach(card => {
                card.classList.remove('selected');
                const img = card.querySelector('.alsolent-driver-image');
                if (img) {
                    img.classList.add('grayscale');
                }
            });

            // Reset the assign button state
            const assignButton = document.getElementById('action-button-' + type + '-employee');
            if (assignButton) {
                assignButton.disabled = true;
                assignButton.classList.remove('btn-loading', 'disabled');
                assignButton.innerText = 'ASSIGN';
            }

            // Clear selected employee
            if (window.selectedEmployeeIds) {
                window.selectedEmployeeIds[type] = null;
            }

            // Reset form inputs
            const employeeInput = document.getElementById('employee-id-input-' + type);
            const caseIdsInput = document.getElementById('case-ids-input-' + type);
            if (employeeInput) employeeInput.value = '';
            if (caseIdsInput) caseIdsInput.value = '';

            // Hide dialog after animation completes
            setTimeout(() => {
                modal.classList.remove('active');
                if (dialogContent) {
                    dialogContent.classList.remove('fade-out', 'fade-in');
                }
            }, 300);
        }

        /**
         * Open employee modal
         */
        function openEmployeeModal(type) {
            const modalId = 'EmployeeDialog' + type;
            const modal = document.getElementById(modalId);

            if (!modal) {
                console.error(`Modal not found: ${modalId}`);
                return;
            }

            // Check if any cases are selected
            const checkboxes = document.querySelectorAll(`input[data-stage-type="${type}"][data-case-checkbox]:checked`);
            if (checkboxes.length === 0) {
                alert('Please select at least one case');
                return;
            }

            console.log(`Opening employee modal: ${modalId}`);

            // Show modal
            modal.classList.add('active');

            // Animate dialog content
            const dialogContent = modal.querySelector('.alsolent-workflow-dialog');
            if (dialogContent) {
                dialogContent.classList.add('fade-in');
            }
        }
    </script>

@endpush

<style>
    .solent-material-shell,
    .solent-material-shell * {
        box-sizing: border-box;
    }

    .solent-material-shell {
        --material-accent: #0f766e;
        --material-accent-bright: #14b8a6;
        --material-accent-soft: #f0fdfa;
        position: relative;
        width: 100%;
        margin: 0 auto 24px;
        padding: clamp(22px, 3vw, 40px);
        overflow: hidden;
        border: 1px solid rgba(15, 118, 110, .1);
        border-radius: 16px;
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
        box-shadow: 0 4px 12px rgba(15, 118, 110, .08), 0 1px 3px rgba(15, 23, 42, .05);
    }

    .solent-material-shell::before {
        position: absolute;
        inset: 0 0 auto;
        height: 4px;
        border-radius: 16px 16px 0 0;
        background: linear-gradient(90deg, var(--material-accent), var(--material-accent-bright), #22d3ee);
        content: "";
    }

    .solent-material-form {
        margin: 0;
    }

    .material-form-section {
        margin-bottom: 38px;
    }

    .material-form-section:last-of-type {
        margin-bottom: 30px;
    }

    .material-section-header {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        margin: -8px -8px 24px;
        padding: 16px 20px 12px;
        border-bottom: 2px solid var(--material-accent-soft);
        border-radius: 8px;
        color: #0f172a;
        font-size: 20px;
        font-weight: 700;
        line-height: 1.35;
    }

    .material-section-header::after {
        position: absolute;
        inset-inline-start: 20px;
        bottom: -2px;
        width: 60px;
        height: 3px;
        border-radius: 2px;
        background: linear-gradient(90deg, var(--material-accent), var(--material-accent-bright));
        content: "";
    }

    .material-section-header i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        color: var(--material-accent);
        font-size: 18px;
    }

    .material-info-grid {
        display: grid;
        grid-template-columns: minmax(190px, 1fr) minmax(170px, .9fr) minmax(260px, 1.45fr) minmax(170px, .8fr);
        gap: 28px 32px;
        align-items: start;
    }

    .material-field {
        min-width: 0;
        margin: 0;
    }

    .material-field-label {
        position: relative;
        display: block;
        margin: 0 0 9px;
        color: #0f172a;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .025em;
        line-height: 1.35;
        text-transform: uppercase;
    }

    .material-field-label::after {
        position: absolute;
        inset-inline-start: 0;
        bottom: -4px;
        width: 20px;
        height: 2px;
        border-radius: 1px;
        background: linear-gradient(90deg, var(--material-accent-bright), #22d3ee);
        content: "";
        opacity: .7;
    }

    .material-input {
        width: 100%;
        height: 48px;
        padding: 0 18px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        color: #1e293b;
        font-size: 15px;
        font-weight: 500;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .03);
        transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    .material-input:hover {
        border-color: #cbd5e1;
    }

    .material-input:focus {
        border-color: var(--material-accent);
        outline: 0;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(15, 118, 110, .12);
    }

    .material-input::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }

    .material-help-text {
        margin-top: 7px;
        color: #64748b;
        font-size: 12px;
        font-weight: 400;
        line-height: 1.45;
    }

    .material-error {
        display: block;
        margin-top: 6px;
        color: #dc2626;
        font-size: 12px;
        line-height: 1.4;
    }

    .solent-material-form .bootstrap-select,
    .solent-material-form .selectpicker {
        width: 100% !important;
    }

    .solent-material-form .bootstrap-select > .dropdown-toggle {
        min-height: 48px;
        margin: 0;
        padding: 10px 14px;
        border: 2px solid #e2e8f0 !important;
        border-radius: 10px !important;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
        color: #1e293b !important;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .03) !important;
    }

    .solent-material-form .bootstrap-select.show > .dropdown-toggle,
    .solent-material-form .bootstrap-select > .dropdown-toggle:focus,
    .solent-material-form .bootstrap-select > .dropdown-toggle:focus-visible {
        border-color: var(--material-accent) !important;
        outline: 0 !important;
        box-shadow: 0 0 0 4px rgba(15, 118, 110, .12) !important;
    }

    .solent-material-form .bootstrap-select .filter-option-inner-inner {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .material-toggle {
        position: relative;
        display: inline-flex;
        width: 54px;
        height: 30px;
        margin: 2px 0 0;
        direction: ltr;
    }

    .material-toggle input {
        position: absolute;
        inset: 0;
        z-index: 2;
        width: 100%;
        height: 100%;
        margin: 0;
        opacity: 0;
        cursor: pointer;
    }

    .material-toggle-track {
        position: absolute;
        inset: 0;
        border-radius: 999px;
        background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
        box-shadow: inset 0 2px 4px rgba(15, 23, 42, .1);
        transition: background .2s ease, box-shadow .2s ease;
    }

    .material-toggle-track::before {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #ffffff;
        box-shadow: 0 2px 4px rgba(15, 23, 42, .22);
        content: "";
        transition: transform .2s ease;
    }

    .material-toggle input:checked + .material-toggle-track {
        background: linear-gradient(135deg, var(--material-accent), var(--material-accent-bright));
    }

    .material-toggle input:checked + .material-toggle-track::before {
        transform: translateX(24px);
    }

    .material-toggle input:focus-visible + .material-toggle-track {
        outline: 2px solid var(--material-accent);
        outline-offset: 3px;
        box-shadow: 0 0 0 4px rgba(15, 118, 110, .12);
    }

    .material-workflow-stack {
        display: grid;
        gap: 28px;
    }

    .material-method-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 28px 40px;
    }

    .material-choice-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 16px;
        margin-top: 8px;
    }

    .material-choice {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        min-height: 44px;
        margin: 0;
        padding: 7px 10px;
        border-radius: 8px;
        color: #1f2937;
        font-size: 15px;
        font-weight: 600;
        line-height: 1.3;
        cursor: pointer;
        user-select: none;
        transition: background-color .18s ease, transform .18s ease;
    }

    .material-choice:hover,
    .material-choice:focus-within {
        background: rgba(15, 118, 110, .06);
    }

    .material-choice-control {
        position: relative;
        flex: 0 0 20px;
        width: 20px;
        height: 20px;
        direction: ltr;
    }

    .material-choice-control input {
        position: absolute;
        inset: 0;
        z-index: 2;
        width: 100%;
        height: 100%;
        margin: 0;
        opacity: 0;
        cursor: pointer;
    }

    .material-choice-indicator {
        position: absolute;
        inset: 0;
        border: 2px solid #cbd5e1;
        border-radius: 50%;
        background: linear-gradient(135deg, #ffffff, #f8fafc);
        box-shadow: 0 1px 3px rgba(15, 23, 42, .1);
        transition: border-color .18s ease, background-color .18s ease, box-shadow .18s ease;
    }

    .material-choice-control--checkbox .material-choice-indicator {
        border-radius: 6px;
    }

    .material-choice-control input:checked + .material-choice-indicator {
        border-color: var(--material-accent);
        background: var(--material-accent);
        box-shadow: 0 2px 8px rgba(15, 118, 110, .25);
    }

    .material-choice-control input:focus-visible + .material-choice-indicator {
        outline: 2px solid var(--material-accent);
        outline-offset: 3px;
    }

    .material-choice-control--radio input:checked + .material-choice-indicator::after {
        position: absolute;
        inset: 4px;
        border-radius: 50%;
        background: #ffffff;
        content: "";
    }

    .material-choice-control--checkbox input:checked + .material-choice-indicator::after {
        position: absolute;
        top: 2px;
        left: 6px;
        width: 5px;
        height: 10px;
        border: solid #ffffff;
        border-width: 0 2px 2px 0;
        content: "";
        transform: rotate(45deg);
    }

    .material-button-group {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        margin-top: 4px;
        padding-top: 30px;
        border-top: 2px solid var(--material-accent-soft);
    }

    .material-button-group::before {
        position: absolute;
        top: -2px;
        inset-inline-start: 50%;
        width: 80px;
        height: 2px;
        background: var(--material-accent-bright);
        content: "";
        transform: translateX(-50%);
    }

    .material-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 48px;
        margin: 0 !important;
        padding: 12px 22px;
        border: 0;
        border-radius: 9px;
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.2;
        cursor: pointer;
        transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    .material-action:hover,
    .material-action:focus-visible {
        color: #ffffff;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .material-action:focus-visible {
        outline: 3px solid rgba(20, 184, 166, .25);
        outline-offset: 2px;
    }

    .material-action--primary {
        background: linear-gradient(135deg, var(--material-accent), #0d9488);
        box-shadow: 0 6px 14px rgba(15, 118, 110, .2);
    }

    .material-action--primary:hover {
        background: linear-gradient(135deg, #115e59, var(--material-accent));
        box-shadow: 0 8px 18px rgba(15, 118, 110, .25);
    }

    .material-action--secondary {
        background: #6b7280;
        box-shadow: 0 4px 10px rgba(75, 85, 99, .16);
    }

    .material-action--secondary:hover {
        background: #4b5563;
    }

    @media (max-width: 1199.98px) {
        .material-info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .solent-material-shell {
            padding: 20px 16px;
            border-radius: 12px;
        }

        .material-section-header {
            margin-inline: 0;
            padding-inline: 4px;
            font-size: 18px;
        }

        .material-section-header::after {
            inset-inline-start: 4px;
        }

        .material-info-grid,
        .material-method-grid {
            grid-template-columns: minmax(0, 1fr);
            gap: 22px;
        }

        .material-form-section {
            margin-bottom: 30px;
        }

        .material-choice-group {
            gap: 4px 8px;
        }

        .material-choice {
            padding-inline: 8px;
        }
    }

    @media (max-width: 479.98px) {
        .material-button-group {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
        }

        .material-action {
            width: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .material-input,
        .material-toggle-track,
        .material-toggle-track::before,
        .material-choice,
        .material-choice-indicator,
        .material-action {
            transition: none;
        }
    }
</style>

<style>
    .user-management-card {
        background: var(--surface, #ffffff);
        border: 1px solid var(--border, rgba(0, 0, 0, .08));
        border-radius: 18px;
        box-shadow: 0 12px 34px var(--shadow-2, rgba(15, 23, 42, .07));
        margin: 0 auto;
        max-width: 1320px;
        overflow: hidden;
        width: 100%;
    }

    .user-management-card .kt-portlet {
        background: transparent;
        border: 0;
        border-radius: 0;
        box-shadow: none;
        margin: 0;
    }

    .user-management-card__header,
    .user-management-card .kt-portlet__head {
        align-items: center;
        display: flex;
        min-height: 84px;
        padding: 20px 28px;
        border-bottom: 1px solid var(--border, rgba(0, 0, 0, .08));
        background: linear-gradient(135deg, var(--surface, #ffffff), var(--surface-raised, #f8fafc));
    }

    .user-management-card__title,
    .user-management-card .kt-portlet__head-title {
        color: var(--text-1, #0f172a);
        font-size: clamp(22px, 2vw, 28px);
        font-weight: 750;
        line-height: 1.25;
        margin: 0;
    }

    .user-management-card__subtitle {
        color: var(--text-2, #64748b);
        font-size: 13px;
        line-height: 1.5;
        margin: 5px 0 0;
    }

    .user-management-card .kt-portlet__body {
        margin: 0;
        padding: 28px;
    }

    .user-management-form {
        color: var(--text-1, #0f172a);
    }

    .user-form-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .user-form-grid--security {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .user-form-grid .form-group {
        min-width: 0;
        margin-bottom: 0;
    }

    .user-management-form .form-group > label,
    .user-field-label {
        margin-bottom: 8px;
        color: var(--text-1, #0f172a);
        font-size: 14px;
        font-weight: 600;
    }

    .user-management-form .user-text-control {
        min-height: 42px;
        border: 1px solid #cbdbe3;
        border-radius: 9px;
        background: var(--surface, #ffffff);
        color: var(--text-1, #0f172a);
        box-shadow: none;
    }

    .user-management-form .user-text-control:focus {
        border-color: var(--accent, #6366f1);
        box-shadow: 0 0 0 3px var(--accent-bg, rgba(99, 102, 241, .12));
    }

    .user-management-form .user-text-control:disabled {
        background: var(--surface-raised, #f8fafc);
        color: var(--text-2, #64748b);
        cursor: not-allowed;
    }

    .user-form-section {
        margin-top: 28px;
    }

    .user-form-section__title {
        margin: 0 0 16px;
        padding-bottom: 11px;
        border-bottom: 1px solid var(--border, rgba(0, 0, 0, .08));
        color: #34455b;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.4;
    }

    .user-toggle-panel {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 42px;
        margin: 0;
        padding: 7px 12px;
        border: 1px solid #dbe3eb;
        border-radius: 9px;
        background: var(--surface-raised, #f8fafc);
        color: #4b5563;
        cursor: pointer;
        user-select: none;
    }

    .user-toggle-panel:hover {
        border-color: #c8d3df;
    }

    .user-switch {
        position: relative;
        display: inline-flex;
        flex: 0 0 44px;
        width: 44px;
        height: 24px;
        direction: ltr;
    }

    .user-switch__input {
        position: absolute;
        inset: 0;
        z-index: 2;
        width: 100%;
        height: 100%;
        margin: 0;
        opacity: 0;
        cursor: pointer;
    }

    .user-switch__track {
        position: absolute;
        inset: 0;
        border-radius: 999px;
        background: #cbd8e8;
        transition: background-color .18s ease;
    }

    .user-switch__track::after {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .24);
        content: "";
        transition: transform .18s ease;
    }

    .user-switch__input:checked + .user-switch__track {
        background: #22a447;
    }

    .user-switch__input:checked + .user-switch__track::after {
        transform: translateX(20px);
    }

    .user-switch__input:focus-visible + .user-switch__track {
        box-shadow: 0 0 0 3px var(--accent-bg, rgba(99, 102, 241, .18));
        outline: 2px solid var(--accent, #6366f1);
        outline-offset: 2px;
    }

    .user-toggle-panel__text {
        min-width: 0;
        color: #4b5563;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.35;
    }

    .permissions-box {
        display: grid;
        grid-template-columns: repeat(5, minmax(170px, 1fr));
        gap: 4px 12px;
        width: 100%;
        max-height: min(420px, 55vh);
        padding: 12px;
        overflow-y: auto;
        overscroll-behavior: contain;
        scrollbar-gutter: stable;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #dbe3eb;
        border-radius: 9px;
        background: var(--surface, #ffffff);
        box-shadow: 0 1px 3px var(--shadow-2, rgba(0, 0, 0, .04));
        transition: opacity .18s ease, background-color .18s ease;
    }

    .permissions-box.is-disabled {
        background: var(--surface-raised, #f8fafc);
        opacity: .64;
    }

    .permission-item {
        position: relative;
        display: flex;
        align-items: center;
        min-width: 0;
        min-height: 44px;
        margin: 0;
        padding: 7px 8px;
        border-radius: 7px;
        cursor: pointer;
        user-select: none;
    }

    .permission-item:hover,
    .permission-item:focus-within {
        background: var(--surface-raised, #f8fafc);
    }

    .permission-item.is-disabled {
        cursor: not-allowed;
    }

    .permissions-box.is-disabled .permission-item:hover,
    .permissions-box.is-disabled .permission-item:focus-within {
        background: transparent;
    }

    .permission-checkbox {
        position: absolute;
        width: 1px;
        height: 1px;
        opacity: 0;
    }

    .permission-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 24px;
        width: 24px;
        height: 24px;
        margin-inline-end: 12px;
        border-radius: 50%;
        color: #ffffff;
        font-size: 13px;
        line-height: 1;
    }

    .permission-icon-off {
        background: var(--danger, #ef4444);
    }

    .permission-icon-on {
        display: none;
        background: #22a447;
    }

    .permission-checkbox:checked + .permission-icon-off {
        display: none;
    }

    .permission-checkbox:checked + .permission-icon-off + .permission-icon-on {
        display: inline-flex;
    }

    .permission-checkbox:focus-visible + .permission-icon-off,
    .permission-checkbox:focus-visible + .permission-icon-off + .permission-icon-on {
        outline: 2px solid var(--accent, #6366f1);
        outline-offset: 2px;
    }

    .permission-name {
        min-width: 0;
        color: #3f5065;
        font-size: 14px;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .permission-checkbox:disabled ~ .permission-icon,
    .permission-checkbox:disabled ~ .permission-name {
        opacity: .62;
    }

    .user-management-form .user-image-picker-container {
        margin-bottom: 0;
    }

    .user-management-card .kt-portlet__foot {
        background: var(--surface-raised, #f8fafc);
        border-top: 1px solid var(--border, rgba(0, 0, 0, .08));
        padding: 18px 28px;
    }

    .user-management-card .kt-form__actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .user-management-card .kt-form__actions .btn {
        align-items: center;
        display: inline-flex;
        justify-content: center;
        margin: 0;
        min-height: 44px;
        min-width: 112px;
    }

    @media (max-width: 1399.98px) {
        .permissions-box {
            grid-template-columns: repeat(4, minmax(160px, 1fr));
        }
    }

    @media (max-width: 1199.98px) {
        .user-form-grid,
        .permissions-box {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .user-form-grid--security {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .user-form-grid,
        .user-form-grid--security,
        .permissions-box {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .user-management-card {
            border-radius: 14px;
        }

        .user-management-card__header,
        .user-management-card .kt-portlet__head {
            min-height: 74px;
            padding: 16px;
        }

        .user-management-card .kt-portlet__body {
            padding: 18px 16px;
        }

        .user-form-grid,
        .user-form-grid--security,
        .permissions-box {
            grid-template-columns: minmax(0, 1fr);
        }

        .permissions-box {
            max-height: min(430px, 58vh);
            padding: 8px;
        }

        .permission-item {
            padding-inline: 8px;
        }

        .user-management-card .kt-portlet__foot {
            padding: 16px;
        }

        .user-management-card .kt-form__actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .user-management-card .kt-form__actions .btn {
            min-height: 44px;
            margin: 0;
            min-width: 0;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .user-switch__track,
        .user-switch__track::after,
        .permissions-box {
            transition: none;
        }
    }
</style>

@extends('layouts.app')

@section('content')
    <div x-data="ledgerApp()" x-init="init()"
        class="ledger-page p-3 sm:p-4 md:p-6 w-full h-full overflow-x-hidden"
        :class="{ 'is-waiting': isWaiting, 'is-booting': isBooting }">
        <style>
            .ledger-page {
                color: var(--text-primary);
                background: transparent;
            }

            .ledger-data {
                text-transform: uppercase;
            }

            .ledger-page .no-scrollbar::-webkit-scrollbar {
                display: none;
            }

            .ledger-page .no-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            .ledger-page .smooth-scroll {
                scroll-behavior: smooth;
            }

            .ledger-card {
                background: #FFFFFF;
                border-radius: 24px;
                border: 1px solid rgba(0, 0, 0, 0.04);
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
                min-width: 0;
                overflow: hidden;
            }

            html.dark .ledger-card {
                background: #1C1C1E;
                border-color: rgba(255, 255, 255, 0.08);
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.45);
            }

            /* Balance flat — no nested box */
            .ledger-balance {
                background: transparent;
                border: 0;
                border-radius: 0;
                padding: 4px 2px 2px;
                box-shadow: none;
            }

            .ledger-balance-label {
                font-size: 13px;
                font-weight: 500;
                color: #8E8E93;
                letter-spacing: -0.01em;
            }

            .ledger-balance-value {
                margin-top: 6px;
                font-size: clamp(30px, 5vw, 38px);
                font-weight: 500;
                letter-spacing: -0.045em;
                line-height: 1.08;
                color: #1D1D1F;
            }

            html.dark .ledger-balance-value {
                color: #F5F5F7;
            }

            .ledger-balance-meta {
                margin-top: 8px;
                font-size: 12px;
                color: #AEAEB2;
                font-weight: 400;
            }

            .ledger-balance-meta span {
                color: #AEAEB2;
                font-weight: 400;
            }

            .ledger-action {
                width: 100%;
                height: 58px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 4px;
                border-radius: 16px;
                background: #FFFFFF;
                border: 1px solid rgba(0, 0, 0, 0.04);
                color: var(--text-primary);
                box-shadow:
                    0 1px 2px rgba(0, 0, 0, 0.04),
                    0 4px 12px rgba(0, 0, 0, 0.06);
                transition:
                    transform 160ms ease,
                    border-color 160ms ease,
                    background-color 160ms ease,
                    box-shadow 160ms ease;
            }

            html.dark .ledger-action {
                background: #2C2C2E;
                border-color: rgba(255, 255, 255, 0.06);
                box-shadow:
                    0 1px 2px rgba(0, 0, 0, 0.25),
                    0 6px 16px rgba(0, 0, 0, 0.35);
            }

            .ledger-action:hover {
                border-color: rgba(0, 122, 255, 0.18);
                background: var(--accent-soft);
                box-shadow:
                    0 2px 6px rgba(0, 0, 0, 0.04),
                    0 8px 18px rgba(0, 122, 255, 0.10);
            }

            html.dark .ledger-action:hover {
                border-color: rgba(10, 132, 255, 0.28);
                box-shadow:
                    0 2px 6px rgba(0, 0, 0, 0.3),
                    0 8px 20px rgba(0, 0, 0, 0.4);
            }

            .ledger-action:active {
                transform: scale(0.97);
            }

            .ledger-action svg {
                width: 22px;
                height: 22px;
                color: #636366;
            }

            html.dark .ledger-action svg {
                color: #AEAEB2;
            }

            .ledger-action:hover svg {
                color: var(--accent);
            }

            .ledger-action span {
                font-size: 12px;
                font-weight: 600;
                color: #636366;
            }

            html.dark .ledger-action span {
                color: #AEAEB2;
            }

            .ledger-wallet {
                position: relative;
                display: flex;
                align-items: center;
                gap: 12px;
                width: 100%;
                text-align: left;
                padding: 14px 12px 14px 14px;
                border-radius: 16px;
                background: transparent;
                border: 0;
                transition:
                    background-color 420ms cubic-bezier(0.22, 1, 0.36, 1),
                    transform 420ms cubic-bezier(0.22, 1, 0.36, 1);
            }

            .ledger-wallet:hover {
                background: rgba(0, 0, 0, 0.025);
            }

            html.dark .ledger-wallet:hover {
                background: rgba(255, 255, 255, 0.05);
            }

            .ledger-wallet.is-on {
                background: rgba(0, 122, 255, 0.08);
            }

            html.dark .ledger-wallet.is-on {
                background: rgba(10, 132, 255, 0.16);
            }

            .ledger-wallet-bar {
                position: absolute;
                left: 0;
                top: 50%;
                width: 4px;
                height: 62%;
                border-radius: 999px;
                background: var(--accent);
                transform: translateY(-50%) scaleY(0.35);
                opacity: 0;
                transform-origin: center;
                transition:
                    opacity 380ms cubic-bezier(0.22, 1, 0.36, 1),
                    transform 420ms cubic-bezier(0.22, 1, 0.36, 1);
                pointer-events: none;
            }

            .ledger-wallet.is-on .ledger-wallet-bar {
                opacity: 1;
                transform: translateY(-50%) scaleY(1);
            }

            /* Icon flat — no box */
            .ledger-wallet-icon {
                width: 28px;
                height: 28px;
                border-radius: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                background: transparent;
                border: 0;
                color: #3A3A3C;
                transition: color 380ms cubic-bezier(0.22, 1, 0.36, 1);
            }

            html.dark .ledger-wallet-icon {
                color: #D1D1D6;
            }

            .ledger-wallet-icon svg {
                width: 22px;
                height: 22px;
                color: currentColor !important;
                stroke: currentColor;
                transition: color 380ms cubic-bezier(0.22, 1, 0.36, 1);
            }

            .ledger-wallet.is-on .ledger-wallet-icon {
                color: var(--accent);
            }

            .ledger-wallet.is-on .ledger-wallet-icon svg {
                color: var(--accent) !important;
            }

            .ledger-wallet-name {
                font-size: 15px;
                font-weight: 600;
                color: #1D1D1F;
                letter-spacing: -0.02em;
            }

            html.dark .ledger-wallet-name {
                color: #F5F5F7;
            }

            .ledger-wallet-value {
                font-size: 14px;
                font-weight: 600;
                color: #1D1D1F;
                letter-spacing: -0.02em;
            }

            html.dark .ledger-wallet-value {
                color: #F5F5F7;
            }

            .ledger-wallet-note,
            .ledger-wallet-type {
                font-size: 12px;
                color: #8E8E93;
            }

            .ledger-wallet-chevron {
                width: 18px;
                height: 18px;
                flex-shrink: 0;
                color: #C7C7CC;
                margin-left: 4px;
                transition: color 380ms cubic-bezier(0.22, 1, 0.36, 1), transform 420ms cubic-bezier(0.22, 1, 0.36, 1);
            }

            .ledger-wallet.is-on .ledger-wallet-chevron {
                color: var(--accent);
                transform: translateX(2px);
            }

            .ledger-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                padding-bottom: 14px;
                border-bottom: 1px solid #E5E5EA;
                min-height: 58px;
            }

            html.dark .ledger-head {
                border-bottom-color: #38383A;
            }

            .ledger-head h2 {
                font-size: 20px;
                font-weight: 500;
                letter-spacing: -0.045em;
                color: #1D1D1F;
                white-space: nowrap;
                overflow: hidden;
                flex: 1 1 auto;
                min-width: 0;
                max-width: 420px;
                transform-origin: left center;
                transition:
                    opacity 280ms cubic-bezier(0.32, 0.72, 0, 1),
                    transform 420ms cubic-bezier(0.32, 0.72, 0, 1),
                    max-width 420ms cubic-bezier(0.32, 0.72, 0, 1);
            }

            .ledger-head h2.is-away {
                opacity: 0;
                transform: translateX(-12px) scale(0.98);
                max-width: 0;
                flex: 0 0 0;
                pointer-events: none;
            }

            html.dark .ledger-head h2 {
                color: #F5F5F7;
            }

            .ledger-icon-btn {
                width: 36px;
                height: 36px;
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #636366;
                background: #F2F2F7;
                border: 0;
            }

            html.dark .ledger-icon-btn {
                background: #2C2C2E;
                color: #AEAEB2;
            }

            .ledger-icon-btn:hover {
                background: #E5E5EA;
                color: #1D1D1F;
            }

            html.dark .ledger-icon-btn:hover {
                background: #3A3A3C;
                color: #F5F5F7;
            }

            /* Morph: icon button → search field */
            .ledger-search-morph {
                position: relative;
                display: flex;
                align-items: center;
                flex: 0 0 auto;
                width: 36px;
                height: 36px;
                margin-left: auto;
                border-radius: 999px;
                background: #F2F2F7;
                overflow: hidden;
                will-change: width, border-radius;
                transition:
                    width 480ms cubic-bezier(0.32, 0.72, 0, 1),
                    max-width 480ms cubic-bezier(0.32, 0.72, 0, 1),
                    flex-grow 480ms cubic-bezier(0.32, 0.72, 0, 1),
                    height 480ms cubic-bezier(0.32, 0.72, 0, 1),
                    border-radius 480ms cubic-bezier(0.32, 0.72, 0, 1),
                    background-color 280ms ease,
                    box-shadow 320ms cubic-bezier(0.32, 0.72, 0, 1),
                    padding 480ms cubic-bezier(0.32, 0.72, 0, 1);
            }

            .ledger-search-morph.is-open {
                flex: 1 1 auto;
                width: 100%;
                max-width: 100%;
                height: 44px;
                border-radius: 12px;
                padding: 0 8px 0 2px;
                background: #F2F2F7;
                box-shadow: none;
            }

            html.dark .ledger-search-morph {
                background: #2C2C2E;
            }

            html.dark .ledger-search-morph.is-open {
                background: #2C2C2E;
                box-shadow: none;
            }

            .ledger-search-morph:not(.is-open):hover {
                background: #E5E5EA;
            }

            html.dark .ledger-search-morph:not(.is-open):hover {
                background: #3A3A3C;
            }

            .ledger-search-trigger {
                flex: 0 0 36px;
                width: 36px;
                height: 36px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 0;
                background: transparent;
                color: #636366;
                cursor: pointer;
                transition:
                    color 220ms ease,
                    transform 420ms cubic-bezier(0.32, 0.72, 0, 1);
            }

            .ledger-search-morph.is-open .ledger-search-trigger {
                flex-basis: 36px;
                width: 36px;
                height: 44px;
                color: #8E8E93;
                pointer-events: none;
                transform: none;
            }

            html.dark .ledger-search-trigger {
                color: #AEAEB2;
            }

            .ledger-page .ledger-search-morph input.ledger-search-field,
            .ledger-page .ledger-search-morph input.ledger-search-field:focus {
                flex: 1 1 auto;
                align-self: stretch;
                min-width: 0;
                width: 0;
                height: auto !important;
                min-height: 0 !important;
                opacity: 0;
                appearance: none;
                -webkit-appearance: none;
                border: 0 !important;
                border-radius: 0 !important;
                outline: none !important;
                box-shadow: none !important;
                background: transparent !important;
                background-color: transparent !important;
                color: #1D1D1F !important;
                font-size: 15px !important;
                font-weight: 500;
                letter-spacing: -0.01em;
                padding: 0 8px 0 0 !important;
                margin: 0 !important;
                line-height: 44px;
                transform: translateX(8px);
                pointer-events: none;
                transition:
                    opacity 260ms cubic-bezier(0.32, 0.72, 0, 1) 80ms,
                    transform 420ms cubic-bezier(0.32, 0.72, 0, 1),
                    width 0ms linear 480ms;
            }

            .ledger-page .ledger-search-morph.is-open input.ledger-search-field,
            .ledger-page .ledger-search-morph.is-open input.ledger-search-field:focus {
                width: auto;
                opacity: 1;
                transform: translateX(0);
                pointer-events: auto;
                border: 0 !important;
                box-shadow: none !important;
                background: transparent !important;
                background-color: transparent !important;
                transition:
                    opacity 300ms cubic-bezier(0.32, 0.72, 0, 1) 120ms,
                    transform 420ms cubic-bezier(0.32, 0.72, 0, 1) 40ms,
                    width 0ms linear 0ms;
            }

            html.dark .ledger-page .ledger-search-morph input.ledger-search-field,
            html.dark .ledger-page .ledger-search-morph input.ledger-search-field:focus {
                color: #F5F5F7 !important;
                background: transparent !important;
                background-color: transparent !important;
            }

            .ledger-search-field::placeholder {
                color: #8E8E93;
                font-weight: 500;
                opacity: 1;
            }

            .ledger-search-close {
                flex: 0 0 28px;
                width: 28px;
                height: 28px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 999px;
                background: rgba(60, 60, 67, 0.12);
                color: #636366;
                cursor: pointer;
                opacity: 0;
                transform: scale(0.55);
                pointer-events: none;
                transition:
                    opacity 240ms cubic-bezier(0.32, 0.72, 0, 1),
                    transform 420ms cubic-bezier(0.32, 0.72, 0, 1),
                    background-color 200ms ease;
            }

            .ledger-search-morph.is-open .ledger-search-close {
                opacity: 1;
                transform: scale(1);
                pointer-events: auto;
                transition-delay: 140ms, 140ms, 0ms;
            }

            .ledger-search-close:hover {
                background: rgba(60, 60, 67, 0.18);
                color: #1D1D1F;
            }

            html.dark .ledger-search-close {
                background: rgba(255, 255, 255, 0.12);
                color: #AEAEB2;
            }

            html.dark .ledger-search-close:hover {
                background: rgba(255, 255, 255, 0.18);
                color: #F5F5F7;
            }

            .ledger-label {
                font-size: 13px;
                font-weight: 600;
                color: #8E8E93;
            }

            /* Soft white pills + sliding blue */
            .ledger-seg-scroll {
                overflow-x: auto;
                overflow-y: hidden;
            }

            .ledger-seg-track {
                position: relative;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                min-width: max-content;
                padding: 1px;
            }

            .ledger-seg-pill {
                position: absolute;
                top: 1px;
                left: 0;
                z-index: 0;
                height: 36px;
                width: 48px;
                border-radius: 999px;
                background: #007AFF;
                transform: translate3d(0, 0, 0);
                transition:
                    transform 480ms cubic-bezier(0.22, 1, 0.36, 1),
                    width 480ms cubic-bezier(0.22, 1, 0.36, 1),
                    height 480ms cubic-bezier(0.22, 1, 0.36, 1);
                pointer-events: none;
                will-change: transform, width;
            }

            html.dark .ledger-seg-pill {
                background: #007AFF;
            }

            .ledger-chip,
            .ledger-day {
                position: relative;
                z-index: 1;
                padding: 8px 16px;
                border-radius: 999px;
                font-size: 13px;
                font-weight: 600;
                white-space: nowrap;
                background: #FFFFFF;
                color: #1D1D1F;
                border: 1px solid rgba(0, 0, 0, 0.04);
                box-shadow:
                    0 1px 2px rgba(0, 0, 0, 0.04),
                    0 3px 10px rgba(0, 0, 0, 0.05);
                transition:
                    color 280ms cubic-bezier(0.22, 1, 0.36, 1),
                    background-color 280ms cubic-bezier(0.22, 1, 0.36, 1),
                    border-color 280ms cubic-bezier(0.22, 1, 0.36, 1),
                    box-shadow 280ms cubic-bezier(0.22, 1, 0.36, 1);
            }

            html.dark .ledger-chip,
            html.dark .ledger-day {
                background: #1C1C1E;
                color: #F5F5F7;
                border-color: rgba(255, 255, 255, 0.06);
                box-shadow:
                    0 1px 2px rgba(0, 0, 0, 0.25),
                    0 4px 12px rgba(0, 0, 0, 0.35);
            }

            .ledger-chip:hover,
            .ledger-day:hover {
                border-color: rgba(0, 0, 0, 0.06);
                box-shadow:
                    0 2px 4px rgba(0, 0, 0, 0.04),
                    0 6px 14px rgba(0, 0, 0, 0.07);
            }

            html.dark .ledger-chip:hover,
            html.dark .ledger-day:hover {
                border-color: rgba(255, 255, 255, 0.10);
                box-shadow:
                    0 2px 4px rgba(0, 0, 0, 0.3),
                    0 6px 16px rgba(0, 0, 0, 0.4);
            }

            .ledger-chip.is-on,
            .ledger-day.is-on {
                background: #007AFF !important;
                color: #ffffff !important;
                border-color: transparent !important;
                box-shadow: 0 4px 12px rgba(0, 122, 255, 0.28) !important;
            }

            html.dark .ledger-chip.is-on,
            html.dark .ledger-day.is-on {
                background: #007AFF !important;
                color: #ffffff !important;
                box-shadow: 0 4px 12px rgba(0, 122, 255, 0.28) !important;
            }

            .ledger-day {
                min-width: 48px;
                height: 36px;
                padding: 0 12px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .ledger-chip {
                height: 36px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .ledger-divider {
                border-color: #E5E5EA !important;
            }

            html.dark .ledger-divider {
                border-color: #38383A !important;
            }

            .ledger-dropdown {
                background: #FFFFFF;
                border: 1px solid rgba(0, 0, 0, 0.06);
                border-radius: 16px;
                box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
            }

            html.dark .ledger-dropdown {
                background: #1C1C1E;
                border-color: rgba(255, 255, 255, 0.08);
            }

            .ledger-month-btn {
                height: 40px;
                border-radius: 12px;
                font-size: 13px;
                font-weight: 600;
                background: #F2F2F7;
                color: #1D1D1F;
                border: 0;
            }

            html.dark .ledger-month-btn {
                background: #2C2C2E;
                color: #F5F5F7;
            }

            .ledger-month-btn.is-on {
                background: var(--accent);
                color: #ffffff;
            }

            .ledger-trx-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
                flex: 1;
                min-height: 0;
                padding: 2px 1px 8px;
            }

            .ledger-trx {
                background: #FFFFFF;
                border-radius: 18px;
                padding: 16px 18px;
                border: 1px solid rgba(0, 0, 0, 0.06);
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
                transition:
                    transform 240ms cubic-bezier(0.22, 1, 0.36, 1),
                    box-shadow 240ms cubic-bezier(0.22, 1, 0.36, 1),
                    border-color 240ms cubic-bezier(0.22, 1, 0.36, 1);
            }

            html.dark .ledger-trx {
                background: #1C1C1E;
                border-color: rgba(255, 255, 255, 0.08);
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.45);
            }

            .ledger-trx:hover {
                transform: translateY(-1px);
                border-color: rgba(0, 0, 0, 0.10);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            }

            html.dark .ledger-trx:hover {
                border-color: rgba(255, 255, 255, 0.14);
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.55);
            }

            .ledger-trx.is-open {
                border-color: rgba(0, 0, 0, 0.10);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            }

            html.dark .ledger-trx.is-open {
                border-color: rgba(255, 255, 255, 0.14);
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.55);
            }

            .ledger-trx-title {
                font-size: 14px;
                font-weight: 600;
                color: #1D1D1F;
                letter-spacing: -0.01em;
            }

            html.dark .ledger-trx-title {
                color: #F5F5F7;
            }

            .ledger-trx-amt {
                font-size: 14px;
                font-weight: 700;
                letter-spacing: -0.02em;
            }

            .ledger-trx-amt.is-in {
                color: #34C759;
            }

            .ledger-trx-amt.is-out {
                color: #FF3B30;
            }

            html.dark .ledger-trx-amt.is-in {
                color: #30D158;
            }

            html.dark .ledger-trx-amt.is-out {
                color: #FF453A;
            }

            .ledger-trx-detail {
                margin-top: 14px;
                padding: 0;
                border-radius: 14px;
                background: #FFFFFF;
                border: 1px solid rgba(60, 60, 67, 0.12);
                overflow: hidden;
                font-size: 13px;
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
            }

            html.dark .ledger-trx-detail {
                background: #2C2C2E;
                border-color: rgba(255, 255, 255, 0.10);
                box-shadow: none;
            }

            .ledger-trx-detail-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                padding: 8px 14px;
            }

            .ledger-trx-detail-row:first-child {
                padding-top: 14px;
            }

            .ledger-trx-detail-row:last-of-type {
                padding-bottom: 14px;
            }

            .ledger-trx-detail .k {
                color: #8E8E93;
                font-size: 12px;
                font-weight: 500;
            }

            .ledger-trx-detail .v {
                color: #1D1D1F;
                font-weight: 600;
                font-size: 13px;
                letter-spacing: -0.01em;
            }

            html.dark .ledger-trx-detail .v {
                color: #F5F5F7;
            }

            .ledger-trx-detail-actions {
                padding: 12px 14px;
                background: #FAFAFC;
                border-top: 1px solid rgba(60, 60, 67, 0.10);
                display: flex;
                justify-content: flex-end;
            }

            html.dark .ledger-trx-detail-actions {
                background: #242426;
                border-top-color: rgba(84, 84, 88, 0.45);
            }

            .ledger-trx-delete {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 9px 14px;
                border-radius: 12px;
                font-size: 13px;
                font-weight: 600;
                border: 0;
                background: #FF3B30;
                color: #FFFFFF;
                transition:
                    background-color 200ms ease,
                    transform 200ms cubic-bezier(0.22, 1, 0.36, 1);
            }

            .ledger-trx-delete:hover {
                background: #E0352B;
            }

            .ledger-trx-delete:active {
                transform: scale(0.97);
            }

            html.dark .ledger-trx-delete {
                background: #FF453A;
                color: #FFFFFF;
            }

            html.dark .ledger-trx-delete:hover {
                background: #E03E35;
            }

            .ledger-empty {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 12px;
                flex: 1;
                min-height: 220px;
                text-align: center;
                color: #8E8E93;
                font-size: 14px;
                margin-top: 8px;
                border: 1px solid #E5E5EA;
                border-radius: 18px;
                padding: 40px 16px;
                background: transparent;
            }

            html.dark .ledger-empty {
                border-color: #38383A;
                color: #8E8E93;
            }

            .ledger-empty svg {
                width: 36px;
                height: 36px;
                color: #C7C7CC;
            }

            .ledger-modal-shell {
                background: #FFFFFF;
                color: var(--text-primary);
                border-radius: 20px;
                border: 1px solid rgba(0, 0, 0, 0.06);
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.14);
            }

            html.dark .ledger-modal-shell {
                background: #1C1C1E;
                border-color: rgba(255, 255, 255, 0.08);
            }

            .ledger-modal-shell label {
                color: #6E6E73;
                font-size: 13px;
                font-weight: 600;
            }

            .ledger-page .ledger-modal-shell input,
            .ledger-page .ledger-modal-shell select {
                width: 100%;
                margin-top: 6px;
                padding: 12px 14px !important;
                font-size: 14px !important;
                line-height: 1.35 !important;
                background-color: #FFFFFF !important;
                border: 1.5px solid #D1D1D6 !important;
                color: #1D1D1F !important;
                border-radius: 12px !important;
                box-shadow: none !important;
                outline: none !important;
                text-transform: none !important;
                -webkit-appearance: none;
                appearance: none;
            }

            .ledger-page .ledger-modal-shell select {
                background-color: #FFFFFF !important;
                border-color: #D1D1D6 !important;
                font-weight: 500;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23636666'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") !important;
                background-repeat: no-repeat !important;
                background-position: right 12px center !important;
                background-size: 16px 16px !important;
                padding-right: 36px !important;
            }

            .ledger-page .ledger-modal-shell input::placeholder {
                text-transform: none !important;
                color: #8E8E93 !important;
                opacity: 1;
            }

            .ledger-page .ledger-modal-shell input:focus,
            .ledger-page .ledger-modal-shell select:focus {
                border-color: var(--accent) !important;
                box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.14) !important;
                background-color: #FFFFFF !important;
            }

            html.dark .ledger-page .ledger-modal-shell input,
            html.dark .ledger-page .ledger-modal-shell select {
                background-color: #1C1C1E !important;
                border-color: #48484A !important;
                color: #F5F5F7 !important;
            }

            html.dark .ledger-page .ledger-modal-shell select {
                background-color: #1C1C1E !important;
            }

            html.dark .ledger-page .ledger-modal-shell input:focus,
            html.dark .ledger-page .ledger-modal-shell select:focus {
                background-color: #1C1C1E !important;
                border-color: var(--accent) !important;
            }

            .ledger-btn-ghost {
                padding: 10px 16px;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 600;
                background: #F2F2F7;
                color: var(--text-primary);
                border: 0;
            }

            html.dark .ledger-btn-ghost {
                background: #2C2C2E;
            }

            .ledger-btn-primary {
                padding: 10px 18px;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 600;
                color: #ffffff;
                border: 0;
                background: var(--accent);
            }

            .ledger-btn-danger {
                padding: 10px 18px;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 600;
                color: #ffffff;
                border: 0;
                background: #FF3B30;
            }

            html.dark .ledger-btn-danger {
                background: #FF453A;
            }

            /* Entrance — soft fade after overlay */
            @keyframes ledgerSoftIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                    filter: blur(8px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                    filter: blur(0);
                }
            }

            @keyframes ledgerChipSlide {
                from {
                    opacity: 0;
                    transform: translateX(16px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            .ledger-page.is-waiting .ledger-enter-card,
            .ledger-page.is-waiting .ledger-enter-block,
            .ledger-page.is-waiting .ledger-chip,
            .ledger-page.is-waiting .ledger-day,
            .ledger-page.is-waiting .ledger-wallet,
            .ledger-page.is-waiting .ledger-trx,
            .ledger-page.is-waiting .ledger-action {
                opacity: 0;
            }

            .ledger-page.is-booting .ledger-enter-card {
                opacity: 0;
                animation: ledgerSoftIn 700ms cubic-bezier(0.16, 1, 0.3, 1) both;
            }

            .ledger-page.is-booting .ledger-enter-card[data-enter="1"] {
                animation-delay: 80ms;
            }

            .ledger-page.is-booting .ledger-enter-card[data-enter="2"] {
                animation-delay: 180ms;
            }

            .ledger-page.is-booting .ledger-enter-block {
                opacity: 0;
                animation: ledgerSoftIn 640ms cubic-bezier(0.16, 1, 0.3, 1) both;
            }

            .ledger-page.is-booting .ledger-enter-block[data-enter="1"] {
                animation-delay: 220ms;
            }

            .ledger-page.is-booting .ledger-enter-block[data-enter="2"] {
                animation-delay: 280ms;
            }

            .ledger-page.is-booting .ledger-action {
                opacity: 0;
                animation: ledgerSoftIn 600ms cubic-bezier(0.16, 1, 0.3, 1) both;
            }

            .ledger-page.is-booting .ledger-action:nth-child(1) {
                animation-delay: 200ms;
            }

            .ledger-page.is-booting .ledger-action:nth-child(2) {
                animation-delay: 260ms;
            }

            .ledger-page.is-booting .ledger-wallet {
                opacity: 0;
                animation: ledgerSoftIn 580ms cubic-bezier(0.16, 1, 0.3, 1) both;
                animation-delay: calc(300ms + (var(--i, 0) * 45ms));
            }

            .ledger-page.is-booting .ledger-chip,
            .ledger-page.is-booting .ledger-day {
                opacity: 0;
                animation: ledgerChipSlide 480ms cubic-bezier(0.16, 1, 0.3, 1) both;
                animation-delay: calc(260ms + (var(--i, 0) * 22ms));
            }

            .ledger-page.is-booting .ledger-trx {
                opacity: 0;
                animation: ledgerSoftIn 620ms cubic-bezier(0.16, 1, 0.3, 1) both;
                animation-delay: calc(420ms + (var(--i, 0) * 40ms));
            }

            @media (prefers-reduced-motion: reduce) {
                .ledger-page.is-waiting .ledger-enter-card,
                .ledger-page.is-waiting .ledger-enter-block,
                .ledger-page.is-waiting .ledger-chip,
                .ledger-page.is-waiting .ledger-day,
                .ledger-page.is-waiting .ledger-wallet,
                .ledger-page.is-waiting .ledger-trx,
                .ledger-page.is-waiting .ledger-action,
                .ledger-page.is-booting .ledger-enter-card,
                .ledger-page.is-booting .ledger-enter-block,
                .ledger-page.is-booting .ledger-chip,
                .ledger-page.is-booting .ledger-day,
                .ledger-page.is-booting .ledger-wallet,
                .ledger-page.is-booting .ledger-trx,
                .ledger-page.is-booting .ledger-action {
                    animation: none !important;
                    opacity: 1 !important;
                    transform: none !important;
                    filter: none !important;
                }
            }
        </style>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_2fr] gap-4 max-w-[1800px] mx-auto">

            <!-- LEFT: Balance & Wallets -->
            <aside class="ledger-card ledger-enter-card p-6 flex flex-col space-y-5" data-enter="1">

                <div class="ledger-balance">
                    <h3 class="ledger-balance-label">Sisa Saldo</h3>
                    <p class="ledger-balance-value" x-text="formatCurrency(totalSaldo)"></p>
                    <p class="ledger-balance-meta">
                        Terakhir di update <span x-text="lastUpdated"></span>
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 select-none">
                    <button type="button" @click="openModal('IN')" class="ledger-action">
                        <x-heroicon-o-arrow-down-tray />
                        <span>Masuk</span>
                    </button>
                    <button type="button" @click="openModal('OUT')" class="ledger-action">
                        <x-heroicon-o-arrow-up-tray />
                        <span>Keluar</span>
                    </button>
                </div>

                <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-90"
                    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm"
                    @click.self="closeModal">

                    <div class="ledger-modal-shell w-[90%] max-w-md p-6 relative">
                        <button @click="closeModal"
                            class="absolute top-3 right-3 text-[color:var(--text-muted)] hover:text-[color:var(--text-primary)] transition">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>

                        <div class="mb-5 text-center">
                            <h2 class="text-xl font-bold"
                                :class="modalType === 'IN' ? 'text-[color:var(--accent)]' : 'text-[#AF52DE]'"
                                x-text="modalType === 'IN' ? 'Tambah Pemasukan' : 'Tambah Pengeluaran'"></h2>
                            <p class="text-sm mt-1" style="color: var(--text-muted)">
                                Lengkapi detail pembukuan di bawah ini
                            </p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label>Deskripsi</label>
                                <input type="text" x-model="form.deskripsi"
                                    class="w-full mt-1 text-sm p-2.5 focus:outline-none"
                                    placeholder="Contoh: Penjualan Pulsa, Bayar Listrik, dll" />
                            </div>

                            <div>
                                <label>Nominal</label>
                                <input type="text" x-model="form.nominalDisplay" @input="formatNominal"
                                    inputmode="numeric" class="w-full mt-1 text-sm p-2.5 focus:outline-none"
                                    placeholder="Rp Masukkan jumlah uang" />
                            </div>

                            <div>
                                <label>Wallet</label>
                                <select x-model="form.cashbook_wallet_id"
                                    class="w-full mt-1 text-sm p-2.5 focus:outline-none">
                                    <template x-for="w in wallets.filter(w => w.id !== 0)" :key="w.id">
                                        <option :value="w.id" x-text="w.name"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="closeModal" class="ledger-btn-ghost">Batal</button>
                            <button type="button" @click="submitTransaction" class="ledger-btn-primary"
                                :style="modalType === 'OUT' ? 'background:#AF52DE' : ''">
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-1 flex-1 overflow-y-auto no-scrollbar pr-1">
                    <template x-for="(w, wIdx) in wallets" :key="w.id">
                        <button type="button" @click="selectWallet(w.id)" class="ledger-wallet"
                            :style="'--i:' + wIdx"
                            :class="selectedWallet === w.id ? 'is-on' : ''">

                            <div class="ledger-wallet-bar" aria-hidden="true"></div>

                            <div class="ledger-wallet-icon">
                                <template x-if="w.id === 0">
                                    <x-heroicon-o-wallet class="w-5 h-5" />
                                </template>
                                <template x-if="w.id === 1">
                                    <x-heroicon-o-credit-card class="w-5 h-5" />
                                </template>
                                <template x-if="w.id === 2">
                                    <x-heroicon-o-banknotes class="w-5 h-5" />
                                </template>
                                <template x-if="w.id === 3">
                                    <x-heroicon-o-building-library class="w-5 h-5" />
                                </template>
                                <template x-if="w.id === 4">
                                    <x-heroicon-o-currency-dollar class="w-5 h-5" />
                                </template>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="ledger-wallet-name truncate" x-text="w.name"></p>
                                <p class="ledger-wallet-note truncate" x-text="w.note"></p>
                            </div>

                            <div class="text-right flex-shrink-0">
                                <p class="ledger-wallet-value" x-text="formatCurrency(w.balance)"></p>
                                <p class="ledger-wallet-type" x-text="w.type"></p>
                            </div>

                            <x-heroicon-o-chevron-right class="ledger-wallet-chevron" />
                        </button>
                    </template>
                </div>
            </aside>

            <!-- RIGHT: Transactions -->
            <section class="ledger-card ledger-enter-card relative p-6 flex flex-col min-w-0" data-enter="2"
                style="height: 640px;">

                <div class="ledger-head ledger-enter-block" data-enter="1">
                    <h2 :class="{ 'is-away': showSearch }">Riwayat Transaksi</h2>

                    <div class="ledger-search-morph" :class="{ 'is-open': showSearch }">
                        <button type="button"
                            class="ledger-search-trigger"
                            @click="!showSearch && toggleSearch()"
                            :tabindex="showSearch ? -1 : 0"
                            :aria-label="showSearch ? 'Pencarian' : 'Buka pencarian'">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                        </button>

                        <input type="search"
                            x-ref="searchInput"
                            x-model="filter"
                            placeholder="Cari transaksi..."
                            class="ledger-search-field"
                            :tabindex="showSearch ? 0 : -1"
                            @keydown.escape.prevent="showSearch && toggleSearch()" />

                        <button type="button"
                            class="ledger-search-close"
                            @click="toggleSearch()"
                            :tabindex="showSearch ? 0 : -1"
                            aria-label="Tutup pencarian">
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <div class="flex-1 flex flex-col overflow-hidden">
                    <div class="flex-shrink-0 relative">
                        <div class="pb-4 border-b ledger-divider">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="ledger-label">Pilih Bulan</h3>
                                <button type="button" @click="toggleDropdown()" class="ledger-icon-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5 transform transition-transform duration-300"
                                        :class="showDropdown ? 'rotate-180 text-[color:var(--accent)]' : 'rotate-0'"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>

                            <div x-show="showDropdown" x-transition:enter="transition ease-out duration-400"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="ledger-dropdown absolute top-[100%] left-0 right-0 mt-2 p-5 space-y-4 z-40 max-h-[380px] overflow-y-auto no-scrollbar">

                                <template x-for="year in years" :key="year">
                                    <div>
                                        <p class="text-lg font-bold mb-3" style="color: var(--text-primary)"
                                            x-text="year"></p>
                                        <div class="grid grid-cols-4 gap-2">
                                            <template x-for="m in months" :key="m.index">
                                                <button type="button" @click="selectYear(year); selectMonth(m.index)"
                                                    class="ledger-month-btn"
                                                    :class="isMonthActive(m.index, year) ? 'is-on' : ''">
                                                    <span x-text="m.name"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="ledger-seg-scroll no-scrollbar smooth-scroll pb-1" x-ref="monthContainer">
                                <div class="ledger-seg-track">
                                    <div class="ledger-seg-pill" x-ref="monthPill" aria-hidden="true"></div>
                                    <template x-for="m in months" :key="m.index">
                                        <button type="button" @click="selectMonth(m.index)" class="ledger-chip"
                                            :data-month="m.index"
                                            :style="'--i:' + m.index"
                                            :class="Number(selectedMonthIndex) === Number(m.index) ? 'is-on' : ''">
                                            <span x-text="m.name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="py-4 border-b ledger-divider">
                            <div class="ledger-seg-scroll no-scrollbar smooth-scroll px-1" x-ref="dateContainer">
                                <div class="ledger-seg-track">
                                    <div class="ledger-seg-pill" x-ref="dayPill" aria-hidden="true"></div>
                                    <template x-for="day in days" :key="day">
                                        <button type="button" @click="selectDay(day)" :data-day="day"
                                            class="ledger-day"
                                            :style="'--i:' + (day - 1)"
                                            :class="Number(selectedDate) === Number(day) ? 'is-on' : ''">
                                            <span x-text="day + '/' + (selectedMonthIndex + 1)"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto no-scrollbar mt-3 pb-1 smooth-scroll flex flex-col">
                        <div class="ledger-trx-list">
                            <template x-if="filteredTransactions.length > 0">
                                <template x-for="(t, tIdx) in filteredTransactions" :key="t.id">
                                    <div class="ledger-trx" :class="activeTransaction === t.id ? 'is-open' : ''"
                                        :style="'--i:' + tIdx"
                                        :data-transaction-id="t.id">
                                        <button type="button" @click="toggleTransaction(t.id)"
                                            class="w-full flex items-center justify-between text-left gap-3">
                                            <div class="min-w-0">
                                                <p class="ledger-trx-title truncate">
                                                    <span
                                                        x-text="new Date(t.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit' })"></span>
                                                    —
                                                    <span class="ledger-data" x-text="t.deskripsi"></span>
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <p class="ledger-trx-amt"
                                                    :class="t.type === 'IN' ? 'is-in' : 'is-out'"
                                                    x-text="formatCurrency(t.nominal)"></p>
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-5 h-5 transform transition-transform duration-300"
                                                    :class="activeTransaction === t.id ?
                                                        'rotate-180 text-[color:var(--accent)]' :
                                                        'rotate-0 text-[color:var(--text-muted)]'"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </button>

                                        <div x-show="activeTransaction === t.id"
                                            x-transition:enter="transition ease-out duration-320"
                                            x-transition:enter-start="opacity-0 -translate-y-1"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-220"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 -translate-y-1"
                                            class="ledger-trx-detail">

                                            <div class="ledger-trx-detail-row">
                                                <span class="k">Created</span>
                                                <span class="v"
                                                    x-text="new Date(t.created_at).toLocaleTimeString('id-ID', {
                                                        hour: '2-digit',
                                                        minute: '2-digit',
                                                        timeZone: 'Asia/Makassar'
                                                    }) + ' WITA'">
                                                </span>
                                            </div>

                                            <div class="ledger-trx-detail-row">
                                                <span class="k">Nominal</span>
                                                <span class="v" x-text="formatCurrency(t.nominal)"></span>
                                            </div>

                                            <div class="ledger-trx-detail-row">
                                                <span class="k">Tipe</span>
                                                <span class="v" x-text="t.type === 'IN' ? 'Masuk' : 'Keluar'"></span>
                                            </div>

                                            <div class="ledger-trx-detail-actions">
                                                <button type="button" @click="requestDelete(t.id)"
                                                    class="ledger-trx-delete">
                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                    Hapus Transaksi
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </template>

                            <template x-if="filteredTransactions.length === 0">
                                <div class="ledger-empty">
                                    <x-heroicon-o-document-text />
                                    <p>Tidak ada pembukuan di tanggal ini.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </section>

            <div x-show="showConfirmModal" x-transition.opacity.duration.300ms
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4" x-cloak>

                <div @click.away="cancelDelete()"
                    class="ledger-modal-shell p-6 w-full max-w-sm transform transition-all duration-300"
                    x-transition.scale.duration.250ms>

                    <h2 class="text-lg font-semibold mb-2" style="color: var(--text-primary)">
                        Konfirmasi Hapus
                    </h2>
                    <p class="text-sm mb-5" style="color: var(--text-muted)">
                        Apakah kamu yakin ingin menghapus transaksi ini? <br>
                        Tindakan ini tidak bisa dibatalkan.
                    </p>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="cancelDelete()" class="ledger-btn-ghost">Batal</button>
                        <button type="button" @click="confirmDelete()" class="ledger-btn-danger">Ya, Hapus</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function ledgerApp() {
            return {
                // === STATE ===
                showSearch: false,
                showDropdown: false,
                filter: '',
                selectedWallet: 0, // default: Semua Wallet
                selectedDate: new Date().getDate(),
                selectedMonthIndex: new Date().getMonth(),
                selectedYear: new Date().getFullYear(),
                currentYear: new Date().getFullYear(),
                days: [],
                wallets: @json($wallets),
                transactions: @json($transactions),
                years: @json($years),
                totalSaldo: {{ $totalSaldo ?? 0 }},
                lastUpdated: '{{ $lastUpdate ? $lastUpdate->format('d M Y H:i') : '-' }}',
                activeTransaction: null,

                // === LIST BULAN ===
                months: [{
                        name: 'Jan',
                        index: 0
                    },
                    {
                        name: 'Feb',
                        index: 1
                    },
                    {
                        name: 'Mar',
                        index: 2
                    },
                    {
                        name: 'Apr',
                        index: 3
                    },
                    {
                        name: 'May',
                        index: 4
                    },
                    {
                        name: 'Jun',
                        index: 5
                    },
                    {
                        name: 'Jul',
                        index: 6
                    },
                    {
                        name: 'Aug',
                        index: 7
                    },
                    {
                        name: 'Sep',
                        index: 8
                    },
                    {
                        name: 'Oct',
                        index: 9
                    },
                    {
                        name: 'Nov',
                        index: 10
                    },
                    {
                        name: 'Dec',
                        index: 11
                    },
                ],
                showModal: false,
                modalType: null,
                form: {
                    deskripsi: '',
                    nominalRaw: 0,
                    nominalDisplay: '',
                    cashbook_wallet_id: '',
                },
                showConfirmModal: false,
                deleteTargetId: null,
                isWaiting: true,
                isBooting: false,

                // === INIT ===
                init() {
                    // set days sesuai bulan dan tahun saat ini
                    this.updateDaysInMonth();

                    // pastikan selectedDate default valid (jika hari > jumlah hari di bulan, set ke last day)
                    const daysInMonth = new Date(this.selectedYear, this.selectedMonthIndex + 1, 0).getDate();
                    if (this.selectedDate > daysInMonth) this.selectedDate = daysInMonth;

                    // tunggu Alpine render, lalu gunakan kombinasi observer + retry fallback
                    this.$nextTick(() => {
                        // 1) MutationObserver: trigger saat DOM berubah sehingga dateContainer muncul
                        let observer;
                        try {
                            observer = new MutationObserver(() => {
                                const el = this.$refs.dateContainer?.querySelector(
                                    `[data-day='${this.selectedDate}']`);
                                if (el) {
                                    // scroll sekali elemen ada
                                    this.scrollSelectedDayIntoView();
                                    observer.disconnect();
                                    if (retryTimer) clearInterval(retryTimer);
                                }
                            });
                            observer.observe(this.$el, {
                                childList: true,
                                subtree: true
                            });
                        } catch (e) {
                            // ignore if MutationObserver unsupported
                        }

                        // 2) Fallback retry: jika observer gagal/terlambat, coba berkali2 selama max 2s
                        const start = Date.now();
                        const retryTimer = setInterval(() => {
                            const el = this.$refs.dateContainer?.querySelector(
                                `[data-day='${this.selectedDate}']`);
                            if (el) {
                                this.scrollSelectedDayIntoView();
                                clearInterval(retryTimer);
                                if (observer) observer.disconnect();
                            } else if (Date.now() - start > 2000) { // timeout 2 detik
                                clearInterval(retryTimer);
                                if (observer) observer.disconnect();
                            }
                        }, 80); // cek tiap 80ms

                        // 3) as last resort, jalankan sekali lagi setelah 300ms untuk keamanan
                        setTimeout(() => {
                            const el = this.$refs.dateContainer?.querySelector(
                                `[data-day='${this.selectedDate}']`);
                            if (el) this.scrollSelectedDayIntoView();
                            this.syncMonthPill();
                            this.syncDayPill();
                        }, 300);

                        requestAnimationFrame(() => {
                            this.syncMonthPill(true);
                            this.syncDayPill(true);
                        });

                        this.playEnterAfterOverlay();
                    });
                },

                playEnterAfterOverlay() {
                    const start = () => {
                        if (this._enterStarted) return;
                        this._enterStarted = true;

                        this.isWaiting = false;
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                this.isBooting = true;
                                this.$nextTick(() => {
                                    this.syncMonthPill(true);
                                    this.syncDayPill(true);
                                });
                                setTimeout(() => {
                                    this.isBooting = false;
                                    this.syncMonthPill(true);
                                    this.syncDayPill(true);
                                }, 1500);
                            });
                        });
                    };

                    const html = document.documentElement;

                    if (html.classList.contains('is-boot') || document.getElementById('appBoot')) {
                        const onReveal = () => {
                            window.removeEventListener('app-boot:reveal', onReveal);
                            setTimeout(start, 100);
                        };
                        window.addEventListener('app-boot:reveal', onReveal);

                        const obs = new MutationObserver(() => {
                            const overlayLeaving = document.querySelector('.app-boot.is-leaving');
                            if (!html.classList.contains('is-boot') || overlayLeaving) {
                                obs.disconnect();
                                window.removeEventListener('app-boot:reveal', onReveal);
                                setTimeout(start, overlayLeaving ? 100 : 40);
                            }
                        });

                        obs.observe(html, {
                            attributes: true,
                            attributeFilter: ['class']
                        });

                        const boot = document.getElementById('appBoot');
                        if (boot) {
                            const bootObs = new MutationObserver(() => {
                                if (boot.classList.contains('is-leaving') || !boot.isConnected) {
                                    bootObs.disconnect();
                                    obs.disconnect();
                                    window.removeEventListener('app-boot:reveal', onReveal);
                                    setTimeout(start, 100);
                                }
                            });
                            bootObs.observe(boot, {
                                attributes: true,
                                attributeFilter: ['class']
                            });
                        }

                        setTimeout(() => {
                            obs.disconnect();
                            window.removeEventListener('app-boot:reveal', onReveal);
                            if (this.isWaiting) start();
                        }, 2800);
                        return;
                    }

                    start();
                },

                // === UPDATE JUMLAH HARI ===
                updateDaysInMonth() {
                    const daysInMonth = new Date(this.selectedYear, this.selectedMonthIndex + 1, 0).getDate();
                    this.days = Array.from({
                        length: daysInMonth
                    }, (_, i) => i + 1);
                },

                // === FORMAT RUPIAH ===
                formatCurrency(v) {
                    if (!v || isNaN(v)) v = 0;
                    return 'Rp ' + Number(v).toLocaleString('id-ID');
                },

                // === TAMPILKAN / SEMBUNYIKAN DETAIL TRANSAKSI ===
                toggleTransaction(id) {
                    this.activeTransaction = this.activeTransaction === id ? null : id;
                },

                openModal(type) {
                    this.modalType = type;
                    this.showModal = true;
                    this.form = {
                        deskripsi: '',
                        nominal: '',
                        cashbook_wallet_id: this.wallets[1]?.id || ''
                    };
                },

                closeModal() {
                    this.showModal = false;
                },

                formatNominal(e) {
                    // Ambil angka murni
                    let raw = e.target.value.replace(/\D/g, '');
                    if (raw === '') raw = '0';

                    // Simpan nilai numeriknya
                    this.form.nominal = parseInt(raw);

                    // Format tampilan Rp 100.000
                    this.form.nominalDisplay = 'Rp ' + new Intl.NumberFormat('id-ID').format(this.form.nominal);
                },

                // === FILTER TRANSAKSI ===
                get filteredTransactions() {
                    let filtered = this.transactions;

                    // Filter berdasarkan wallet
                    if (this.selectedWallet !== 0) {
                        filtered = filtered.filter(t => t.cashbook_wallet_id === this.selectedWallet);
                    }

                    // Filter berdasarkan tahun & bulan
                    filtered = filtered.filter(t => {
                        const d = new Date(t.created_at);
                        return d.getFullYear() === this.selectedYear && d.getMonth() === this
                            .selectedMonthIndex;
                    });

                    // Filter berdasarkan tanggal
                    filtered = filtered.filter(t => {
                        const d = new Date(t.created_at);
                        return d.getDate() === this.selectedDate;
                    });

                    // Filter berdasarkan keyword pencarian
                    if (this.filter) {
                        const q = this.filter.toLowerCase();
                        filtered = filtered.filter(t =>
                            (t.deskripsi?.toLowerCase().includes(q) || '') ||
                            t.nominal?.toString().includes(q)
                        );
                    }

                    return filtered;
                },

                // === EVENT HANDLER FILTER ===
                selectWallet(id) {
                    this.selectedWallet = id;
                },
                selectYear(year) {
                    this.selectedYear = year;
                    this.updateDaysInMonth();
                    this.showDropdown = false;
                    this.scrollSelectedDayIntoView();
                    this.$nextTick(() => {
                        this.syncMonthPill();
                        this.syncDayPill();
                    });
                },
                selectMonth(index) {
                    this.selectedMonthIndex = index;
                    this.updateDaysInMonth();
                    this.showDropdown = false;
                    this.$nextTick(() => {
                        this.syncMonthPill();
                        this.scrollSelectedDayIntoView();
                        this.syncDayPill();
                    });
                },
                selectDay(day) {
                    this.selectedDate = day;
                    this.scrollSelectedDayIntoView();
                    this.$nextTick(() => this.syncDayPill());
                },

                syncSegPill(btn, pill, instant = false) {
                    if (!btn || !pill) return;
                    const w = btn.offsetWidth;
                    const h = btn.offsetHeight;
                    if (w <= 0 || h <= 0) return;
                    if (instant) pill.style.transition = 'none';
                    pill.style.width = w + 'px';
                    pill.style.height = h + 'px';
                    pill.style.transform = `translate3d(${btn.offsetLeft}px, ${btn.offsetTop}px, 0)`;
                    if (instant) {
                        // force reflow then restore transition
                        void pill.offsetWidth;
                        pill.style.transition = '';
                    }
                },

                syncMonthPill(instant = false) {
                    const container = this.$refs.monthContainer;
                    const pill = this.$refs.monthPill;
                    if (!container || !pill) return;
                    const btn = container.querySelector('.ledger-chip.is-on') ||
                        container.querySelector(`[data-month="${this.selectedMonthIndex}"]`);
                    this.syncSegPill(btn, pill, instant);
                },

                syncDayPill(instant = false) {
                    const container = this.$refs.dateContainer;
                    const pill = this.$refs.dayPill;
                    if (!container || !pill) return;
                    const btn = container.querySelector(`[data-day="${this.selectedDate}"]`);
                    this.syncSegPill(btn, pill, instant);
                },

                // === DROPDOWN & SEARCH ===
                toggleSearch() {
                    this.showSearch = !this.showSearch;
                    if (!this.showSearch) {
                        this.filter = '';
                        return;
                    }
                    this.$nextTick(() => {
                        setTimeout(() => this.$refs.searchInput?.focus(), 180);
                    });
                },
                toggleDropdown() {
                    this.showDropdown = !this.showDropdown;
                },

                // === AUTO SCROLL KE TANGGAL AKTIF ===
                scrollSelectedDayIntoView() {
                    // extra $nextTick supaya Alpine benar-benar menyelesaikan binding
                    this.$nextTick(() => {
                        const container = this.$refs.dateContainer;
                        if (!container) return;

                        const el = container.querySelector(`[data-day='${this.selectedDate}']`);
                        if (!el) return;

                        el.scrollIntoView({
                            behavior: 'smooth',
                            inline: 'center',
                            block: 'nearest'
                        });

                        this.syncDayPill();
                    });
                },

                async submitTransaction() {
                    if (!this.form.deskripsi || !this.form.nominal) {
                        this.showToast('Harap isi semua field.', 'error');
                        return;
                    }

                    try {
                        const response = await fetch('{{ route('cashbooks.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                deskripsi: this.form.deskripsi,
                                nominal: this.form.nominal,
                                cashbook_wallet_id: this.form.cashbook_wallet_id,
                                type: this.modalType,
                            }),
                        });

                        if (response.ok) {
                            const newData = await response.json(); // 🆕 Ambil data baru dari response

                            // 🆕 Tambahkan ke daftar transaksi
                            this.transactions.unshift({
                                id: newData.id,
                                deskripsi: newData.deskripsi,
                                nominal: parseFloat(newData.nominal),
                                type: newData.type,
                                cashbook_wallet_id: parseInt(newData.cashbook_wallet_id),
                                created_at: newData.created_at,
                            });

                            // 🆕 Update saldo total
                            if (newData.type === 'IN') {
                                this.totalSaldo += parseFloat(newData.nominal);
                            } else {
                                this.totalSaldo -= parseFloat(newData.nominal);
                            }

                            // 🆕 Update saldo wallet yang bersangkutan
                            const targetWallet = this.wallets.find(w => w.id == newData.cashbook_wallet_id);
                            if (targetWallet) {
                                const currentBalance = parseFloat(targetWallet.balance) || 0;
                                const amount = parseFloat(newData.nominal) || 0;

                                // Update saldo wallet spesifik
                                if (newData.type === 'IN') {
                                    targetWallet.balance = currentBalance + amount;
                                } else {
                                    targetWallet.balance = currentBalance - amount;
                                }

                                targetWallet.note = 'Aktif';
                            }

                            // 🆕 Update saldo wallet "Semua Wallet" (gabungan)
                            const mainWallet = this.wallets.find(w => w.id === 0);
                            if (mainWallet) {
                                const mainBalance = parseFloat(mainWallet.balance) || 0;
                                const amount = parseFloat(newData.nominal) || 0;
                                if (newData.type === 'IN') {
                                    mainWallet.balance = mainBalance + amount;
                                } else {
                                    mainWallet.balance = mainBalance - amount;
                                }
                            }

                            // 🆕 Update total saldo global
                            const totalInWallets = this.wallets
                                .filter(w => w.id !== 0)
                                .reduce((sum, w) => sum + (parseFloat(w.balance) || 0), 0);

                            this.totalSaldo = totalInWallets;
                            this.lastUpdated = new Date().toLocaleString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });

                            this.showToast('Transaksi berhasil disimpan!', 'success');
                            this.closeModal();
                            this.form = {
                                deskripsi: '',
                                nominal: '',
                                cashbook_wallet_id: ''
                            };
                        } else {
                            this.showToast('Gagal menyimpan transaksi.', 'error');
                        }
                    } catch (e) {
                        console.error('Error:', e);
                        this.showToast('Terjadi kesalahan koneksi.', 'error');
                    }
                },

                // Step 1: Tampilkan modal
                requestDelete(id) {
                    this.deleteTargetId = id;
                    this.showConfirmModal = true;
                },

                // Step 2: Batalkan
                cancelDelete() {
                    this.showConfirmModal = false;
                    this.deleteTargetId = null;
                },

                // Step 3: Konfirmasi dan eksekusi delete
                async confirmDelete() {
                    const id = this.deleteTargetId;
                    if (!id) return;

                    this.showConfirmModal = false;

                    try {
                        const response = await fetch(`{{ url('cashbook') }}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                        });

                        if (response.ok) {
                            // Efek fade-out + hapus data
                            const card = document.querySelector(`[data-transaction-id="${id}"]`);
                            if (card) {
                                card.classList.add('opacity-0', 'scale-95', 'transition-all', 'duration-300');
                                setTimeout(() => {
                                    this.transactions = this.transactions.filter(t => t.id !== id);
                                    this.recalculateWallets();
                                }, 250);
                            } else {
                                this.transactions = this.transactions.filter(t => t.id !== id);
                                this.recalculateWallets();
                            }

                            this.showToast('Transaksi berhasil dihapus!', 'success');
                        } else {
                            this.showToast('Gagal menghapus transaksi.', 'error');
                        }
                    } catch (e) {
                        console.error(e);
                        this.showToast('Terjadi kesalahan koneksi.', 'error');
                    } finally {
                        this.deleteTargetId = null;
                    }
                },

                recalculateWallets() {
                    // Reset saldo semua wallet (kecuali gabungan)
                    this.wallets.forEach(w => {
                        if (w.id !== 0) w.balance = 0;
                    });

                    // Hitung ulang berdasarkan transaksi yang tersisa
                    this.transactions.forEach(t => {
                        const wallet = this.wallets.find(w => w.id === t.cashbook_wallet_id);
                        if (wallet) {
                            const nominal = parseFloat(t.nominal) || 0;
                            wallet.balance += (t.type === 'IN' ? nominal : -nominal);
                        }
                    });

                    // 🔹 Hitung total semua wallet aktif (bukan gabungan)
                    const total = this.wallets
                        .filter(w => w.id !== 0)
                        .reduce((sum, w) => sum + (parseFloat(w.balance) || 0), 0);

                    // 🔹 Update totalSaldo global
                    this.totalSaldo = total;

                    // 🔹 Update wallet gabungan (id: 0)
                    const mainWallet = this.wallets.find(w => w.id === 0);
                    if (mainWallet) {
                        mainWallet.balance = total;
                    }

                    // 🔹 Update waktu terakhir
                    this.lastUpdated = new Date().toLocaleString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                },

                showToast(message, type = 'success') {
                    const toast = document.createElement('div');
                    toast.textContent = message;
                    toast.className = `
                    fixed bottom-5 right-5 px-4 py-3 rounded-lg shadow-lg text-sm font-medium
                    text-white z-50 transition-all duration-500 transform
                    ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}
                    translate-y-5 opacity-0
                `;

                    document.body.appendChild(toast);

                    // animasi masuk
                    requestAnimationFrame(() => {
                        toast.classList.remove('translate-y-5', 'opacity-0');
                        toast.classList.add('translate-y-0', 'opacity-100');
                    });

                    // animasi keluar
                    setTimeout(() => {
                        toast.classList.remove('translate-y-0', 'opacity-100');
                        toast.classList.add('translate-y-5', 'opacity-0');
                        setTimeout(() => toast.remove(), 500);
                    }, 2500);
                },

                // === UTILITAS ===
                isMonthActive(monthIndex, year) {
                    return this.selectedMonthIndex === monthIndex && this.selectedYear === year;
                },
                get displayMonth() {
                    return new Intl.DateTimeFormat('id-ID', {
                            month: 'long'
                        })
                        .format(new Date(this.selectedYear, this.selectedMonthIndex));
                },
            };
        }
    </script>
@endsection

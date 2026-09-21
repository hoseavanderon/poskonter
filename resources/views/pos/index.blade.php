@extends('layouts.app')

@section('content')
    <style>
        :root {
            --app-bg: #F5F5F7;
            --surface: #FFFFFF;
            --surface-secondary: #F2F2F7;
            --border: #D1D1D6;
            --divider: #E5E5EA;
            --text-primary: #1D1D1F;
            --text-secondary: #6E6E73;
            --text-muted: #86868B;
            --accent: #007AFF;
            --accent-soft: rgba(0, 122, 255, 0.10);
            --border-hairline: rgba(0, 0, 0, 0.04);
            --shadow-card: 0 4px 20px rgba(0, 0, 0, 0.06);
            --shadow-card-lg: 0 8px 30px rgba(0, 0, 0, 0.06);
            --shadow-modal: 0 20px 60px rgba(0, 0, 0, 0.12);
            --shadow-focus: 0 0 0 3px rgba(0, 122, 255, 0.12);
            --icon: #3A3A3C;

            --pos-bg: var(--app-bg);
            --pos-surface: var(--surface);
            --pos-surface-elevated: var(--surface-secondary);
            --pos-card: var(--surface);
            --pos-border: var(--border);
            --pos-border-subtle: var(--divider);
            --pos-border-strong: var(--border-hairline);
            --pos-border-hover: #C7C7CC;
            --pos-text: var(--text-primary);
            --pos-text-secondary: var(--text-secondary);
            --pos-text-muted: var(--text-muted);
            --pos-icon: var(--icon);
            --pos-icon-muted: var(--text-muted);
            --pos-primary: var(--accent);
            --pos-primary-text: #FFFFFF;
            --pos-disabled: #C7C7CC;
            --pos-hover: var(--surface-secondary);
            --pos-track: var(--surface-secondary);
            --pos-thumb: #C7C7CC;
            --pos-panel: var(--surface);
            --pos-input: var(--surface);
            --pos-accent: var(--accent);
            --pos-accent-soft: var(--accent-soft);
        }

        html.dark {
            --app-bg: #000000;
            --surface: #1C1C1E;
            --surface-secondary: #2C2C2E;
            --border: #38383A;
            --divider: #38383A;
            --text-primary: #F5F5F7;
            --text-secondary: #AEAEB2;
            --text-muted: #8E8E93;
            --accent: #0A84FF;
            --accent-soft: rgba(10, 132, 255, 0.15);
            --border-hairline: rgba(255, 255, 255, 0.08);
            --shadow-card: 0 4px 24px rgba(0, 0, 0, 0.45);
            --shadow-card-lg: 0 8px 32px rgba(0, 0, 0, 0.50);
            --shadow-modal: 0 20px 60px rgba(0, 0, 0, 0.65);
            --shadow-focus: 0 0 0 3px rgba(10, 132, 255, 0.16);
            --icon: #D1D1D6;

            --pos-bg: var(--app-bg);
            --pos-surface: var(--surface);
            --pos-surface-elevated: var(--surface-secondary);
            --pos-card: var(--surface);
            --pos-border: var(--border);
            --pos-border-subtle: var(--divider);
            --pos-border-hover: #636366;
            --pos-border-strong: var(--border-hairline);
            --pos-text: var(--text-primary);
            --pos-text-secondary: var(--text-secondary);
            --pos-text-muted: var(--text-muted);
            --pos-icon: var(--icon);
            --pos-icon-muted: var(--text-muted);
            --pos-primary: var(--accent);
            --pos-primary-text: #FFFFFF;
            --pos-disabled: #48484A;
            --pos-hover: var(--surface-secondary);
            --pos-track: var(--surface-secondary);
            --pos-thumb: #48484A;
            --pos-panel: var(--surface);
            --pos-input: var(--surface);
            --pos-accent: var(--accent);
            --pos-accent-soft: var(--accent-soft);
        }

        html {
            scroll-behavior: smooth;
        }

        html,
        body,
        main,
        header {
            font-family: var(--font-sf) !important;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            letter-spacing: -0.01em;
        }

        html,
        body {
            background-color: var(--pos-bg) !important;
            color: var(--pos-text) !important;
        }

        .pos-product-card {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            padding: 16px;
            height: 100%;
            box-sizing: border-box;
            background-color: var(--surface) !important;
            border: 1px solid var(--border-hairline) !important;
            border-radius: 18px;
            box-shadow: var(--shadow-card);
            cursor: pointer;
            overflow: visible;
            transform: none;
            filter: none;
            background-clip: padding-box;
            transition: box-shadow 0.25s ease-out, background-color 0.15s ease;
        }

        html.dark .pos-product-card {
            border-color: var(--border-hairline) !important;
        }

        .pos-product-card:hover {
            border-color: var(--border-hairline) !important;
            background-color: var(--surface) !important;
            box-shadow: var(--shadow-card-lg);
        }

        html.dark .pos-product-card:hover {
            border-color: var(--border-hairline) !important;
        }

        .pos-product-card.opacity-40 {
            opacity: 0.62 !important;
        }

        .pos-product-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: var(--surface-secondary);
            border: 1px solid var(--border-hairline);
            color: var(--icon);
        }

        html.dark .pos-product-icon {
            background: var(--surface-secondary);
            border-color: var(--border-hairline);
            color: var(--icon);
        }

        .pos-product-top {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            min-width: 0;
        }

        .pos-product-titles {
            flex: 1 1 auto;
            min-width: 0;
        }

        .pos-product-badge {
            flex-shrink: 0;
            min-width: 22px;
            height: 22px;
            padding: 0 7px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            line-height: 1;
            background: var(--surface-secondary);
            color: var(--pos-text);
        }

        .pos-product-body {
            min-width: 0;
        }

        .pos-product-header {
            min-height: 0;
        }

        .pos-product-price {
            margin: 14px 0 0;
            font-size: 22px;
            font-weight: 700;
            line-height: 1.2;
            color: var(--pos-text);
            white-space: nowrap;
        }

        .pos-product-name {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.3;
            color: var(--pos-text);
            text-transform: none;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pos-product-category {
            margin: 2px 0 0;
            font-size: 13px;
            font-weight: 400;
            line-height: 1.3;
            color: var(--pos-text-secondary);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pos-product-barcode {
            margin: 8px 0 0;
            display: flex;
            align-items: center;
            min-width: 0;
            gap: 6px;
            font-size: 12px;
            font-weight: 400;
            color: var(--pos-text-muted);
        }

        .pos-product-variant {
            margin: 10px 0 0;
            display: inline-flex;
            align-self: flex-start;
            align-items: center;
            gap: 0;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.2;
            background: var(--surface-secondary);
            color: var(--pos-text);
        }

        .pos-input {
            background-color: var(--surface) !important;
            border: 1px solid var(--border) !important;
            border-radius: 12px;
        }

        .pos-product-barcode,
        .pos-product-variant {
            transform: none;
            filter: none;
            will-change: auto;
            opacity: 1;
        }

        #productScrollArea {
            background-color: var(--pos-bg);
            -webkit-overflow-scrolling: auto;
        }

        .pos-panel-divider {
            border-left: none !important;
            background-color: var(--surface) !important;
            border: 1px solid var(--border-hairline) !important;
            border-radius: 18px;
            box-shadow: var(--shadow-card);
            margin-left: 12px;
            -webkit-overflow-scrolling: auto;
        }

        .pos-cart-foot {
            position: relative;
            z-index: 3;
            flex-shrink: 0;
            background-color: var(--surface) !important;
            background-clip: padding-box;
            transform: none;
            filter: none;
            opacity: 1;
        }

        .pos-cart-foot button {
            transform: none;
            filter: none;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
        }

        .pos-cart-btn:disabled {
            opacity: 1 !important;
            background-color: var(--surface-secondary) !important;
            color: var(--text-muted) !important;
            border-color: var(--border-hairline) !important;
        }

        @media (max-width: 1023px) {
            .pos-tabs {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                align-items: center;
                gap: 12px;
                overflow: visible;
            }

            .pos-seg-wrap {
                min-width: 0;
                overflow: hidden;
            }

            .pos-seg {
                flex: none;
                width: 100%;
                min-width: 0;
                max-width: 100%;
            }

            .pos-physical-tools {
                grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr) !important;
                gap: 8px !important;
                margin-bottom: 12px !important;
            }

            .pos-physical-tools>div {
                min-width: 0;
            }

            .pos-physical-tools .pos-input {
                height: 48px;
                min-height: 48px;
                width: 100%;
                min-width: 0;
                font-size: 14px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .pos-physical-tools .relative:focus-within .pos-ico {
                color: var(--accent);
            }

            .pos-physical #productScrollArea>.grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 10px !important;
            }

            body.is-pos .pos-root {
                min-width: 0;
                max-width: 100%;
                width: 100%;
            }

            body.is-pos .app-shell,
            body.is-pos main,
            body.is-pos .pos-tabs,
            body.is-pos .pos-digital,
            body.is-pos .pos-dig-body,
            body.is-pos .pos-physical,
            body.is-pos .pos-manual {
                min-width: 0;
                max-width: 100%;
            }

            .pos-digital {
                width: 100%;
                overflow-x: hidden;
            }

            .pos-wiz-row {
                min-width: 0;
                width: 100%;
                max-width: 100%;
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .pos-wiz-row::-webkit-scrollbar {
                display: none;
                width: 0;
                height: 0;
            }

            .pos-dig-body {
                width: 100%;
                overflow-x: hidden;
            }

            .pos-digital .grid {
                width: 100%;
                max-width: 100%;
            }
        }

        @media (max-width: 767px) {
            html,
            body {
                overflow-x: hidden;
            }

            .pos-physical {
                height: auto !important;
                min-height: 0 !important;
                overflow: visible !important;
            }

            .pos-physical-split {
                flex-direction: column;
                overflow: visible;
                min-height: 0 !important;
            }

            .pos-physical-workspace {
                padding-right: 0 !important;
                min-width: 0;
            }

            .pos-physical #productScrollArea {
                flex: none;
                min-height: 0;
                max-height: none;
                overflow: visible;
                padding-right: 0;
            }

            .pos-physical .pos-panel-divider {
                width: 100% !important;
                margin-left: 0;
                margin-top: 12px;
                overflow: visible;
            }

            .pos-physical .pos-panel-divider .flex-1.min-h-0 {
                flex: none;
                max-height: none;
                min-height: 0;
                overflow: visible !important;
            }

            .pos-physical .pos-panel-divider .w-7.h-7 {
                width: 44px !important;
                height: 44px !important;
            }

            .pos-physical .pos-panel-divider .grid.grid-cols-2.gap-2>button {
                min-height: 48px;
                height: 48px;
            }

            .pos-tabs {
                display: none !important;
            }

            .pos-bottom-nav {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 15;
                height: calc(64px + env(safe-area-inset-bottom, 0px));
                padding: 6px 8px calc(6px + env(safe-area-inset-bottom, 0px));
                box-sizing: border-box;
                background: var(--surface);
                border-top: 1px solid var(--border-hairline);
                box-shadow: none;
            }

            .pos-bottom-nav button {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 2px;
                min-width: 0;
                border: 0;
                background: transparent;
                color: var(--text-secondary);
                font-size: 10px;
                font-weight: 600;
                letter-spacing: -0.01em;
                font-family: inherit;
                padding: 4px 2px;
            }

            .pos-bottom-nav .pos-ico {
                width: 22px;
                height: 22px;
                color: currentColor;
                stroke: currentColor;
            }

            .pos-bottom-nav button.is-on {
                color: var(--accent);
            }

            .pos-bottom-nav button.is-book {
                color: var(--accent);
            }

            body.is-pos main {
                padding-bottom: calc(80px + env(safe-area-inset-bottom, 0px)) !important;
            }

            body.is-pos .pwa-install {
                bottom: calc(80px + env(safe-area-inset-bottom, 0px));
            }

            html,
            html.is-pos,
            body.is-pos {
                height: auto !important;
                min-height: 0 !important;
                overflow-x: visible;
                overflow-y: visible;
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
                filter: none !important;
                will-change: auto !important;
                padding-bottom: 0 !important;
            }

            body.is-pos .app-shell {
                height: auto !important;
                min-height: 0 !important;
                overflow: visible !important;
                transform: none !important;
                filter: none !important;
                will-change: auto !important;
            }

            body.is-pos main {
                overflow: visible !important;
                height: auto !important;
                min-width: 0;
                max-width: 100%;
                -webkit-overflow-scrolling: auto;
                padding-bottom: calc(1rem + env(safe-area-inset-bottom, 0px));
            }

            body.is-pos .app-sidebar {
                top: 64px;
                bottom: calc(64px + env(safe-area-inset-bottom, 0px));
                height: auto !important;
                transform: none !important;
                will-change: auto !important;
                filter: none !important;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
                transition: left 300ms ease-in-out !important;
                left: 0;
            }

            body.is-pos .app-sidebar.-translate-x-full {
                left: -15rem;
            }

            body.is-pos .app-sidebar.translate-x-0 {
                left: 0;
            }

            body.is-pos .app-sidebar-overlay {
                top: 64px;
                right: 0;
                bottom: calc(64px + env(safe-area-inset-bottom, 0px));
                left: 0;
                height: auto;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
                filter: none !important;
                transform: none !important;
            }

            .pos-product-card {
                transition: box-shadow 0.25s ease-out, background-color 0.15s ease;
                padding: 12px;
                border-radius: 16px;
                box-shadow: none;
                transform: none !important;
                filter: none !important;
            }

            .pos-panel-divider {
                box-shadow: none;
            }

            .pos-product-icon {
                width: 40px;
                height: 40px;
            }

            .pos-product-name {
                font-size: 14px;
            }

            .pos-product-price {
                font-size: 20px;
            }

            .pos-product-category {
                font-size: 12px;
            }

            .pos-product-barcode,
            .pos-product-variant {
                font-size: 11px;
            }
        }

        @media (min-width: 768px) {
            body.is-pos .app-shell {
                height: calc(100svh - 64px) !important;
                max-height: calc(100svh - 64px) !important;
                min-height: 0 !important;
                overflow: hidden !important;
            }

            body.is-pos main {
                display: flex;
                flex-direction: column;
                height: 100% !important;
                max-height: 100% !important;
                min-height: 0 !important;
                overflow: hidden !important;
            }

            body.is-pos .pos-root {
                flex: 1 1 auto;
                min-height: 0;
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .pos-tabs {
                flex-shrink: 0;
            }

            .pos-physical {
                flex: 1 1 auto !important;
                min-height: 0 !important;
                height: auto !important;
                max-height: none !important;
                overflow: hidden !important;
            }

            .pos-physical-split {
                flex-direction: row !important;
                flex: 1 1 auto;
                min-height: 0 !important;
                height: 100%;
                overflow: hidden !important;
            }

            .pos-physical-workspace {
                min-width: 0;
                min-height: 0;
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }

            .pos-physical #productScrollArea {
                flex: 1 1 auto !important;
                min-height: 0 !important;
                max-height: none !important;
                overflow-x: hidden !important;
                overflow-y: auto !important;
            }

            .pos-physical .pos-panel-divider {
                position: sticky;
                top: 0;
                align-self: stretch;
                height: 100% !important;
                max-height: 100% !important;
                overflow: hidden !important;
                flex-shrink: 0;
                margin-top: 0 !important;
            }

            .pos-physical .pos-panel-divider .flex-1.min-h-0 {
                flex: 1 1 auto !important;
                min-height: 0 !important;
                max-height: none !important;
                overflow-y: auto !important;
            }
        }

        .pos-item-divider {
            border-color: var(--pos-border-subtle) !important;
        }

        .pos-total-divider {
            border-color: var(--divider) !important;
        }

        .fa-solid,
        .fa-regular,
        i[class*="fa-"] {
            color: inherit !important;
        }

        .backdrop-blur-sm,
        .backdrop-blur-md {
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }

        .bg-white\/90,
        .dark .dark\:bg-gray-800\/90 {
            background-color: var(--pos-surface) !important;
        }

        .shadow-2xl,
        .shadow-xl {
            box-shadow: var(--shadow-modal) !important;
        }

        .shadow-lg,
        .shadow-md,
        .shadow-sm {
            box-shadow: var(--shadow-card) !important;
        }

        .dark .shadow-2xl,
        .dark .shadow-xl {
            box-shadow: var(--shadow-modal) !important;
        }

        .dark .shadow-lg,
        .dark .shadow-md,
        .dark .shadow-sm {
            box-shadow: var(--shadow-card) !important;
        }

        header {
            background-color: var(--surface) !important;
            border-color: var(--border-hairline) !important;
            color: var(--text-primary) !important;
            box-shadow: 0 1px 0 var(--border-hairline) !important;
        }

        header .bg-gradient-to-br,
        header .from-blue-600,
        header .to-blue-400 {
            background-image: none !important;
            background-color: var(--pos-primary) !important;
            color: var(--pos-primary-text) !important;
        }

        main {
            background-color: var(--pos-bg) !important;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--pos-track);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--pos-thumb);
            border-radius: 10px;
            border: 2px solid var(--pos-track);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--pos-icon-muted);
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: var(--pos-thumb) var(--pos-track);
        }

        .pos-dig-body,
        .pos-digital {
            scrollbar-width: none;
            -ms-overflow-style: none;
            -webkit-overflow-scrolling: touch;
        }

        .pos-dig-body::-webkit-scrollbar,
        .pos-digital::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        .pos-tabs {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            column-gap: 12px;
            width: 100%;
            min-width: 0;
            max-width: 100%;
            overflow: visible;
            flex-wrap: nowrap;
            -webkit-overflow-scrolling: auto;
            border-color: var(--divider) !important;
        }

        .pos-seg-wrap {
            position: relative;
            min-width: 0;
            overflow: hidden;
        }

        .pos-seg {
            display: flex;
            align-items: center;
            width: 100%;
            min-width: 0;
            max-width: 100%;
            padding: 4px;
            border-radius: 18px;
            background: var(--surface-secondary);
            border: 1px solid var(--border-hairline);
            overflow-x: auto;
            overflow-y: hidden;
            scrollbar-width: none;
            -ms-overflow-style: none;
            -webkit-overflow-scrolling: touch;
            box-shadow: none;
            flex: none;
        }

        .pos-seg::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        .pos-seg-track {
            position: relative;
            isolation: isolate;
            display: inline-flex;
            align-items: center;
            gap: 2px;
            min-width: max-content;
            z-index: 0;
        }

        .pos-seg-pill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 0;
            background: var(--accent);
            border-radius: 14px;
            pointer-events: none;
            z-index: 0;
            box-shadow: none;
            transform: translate3d(0, 0, 0);
            will-change: transform, width;
            transition: none;
        }

        .pos-seg.is-pos-seg-ready .pos-seg-pill {
            transition:
                transform 300ms cubic-bezier(0.22, 1, 0.36, 1),
                width 300ms cubic-bezier(0.22, 1, 0.36, 1);
        }

        .pos-seg-noanim .pos-seg-pill {
            transition: none !important;
        }

        .pos-seg:not(.is-pos-seg-ready) .pos-seg-pill {
            visibility: hidden;
        }

        .pos-seg:not(.is-pos-seg-ready) [data-pos-tab="physical"] {
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
            opacity: 1 !important;
        }

        .pos-seg:not(.is-pos-seg-ready) [data-pos-tab="physical"] .pos-ico,
        .pos-seg:not(.is-pos-seg-ready) [data-pos-tab="physical"] svg,
        .pos-seg:not(.is-pos-seg-ready) [data-pos-tab="physical"] use {
            color: #ffffff !important;
            stroke: #ffffff !important;
            opacity: 1 !important;
        }

        .pos-seg:not(.is-pos-seg-ready) [data-pos-tab="physical"]::before {
            content: "";
            position: absolute;
            inset: 0;
            background: var(--accent);
            border-radius: 14px;
            z-index: -1;
            pointer-events: none;
        }

        .pos-seg-track>button {
            position: relative;
            isolation: isolate;
            z-index: 2;
            min-height: 44px;
            border-radius: 14px !important;
            box-shadow: none !important;
            border: none !important;
            background-color: transparent !important;
            background-image: none !important;
            color: #6E6E73 !important;
            -webkit-text-fill-color: #6E6E73;
            font-weight: 600;
            white-space: nowrap;
            flex-shrink: 0;
            opacity: 1 !important;
            transition: color 200ms ease, -webkit-text-fill-color 200ms ease;
        }

        .pos-seg-track>button .pos-ico,
        .pos-seg-track>button svg,
        .pos-seg-track>button use {
            color: #6E6E73 !important;
            stroke: currentColor;
            opacity: 1 !important;
            transition: color 200ms ease;
        }

        .pos-seg-track>button.is-pos-seg-on,
        .pos-seg:not(.is-pos-seg-ready) [data-pos-tab="physical"] {
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
            opacity: 1 !important;
        }

        .pos-seg-track>button.is-pos-seg-on .pos-ico,
        .pos-seg-track>button.is-pos-seg-on svg,
        .pos-seg-track>button.is-pos-seg-on use,
        .pos-seg:not(.is-pos-seg-ready) [data-pos-tab="physical"] .pos-ico,
        .pos-seg:not(.is-pos-seg-ready) [data-pos-tab="physical"] svg,
        .pos-seg:not(.is-pos-seg-ready) [data-pos-tab="physical"] use {
            color: #ffffff !important;
            stroke: #ffffff !important;
            opacity: 1 !important;
        }

        html.dark .pos-seg-track>button:not(.is-pos-seg-on) {
            color: #AEAEB2 !important;
            -webkit-text-fill-color: #AEAEB2;
        }

        html.dark .pos-seg-track>button:not(.is-pos-seg-on) .pos-ico,
        html.dark .pos-seg-track>button:not(.is-pos-seg-on) svg {
            color: #AEAEB2 !important;
        }

        html.dark .pos-seg-track>button.is-pos-seg-on,
        html.dark .pos-seg-track>button.is-pos-seg-on .pos-ico,
        html.dark .pos-seg-track>button.is-pos-seg-on svg,
        html.dark .pos-seg-track>button.is-pos-seg-on use {
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
            stroke: #ffffff !important;
        }

        .pos-close-book {
            position: relative;
            z-index: 2;
            flex-shrink: 0;
            min-height: 52px;
            white-space: nowrap;
            border-radius: 16px !important;
            background-color: var(--accent) !important;
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff;
            box-shadow: none !important;
        }

        .pos-close-book .pos-ico,
        .pos-close-book svg {
            color: #ffffff !important;
            stroke: #ffffff;
        }

        html.dark .pos-seg {
            background: #2C2C2E;
        }

        .pos-bottom-nav {
            display: none;
        }

        .pos-manual-pane {
            width: 100%;
            min-width: 0;
            margin-top: 8px;
        }

        .pos-manual {
            display: flex;
            justify-content: center;
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }

        .pos-manual-card {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 640px;
            min-width: 0;
            min-height: 420px;
            box-sizing: border-box;
            background: var(--surface) !important;
            color: var(--pos-text) !important;
            border: 1px solid var(--border-hairline);
            border-radius: 18px;
            box-shadow: var(--shadow-card);
            padding: 20px;
        }

        .pos-manual-footer {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid var(--divider);
        }

        .pos-manual-total {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
            min-width: 0;
        }

        .pos-manual-total span:first-child {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .pos-manual-total span:last-child {
            font-size: 22px;
            font-weight: 700;
            color: var(--accent);
            min-width: 0;
        }

        .pos-manual-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 16px;
        }

        .pos-manual-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .pos-manual-fields {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 16px;
        }

        .pos-manual-fields>* {
            min-width: 0;
        }

        .pos-manual-input {
            width: 100%;
            min-width: 0;
            height: 48px;
            box-sizing: border-box;
            padding: 0 14px;
            font-size: 15px;
            font-weight: 500;
            color: var(--text-primary) !important;
            outline: none !important;
            transition: border-color 200ms ease, box-shadow 200ms ease;
        }

        .pos-manual-price {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            min-width: 0;
            height: 48px;
            box-sizing: border-box;
            padding: 0 14px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            transition: border-color 200ms ease, box-shadow 200ms ease;
        }

        .pos-manual-price:focus-within {
            border-color: var(--accent);
            box-shadow: var(--shadow-focus);
        }

        .pos-manual-rp {
            flex-shrink: 0;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .pos-manual-price-input {
            flex: 1 1 auto;
            min-width: 0;
            width: 100%;
            height: 100%;
            border: 0 !important;
            outline: none !important;
            box-shadow: none !important;
            background: transparent !important;
            font-size: 17px;
            font-weight: 600;
            color: var(--text-primary) !important;
            padding: 0;
        }

        .pos-manual-price-input:focus {
            border: 0 !important;
            box-shadow: none !important;
            outline: none !important;
        }

        .pos-manual-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            min-width: 0;
        }

        .pos-manual-row span {
            min-width: 0;
        }

        .pos-manual-paybox h3 {
            color: var(--text-secondary) !important;
        }

        .pos-manual-actions {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 12px;
            margin-top: 0;
        }

        .pos-manual-actions button {
            min-height: 44px;
            min-width: 0;
            width: 100%;
            box-sizing: border-box;
        }

        @media (max-width: 1023px) {
            .pos-manual-card {
                padding: 16px;
                min-height: 0;
            }
        }

        @media (min-width: 768px) {
            .pos-manual-pane {
                flex: 1 1 auto;
                min-height: 0;
                overflow-x: hidden;
                overflow-y: auto;
                display: flex;
                flex-direction: column;
            }

            .pos-manual {
                flex: 1 1 auto;
                align-items: stretch;
                justify-content: flex-start;
                width: 100%;
                max-width: 100%;
            }

            .pos-manual-card {
                max-width: 100%;
                width: 100%;
                min-height: 0;
                height: auto;
            }

            .pos-manual-fields {
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            }
        }

        @media (min-width: 768px) and (max-width: 1023px) {
            .pos-manual-card {
                padding: 20px 22px;
            }

            .pos-manual-title {
                font-size: 18px;
            }

            .pos-manual-actions button {
                min-height: 48px;
            }
        }

        @media (max-width: 767px) {
            .pos-manual-title {
                font-size: 18px;
            }

            .pos-manual-input,
            .pos-manual-price {
                height: 48px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .pos-seg>button,
            .pos-seg-track>button,
            .pos-seg-pill,
            .pos-close-book {
                transition: none !important;
            }
        }

        .modal-scroll {
            scrollbar-width: thin;
            scrollbar-color: var(--pos-thumb) transparent;
        }

        [x-cloak] {
            display: none !important;
        }

        aside::-webkit-scrollbar {
            width: 6px;
        }

        aside::-webkit-scrollbar-thumb {
            background-color: var(--pos-thumb);
            border-radius: 3px;
        }

        .pos-sprite {
            position: absolute;
            width: 0;
            height: 0;
            overflow: hidden;
        }

        .pos-ico {
            fill: none;
            stroke: currentColor;
            stroke-width: 1.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        header h1,
        header .text-gray-800,
        header .dark\:text-gray-100 {
            color: var(--pos-text) !important;
        }

        header svg {
            color: var(--pos-icon);
        }

        header .hover\:bg-gray-200:hover {
            background-color: var(--pos-hover) !important;
        }

        .dark header .dark\:hover\:bg-gray-700:hover {
            background-color: var(--pos-hover) !important;
        }

        input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]),
        textarea,
        select {
            outline: none;
            transition: border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
        }

        input:focus,
        button:focus-visible,
        textarea:focus,
        select:focus {
            outline: none;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: var(--accent) !important;
            box-shadow: var(--shadow-focus) !important;
            --tw-ring-color: transparent !important;
        }

        /* Surfaces */
        .bg-white {
            background-color: var(--pos-surface) !important;
        }

        .bg-neutral-50,
        .hover\:bg-neutral-50:hover,
        .hover\:bg-neutral-100:hover {
            background-color: var(--pos-hover) !important;
        }

        .bg-neutral-100 {
            background-color: var(--surface) !important;
        }

        .bg-neutral-900 {
            background-color: var(--pos-primary) !important;
            color: var(--pos-primary-text) !important;
        }

        .hover\:bg-black:hover {
            background-color: #0066D6 !important;
            color: var(--pos-primary-text) !important;
        }

        .dark .dark\:bg-white {
            background-color: var(--pos-primary) !important;
            color: var(--pos-primary-text) !important;
        }

        .dark .dark\:bg-neutral-800,
        .dark .dark\:hover\:bg-neutral-800:hover,
        .dark .dark\:hover\:bg-neutral-800\/70:hover {
            background-color: var(--pos-surface-elevated) !important;
        }

        .dark .dark\:bg-neutral-900 {
            background-color: var(--pos-card) !important;
        }

        .dark .dark\:bg-neutral-950 {
            background-color: var(--surface) !important;
        }

        .dark .dark\:hover\:bg-neutral-200:hover {
            background-color: #409CFF !important;
            color: #ffffff !important;
        }

        .dark .dark\:bg-gray-800,
        .dark .dark\:bg-gray-900 {
            background-color: var(--pos-surface) !important;
        }

        .dark .dark\:bg-gray-700 {
            background-color: var(--pos-surface-elevated) !important;
        }

        .bg-gray-100 {
            background-color: var(--surface) !important;
        }

        .bg-gray-800,
        .bg-gray-900,
        .bg-slate-800,
        .bg-gray-800\/95 {
            background-color: var(--surface) !important;
            color: var(--text-primary) !important;
        }

        .bg-gray-700 {
            background-color: var(--surface) !important;
            color: var(--text-primary) !important;
        }

        .hover\:bg-gray-700:hover,
        .hover\:bg-gray-600:hover {
            background-color: var(--surface-secondary) !important;
            color: var(--text-primary) !important;
        }

        .text-gray-100,
        .text-gray-200 {
            color: var(--text-primary) !important;
        }

        .text-gray-300 {
            color: var(--text-secondary) !important;
        }

        .hover\:text-white:hover {
            color: var(--text-primary) !important;
        }

        .bg-black\/70,
        .bg-black\/60,
        .fixed.bg-black.bg-opacity-50 {
            background-color: rgba(0, 0, 0, 0.30) !important;
        }

        /* Borders */
        .border-neutral-200,
        .border-gray-300,
        .border-gray-200,
        .border-gray-700,
        .border-gray-600 {
            border-color: var(--border-hairline) !important;
        }

        .border-neutral-300 {
            border-color: var(--pos-border-strong) !important;
        }

        .dark .dark\:border-neutral-800,
        .dark .dark\:border-neutral-700,
        .dark .dark\:border-gray-700 {
            border-color: var(--pos-border) !important;
        }

        .hover\:border-neutral-400:hover {
            border-color: var(--pos-border-hover) !important;
        }

        .border-neutral-900 {
            border-color: var(--pos-text) !important;
        }

        .dark .dark\:hover\:border-neutral-600:hover {
            border-color: var(--pos-border-hover) !important;
        }

        .dark .dark\:border-white {
            border-color: var(--pos-text) !important;
        }

        .dark .dark\:bg-neutral-950 {
            background-color: var(--pos-panel) !important;
        }

        /* Text */
        .text-neutral-900,
        .text-neutral-800,
        .text-gray-900,
        .text-gray-800 {
            color: var(--pos-text) !important;
        }

        .text-neutral-700,
        .text-neutral-600,
        .text-neutral-500,
        .text-gray-600,
        .text-gray-500 {
            color: var(--pos-text-secondary) !important;
        }

        .text-neutral-400,
        .text-neutral-300,
        .text-gray-400,
        .placeholder\:text-neutral-400::placeholder,
        .placeholder\:text-neutral-500::placeholder {
            color: var(--pos-text-muted) !important;
        }

        .dark .dark\:text-white,
        .dark .dark\:text-neutral-100,
        .dark .dark\:text-gray-100,
        .dark .dark\:text-gray-200 {
            color: var(--pos-text) !important;
        }

        .dark .dark\:text-neutral-900 {
            color: var(--pos-primary-text) !important;
        }

        .dark .dark\:text-neutral-300,
        .dark .dark\:text-neutral-200,
        .dark .dark\:text-gray-300,
        .dark .dark\:text-gray-400 {
            color: var(--pos-text-secondary) !important;
        }

        .dark .dark\:placeholder\:text-neutral-500::placeholder {
            color: var(--pos-text-muted) !important;
        }

        /* Hue remaps — visual only, Alpine class names stay */
        .bg-blue-50,
        .bg-blue-100,
        .hover\:bg-blue-50:hover,
        .hover\:bg-blue-100:hover {
            background-color: var(--pos-accent-soft) !important;
            background-image: none !important;
            color: var(--pos-text) !important;
        }

        .bg-green-50,
        .bg-green-100,
        .bg-red-50,
        .bg-red-50\/80,
        .hover\:bg-red-50:hover,
        .hover\:bg-red-100:hover,
        .bg-yellow-50,
        .bg-amber-50,
        .from-blue-50,
        .from-green-50,
        .from-cyan-50,
        .to-blue-100,
        .to-green-100 {
            background-color: var(--pos-hover) !important;
            background-image: none !important;
            color: var(--pos-text) !important;
        }

        .bg-blue-400,
        .bg-blue-500,
        .bg-blue-600,
        .bg-blue-700,
        .bg-blue-800,
        .hover\:bg-blue-700:hover,
        .hover\:bg-blue-800:hover,
        .hover\:bg-blue-600:hover,
        .bg-green-500,
        .bg-green-600,
        .hover\:bg-green-700:hover,
        .bg-amber-500,
        .hover\:bg-amber-600:hover,
        .bg-red-600,
        .hover\:bg-red-700:hover,
        .bg-emerald-600,
        .dark .dark\:bg-blue-400,
        .dark .dark\:bg-blue-700,
        .dark .dark\:hover\:bg-blue-800:hover {
            background-color: var(--pos-accent) !important;
            background-image: none !important;
            color: #ffffff !important;
        }

        .dark .dark\:bg-blue-800,
        .dark .dark\:bg-blue-900,
        .dark .dark\:bg-blue-900\/40,
        .dark .dark\:bg-blue-900\/30,
        .dark .dark\:bg-blue-800\/20,
        .dark .dark\:from-blue-900\/40,
        .dark .dark\:to-blue-800\/20,
        .dark .dark\:from-green-900\/40,
        .dark .dark\:to-green-800\/20,
        .dark .dark\:from-cyan-900\/40,
        .dark .dark\:hover\:bg-blue-700\/40:hover,
        .dark .dark\:hover\:bg-red-900\/40:hover,
        .dark .dark\:hover\:bg-red-800\/40:hover,
        .dark .dark\:bg-green-900,
        .dark .dark\:bg-red-900\/30 {
            background-color: var(--pos-hover) !important;
            background-image: none !important;
            color: var(--pos-text) !important;
        }

        .dark .dark\:text-green-300,
        .dark .dark\:text-green-400,
        .dark .dark\:text-red-300,
        .dark .dark\:text-red-400,
        .dark .dark\:text-yellow-400,
        .dark .dark\:text-amber-400,
        .dark .dark\:text-cyan-300 {
            color: var(--pos-text-secondary) !important;
        }

        .text-blue-200,
        .text-blue-300,
        .text-blue-400,
        .text-blue-500,
        .text-blue-600,
        .text-blue-700,
        .hover\:text-blue-600:hover {
            color: var(--pos-accent) !important;
        }

        .text-green-300,
        .text-green-400,
        .text-green-500,
        .text-green-600,
        .text-green-700,
        .text-red-300,
        .text-red-400,
        .text-red-500,
        .text-red-600,
        .text-red-700,
        .text-yellow-400,
        .text-yellow-500,
        .text-yellow-600,
        .text-yellow-700,
        .text-amber-400,
        .text-amber-500,
        .text-amber-600,
        .text-cyan-300,
        .text-cyan-400,
        .text-cyan-700,
        .text-emerald-400,
        .text-emerald-500 {
            color: var(--pos-text-secondary) !important;
        }

        .dark .dark\:text-blue-300,
        .dark .dark\:text-blue-400,
        .dark .dark\:text-blue-500 {
            color: var(--pos-accent) !important;
        }

        .border-blue-400,
        .border-blue-500,
        .border-blue-600 {
            border-color: var(--pos-accent) !important;
        }

        .border-blue-100,
        .border-blue-200,
        .border-green-100,
        .border-green-500,
        .border-red-200,
        .border-red-500,
        .border-amber-500,
        .border-cyan-100 {
            border-color: var(--pos-border) !important;
        }

        .dark .dark\:border-blue-700\/50,
        .dark .dark\:border-green-700\/50,
        .dark .dark\:border-cyan-700\/50,
        .dark .dark\:border-red-800 {
            border-color: var(--pos-border) !important;
        }

        .focus\:border-blue-400:focus,
        .focus\:border-blue-500:focus,
        .focus\:ring-blue-500:focus,
        .ring-blue-300,
        .ring-blue-500 {
            border-color: var(--pos-accent) !important;
            --tw-ring-color: var(--pos-accent) !important;
        }

        .dark .dark\:ring-blue-700 {
            --tw-ring-color: var(--pos-accent) !important;
        }

        .bg-\[\#0e1420\],
        .bg-\[\#121a26\] {
            background-color: var(--surface) !important;
            color: var(--text-primary) !important;
            border-color: var(--border-hairline) !important;
            border-radius: 20px !important;
            box-shadow: var(--shadow-modal) !important;
        }

        .bg-gradient-to-r {
            background-image: none !important;
            background-color: var(--surface) !important;
            color: var(--text-primary) !important;
            box-shadow: var(--shadow-card);
            border-color: var(--border-hairline) !important;
        }

        .rounded-2xl {
            border-radius: 20px !important;
        }

        .pos-input {
            background-color: var(--surface) !important;
            border: 1px solid var(--border) !important;
            border-radius: 12px;
        }

        .pos-input:focus {
            border-color: var(--accent) !important;
            box-shadow: var(--shadow-focus) !important;
        }

        .dark .dark\:from-blue-900\/30,
        .dark .dark\:border-blue-800 {
            background-color: var(--pos-hover) !important;
            border-color: var(--pos-border) !important;
            background-image: none !important;
        }

        .pos-ico {
            color: inherit;
        }

        .pos-tab-ico {
            width: 1rem;
            height: 1rem;
            flex-shrink: 0;
        }

        .pos-digital .border.rounded-xl {
            border-radius: 18px !important;
            border-color: var(--border-hairline) !important;
            box-shadow: var(--shadow-card);
            background-color: var(--surface) !important;
        }

        .pos-digital {
            gap: 10px;
            padding: 4px 10px 12px;
            min-width: 0;
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .pos-digital::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        .pos-dig-head {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            padding-bottom: 4px;
            border-bottom: none;
        }

        .pos-dig-meta {
            display: none !important;
        }

        .pos-dig-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px 14px;
            font-size: 14px;
            line-height: 1.3;
            min-width: 0;
        }

        .pos-dig-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--pos-text);
        }

        .pos-dig-meta-item .pos-ico,
        .pos-dig-meta-item svg {
            width: 14px;
            height: 14px;
            color: var(--pos-icon);
            flex-shrink: 0;
        }

        .pos-dig-stepn {
            flex-shrink: 0;
            font-size: 14px;
            color: var(--pos-text-secondary);
            padding-top: 1px;
        }

        .pos-wiz-row {
            position: relative;
            display: flex;
            align-items: flex-start;
            width: 100%;
            min-width: 0;
            max-width: 100%;
            flex-shrink: 0;
        }

        .pos-wiz-item {
            display: flex;
            align-items: flex-start;
            flex: 1;
            min-width: 0;
        }

        .pos-wiz-item:last-child {
            flex: 0 0 auto;
        }

        .pos-wiz-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 5.5rem;
            flex-shrink: 0;
            z-index: 1;
        }

        .pos-wiz-connector {
            flex: 1;
            height: 2px;
            margin-top: 23px;
            margin-left: 6px;
            margin-right: 6px;
            background-color: #D1D1D6;
            border-radius: 1px;
            position: relative;
            overflow: hidden;
            align-self: flex-start;
        }

        html.dark .pos-wiz-connector {
            background-color: #38383A;
        }

        .pos-wiz-item:last-child .pos-wiz-connector {
            display: none;
        }

        .pos-wiz-connector-fill {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 0;
            background-color: var(--accent);
            transition: width 350ms ease-out;
        }

        .pos-wiz-item:has(.pos-wiz-done) .pos-wiz-connector-fill {
            width: 100%;
        }

        .pos-wiz-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border-width: 1.5px;
            border-style: solid;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 10;
            transition: background-color 280ms ease-out, border-color 280ms ease-out, color 280ms ease-out, box-shadow 280ms ease-out, transform 280ms ease-out, opacity 280ms ease-out;
        }

        .pos-wiz-circle .pos-ico {
            width: 20px;
            height: 20px;
        }

        .pos-wiz-check {
            display: none;
        }

        .pos-wiz-ico {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pos-digital .pos-wiz-done {
            background-color: var(--accent) !important;
            border-color: var(--accent) !important;
            color: #ffffff !important;
            animation: pos-wiz-complete 280ms ease-out;
        }

        .pos-digital .pos-wiz-done .pos-wiz-check {
            display: block;
            animation: pos-wiz-ico-in 220ms ease-out;
        }

        .pos-digital .pos-wiz-done .pos-wiz-ico {
            display: none;
        }

        .pos-digital .pos-wiz-active {
            background-color: var(--accent) !important;
            border-color: var(--accent) !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.12);
            animation: pos-wiz-complete 280ms ease-out;
        }

        html.dark .pos-digital .pos-wiz-active {
            box-shadow: 0 0 0 4px rgba(10, 132, 255, 0.16);
        }

        .pos-digital .pos-wiz-active .pos-wiz-check {
            display: none;
        }

        .pos-digital .pos-wiz-active .pos-wiz-ico {
            display: flex;
            animation: pos-wiz-ico-in 220ms ease-out;
        }

        .pos-digital .pos-wiz-todo {
            background-color: transparent !important;
            border-color: #D1D1D6 !important;
            color: #86868B !important;
            opacity: 0.8;
            box-shadow: none;
        }

        html.dark .pos-digital .pos-wiz-todo {
            background-color: #1C1C1E !important;
            border-color: #38383A !important;
            color: #8E8E93 !important;
        }

        @keyframes pos-wiz-complete {
            from {
                transform: scale(0.85);
                opacity: 0.7;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes pos-wiz-ico-in {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .pos-wiz-label {
            margin-top: 8px;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.2;
            text-align: center;
            max-width: 5.5rem;
            transition: color 200ms ease-out, opacity 200ms ease-out;
        }

        .pos-digital .pos-wiz-label-active,
        .pos-digital .pos-wiz-label-done {
            color: var(--accent) !important;
            opacity: 1;
        }

        .pos-digital .pos-wiz-label-todo {
            color: var(--text-muted) !important;
            opacity: 0.8;
        }

        @media (prefers-reduced-motion: reduce) {

            .pos-wiz-circle,
            .pos-wiz-connector-fill,
            .pos-wiz-label,
            .pos-wiz-check,
            .pos-wiz-ico {
                animation: none !important;
                transition: background-color 80ms linear, border-color 80ms linear, color 80ms linear, width 80ms linear !important;
            }
        }

        .pos-dig-back {
            font-size: 13px !important;
            padding: 0 !important;
            background: none !important;
            box-shadow: none !important;
        }

        .pos-dig-checkout {
            display: grid;
            grid-template-columns: 1fr;
            background: var(--surface);
            border: 1px solid var(--border-hairline);
            border-radius: 20px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
        }

        .pos-dig-rincian {
            padding: 16px 18px !important;
            border-radius: 0 !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        .pos-dig-rincian h4 {
            font-size: 20px !important;
            font-weight: 600 !important;
            margin-bottom: 12px !important;
        }

        .pos-dig-rincian h2 {
            font-size: 13px !important;
            font-weight: 500 !important;
            margin-bottom: 6px !important;
            color: var(--pos-text-secondary) !important;
        }

        .pos-dig-rincian input[type="text"] {
            height: 46px;
            padding: 0 12px !important;
            border-radius: 12px !important;
            background: var(--surface) !important;
            border: 1px solid var(--border) !important;
            margin-bottom: 8px !important;
        }

        .pos-dig-rincian input[type="text"]:focus {
            border-color: var(--accent) !important;
            box-shadow: var(--shadow-focus) !important;
        }

        .pos-dig-items {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 16px 28px !important;
            margin: 0 0 16px;
        }

        .pos-dig-items>div {
            display: flex !important;
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
            min-width: 0;
            min-height: 0 !important;
            max-height: none !important;
            padding: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        .pos-dig-items>div:last-child {
            grid-column: 1 / -1 !important;
        }

        .pos-dig-row-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
            min-width: 0;
        }

        .pos-dig-row-label .pos-ico {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            color: var(--accent);
        }

        .pos-dig-row-value {
            font-size: 16px;
            font-weight: 600;
            line-height: 1.3;
            color: var(--text-primary);
            text-align: left;
            min-width: 0;
            padding-left: 26px;
        }

        .pos-dig-items>div:last-child .pos-dig-row-value {
            font-size: 17px;
            font-weight: 700;
        }

        .pos-dig-pay-divider {
            height: 1px;
            background: var(--divider);
            border: none;
            margin: 4px 0 16px;
        }

        @media (max-width: 767px) {
            .pos-dig-items {
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
                gap: 12px 12px !important;
            }

            .pos-dig-row-label {
                font-size: 12px;
                gap: 6px;
            }

            .pos-dig-row-label .pos-ico {
                width: 15px;
                height: 15px;
            }

            .pos-dig-row-value {
                font-size: 14px;
                padding-left: 21px;
                text-align: left;
            }

            .pos-dig-items>div:last-child .pos-dig-row-value {
                font-size: 15px;
            }
        }

        .pos-dig-pay {
            padding: 16px 18px !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            display: flex;
            flex-direction: column;
            min-height: 0;
            border: none !important;
            border-top: 1px solid var(--divider) !important;
            text-align: left;
        }

        .pos-dig-pay h3 {
            font-size: 12px !important;
            font-weight: 600 !important;
            letter-spacing: 0.04em;
            color: var(--text-secondary) !important;
            margin-bottom: 8px !important;
        }

        .pos-dig-pay .text-4xl,
        .pos-dig-pay .text-blue-600,
        .pos-dig-pay .text-blue-400 {
            font-size: 32px !important;
            line-height: 1.15 !important;
            margin-bottom: 0 !important;
            color: var(--accent) !important;
        }

        .pos-dig-pay .mb-5 {
            margin-bottom: 10px !important;
        }

        .pos-dig-bayar {
            position: relative;
            z-index: 2;
            pointer-events: auto;
            height: 54px;
            border-radius: 14px !important;
            font-size: 15px !important;
            box-shadow: none !important;
            margin-top: auto;
        }

        .pos-dig-body>div.absolute {
            pointer-events: none !important;
        }

        @media (min-width: 768px) {
            .pos-dig-checkout {
                grid-template-columns: 1.15fr 0.85fr;
                align-items: stretch;
            }

            .pos-dig-pay {
                border-top: none !important;
                border-left: 1px solid var(--divider) !important;
                min-height: 100%;
            }
        }

        @media (min-width: 1024px) {
            main {
                overflow: hidden !important;
                padding: 8px 12px !important;
            }

            .pos-digital {
                height: calc(100vh - 64px - 16px - 3.25rem);
                max-height: calc(100vh - 64px - 16px - 3.25rem);
                overflow: hidden;
            }

            .pos-dig-body {
                flex: 1;
                min-height: 0;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .pos-dig-body::-webkit-scrollbar {
                display: none;
                width: 0;
                height: 0;
            }

            .pos-dig-checkout {
                min-height: 0;
            }

            .pos-dig-pay {
                min-height: 100%;
            }
        }

        .pos-digital .text-blue-600,
        .pos-digital .text-blue-400,
        .pos-digital .text-blue-500,
        .pos-digital .hover\:text-blue-600:hover,
        .pos-digital .dark .dark\:text-blue-400,
        .pos-digital .dark .dark\:text-blue-300 {
            color: var(--pos-accent) !important;
        }

        .pos-digital .bg-blue-50,
        .pos-digital .hover\:bg-blue-50:hover,
        .pos-digital .hover\:bg-blue-100:hover {
            background-color: var(--pos-accent-soft) !important;
            color: var(--pos-text) !important;
        }

        .pos-digital .bg-blue-600:not(:disabled),
        .pos-digital .bg-blue-500:not(:disabled),
        .pos-digital .hover\:bg-blue-700:hover:not(:disabled),
        .pos-digital .dark .dark\:bg-blue-700:not(:disabled),
        .pos-digital .dark .dark\:hover\:bg-blue-800:hover:not(:disabled) {
            background-color: var(--pos-accent) !important;
            color: #ffffff !important;
        }

        .pos-digital .border-blue-500,
        .pos-digital .border-blue-600,
        .pos-digital .border-blue-400 {
            border-color: var(--pos-accent) !important;
        }

        .pos-digital .ring-blue-300,
        .pos-digital .ring-blue-500,
        .pos-digital .dark .dark\:ring-blue-700 {
            --tw-ring-color: var(--pos-accent) !important;
        }

        .pos-digital .focus\:ring-blue-500:focus,
        .pos-digital .focus\:border-blue-500:focus {
            --tw-ring-color: var(--pos-accent) !important;
            border-color: var(--pos-accent) !important;
        }

        .pos-thumb {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            color: var(--pos-icon);
        }

        .pos-thumb img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #ffffff;
        }

        html.dark .pos-thumb img {
            background: #2C2C2E;
        }

        input.pos-input:focus,
        .pos-input:focus {
            border-color: var(--accent) !important;
            box-shadow: var(--shadow-focus) !important;
            outline: none !important;
        }

        main.relative {
            z-index: auto !important;
        }

        header {
            z-index: 40 !important;
        }

        .pos-modal-overlay {
            position: fixed;
            top: 64px;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            box-sizing: border-box;
            overflow: auto;
            background: rgba(0, 0, 0, 0.42);
        }

        .pos-modal-shell {
            position: relative;
            height: auto;
            max-height: min(90vh, 100%);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            box-sizing: border-box;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            background: var(--surface);
            color: var(--text-primary);
        }

        html.dark .pos-modal-shell {
            background: #1C1C1E;
            color: #F5F5F7;
        }

        .pos-modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 1rem;
        }

        .pos-modal-head h2 {
            min-width: 0;
            color: var(--text-primary);
        }

        .pos-modal-close {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            min-height: 44px;
            padding: 0;
            border: 0;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
        }

        .pos-modal-close:hover {
            color: var(--text-primary);
        }

        .pos-modal-body {
            min-width: 0;
        }

        .pos-modal-foot {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid var(--divider);
        }

        .pos-copy-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 118px;
            min-height: 40px;
            padding: 8px 16px;
            border-radius: 12px;
            border: 0;
            background: var(--surface-secondary);
            color: var(--text-primary);
            font-size: 14px;
            font-weight: 600;
            overflow: hidden;
            cursor: pointer;
            transition:
                background-color 280ms cubic-bezier(0.22, 1, 0.36, 1),
                color 280ms cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 280ms cubic-bezier(0.22, 1, 0.36, 1);
        }

        .pos-copy-btn.is-copied {
            background: #34C759;
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(52, 199, 89, 0.18);
        }

        html.dark .pos-copy-btn {
            background: #2C2C2E;
            color: #F5F5F7;
        }

        html.dark .pos-copy-btn.is-copied {
            background: #30D158;
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(48, 209, 88, 0.22);
        }

        .pos-copy-slot {
            position: relative;
            display: grid;
            place-items: center;
            min-height: 1.25rem;
            width: 100%;
        }

        .pos-copy-state {
            grid-area: 1 / 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            opacity: 0;
            transform: translateY(7px);
            pointer-events: none;
            transition:
                opacity 280ms cubic-bezier(0.22, 1, 0.36, 1),
                transform 280ms cubic-bezier(0.22, 1, 0.36, 1);
        }

        .pos-copy-state.is-on {
            opacity: 1;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: reduce) {

            .pos-copy-btn,
            .pos-copy-state {
                transition: none;
            }
        }

        .pos-ok-overlay {
            position: fixed;
            top: 64px;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 35;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            box-sizing: border-box;
            background: rgba(0, 0, 0, 0.24);
            pointer-events: auto;
        }

        .pos-ok-overlay.is-pos-ok-out {
            animation: pos-ok-fade-out 220ms cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        .pos-ok-card {
            width: min(90vw, 360px);
            box-sizing: border-box;
            padding: 32px 28px 28px;
            border-radius: 24px;
            background: #ffffff;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
            text-align: center;
            animation: pos-ok-card-in 320ms cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        html.dark .pos-ok-card {
            background: #1C1C1E;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.45);
        }

        .pos-ok-mark {
            width: 72px;
            height: 72px;
            margin: 0 auto 18px;
        }

        .pos-ok-mark svg {
            width: 72px;
            height: 72px;
            display: block;
        }

        .pos-ok-ring {
            fill: rgba(52, 199, 89, 0.12);
            stroke: #34C759;
            stroke-width: 2.5;
            transform-origin: 40px 40px;
            animation: pos-ok-ring-in 300ms cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .pos-ok-check {
            fill: none;
            stroke: #34C759;
            stroke-width: 5;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-dasharray: 44;
            stroke-dashoffset: 44;
            animation: pos-ok-draw 420ms cubic-bezier(0.22, 1, 0.36, 1) 280ms forwards;
        }

        .pos-ok-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #1D1D1F;
            animation: pos-ok-text-in 260ms cubic-bezier(0.22, 1, 0.36, 1) 640ms both;
        }

        html.dark .pos-ok-title {
            color: #F5F5F7;
        }

        .pos-ok-amount {
            margin: 8px 0 0;
            font-size: 15px;
            font-weight: 500;
            color: #6E6E73;
            animation: pos-ok-text-in 260ms cubic-bezier(0.22, 1, 0.36, 1) 780ms both;
        }

        html.dark .pos-ok-amount {
            color: #AEAEB2;
        }

        @keyframes pos-ok-card-in {
            from { opacity: 0; transform: scale(0.85); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes pos-ok-ring-in {
            from { opacity: 0; transform: scale(0.85); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes pos-ok-draw {
            to { stroke-dashoffset: 0; }
        }

        @keyframes pos-ok-text-in {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pos-ok-fade-out {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        .pos-ok-overlay.is-pos-ok-out .pos-ok-card {
            animation: pos-ok-card-out 220ms cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        @keyframes pos-ok-card-out {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.98); }
        }

        @media (prefers-reduced-motion: reduce) {
            .pos-ok-card,
            .pos-ok-ring,
            .pos-ok-check,
            .pos-ok-title,
            .pos-ok-amount,
            .pos-ok-overlay.is-pos-ok-out,
            .pos-ok-overlay.is-pos-ok-out .pos-ok-card {
                animation: none !important;
            }

            .pos-ok-check {
                stroke-dashoffset: 0;
            }
        }

        .pos-modal-closebook {
            background: var(--surface);
            color: var(--text-primary);
            border: 1px solid var(--divider);
        }

        .pos-modal-closebook .pos-modal-head {
            display: block;
            text-align: center;
            margin-bottom: 0;
        }

        .pos-modal-closebook .pos-modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            color: var(--text-secondary);
        }

        .pos-modal-closebook .pos-modal-close:hover {
            color: var(--text-primary);
        }

        .pos-modal-closebook .text-gray-400,
        .pos-modal-closebook .text-gray-300 {
            color: var(--text-secondary) !important;
        }

        .pos-modal-closebook hr,
        .pos-modal-closebook .border-gray-700 {
            border-color: var(--divider) !important;
        }

        .pos-modal-closebook input[type="text"] {
            color: var(--text-primary);
            background: var(--surface-secondary);
            border-bottom-color: var(--border);
            border-radius: 8px;
            padding: 4px 8px;
        }

        .pos-modal-closebook .bg-gray-700 {
            background: var(--surface-secondary) !important;
            color: var(--text-primary) !important;
        }

        .pos-modal-closebook .hover\:bg-gray-600:hover {
            background: var(--divider) !important;
        }

        .pos-modal-closebook .pos-cb-amount-debt {
            color: #E25B54 !important;
            font-weight: 600;
        }

        .pos-modal-closebook .pos-cb-amount-pay {
            color: #34C759 !important;
            font-weight: 600;
        }

        .pos-modal-closebook .pos-cb-title-transfer {
            color: #4A8FD9 !important;
        }

        .pos-modal-closebook .pos-cb-title-tarik {
            color: #E25B54 !important;
        }

        .pos-modal-closebook .pos-cb-grand-akhir,
        .pos-modal-closebook .pos-cb-grand-akhir span {
            color: #34C759 !important;
        }

        html.dark .pos-modal-closebook .pos-cb-amount-debt,
        html.dark .pos-modal-closebook .pos-cb-title-tarik {
            color: #F0716A !important;
        }

        html.dark .pos-modal-closebook .pos-cb-title-transfer {
            color: #6BA3E8 !important;
        }

        html.dark .pos-modal-closebook .pos-cb-amount-pay,
        html.dark .pos-modal-closebook .pos-cb-grand-akhir,
        html.dark .pos-modal-closebook .pos-cb-grand-akhir span {
            color: #30D158 !important;
        }

        .pos-modal-confirm {
            background: var(--surface);
            color: var(--text-primary);
            border: 1px solid var(--divider);
        }

        .pos-modal-confirm .bg-gray-700 {
            background: var(--surface-secondary) !important;
            color: var(--text-primary) !important;
        }

        html.dark .pos-modal-confirm {
            background: #1C1C1E;
            color: #F5F5F7;
            border-color: #38383A;
        }

        .pos-modal-shell-surface {
            background: #F5F5F7;
        }

        html.dark .pos-modal-shell-surface {
            background: #000000;
        }

        .pos-hist-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 18px;
        }

        .pos-hist-stat {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-width: 0;
            padding: 16px 8px 14px;
            border-radius: 20px;
            background: #ffffff;
            border: none !important;
            box-shadow: none !important;
            background-image: none !important;
        }

        html.dark .pos-hist-stat {
            background: #1C1C1E;
        }

        .pos-hist-stat-label {
            font-size: 12px;
            font-weight: 500;
            line-height: 1.2;
            color: #6E6E73;
        }

        html.dark .pos-hist-stat-label {
            color: #8E8E93;
        }

        .pos-hist-stat-value {
            margin-top: 6px;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.15;
            color: #1D1D1F;
            word-break: break-word;
        }

        html.dark .pos-hist-stat-value {
            color: #F5F5F7;
        }

        .pos-hist-stat.is-accent .pos-hist-stat-value {
            color: var(--accent);
        }

        .pos-hist-cats {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .pos-hist-cat {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 96px;
            padding: 10px 14px;
            border-radius: 16px;
            background: #ffffff;
            border: none !important;
            box-shadow: none !important;
            text-align: center;
        }

        html.dark .pos-hist-cat {
            background: #1C1C1E;
        }

        .pos-hist-list {
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            border: none;
            box-shadow: none;
        }

        html.dark .pos-hist-list {
            background: #1C1C1E;
        }

        .pos-hist-row {
            padding: 14px 16px;
            border: none !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
            cursor: pointer;
        }

        .pos-hist-row:not(:last-child) {
            box-shadow: inset 0 -0.5px 0 rgba(60, 60, 67, 0.18) !important;
        }

        html.dark .pos-hist-row:not(:last-child) {
            box-shadow: inset 0 -0.5px 0 rgba(84, 84, 88, 0.45) !important;
        }

        .pos-hist-nota {
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .pos-hist-row.is-debt .pos-hist-nota,
        .pos-hist-row.is-debt .pos-hist-status {
            color: #E25B54;
        }

        @media (max-width: 767px) {
            .pos-modal-overlay {
                padding: 8px;
            }

            .pos-modal-shell.pos-modal-shell-surface {
                width: 100%;
                max-width: none;
                padding: 16px;
                border-radius: 20px;
                max-height: calc(100svh - 64px - 16px);
            }

            .pos-modal-head h2 {
                font-size: 17px;
            }

            .pos-hist-stats {
                gap: 8px;
                margin-bottom: 14px;
            }

            .pos-hist-stat {
                padding: 12px 6px 11px;
                border-radius: 16px;
            }

            .pos-hist-stat-label {
                font-size: 10px;
            }

            .pos-hist-stat-value {
                font-size: 15px;
            }

            .pos-hist-list {
                border-radius: 16px;
            }

            .pos-hist-row {
                padding: 12px 14px;
            }

            .pos-hist-nota {
                font-size: 11px;
                line-height: 1.35;
                word-break: break-all;
            }

            .pos-hist-status {
                font-size: 10px;
                font-weight: 600;
                margin-top: 2px;
            }

            .pos-hist-status i {
                display: none;
            }

            .pos-hist-time {
                font-size: 10px;
                line-height: 1.3;
                white-space: nowrap;
            }

            .pos-hist-head-right button {
                padding: 4px;
                margin-left: 2px;
            }

            .pos-hist-head-right svg {
                width: 14px;
                height: 14px;
            }

            .pos-hist-item,
            .pos-hist-item-name,
            .pos-hist-item-amt {
                font-size: 11px;
                line-height: 1.35;
            }

            .pos-hist-item-name {
                overflow: hidden;
                text-overflow: ellipsis;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
            }

            .pos-hist-total {
                font-size: 13px;
                margin-top: 6px;
            }
        }

        html.dark .pos-modal-shell-surface {
            background: #000000;
        }

        .pos-modal-dig-hist {
            background: #F5F5F7;
        }

        html.dark .pos-modal-dig-hist {
            background: #000000;
        }

        .pos-dig-hist-card {
            background: #ffffff !important;
            border: none !important;
            border-radius: 20px !important;
            box-shadow: none !important;
            color: var(--text-primary);
        }

        .pos-dig-hist-card p.text-sm {
            color: #1D1D1F !important;
            font-weight: 700;
        }

        .pos-dig-hist-card p.text-xs {
            color: #6E6E73 !important;
            opacity: 1 !important;
        }

        html.dark .pos-dig-hist-card {
            background: #1C1C1E !important;
            border-color: rgba(255, 255, 255, 0.10) !important;
        }

        html.dark .pos-dig-hist-card p.text-sm {
            color: #f5f5f7 !important;
        }

        html.dark .pos-dig-hist-card p.text-xs {
            color: #AEAEB2 !important;
        }

        @media (max-width: 767px) {
            .pos-modal-overlay {
                padding: 8px;
            }
        }

        @media (max-width: 767px) {
            body.is-pos .pos-seg-pill {
                will-change: auto;
                transform: translateX(0);
            }

            html.is-pos {
                padding-bottom: 0 !important;
                height: auto !important;
                min-height: 0 !important;
                overflow: visible !important;
            }

            body.is-pos {
                height: auto !important;
                min-height: 0 !important;
                overflow: visible !important;
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
                filter: none !important;
            }

            body.is-pos * {
                transition-property: none !important;
            }

            body.is-pos .pos-seg.is-pos-seg-ready .pos-seg-pill {
                transition-property: transform, width !important;
                transition-duration: 300ms !important;
                transition-timing-function: cubic-bezier(0.22, 1, 0.36, 1) !important;
            }

            body.is-pos .app-sidebar.-translate-x-full {
                visibility: hidden !important;
                pointer-events: none !important;
                width: 0 !important;
                min-width: 0 !important;
                max-width: 0 !important;
                height: 0 !important;
                top: 64px !important;
                bottom: auto !important;
                overflow: hidden !important;
                border: 0 !important;
                box-shadow: none !important;
                transform: none !important;
            }

            body.is-pos .app-sidebar.translate-x-0 {
                visibility: visible !important;
                width: 15rem !important;
                max-width: 15rem !important;
                top: 64px !important;
                bottom: calc(64px + env(safe-area-inset-bottom, 0px)) !important;
                height: auto !important;
            }

            html.is-pos,
            body.is-pos {
                overscroll-behavior-x: none;
                overscroll-behavior-y: auto;
                background-color: var(--pos-bg) !important;
            }

            body.is-pos .app-shell,
            body.is-pos main {
                background-color: var(--pos-bg) !important;
                -webkit-overflow-scrolling: auto !important;
            }

            body.is-pos .pos-digital,
            body.is-pos .pos-dig-body {
                overflow-x: hidden;
                overflow-y: visible !important;
                -webkit-overflow-scrolling: auto;
                transform: none !important;
                filter: none !important;
            }

            body.is-pos .pos-dig-body > div {
                transform: none !important;
            }

            body.is-pos .pos-physical .pos-panel-divider .overflow-y-auto {
                overflow: visible !important;
                max-height: none !important;
                -webkit-overflow-scrolling: auto;
            }
        }
    </style>

    <main>
        <div x-data="posApp()" x-init="init()" class="pos-root flex flex-col gap-3 min-w-0 w-full">
            <svg class="pos-sprite" aria-hidden="true">
                <symbol id="pos-i-viewfinder" viewBox="0 0 24 24">
                    <path
                        d="M3 8V6a3 3 0 013-3h2M21 8V6a3 3 0 00-3-3h-2M3 16v2a3 3 0 003 3h2M21 16v2a3 3 0 01-3 3h-2M8 8v8M11 8v8M14 10v4M17 8v8" />
                </symbol>
                <symbol id="pos-i-bag" viewBox="0 0 24 24">
                    <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l-1.2 11.2A2 2 0 0115.81 22H8.19a2 2 0 01-1.99-1.8L5 9z" />
                </symbol>
                <symbol id="pos-i-barcode" viewBox="0 0 24 24">
                    <path d="M4 5v14M7 7v10M10 5v14M13 8v8M16 5v14M19 7v10" />
                </symbol>
                <symbol id="pos-i-search" viewBox="0 0 24 24">
                    <path d="M21 21l-4.35-4.35M16.65 10.35a6.3 6.3 0 11-12.6 0 6.3 6.3 0 0112.6 0z" />
                </symbol>
                <symbol id="pos-i-user" viewBox="0 0 24 24">
                    <path d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.118a7.5 7.5 0 0115 0" />
                </symbol>
                <symbol id="pos-i-cart" viewBox="0 0 24 24">
                    <path
                        d="M3 3h1.5l1.5 9h11l2-6H7M7.5 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm10.5 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                </symbol>
                <symbol id="pos-i-box" viewBox="0 0 24 24">
                    <path d="M21 8l-9-5-9 5m18 0l-9 5m9-5v8l-9 5M3 8l9 5M3 8v8l9 5m0-8v8" />
                </symbol>
                <symbol id="pos-i-receipt" viewBox="0 0 24 24">
                    <path
                        d="M8.25 3h5.25a3 3 0 013 3v15l-2.25-1.5L12 21l-2.25-1.5L7.5 21V6a3 3 0 01.75-3zM9 8.25h6M9 12h6M9 15.75h3.75" />
                </symbol>
                <symbol id="pos-i-clock" viewBox="0 0 24 24">
                    <path d="M12 6v6h4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </symbol>
                <symbol id="pos-i-trash" viewBox="0 0 24 24">
                    <path
                        d="M14.74 9l-.35 9m-4.78 0L9.26 9m9.97-3.21A48.1 48.1 0 0018.16 5.79L16.92 19.67A2.25 2.25 0 0114.68 21.75H9.32a2.25 2.25 0 01-2.24-2.08L5.84 5.79m12.4 0a48.67 48.67 0 00-7.5 0m7.5 0V4.87c0-1.18-.91-2.16-2.09-2.2a51.96 51.96 0 00-3.32 0c-1.18.04-2.09 1.02-2.09 2.2v.92" />
                </symbol>
                <symbol id="pos-i-plus" viewBox="0 0 24 24">
                    <path d="M12 4.5v15m7.5-7.5h-15" />
                </symbol>
                <symbol id="pos-i-minus" viewBox="0 0 24 24">
                    <path d="M5 12h14" />
                </symbol>
                <symbol id="pos-i-chevron" viewBox="0 0 24 24">
                    <path d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </symbol>
                <symbol id="pos-i-sliders" viewBox="0 0 24 24">
                    <path
                        d="M10.5 6h9.75M10.5 6a1.5 1.5 0 10-3 0m3 0a1.5 1.5 0 11-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 10-3 0m3 0a1.5 1.5 0 11-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 10-3 0m3 0a1.5 1.5 0 11-3 0m-9.75 0h9.75" />
                </symbol>
                <symbol id="pos-i-phone" viewBox="0 0 24 24">
                    <path
                        d="M8.25 3.75h7.5A1.5 1.5 0 0117.25 5.25v13.5a1.5 1.5 0 01-1.5 1.5h-7.5a1.5 1.5 0 01-1.5-1.5V5.25a1.5 1.5 0 011.5-1.5zM10.5 18h3" />
                </symbol>
                <symbol id="pos-i-cable" viewBox="0 0 24 24">
                    <path d="M8 4h8v4H8V4zM12 8v5m-3 3h6m-4.5 0v4m3-4v4" />
                </symbol>
                <symbol id="pos-i-plug" viewBox="0 0 24 24">
                    <path d="M8 3v5m8-5v5M7 8h10v4a5 5 0 01-10 0V8zm5 9v4" />
                </symbol>
                <symbol id="pos-i-battery" viewBox="0 0 24 24">
                    <path
                        d="M4.5 8.25h13.5A1.5 1.5 0 0119.5 9.75v4.5a1.5 1.5 0 01-1.5 1.5H4.5a1.5 1.5 0 01-1.5-1.5v-4.5a1.5 1.5 0 011.5-1.5zM21 11.25v1.5" />
                </symbol>
                <symbol id="pos-i-headphones" viewBox="0 0 24 24">
                    <path
                        d="M4 13a8 8 0 0116 0m-16 0v4a2 2 0 002 2h1a2 2 0 002-2v-2H4zm16 0v4a2 2 0 01-2 2h-1a2 2 0 01-2-2v-2h5z" />
                </symbol>
                <symbol id="pos-i-card" viewBox="0 0 24 24">
                    <path
                        d="M3 8.25h18M3 10.5h18M5.25 16.5h3.75M4.5 6h15A1.5 1.5 0 0121 7.5v9a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 16.5v-9A1.5 1.5 0 014.5 6z" />
                </symbol>
                <symbol id="pos-i-shield" viewBox="0 0 24 24">
                    <path
                        d="M12 3.75l7.5 3v5.4c0 4.2-2.93 8.02-7.5 9.6-4.57-1.58-7.5-5.4-7.5-9.6v-5.4l7.5-3zM9.75 12.75l1.5 1.5 3-3" />
                </symbol>
                <symbol id="pos-i-check" viewBox="0 0 24 24">
                    <path d="M4.5 12.75l6 6 9-13.5" />
                </symbol>
                <symbol id="pos-i-chevron-down" viewBox="0 0 24 24">
                    <path d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </symbol>
                <symbol id="pos-i-bolt" viewBox="0 0 24 24">
                    <path d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                </symbol>
                <symbol id="pos-i-pencil" viewBox="0 0 24 24">
                    <path
                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L8.25 18.002H4.5v-3.75L16.862 4.487zM19.5 7.125L16.875 4.5" />
                </symbol>
                <symbol id="pos-i-book" viewBox="0 0 24 24">
                    <path
                        d="M4.5 4.5A2.25 2.25 0 016.75 2.25h10.5A2.25 2.25 0 0119.5 4.5v15.75a.75.75 0 01-1.14.64L12 16.89l-6.36 4.002A.75.75 0 014.5 20.25V4.5z" />
                </symbol>
                <symbol id="pos-i-x" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" />
                </symbol>
                <symbol id="pos-i-copy" viewBox="0 0 24 24">
                    <path
                        d="M8.25 7.5V6A2.25 2.25 0 0110.5 3.75h7.5A2.25 2.25 0 0120.25 6v7.5A2.25 2.25 0 0118 15.75h-1.5M15.75 8.25H6A2.25 2.25 0 003.75 10.5V18A2.25 2.25 0 006 20.25h9.75A2.25 2.25 0 0018 18v-7.5A2.25 2.25 0 0015.75 8.25z" />
                </symbol>
                <symbol id="pos-i-arrow-left" viewBox="0 0 24 24">
                    <path d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </symbol>
                <symbol id="pos-i-apps" viewBox="0 0 24 24">
                    <path d="M4.5 4.5h6v6h-6zM13.5 4.5h6v6h-6zM4.5 13.5h6v6h-6zM13.5 13.5h6v6h-6z" />
                </symbol>
                <symbol id="pos-i-grid" viewBox="0 0 24 24">
                    <path d="M4 5.25h6.75V12H4zM13.25 5.25H20V12h-6.75zM4 14.25h6.75V21H4zM13.25 14.25H20V21h-6.75z" />
                </symbol>
                <symbol id="pos-i-list" viewBox="0 0 24 24">
                    <path d="M4.5 6.75h15M4.5 12h15M4.5 17.25h9.75" />
                </symbol>
                <symbol id="pos-i-tag" viewBox="0 0 24 24">
                    <path
                        d="M3.75 12.75l8.03 8.03a1.5 1.5 0 002.12 0l6.88-6.88a1.5 1.5 0 000-2.12L12.75 3.75H6.75A3 3 0 003.75 6.75v6zM8.25 8.25h.008v.008H8.25z" />
                </symbol>
                <symbol id="pos-i-wrench" viewBox="0 0 24 24">
                    <path
                        d="M21.75 6.75a4.5 4.5 0 01-6.29 4.14L8.03 18.32a2.25 2.25 0 01-3.18 0l-.17-.17a2.25 2.25 0 010-3.18l7.43-7.43A4.5 4.5 0 0121.75 6.75z" />
                </symbol>
                <symbol id="pos-i-cash" viewBox="0 0 24 24">
                    <path
                        d="M2.25 8.25h19.5v9a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25v-9zM2.25 8.25V6.75A2.25 2.25 0 014.5 4.5h15a2.25 2.25 0 012.25 2.25v1.5M12 15.75a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z" />
                </symbol>
            </svg>

            {{-- Tabs atas --}}
            <div class="pos-tabs border-b border-gray-300 dark:border-gray-700 pb-2">
                <div class="pos-seg-wrap">
                    <div class="pos-seg" x-ref="posSeg">
                        <div class="pos-seg-track">
                            <div class="pos-seg-pill" aria-hidden="true"></div>
                            <button type="button" data-pos-tab="physical" @click="activeTab = 'physical'"
                                :class="activeTab === 'physical' ? 'is-pos-seg-on' : ''"
                                class="px-4 py-2 rounded-xl font-semibold text-[16px] transition inline-flex items-center gap-2">
                                <svg class="pos-ico pos-tab-ico">
                                    <use href="#pos-i-bag"></use>
                                </svg>
                                Produk Fisik
                            </button>

                            <button type="button" data-pos-tab="digital" @click="activeTab = 'digital'"
                                :class="activeTab === 'digital' ? 'is-pos-seg-on' : ''"
                                class="px-4 py-2 rounded-xl font-semibold text-[16px] transition inline-flex items-center gap-2">
                                <svg class="pos-ico pos-tab-ico">
                                    <use href="#pos-i-bolt"></use>
                                </svg>
                                Produk Digital
                            </button>

                            <button type="button" data-pos-tab="manual" @click="activeTab = 'manual'"
                                :class="activeTab === 'manual' ? 'is-pos-seg-on' : ''"
                                class="px-4 py-2 rounded-xl font-semibold text-[16px] transition inline-flex items-center gap-2">
                                <svg class="pos-ico pos-tab-ico">
                                    <use href="#pos-i-pencil"></use>
                                </svg>
                                Input Manual
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" @click="handleCloseBook()"
                    class="pos-close-book bg-neutral-900 hover:bg-black text-white dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200 font-semibold text-[16px] px-4 py-2 rounded-xl transition inline-flex items-center gap-2">
                    <svg class="pos-ico pos-tab-ico">
                        <use href="#pos-i-book"></use>
                    </svg>
                    Tutup Buku
                </button>
            </div>

            <!-- MODAL CLOSE BOOK - CLEAN & NEAT VERSION -->
            <div x-show="showCloseBookModal" x-transition.opacity @keydown.escape.window="showCloseBookModal = false"
                @click.self="showCloseBookModal = false" class="pos-modal-overlay" x-cloak>

                <div class="pos-modal-shell pos-modal-closebook w-full max-w-md">

                    <div class="pos-modal-head">
                        <h2 class="text-lg font-bold">Transaction</h2>
                        <button @click="showCloseBookModal = false" class="pos-modal-close">
                            <svg class="pos-ico w-5 h-5">
                                <use href="#pos-i-x"></use>
                            </svg>
                        </button>
                    </div>

                    <div class="pos-modal-body">
                        <div class="text-center text-sm text-gray-400 mb-5" x-text="closeBookData?.tanggal">
                        </div>

                        <!-- BODY WRAPPER -->
                        <template x-if="closeBookData">
                            <div class="text-sm space-y-4" x-show="closeBookData">

                                <!-- BARANG + DIGITAL PER APP -->
                                <div class="space-y-0">
                                    <div class="flex justify-between py-1">
                                        <span>Barang</span>
                                        <span x-text="formatRupiah(closeBookData.barangTotal)"></span>
                                    </div>

                                    <template x-if="closeBookData.digitalPerApp.length > 0">
                                        <div class="space-y-0">
                                            <template x-for="app in closeBookData.digitalPerApp" :key="app.name">
                                                <div class="flex justify-between py-1">
                                                    <span x-text="app.name"></span>
                                                    <span x-text="formatRupiah(app.total)"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>

                                <hr class="border-gray-700">

                                <!-- TOTAL PENJUALAN -->
                                <div class="flex justify-between font-semibold">
                                    <span>Total Penjualan</span>
                                    <span x-text="formatRupiah(closeBookData.totalPenjualan)"></span>
                                </div>

                                <!-- UTANG -->
                                <template x-if="closeBookData.utangList.length > 0">
                                    <div class="pt-1">
                                        <div class="font-semibold text-gray-300 mb-1">UTANG</div>
                                        <template x-for="u in closeBookData.utangList" :key="u.name">
                                            <div class="flex justify-between">
                                                <span x-text="u.name"></span>
                                                <span class="pos-cb-amount-debt"
                                                    x-text="'-' + formatRupiah(u.subtotal)"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- BAYAR UTANG -->
                                <template x-if="closeBookData.bayarUtangList.length > 0">
                                    <div class="pt-1">
                                        <div class="font-semibold text-gray-300 mb-1">BAYAR UTANG</div>
                                        <template x-for="u in closeBookData.bayarUtangList" :key="u.name">
                                            <div class="flex justify-between">
                                                <span x-text="u.name"></span>
                                                <span class="pos-cb-amount-pay" x-text="formatRupiah(u.subtotal)"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <hr class="border-gray-700">

                                <!-- TOTAL SETELAH UTANG -->
                                <div class="flex justify-between font-semibold">
                                    <span>Total Setelah Utang</span>
                                    <span
                                        x-text="formatRupiah(Number(closeBookData.totalPenjualan) + Number(closeBookData.bayarUtang) - Number(closeBookData.totalUtang))">
                                    </span>
                                </div>

                                <hr class="border-gray-700">

                                <!-- GRAND TOTAL -->
                                <div class="flex justify-between font-bold text-lg">
                                    <span>Grand Total</span>
                                    <span x-text="formatRupiah(closeBookData.grandTotal)"></span>
                                </div>

                                <!-- LEBIH INPUT -->
                                <div class="flex justify-between items-center">
                                    <span>Lebih</span>
                                    <input type="text" placeholder="0" x-on:input="formatLebihInput($event)"
                                        class="w-20 bg-transparent border-0 border-b border-gray-600 text-right focus:border-blue-400 focus:outline-none focus:ring-0">
                                </div>

                                <!-- GRAND TOTAL AKHIR -->
                                <div class="pos-cb-grand-akhir flex justify-between font-bold text-lg">
                                    <span>Grand Total Akhir</span>
                                    <span
                                        x-text="formatRupiah(Number(closeBookData.grandTotal) + Number(lebih || 0))"></span>
                                </div>

                                <hr class="border-gray-700">

                                <!-- TRANSFER -->
                                <template x-if="closeBookData.transferDetail.length > 0">
                                    <div>
                                        <div class="pos-cb-title-transfer font-semibold mb-1">TRANSFER</div>
                                        <template x-for="t in closeBookData.transferDetail" :key="t.name">
                                            <div class="flex justify-between text-sm">
                                                <span x-text="t.name"></span>
                                                <span x-text="formatRupiah(t.total)"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- TARIK -->
                                <template x-if="closeBookData.tarikDetail.length > 0">
                                    <div>
                                        <div class="pos-cb-title-tarik font-semibold mb-1">TARIK</div>
                                        <template x-for="t in closeBookData.tarikDetail" :key="t.name">
                                            <div class="flex justify-between text-sm">
                                                <span x-text="t.name"></span>
                                                <span x-text="formatRupiah(t.total)"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                    </div>
                    <!-- FOOTER BUTTONS -->
                    <div class="pos-modal-foot flex justify-end gap-3">
                        <button @click="copyCloseBook" class="pos-copy-btn" :class="{ 'is-copied': copied }">
                            <span class="pos-copy-slot">
                                <span class="pos-copy-state" :class="{ 'is-on': !copied }">
                                    <svg class="pos-ico w-4 h-4">
                                        <use href="#pos-i-copy"></use>
                                    </svg>
                                    Copy
                                </span>
                                <span class="pos-copy-state" :class="{ 'is-on': copied }">
                                    <svg class="pos-ico w-4 h-4">
                                        <use href="#pos-i-check"></use>
                                    </svg>
                                    Tersalin
                                </span>
                            </span>
                        </button>

                        <button
                            @click="grandTotalAkhir = Number(closeBookData.grandTotal) + Number(lebih || 0); showConfirmClose = true"
                            class="flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm transition">
                            <svg class="pos-ico w-4 h-4">
                                <use href="#pos-i-book"></use>
                            </svg>
                            Tutup Buku
                        </button>
                    </div>

                    <div x-show="showConfirmClose" x-cloak x-transition.opacity @keydown.escape.window="showConfirmClose = false"
                        @click="showConfirmClose = false" class="pos-modal-overlay z-[90]">
                        <div @click.stop class="pos-modal-confirm p-6 rounded-xl shadow-2xl w-full max-w-sm">


                            <h3 class="text-lg font-semibold text-center mb-4">Yakin ingin tutup buku?</h3>


                            <p class="text-center mb-6">
                                Total akhir:
                                <span class="font-bold text-green-400" x-text="formatRupiah(grandTotalAkhir)"></span>
                            </p>


                            <div class="flex justify-center gap-3">
                                <button @click="showConfirmClose = false"
                                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm">Batal</button>


                                <button @click="showConfirmClose = false; handleFinalCloseBook();"
                                    class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm">Ya,
                                    Tutup Buku</button>
                            </div>


                        </div>
                    </div>

                </div>
            </div>

            {{-- ============================= --}}
            {{-- TAB: PRODUK FISIK --}}
            {{-- ============================= --}}
            <div x-show="activeTab === 'physical'"
                class="pos-physical h-[calc(100vh-9rem)] min-h-[520px] flex flex-col overflow-hidden">

                <div class="pos-physical-split flex flex-1 min-h-0 gap-0">
                    {{-- Product workspace --}}
                    <div class="pos-physical-workspace flex-1 min-w-0 flex flex-col pr-5">
                        <div class="pos-physical-tools grid grid-cols-[1.65fr_1fr] gap-3 mb-4 shrink-0">
                            <div class="relative" x-data="{ focused: false }">
                                <input id="barcodeInput" type="text" placeholder="Scan barcode produk..."
                                    autocomplete="off" @focus="focused = true" @blur="focused = false"
                                    @keydown.enter.prevent="
                handleBarcodeInput($event);
                $event.target.value = '';
            "
                                    class="pos-input w-full h-12 pl-11 pr-4 outline-none text-[15px] text-neutral-900 dark:text-neutral-100 placeholder:text-neutral-400 dark:placeholder:text-neutral-500"
                                    :class="focused ? 'border-neutral-900 dark:border-white' :
                                        'border-neutral-200 dark:border-neutral-800'"
                                    autofocus>
                                <div class="absolute left-3.5 top-3.5 pointer-events-none"
                                    :class="focused ? 'text-neutral-900 dark:text-white' : 'text-neutral-400'">
                                    <svg class="pos-ico w-5 h-5">
                                        <use href="#pos-i-viewfinder"></use>
                                    </svg>
                                </div>
                            </div>

                            <div class="relative" x-data="{ focused: false }">
                                <input id="searchInput" type="text" placeholder="Cari produk atau barcode..."
                                    x-model="searchQuery" @focus="focused = true" @blur="focused = false"
                                    class="pos-input w-full h-12 pl-11 pr-4 outline-none text-[15px] text-neutral-900 dark:text-neutral-100 placeholder:text-neutral-400 dark:placeholder:text-neutral-500"
                                    :class="focused ? 'border-neutral-900 dark:border-white' :
                                        'border-neutral-200 dark:border-neutral-800'">
                                <div class="absolute left-3.5 top-3.5 text-neutral-400 pointer-events-none">
                                    <svg class="pos-ico w-5 h-5">
                                        <use href="#pos-i-search"></use>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div id="productScrollArea" class="relative flex-1 min-h-0 overflow-y-auto pr-1">
                            <div class="grid grid-cols-2 xl:grid-cols-3 gap-2.5"
                                x-show="!transitioning && !isCategoryLoading">

                                <template x-for="product in filteredProducts" :key="product.id">
                                    <div @click.stop="openProductOptions(product)"
                                        :class="{ 'opacity-40 pointer-events-none': product.stock <= 0 }"
                                        class="pos-product-card">

                                        <div class="pos-product-top">
                                            <div class="pos-product-icon">
                                                <svg class="pos-ico w-5 h-5">
                                                    <use :href="'#pos-i-' + productIconKey(product)"></use>
                                                </svg>
                                            </div>
                                            <div class="pos-product-titles">
                                                <h3 class="pos-product-name" x-text="product.name"></h3>
                                                <p class="pos-product-category" x-text="product.category_name"></p>
                                            </div>
                                            <span class="pos-product-badge" x-text="displayStock(product)"></span>
                                        </div>
                                        <p class="pos-product-price"
                                            x-text="'Rp ' + Number(product.price).toLocaleString('id-ID')"></p>
                                        <p class="pos-product-barcode">
                                            <svg class="pos-ico w-3.5 h-3.5">
                                                <use href="#pos-i-barcode"></use>
                                            </svg>
                                            <span class="truncate" x-text="product.code"></span>
                                        </p>
                                        <template x-if="(product.attribute_values?.length || 0) > 1">
                                            <p class="pos-product-variant">Multiple Options</p>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <template x-if="!isCategoryLoading && filteredProducts.length === 0">
                                <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                                    <svg class="pos-ico w-10 h-10 mb-3 text-gray-500">
                                        <use href="#pos-i-box"></use>
                                    </svg>
                                    <p class="text-sm">Tidak ada produk.</p>
                                </div>
                            </template>

                            <template x-if="loadingMore">
                                <div class="text-center text-gray-400 py-4 animate-pulse text-sm">
                                    Memuat produk tambahan...
                                </div>
                            </template>
                            <template x-if="isCategoryLoading">
                                <div class="flex justify-center items-center py-10">
                                    <svg class="animate-spin h-7 w-7 text-neutral-400" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 000 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z">
                                        </path>
                                    </svg>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div x-show="showToast" x-cloak x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="fixed right-6 z-[9999] pointer-events-auto" style="top: calc(64px + 0.75rem);">
                        <div
                            class="bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 px-4 py-2 rounded-lg text-sm font-semibold">
                            <span x-text="toastMsg"></span>
                        </div>
                    </div>

                    <aside class="pos-panel-divider w-[300px] xl:w-[340px] shrink-0 flex flex-col overflow-hidden">
                        <div class="px-4 pt-4 pb-3 shrink-0">
                            <h2
                                class="flex items-center justify-between text-[11px] font-semibold tracking-wide text-neutral-500 uppercase">
                                <span class="flex items-center gap-2">
                                    <svg class="pos-ico w-4 h-4">
                                        <use href="#pos-i-bag"></use>
                                    </svg>
                                    Keranjang
                                </span>
                                <span class="normal-case tracking-normal font-medium text-neutral-900 dark:text-white"
                                    x-text="cart.length"></span>
                            </h2>
                        </div>

                        <div class="px-4 pb-3 shrink-0" x-data="{ open: false }">
                            <p class="text-[11px] text-neutral-400 mb-1.5">Pelanggan</p>
                            <div @click="open = !open"
                                class="w-full border border-neutral-300 dark:border-neutral-700 rounded-xl px-3 py-2 text-[15px] flex justify-between items-center cursor-pointer hover:border-neutral-400 dark:hover:border-neutral-600">
                                <span class="flex items-center gap-2 text-neutral-800 dark:text-neutral-100">
                                    <svg class="pos-ico w-4 h-4 text-neutral-400">
                                        <use href="#pos-i-user"></use>
                                    </svg>
                                    <span
                                        x-text="selectedCustomer
                            ? (customers.find(c => c.id == selectedCustomer)?.name || '')
                            : 'Pelanggan umum'">
                                    </span>
                                </span>
                                <svg class="pos-ico w-4 h-4 text-neutral-400">
                                    <use href="#pos-i-chevron-down"></use>
                                </svg>
                            </div>

                            <div x-show="open" @click.outside="open = false"
                                class="mt-2 border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 rounded-lg shadow-sm relative z-20">

                                <input type="text" x-model="customerSearch" placeholder="Cari pelanggan..."
                                    data-pos-keep-focus="1"
                                    class="w-full p-2 border-b border-neutral-200 dark:border-neutral-800 bg-transparent text-sm outline-none">

                                <div class="p-2 cursor-pointer hover:bg-neutral-100 dark:hover:bg-neutral-800 text-sm"
                                    @click="selectedCustomer = ''; open = false; customerSearch = ''; focusScanner()">
                                    Pelanggan umum
                                </div>

                                <div class="max-h-40 overflow-y-auto">
                                    <template x-for="cust in filteredCustomers" :key="cust.id">
                                        <div class="p-2 cursor-pointer hover:bg-neutral-100 dark:hover:bg-neutral-800 text-sm"
                                            @click="selectedCustomer = cust.id; open = false; customerSearch = cust.name; focusScanner()"
                                            x-text="cust.name"></div>
                                    </template>

                                    <template x-if="filteredCustomers.length === 0">
                                        <div class="p-2 text-neutral-400 text-sm">Tidak ditemukan.</div>
                                    </template>
                                </div>
                            </div>

                            <template x-if="selectedCustomer">
                                <p class="mt-1.5 text-xs text-neutral-500">
                                    Transaksi akan dicatat sebagai utang.
                                </p>
                            </template>
                        </div>

                        <div class="flex-1 min-h-0 overflow-y-auto px-4 border-t pos-item-divider pt-2">
                            <template x-if="cart.length === 0">
                                <div class="flex flex-col items-center text-center text-neutral-400 py-12 px-4">
                                    <svg class="pos-ico w-10 h-10 mb-3">
                                        <use href="#pos-i-bag"></use>
                                    </svg>
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Keranjang kosong</p>
                                    <p class="text-xs text-neutral-400 mt-1">Scan barcode untuk mulai</p>
                                </div>
                            </template>

                            <template x-for="(item, index) in cart" :key="item.id + '-' + (item.variant_id ?? 'default')">
                                <div class="py-3 border-b pos-item-divider last:border-0">
                                    <div class="flex justify-between gap-3">
                                        <div class="min-w-0">
                                            <div x-text="item.name"
                                                class="font-medium text-sm leading-snug text-neutral-900 dark:text-white">
                                            </div>
                                            <div class="text-xs text-neutral-400 mt-0.5"
                                                x-text="(item.variant ? (item.variant + ' · ') : '') + 'Rp ' + Number(item.price).toLocaleString('id-ID') + ' / unit'">
                                            </div>
                                        </div>
                                        <div class="text-sm font-semibold tabular-nums whitespace-nowrap text-neutral-900 dark:text-white"
                                            x-text="'Rp ' + (item.price * item.qty).toLocaleString('id-ID')"></div>
                                    </div>
                                    <div class="flex items-center justify-between mt-2">
                                        <div class="flex items-center gap-2">
                                            <button @click="decreaseQty(index)"
                                                class="w-7 h-7 rounded-xl border border-neutral-300 dark:border-neutral-700 text-neutral-500 hover:text-neutral-900 dark:hover:text-white flex items-center justify-center">
                                                <svg class="pos-ico w-3.5 h-3.5">
                                                    <use href="#pos-i-minus"></use>
                                                </svg>
                                            </button>
                                            <span class="w-6 text-center text-sm font-medium" x-text="item.qty"></span>
                                            <button @click="increaseQty(index)"
                                                class="w-7 h-7 rounded-xl border border-neutral-300 dark:border-neutral-700 text-neutral-500 hover:text-neutral-900 dark:hover:text-white flex items-center justify-center">
                                                <svg class="pos-ico w-3.5 h-3.5">
                                                    <use href="#pos-i-plus"></use>
                                                </svg>
                                            </button>
                                        </div>
                                        <button @click="removeCartItem(index)"
                                            class="w-7 h-7 rounded-md text-neutral-400 hover:text-neutral-900 dark:hover:text-white flex items-center justify-center"
                                            title="Hapus">
                                            <svg class="pos-ico w-3.5 h-3.5">
                                                <use href="#pos-i-trash"></use>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="pos-cart-foot shrink-0 px-4 py-4 border-t pos-total-divider">
                            <div class="mb-4">
                                <h3
                                    class="flex items-center gap-2 text-[11px] font-semibold tracking-wide text-neutral-400 mb-1">
                                    <svg class="pos-ico w-4 h-4">
                                        <use href="#pos-i-receipt"></use>
                                    </svg>
                                    TOTAL BELANJA
                                </h3>

                                <template x-if="!editingTotal">
                                    <div @click="editingTotal = true"
                                        class="text-right text-[36px] font-semibold text-neutral-900 dark:text-white cursor-pointer select-none leading-tight"
                                        x-text="'Rp ' + payment.total.toLocaleString('id-ID')">
                                    </div>
                                </template>

                                <template x-if="editingTotal">
                                    <input type="text" x-ref="totalInput" inputmode="numeric" pattern="[0-9]*"
                                        @focus="payment.editingPaid = true"
                                        @blur="editingTotal = false; payment.editingPaid = false; focusScanner()"
                                        @input="formatTotalInput($event)" @keydown.enter="$el.blur()"
                                        class="w-full text-right text-[36px] font-semibold border border-neutral-900 dark:border-white bg-transparent
                 text-neutral-900 dark:text-white rounded-xl px-2 py-1 outline-none"
                                        placeholder="Total belanja">
                                </template>

                                <template x-if="payment.total !== total()">
                                    <div class="text-sm text-neutral-400 mt-1 text-right">
                                        Asli:
                                        <span class="line-through" x-text="'Rp ' + total().toLocaleString('id-ID')">
                                        </span>
                                    </div>
                                </template>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <button @click="loadTodayTransactions()"
                                    class="pos-cart-btn h-11 flex items-center justify-center gap-2 rounded-xl border border-neutral-300 dark:border-[#48484A] bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 text-[15px] font-semibold hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                                    <svg class="pos-ico w-4 h-4">
                                        <use href="#pos-i-clock"></use>
                                    </svg>
                                    Riwayat
                                </button>
                                <button @click="openReviewModal()" :disabled="cart.length === 0"
                                    class="pos-cart-btn h-11 flex items-center justify-center gap-2 rounded-xl border border-neutral-900 dark:border-white bg-neutral-900 text-white dark:bg-white dark:text-neutral-900 text-[15px] font-semibold hover:bg-neutral-800 dark:hover:bg-neutral-200 disabled:cursor-not-allowed transition">
                                    <svg class="pos-ico w-4 h-4">
                                        <use href="#pos-i-receipt"></use>
                                    </svg>
                                    Bayar
                                </button>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>



            {{-- ============================= --}}
            {{-- MODAL: PILIH VARIAN PRODUK --}}
            {{-- ============================= --}}
            <div x-show="showOptionModal" x-cloak x-transition @keydown.window.escape="showOptionModal = false; focusScanner()"
                @click.self="showOptionModal = false; focusScanner()" class="pos-modal-overlay">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-3xl mx-4 p-8 relative">
                    {{-- Tombol Close --}}
                    <button @click="showOptionModal = false; focusScanner()"
                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">
                        <i class="fa-solid fa-xmark text-3xl"></i>
                    </button>

                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100" x-text="selectedProduct?.name">
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="selectedProduct?.code"></p>
                    </div>

                    <div
                        class="mb-4 text-blue-700 bg-blue-50 dark:bg-blue-900/40 dark:text-blue-300 rounded-lg p-3 text-sm">
                        Produk ini memiliki beberapa varian. Silakan pilih salah satu:
                    </div>

                    {{-- Grid varian lebih besar --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        <template x-for="opt in selectedProductOptions" :key="opt.id">
                            <button @click="opt.stok > 0 && chooseOption(opt)" :disabled="opt.stok <= 0"
                                :class="opt.stok > 0 ?
                                    'border-2 rounded-xl p-4 text-left hover:bg-blue-50 dark:hover:bg-blue-800 transition flex flex-col justify-between min-h-[110px]' :
                                    'border-2 rounded-xl p-4 text-left bg-gray-100 dark:bg-gray-700 opacity-60 cursor-not-allowed flex flex-col justify-between min-h-[110px]'">
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-lg"
                                    x-text="opt.attribute_value"></div>
                                <div class="text-sm"
                                    :class="opt.stok > 0 ? 'text-green-600 dark:text-green-400' :
                                        'text-red-500 dark:text-red-400'"
                                    x-text="opt.stok > 0 ? ('Stok: ' + opt.stok) : 'Stok Habis'"></div>
                                <div class="text-base font-bold text-blue-600"
                                    x-text="selectedProduct ? 'Rp ' + Number(selectedProduct.price).toLocaleString() : 'Rp 0'">
                                </div>
                            </button>
                        </template>
                    </div>

                    <div class="text-xs text-gray-500 mt-6 border-t pt-4 text-center">
                        Klik varian untuk menambah ke keranjang.<br>
                        <span class="block text-gray-400 mt-1">Tekan <strong>ESC</strong> atau klik di luar area untuk
                            menutup.</span>
                    </div>
                </div>
            </div>

            {{-- ============================= --}}
            {{-- TAB: PRODUK DIGITAL (Final Enhanced Version) --}}
            {{-- ============================= --}}
            <div x-show="activeTab === 'digital'" x-cloak
                class="pos-digital flex flex-col text-gray-800 dark:text-gray-100">

                {{-- Header --}}
                <div class="pos-dig-head">
                    <div class="pos-dig-meta">
                        <template x-if="selectedDevice">
                            <div class="pos-dig-meta-item">
                                <svg class="pos-ico">
                                    <use href="#pos-i-phone"></use>
                                </svg>
                                <span class="text-gray-500 dark:text-gray-400">Device:</span>
                                <div class="flex items-center gap-1.5">
                                    <template x-if="selectedDevice.icon && window.heroicons[selectedDevice.icon]">
                                        <div class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400 [&>svg]:w-3.5 [&>svg]:h-3.5"
                                            x-html="window.heroicons[selectedDevice.icon]"></div>
                                    </template>
                                    <span class="font-medium text-gray-700 dark:text-gray-200"
                                        x-text="selectedDevice.name"></span>
                                </div>
                            </div>
                        </template>

                        <template x-if="selectedApp">
                            <div class="pos-dig-meta-item">
                                <svg class="pos-ico">
                                    <use href="#pos-i-apps"></use>
                                </svg>
                                <span class="text-gray-500 dark:text-gray-400">Aplikasi:</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="pos-thumb w-3.5 h-3.5 rounded">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                                            <rect x="4" y="4" width="16" height="16" rx="3"
                                                stroke="currentColor" stroke-width="1.2" />
                                        </svg>
                                        <template x-if="selectedApp.logo">
                                            <img :src="(selectedApp.logo.startsWith('http') || selectedApp.logo.startsWith('/')) ?
                                            selectedApp.logo: ('/storage/' + selectedApp.logo)"
                                                class="rounded" alt="" onerror="this.remove()">
                                        </template>
                                    </span>
                                    <span class="font-medium text-gray-700 dark:text-gray-200"
                                        x-text="selectedApp.name"></span>
                                </div>
                            </div>
                        </template>

                        <template x-if="selectedCategory">
                            <div class="pos-dig-meta-item">
                                <svg class="pos-ico">
                                    <use href="#pos-i-grid"></use>
                                </svg>
                                <span class="text-gray-500 dark:text-gray-400">Kategori:</span>
                                <span class="font-medium text-gray-700 dark:text-gray-200"
                                    x-text="selectedCategory.name"></span>
                            </div>
                        </template>

                        <template x-if="selectedBrand">
                            <div class="pos-dig-meta-item">
                                <svg class="pos-ico">
                                    <use href="#pos-i-tag"></use>
                                </svg>
                                <span class="text-gray-500 dark:text-gray-400">Brand:</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="pos-thumb w-3.5 h-3.5 rounded-full">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                                            <path d="M7 7h10l1 4H6l1-4zM6 11h12v8H6z" stroke="currentColor"
                                                stroke-width="1.2" />
                                        </svg>
                                        <template x-if="selectedBrand.logo">
                                            <img :src="selectedBrand.logo.startsWith('/storage') || selectedBrand.logo.startsWith(
                                                'http') ? selectedBrand.logo : '/storage/' + selectedBrand.logo"
                                                class="rounded-full" alt="" onerror="this.remove()">
                                        </template>
                                    </span>
                                    <span class="font-medium text-gray-700 dark:text-gray-200"
                                        x-text="selectedBrand.name"></span>
                                </div>
                            </div>
                        </template>

                        <template x-if="selectedProduct">
                            <div class="pos-dig-meta-item">
                                <svg class="pos-ico">
                                    <use href="#pos-i-box"></use>
                                </svg>
                                <span class="text-gray-500 dark:text-gray-400">Produk:</span>
                                <span class="font-medium text-gray-700 dark:text-gray-200"
                                    x-text="selectedProduct.name"></span>
                            </div>
                        </template>
                    </div>
                    <div class="pos-dig-stepn">
                        Langkah <span x-text="step"></span> dari 6
                    </div>
                </div>

                {{-- Progress Wizard --}}
                <div class="pos-wiz-row">
                    <template
                        x-for="(item, index) in [
                                { icon: 'device', label: 'Device' },
                                { icon: 'app', label: 'Aplikasi' },
                                { icon: 'category', label: 'Kategori' },
                                { icon: 'brand', label: 'Brand' },
                                { icon: 'product', label: 'Produk' },
                                { icon: 'payment', label: 'Pembayaran' }
                        ]"
                        :key="index">
                        <div class="pos-wiz-item">
                            <div class="pos-wiz-step">
                                <div class="pos-wiz-circle"
                                    :class="{
                                        'pos-wiz-done': step > (index + 1),
                                        'pos-wiz-active': step === (index + 1),
                                        'pos-wiz-todo': step < (index + 1)
                                    }">
                                    <svg class="pos-ico pos-wiz-check">
                                        <use href="#pos-i-check"></use>
                                    </svg>
                                    <span class="pos-wiz-ico">
                                        <template x-if="item.icon === 'device'">
                                            <svg class="pos-ico">
                                                <use href="#pos-i-phone"></use>
                                            </svg>
                                        </template>
                                        <template x-if="item.icon === 'app'">
                                            <svg class="pos-ico">
                                                <use href="#pos-i-apps"></use>
                                            </svg>
                                        </template>
                                        <template x-if="item.icon === 'category'">
                                            <svg class="pos-ico">
                                                <use href="#pos-i-grid"></use>
                                            </svg>
                                        </template>
                                        <template x-if="item.icon === 'brand'">
                                            <svg class="pos-ico">
                                                <use href="#pos-i-tag"></use>
                                            </svg>
                                        </template>
                                        <template x-if="item.icon === 'product'">
                                            <svg class="pos-ico">
                                                <use href="#pos-i-box"></use>
                                            </svg>
                                        </template>
                                        <template x-if="item.icon === 'payment'">
                                            <svg class="pos-ico">
                                                <use href="#pos-i-card"></use>
                                            </svg>
                                        </template>
                                    </span>
                                </div>
                                <span class="pos-wiz-label"
                                    :class="step >= (index + 1) ? 'pos-wiz-label-active font-semibold' :
                                        'pos-wiz-label-todo'">
                                    <span x-text="item.label"></span>
                                </span>
                            </div>
                            <div class="pos-wiz-connector" aria-hidden="true">
                                <div class="pos-wiz-connector-fill"></div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Step Content Container with Directional Animation --}}
                <div class="pos-dig-body relative overflow-y-auto">
                    {{-- Use x-show and x-transition for directional slide --}}
                    <template x-for="s in [1, 2, 3, 4, 5, 6]" :key="s">
                        <div x-show="step === s"
                            :class="{
                                'absolute inset-0 w-full': step !== s,
                            }"
                            x-transition:enter="transition ease-out duration-500"
                            :x-transition:enter-start="s > $el.parentNode.__x_original_step ? 'opacity-0 translate-x-full' : (s < $el.parentNode.__x_original_step ? 'opacity-0 -translate-x-full' : 'opacity-0')"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-500 absolute inset-0 w-full"
                            :x-transition:leave-end="s < $el.parentNode.__x_original_step ? 'opacity-0 translate-x-full' : (s > $el.parentNode.__x_original_step ? 'opacity-0 -translate-x-full' : 'opacity-0')"
                            x-init="$el.parentNode.__x_original_step = step">
                            {{-- Store original step for comparison, ensuring the initial state is set --}}
                            <div x-show="step === s">
                                <div x-init="$el.parentNode.parentNode.__x_original_step = step">

                                    {{-- Step 1: Pilih Device --}}
                                    <template x-if="s === 1">
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <h3
                                                    class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                                    1. Pilih Device
                                                </h3>

                                                {{-- Tombol Riwayat Transaksi Digital --}}
                                                <button @click="showHistoryDigital = true"
                                                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg
                                                    bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200
                                                    hover:bg-gray-300 dark:hover:bg-gray-600 transition text-sm font-semibold shadow-sm active:scale-[0.98]">
                                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-9-9 9 9 0 019 9z" />
                                                    </svg>
                                                    <span>Riwayat</span>
                                                </button>
                                            </div>

                                            {{-- ✅ Jika ada device --}}
                                            <template x-if="devices.length > 0">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                                    <template x-for="dev in devices" :key="dev.id">
                                                        <div @click="selectedDevice = dev; $el.parentNode.parentNode.__x_original_step = 1; step = 2; selectedApp = null; selectedCategory = null; selectedProduct = null;"
                                                            class="border dark:border-gray-700 rounded-xl p-4 cursor-pointer bg-white dark:bg-gray-800 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200"
                                                            :class="selectedDevice?.id === dev.id ?
                                                                'border-blue-500 ring-2 ring-blue-300 dark:ring-blue-700' :
                                                                'border-gray-300 dark:border-gray-700'">
                                                            <div class="flex flex-col items-center text-center">
                                                                <div
                                                                    class="w-12 h-12 rounded-full flex items-center justify-center mb-2 bg-gray-100 dark:bg-gray-700">
                                                                    <template
                                                                        x-if="dev.icon && window.heroicons[dev.icon]">
                                                                        <div
                                                                            class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                                                                            <div x-html="window.heroicons[dev.icon]"
                                                                                class="w-6 h-6 text-blue-600 dark:text-blue-400 [&>svg]:w-6 [&>svg]:h-6 [&>svg]:stroke-current">
                                                                            </div>
                                                                        </div>
                                                                    </template>

                                                                    <template
                                                                        x-if="!dev.icon || !window.heroicons[dev.icon]">
                                                                        <svg class="w-6 h-6 text-gray-500"
                                                                            viewBox="0 0 24 24" fill="none">
                                                                            <rect x="7" y="2" width="10"
                                                                                height="20" rx="2"
                                                                                stroke="currentColor"
                                                                                stroke-width="1.2" />
                                                                        </svg>
                                                                    </template>
                                                                </div>
                                                                <h4 class="font-semibold text-gray-800 dark:text-gray-100"
                                                                    x-text="dev.name"></h4>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400"
                                                                    x-text="dev.notes"></p>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>

                                            {{-- 🚫 Jika tidak ada device --}}
                                            <template x-if="devices.length === 0">
                                                <div
                                                    class="flex flex-col items-center justify-center py-8 text-center text-gray-500 dark:text-gray-400">
                                                    <svg class="w-10 h-10 mb-3 text-gray-400 dark:text-gray-500"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 12h6m-3-3v6m9-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <p class="text-sm font-medium">Belum ada device</p>
                                                    <p class="text-xs text-gray-400">Silakan tambahkan device di halaman
                                                        admin</p>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- Step 2: Pilih Aplikasi --}}
                                    <template x-if="s === 2">
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <button @click="$el.parentNode.parentNode.__x_original_step = 2; step = 1"
                                                    class="text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 transition inline-flex items-center gap-1">
                                                    <svg class="pos-ico w-4 h-4">
                                                        <use href="#pos-i-arrow-left"></use>
                                                    </svg>
                                                    Kembali</button>
                                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">2.
                                                    Pilih
                                                    Aplikasi</h3>
                                            </div>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                                <template x-for="app in (selectedDevice?.apps || [])"
                                                    :key="app.id">
                                                    <div @click="selectedApp = app; $el.parentNode.parentNode.__x_original_step = 2; step = 3; selectedCategory = null; selectedProduct = null;"
                                                        class="border dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-center"
                                                        :class="selectedApp?.id === app.id ?
                                                            'border-blue-500 ring-2 ring-blue-300 dark:ring-blue-700' :
                                                            'border-gray-300 dark:border-gray-700'">
                                                        <div
                                                            class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2 bg-gray-100 dark:bg-gray-700 overflow-hidden pos-thumb">
                                                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                                                                <rect x="3" y="3" width="18" height="18"
                                                                    rx="3" stroke="currentColor"
                                                                    stroke-width="1.2" />
                                                            </svg>
                                                            <template x-if="app.logo">
                                                                <img :src="(app.logo.startsWith('http') || app.logo.startsWith(
                                                                    '/')) ? app.logo: `/storage/${app.logo}`"
                                                                    class="rounded-full" alt=""
                                                                    onerror="this.remove()">
                                                            </template>
                                                        </div>
                                                        <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-100"
                                                            x-text="app.name"></h4>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400"
                                                            x-text="app.description"></p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Step 3: Pilih Kategori --}}
                                    <template x-if="s === 3">
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <button @click="$el.parentNode.parentNode.__x_original_step = 3; step = 2"
                                                    class="text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 transition inline-flex items-center gap-1">
                                                    <svg class="pos-ico w-4 h-4">
                                                        <use href="#pos-i-arrow-left"></use>
                                                    </svg>
                                                    Kembali</button>
                                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">3.
                                                    Pilih
                                                    Kategori Digital</h3>
                                            </div>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                                <template x-for="cat in categoriesForSelectedApp" :key="cat.id">
                                                    <div @click="selectedCategory = cat; $el.parentNode.parentNode.__x_original_step = 3; step = 4; selectedProduct = null;"
                                                        class="border dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-center"
                                                        :class="selectedCategory?.id === cat.id ?
                                                            'border-blue-500 ring-2 ring-blue-300 dark:ring-blue-700' :
                                                            'border-gray-300 dark:border-gray-700'">
                                                        <div
                                                            class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2 bg-gray-100 dark:bg-gray-700">
                                                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400"
                                                                viewBox="0 0 24 24" fill="none">
                                                                <path d="M4 5h8v8H4zM14 5h6v8h-6zM4 15h8v4H4zM14 15h6v4h-6z"
                                                                    stroke="currentColor" stroke-width="1.2" />
                                                            </svg>
                                                        </div>
                                                        <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-100"
                                                            x-text="cat.name"></h4>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Step 4: Pilih Brand Digital --}}
                                    <template x-if="s === 4">
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <button @click="$el.parentNode.parentNode.__x_original_step = 4; step = 3"
                                                    class="text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 transition inline-flex items-center gap-1">
                                                    <svg class="pos-ico w-4 h-4">
                                                        <use href="#pos-i-arrow-left"></use>
                                                    </svg>
                                                    Kembali</button>
                                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">4.
                                                    Pilih
                                                    Brand Digital</h3>
                                            </div>

                                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                                <template x-for="brand in filteredBrandsForSelectedAppAndCategory"
                                                    :key="brand.id">
                                                    <div @click="selectedBrand = brand; $el.parentNode.parentNode.__x_original_step = 4; step = 5;"
                                                        class="border dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-center"
                                                        :class="selectedBrand?.id === brand.id ?
                                                            'border-blue-500 ring-2 ring-blue-300 dark:ring-blue-700' :
                                                            'border-gray-300 dark:border-gray-700'">
                                                        <div
                                                            class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2 bg-gray-100 dark:bg-gray-700 overflow-hidden pos-thumb">
                                                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                                                                <path d="M7 7h10l1 4H6l1-4zM6 11h12v8H6z"
                                                                    stroke="currentColor" stroke-width="1.2" />
                                                            </svg>
                                                            <template x-if="brand.icon || brand.logo">
                                                                <img :src="((brand.icon || brand.logo).startsWith('/storage') || (
                                                                    brand.icon || brand.logo).startsWith('http')) ? (
                                                                    brand.icon || brand.logo) : ('/storage/' + (brand
                                                                    .icon || brand.logo))"
                                                                    class="rounded-full" alt=""
                                                                    onerror="this.remove()">
                                                            </template>
                                                        </div>
                                                        <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-100 mb-1"
                                                            x-text="brand.name"></h4>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400"
                                                            x-text="brand.description || '-'"></p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Step 4: Pilih Produk --}}
                                    <template x-if="s === 5">
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <button @click="$el.parentNode.parentNode.__x_original_step = 4; step = 3"
                                                    class="text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 transition">
                                                    <span class="inline-flex items-center gap-1"><svg
                                                            class="pos-ico w-4 h-4">
                                                            <use href="#pos-i-arrow-left"></use>
                                                        </svg> Kembali</span>
                                                </button>
                                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                                    5. Pilih Produk Digital
                                                </h3>
                                            </div>

                                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                                                <template x-for="prod in digitalProductsForSelectedCategoryAndApp"
                                                    :key="prod.id">
                                                    <div @click="selectedProduct = prod; payment.total = parseInt(prod.base_price) || 0; $el.parentNode.parentNode.__x_original_step = 5; step = 6;"
                                                        class="border dark:border-gray-700 rounded-xl p-4 bg-white dark:bg-gray-800 cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-center"
                                                        :class="selectedProduct?.id === prod.id ?
                                                            'border-blue-500 ring-2 ring-blue-300 dark:ring-blue-700' :
                                                            'border-gray-300 dark:border-gray-700'">

                                                        <div
                                                            class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2 bg-gray-100 dark:bg-gray-700">
                                                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400"
                                                                viewBox="0 0 24 24" fill="none">
                                                                <path d="M12 2l7 4v6l-7 4-7-4V6z" stroke="currentColor"
                                                                    stroke-width="1.2" />
                                                            </svg>
                                                        </div>

                                                        <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-100 mb-1"
                                                            x-text="prod.name"></h4>

                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                            Rp <span
                                                                x-text="new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(prod.base_price)">
                                                            </span>
                                                        </p>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Step 5: Pembayaran --}}
                                    <template x-if="s === 6">
                                        <div>
                                            <div class="flex items-center justify-between mb-2">
                                                <button @click="$el.parentNode.parentNode.__x_original_step = 5; step = 4"
                                                    class="pos-dig-back text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 transition inline-flex items-center gap-1">
                                                    <svg class="pos-ico w-4 h-4">
                                                        <use href="#pos-i-arrow-left"></use>
                                                    </svg>
                                                    Kembali</button>
                                            </div>
                                            <div class="pos-dig-checkout">
                                                {{-- LEFT SIDE --}}
                                                <div class="pos-dig-rincian">
                                                    <h4
                                                        class="font-semibold mb-4 flex items-center gap-2 text-lg text-gray-700 dark:text-gray-100">
                                                        <svg class="pos-ico w-5 h-5 text-gray-500">
                                                            <use href="#pos-i-list"></use>
                                                        </svg>
                                                        Rincian Transaksi
                                                    </h4>

                                                    <div class="mb-3">
                                                        <h2 class="text-lg font-semibold mb-3">Pelanggan</h2>

                                                        <!-- Jika belum memilih customer, tampilkan input search -->
                                                        <template x-if="!selectedCustomer">
                                                            <div>
                                                                <input type="text" x-model="customerSearch"
                                                                    placeholder="Cari nama pelanggan..."
                                                                    class="w-full border dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg p-2 mb-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">

                                                                <!-- Dropdown pencarian -->
                                                                <div class="relative"
                                                                    x-show="customerSearch.length > 0 && filteredCustomers.length > 0">
                                                                    <div
                                                                        class="max-h-40 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 absolute w-full z-20">

                                                                        <template x-for="cust in filteredCustomers"
                                                                            :key="cust.id">
                                                                            <div class="px-3 py-2 text-sm cursor-pointer hover:bg-blue-100 dark:hover:bg-blue-700/40"
                                                                                @click="
                                selectedCustomer = cust.id;
                                customerSearch = cust.name;
                            "
                                                                                x-text="cust.name">
                                                                            </div>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        <!-- Jika sudah memilih customer -->
                                                        <template x-if="selectedCustomer">
                                                            <div>
                                                                <input type="text" x-model="customerSearch"
                                                                    class="w-full border dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg p-2 text-sm mb-2"
                                                                    readonly>

                                                                <button class="text-xs text-red-400 underline"
                                                                    @click="selectedCustomer = null; customerSearch = ''">
                                                                    Ganti pelanggan
                                                                </button>
                                                            </div>
                                                        </template>

                                                        <!-- Info utang -->
                                                        <template x-if="selectedCustomer">
                                                            <p
                                                                class="mt-1 text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                                                <i class="fa-solid fa-circle-exclamation"></i>
                                                                Transaksi akan dicatat sebagai <strong>utang</strong>.
                                                            </p>
                                                        </template>
                                                    </div>
                                                </div>


                                                {{-- RIGHT SIDE --}}
                                                <div class="pos-dig-pay">
                                                    {{-- 💻 Detail Transaksi --}}
                                                    <div class="pos-dig-items">
                                                        {{-- Device --}}
                                                        <div>
                                                            <div class="pos-dig-row-label">
                                                                <svg class="pos-ico">
                                                                    <use href="#pos-i-phone"></use>
                                                                </svg>
                                                                Device:
                                                            </div>
                                                            <div class="pos-dig-row-value"
                                                                x-text="selectedDevice?.name || '-'"></div>
                                                        </div>

                                                        {{-- Brand --}}
                                                        <div>
                                                            <div class="pos-dig-row-label">
                                                                <svg class="pos-ico">
                                                                    <use href="#pos-i-tag"></use>
                                                                </svg>
                                                                Brand:
                                                            </div>
                                                            <div class="pos-dig-row-value"
                                                                x-text="selectedBrand?.name || '-'"></div>
                                                        </div>

                                                        {{-- Aplikasi --}}
                                                        <div>
                                                            <div class="pos-dig-row-label">
                                                                <svg class="pos-ico">
                                                                    <use href="#pos-i-apps"></use>
                                                                </svg>
                                                                Aplikasi:
                                                            </div>
                                                            <div class="pos-dig-row-value"
                                                                x-text="selectedApp?.name || '-'"></div>
                                                        </div>

                                                        {{-- Kategori --}}
                                                        <div>
                                                            <div class="pos-dig-row-label">
                                                                <svg class="pos-ico">
                                                                    <use href="#pos-i-grid"></use>
                                                                </svg>
                                                                Kategori:
                                                            </div>
                                                            <div class="pos-dig-row-value"
                                                                x-text="selectedCategory?.name || '-'"></div>
                                                        </div>

                                                        {{-- Produk --}}
                                                        <div>
                                                            <div class="pos-dig-row-label">
                                                                <svg class="pos-ico">
                                                                    <use href="#pos-i-box"></use>
                                                                </svg>
                                                                Produk:
                                                            </div>
                                                            <div class="pos-dig-row-value"
                                                                x-text="selectedProduct?.name || '-'"></div>
                                                        </div>
                                                    </div>
                                                    <div class="pos-dig-pay-divider"></div>
                                                    <div>
                                                        <h3
                                                            class="font-bold mb-1 text-lg text-gray-700 dark:text-gray-200">
                                                            TOTAL PEMBAYARAN</h3>
                                                        <div class="mb-5">

                                                            <!-- DISPLAY MODE -->
                                                            <template x-if="!editingTotal">
                                                                <div @click="editingTotal = true"
                                                                    class="text-4xl font-bold text-blue-600 dark:text-blue-400 cursor-pointer select-none hover:opacity-80 transition"
                                                                    x-text="'Rp ' + payment.total.toLocaleString()"
                                                                    title="Klik untuk ubah total secara manual">
                                                                </div>
                                                            </template>

                                                            <!-- EDIT MODE -->
                                                            <template x-if="editingTotal">
                                                                <input type="text" inputmode="numeric"
                                                                    x-ref="totalInput" @input="formatTotalInput($event)"
                                                                    @blur="editingTotal = false"
                                                                    @keydown.enter="$el.blur()"
                                                                    class="w-full text-left text-3xl font-bold border border-blue-400 bg-white dark:bg-gray-700
                                                                    text-blue-600 dark:text-blue-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-blue-500
                                                                    outline-none transition"
                                                                    placeholder="Masukkan total bayar">
                                                            </template>

                                                        </div>
                                                    </div>
                                                    <div class="mt-auto pt-3">
                                                        <button @click="showDigitalReviewModal = true"
                                                            :disabled="payment.paid < payment.total"
                                                            class="pos-dig-bayar w-full py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm transition disabled:bg-gray-400 disabled:cursor-not-allowed dark:disabled:bg-gray-600">
                                                            <svg class="pos-ico w-5 h-5 inline mr-1">
                                                                <use href="#pos-i-receipt"></use>
                                                            </svg>
                                                            BAYAR
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ====================== TAB INPUT MANUAL ====================== -->


            <div x-show="activeTab === 'manual'" class="pos-manual-pane" x-cloak>
                <div class="pos-manual">
                    <div class="pos-manual-card">
                        <h2 class="pos-manual-title">Input Manual / Jasa</h2>

                        <div class="pos-manual-fields">
                            <!-- NAMA -->
                            <div>
                                <label class="pos-manual-label">Nama Item / Jasa</label>
                                <input type="text" x-model="manualName" class="pos-input pos-manual-input"
                                    placeholder="Contoh: Service Ganti LCD">
                            </div>

                            <!-- HARGA -->
                            <div>
                                <label class="pos-manual-label">Jasa</label>

                                <div class="pos-manual-price">
                                    <span class="pos-manual-rp">Rp</span>
                                    <input type="text" x-model="manualPriceDisplay" @input="formatManualPrice"
                                        inputmode="numeric" class="pos-manual-price-input" placeholder="0" />
                                </div>
                            </div>
                        </div>

                        <div class="pos-manual-footer">
                            <div class="pos-manual-total">
                                <span>Total</span>
                                <span x-text="'Rp ' + manualPrice.toLocaleString('id-ID')"></span>
                            </div>
                            <div class="pos-manual-actions">
                                <button @click="loadTodayTransactions()"
                                    class="flex items-center justify-center gap-2 bg-gray-800 hover:bg-gray-700
                   text-gray-200 py-3 rounded-lg font-semibold text-sm transition border border-gray-600">
                                    <i class="fa-solid fa-clock-rotate-left text-base"></i>
                                    <span>Riwayat</span>
                                </button>

                                <button @click="showManualConfirm = true"
                                    class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700
text-white py-3 rounded-lg font-semibold text-sm transition">
                                    <i class="fa-solid fa-cash-register text-base"></i>
                                    <span>Bayar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🧾 Modal Konfirmasi Manual --}}
            <div x-show="showManualConfirm" class="pos-modal-overlay" x-cloak x-transition>
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 md:p-8 w-[95%] max-w-2xl shadow-2xl relative overflow-hidden"
                    x-transition.scale>

                    {{-- Judul --}}
                    <div class="border-b border-gray-300 dark:border-gray-700 pb-4 mb-4">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <i class="fa-solid fa-file-invoice-dollar text-green-500"></i>
                            Konfirmasi Pembayaran Manual
                        </h2>
                    </div>

                    {{-- Detail Manual --}}
                    <div class="space-y-3 text-base">

                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <svg class="pos-ico w-4 h-4">
                                    <use href="#pos-i-wrench"></use>
                                </svg>
                                Nama Jasa:
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white" x-text="manualName || '-'">
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <svg class="pos-ico w-4 h-4">
                                    <use href="#pos-i-cash"></use>
                                </svg>
                                Harga:
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white"
                                x-text="'Rp ' + manualPrice.toLocaleString('id-ID')">
                            </span>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex justify-end gap-3 mt-6 pt-4">
                        <button @click="showManualConfirm = false"
                            class="px-5 py-2.5 rounded-lg bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-100 font-medium hover:bg-gray-400 dark:hover:bg-gray-600 transition">
                            <i class="fa-solid fa-times mr-1"></i> Batalkan
                        </button>

                        <button @click="confirmManualCheckout()"
                            class="px-5 py-2.5 rounded-lg bg-green-600 hover:bg-green-700 text-white font-semibold shadow">
                            <i class="fa-solid fa-check mr-1"></i> Konfirmasi
                        </button>
                    </div>

                </div>
            </div>


            {{-- 🧾 Modal Konfirmasi Transaksi --}}
            <div x-show="showReview" class="pos-modal-overlay" x-cloak x-transition>

                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl p-6 md:p-8 w-[95%] max-w-2xl shadow-2xl relative overflow-hidden">

                    {{-- Judul --}}
                    <div class="border-b border-neutral-200 dark:border-neutral-800 pb-4 mb-6">
                        <h2 class="text-2xl font-semibold text-neutral-900 dark:text-white">
                            Konfirmasi Transaksi
                        </h2>
                    </div>

                    {{-- Daftar Produk --}}
                    <div class="text-sm md:text-base space-y-4 max-h-[45vh] overflow-y-auto pr-2 pb-4">
                        <template x-for="item in cart" :key="item.id + '-' + (item.variant_id ?? 'default')">
                            <div class="pb-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex justify-between items-center">
                                    <div class="font-semibold text-gray-900 dark:text-gray-100" x-text="item.name"></div>

                                    <div class="font-semibold text-gray-800 dark:text-gray-100"
                                        x-text="'Rp ' + (item.price * item.qty).toLocaleString()"></div>
                                </div>

                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1"
                                    x-text="item.qty + ' × Rp ' + item.price.toLocaleString()"></div>
                            </div>
                        </template>
                    </div>

                    {{-- Total --}}
                    <div class="mt-6 pt-4 space-y-4">
                        <div class="flex justify-between items-center text-lg">
                            <span class="text-gray-600 dark:text-gray-300">
                                Total Belanja
                            </span>
                            <span class="font-bold text-2xl text-gray-900 dark:text-white"
                                x-text="'Rp ' + payment.total.toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-300 dark:border-gray-700">
                        <button @click="showReview = false; focusScanner()"
                            class="px-5 py-2.5 rounded-lg border border-neutral-200 dark:border-neutral-700 text-neutral-800 dark:text-neutral-100 font-medium hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            Batalkan
                        </button>

                        <button @click="confirmCheckout()"
                            class="px-5 py-2.5 rounded-lg bg-neutral-900 text-white dark:bg-white dark:text-neutral-900 font-semibold">
                            Bayar
                        </button>
                    </div>

                </div>
            </div>


            {{-- Success overlay (Physical / Digital / Manual) --}}
            <template x-if="showSuccess">
            <div x-ref="posOk"
                class="pos-ok-overlay" role="status" aria-live="polite">
                <div class="pos-ok-card">
                    <div class="pos-ok-mark">
                        <svg viewBox="0 0 80 80" aria-hidden="true">
                            <circle class="pos-ok-ring" cx="40" cy="40" r="32"></circle>
                            <path class="pos-ok-check" d="M26 41.5 L36 51 L55 30"></path>
                        </svg>
                    </div>
                    <p class="pos-ok-title">Transaksi Berhasil</p>
                    <p class="pos-ok-amount"
                        x-text="'Rp ' + Number(lastTransaction.total || 0).toLocaleString('id-ID')"></p>
                </div>
            </div>
            </template>

            <div x-show="showHistory" @click.self="showHistory = false" class="pos-modal-overlay" x-cloak x-transition>
                <div x-transition.scale.duration.300ms
                    class="pos-modal-shell pos-modal-shell-surface w-[95%] max-w-[896px]">

                    {{-- Header --}}
                    <div class="pos-modal-head">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <svg class="pos-ico w-6 h-6">
                                <use href="#pos-i-clock"></use>
                            </svg>
                            Riwayat Transaksi Hari Ini
                        </h2>
                        <button @click="showHistory=false" class="pos-modal-close">
                            <svg class="pos-ico w-5 h-5">
                                <use href="#pos-i-x"></use>
                            </svg>
                        </button>
                    </div>
                    <div class="pos-modal-body">

                        {{-- Ringkasan Penjualan --}}
                        <div class="pos-hist-stats">
                            <div class="pos-hist-stat is-accent">
                                <span class="pos-hist-stat-label">Total Penjualan</span>
                                <span class="pos-hist-stat-value"
                                    x-text="'Rp ' + summary.total_penjualan.toLocaleString()"></span>
                            </div>
                            <div class="pos-hist-stat">
                                <span class="pos-hist-stat-label">Jumlah Transaksi</span>
                                <span class="pos-hist-stat-value" x-text="summary.jumlah_transaksi"></span>
                            </div>
                            <div class="pos-hist-stat">
                                <span class="pos-hist-stat-label">Produk Terjual</span>
                                <span class="pos-hist-stat-value" x-text="summary.total_produk_terjual"></span>
                            </div>
                        </div>

                        {{-- Ringkasan Kategori yang Terjual --}}
                        <template x-if="summary.categories && summary.categories.length > 0">
                            <div class="mb-6">
                                <h3 class="text-gray-700 dark:text-gray-300 font-semibold mb-3">
                                    Kategori Terjual:
                                </h3>
                                <div class="pos-hist-cats">
                                    <template x-for="cat in summary.categories" :key="cat.name">
                                        <div class="pos-hist-cat">
                                            <span
                                                class="text-gray-800 dark:text-gray-100 font-bold text-xs tracking-wide uppercase"
                                                x-text="cat.name">
                                            </span>
                                            <span class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                                                (<span x-text="cat.pcs"></span> pcs)
                                            </span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Daftar Transaksi --}}
                        <template x-if="transactionsToday.length === 0">
                            <p class="text-gray-500 text-center py-8">Belum ada transaksi hari ini.</p>
                        </template>

                        <div class="pos-hist-list" x-show="transactionsToday.length > 0">
                            <template x-for="trx in transactionsToday" :key="trx.id">
                                <div
                                    :class="trx.customer_id ? 'pos-hist-row is-debt' : 'pos-hist-row'">

                                    <div class="pos-hist-head flex justify-between items-start gap-2 mb-1.5">
                                        <div class="pos-hist-head-left flex flex-col min-w-0">
                                            <span class="pos-hist-nota" x-text="trx.nomor_nota"></span>

                                            <template x-if="trx.customer_id">
                                                <span
                                                    class="pos-hist-status inline-flex items-center gap-1 text-xs font-semibold text-red-600 dark:text-red-400">
                                                    <i class="fa-solid fa-clock"></i> Belum Lunas
                                                </span>
                                            </template>

                                            <template x-if="!trx.customer_id">
                                                <span
                                                    class="pos-hist-status inline-flex items-center gap-1 text-xs font-semibold text-green-600 dark:text-green-400">
                                                    <i class="fa-solid fa-circle-check"></i> Lunas
                                                </span>
                                            </template>

                                            <template x-if="trx.customer_id && trx.customer">
                                                <span class="text-xs text-red-700 dark:text-red-300 font-medium mt-0.5">
                                                    <i class="fa-solid fa-user mr-1"></i>
                                                    <span x-text="trx.customer.name"></span>
                                                </span>
                                            </template>
                                        </div>

                                        <div class="pos-hist-head-right relative flex items-start justify-end shrink-0">
                                            <span x-text="trx.created_at + ' WITA'"
                                                class="pos-hist-time text-sm text-gray-500 dark:text-gray-400"></span>

                                            <!-- ⋮ Tombol Dropdown -->
                                            <div x-data="{ open: false }" class="relative">
                                                <button @click="open = !open"
                                                    class="ml-2 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none"
                                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M12 5h.01M12 12h.01M12 19h.01" />
                                                    </svg>
                                                </button>

                                                <!-- Dropdown -->
                                                <div x-show="open" @click.away="open = false"
                                                    class="absolute right-0 mt-2 w-40 backdrop-blur-md bg-white/90 dark:bg-gray-800/90 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-50"
                                                    x-transition>
                                                    <button @click="confirmDelete(trx); open=false"
                                                        class="w-full flex items-center gap-2 px-4 py-3 text-sm font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/40 transition">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-9 0h10" />
                                                        </svg>
                                                        <span>Hapus Transaksi</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <template x-for="item in trx.details.slice(0, 3)" :key="item.id">
                                        <div class="pos-hist-item flex justify-between gap-3 text-sm text-gray-600 dark:text-gray-300">
                                            <span class="pos-hist-item-name min-w-0">
                                                <span
                                                    x-text="item.item_type === 'service'
                                                ? item.manual_name
                                                : item.product">
                                                </span>
                                                × <span x-text="item.qty"></span> pcs
                                            </span>
                                            <span class="pos-hist-item-amt shrink-0" x-text="'Rp ' + item.subtotal.toLocaleString()"></span>
                                        </div>
                                    </template>

                                    {{-- Jika lebih dari 3 produk, tampilkan indikator tambahan --}}
                                    <template x-if="trx.details.length > 3">
                                        <div class="text-xs text-gray-400 italic mt-1">
                                            + <span x-text="trx.details.length - 3"></span> produk lainnya...
                                        </div>
                                    </template>

                                    {{-- Total transaksi --}}
                                    <div class="pos-hist-total text-right font-semibold text-blue-600 dark:text-blue-400 mt-1.5">
                                        <span x-text="'Rp ' + trx.subtotal.toLocaleString()"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Konfirmasi Hapus -->
            <div x-show="showDeleteConfirm" x-cloak x-transition.opacity.duration.300ms class="pos-modal-overlay">
                <div x-show="showDeleteConfirm" x-transition.scale.duration.300ms
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 w-[90%] max-w-sm text-center">

                    <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-100">
                        Hapus Transaksi?
                    </h3>

                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-5">
                        Nomor nota:
                        <span class="font-semibold text-red-600 dark:text-red-400"
                            x-text="transactionToDelete?.nomor_nota || '-'"></span>
                    </p>

                    <div class="flex justify-center gap-3">
                        <button @click="showDeleteConfirm = false"
                            class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100
                           hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                            Batal
                        </button>

                        <button @click="deleteTransaction(transactionToDelete)"
                            class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 active:scale-[0.97]
                           transition shadow-md">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>

            {{-- Modal Detail Transaksi --}}
            <div x-show="showDetailModal" class="pos-modal-overlay" x-cloak x-transition>
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl p-6 w-[90%] md:w-[600px] max-h-[85vh] overflow-y-auto shadow-2xl">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100"
                            x-text="'Detail ' + (selectedTransaction?.nomor_nota ?? '')"></h2>
                        <button @click="showDetailModal=false"
                            class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                            <svg class="pos-ico w-5 h-5">
                                <use href="#pos-i-x"></use>
                            </svg>
                        </button>
                    </div>

                    <div class="divide-y divide-gray-300 dark:divide-gray-700">
                        <template x-for="item in selectedTransaction?.details ?? []" :key="item.product">
                            <div class="py-2 flex justify-between text-gray-700 dark:text-gray-200 text-sm">
                                <span>
                                    <span class="font-medium" x-text="item.product"></span>
                                    <span class="text-xs text-gray-500 ml-1">(x<span x-text="item.qty"></span>)</span>
                                </span>
                                <span x-text="'Rp ' + item.subtotal.toLocaleString()"></span>
                            </div>
                        </template>
                    </div>

                    <hr class="my-4">

                    <div class="text-right space-y-1 text-gray-700 dark:text-gray-300">
                        <div>Total: <span class="font-semibold"
                                x-text="'Rp ' + selectedTransaction?.subtotal.toLocaleString()"></span></div>
                        <div>Dibayar: <span class="font-semibold"
                                x-text="'Rp ' + selectedTransaction?.dibayar.toLocaleString()"></span></div>
                        <div>Kembalian: <span class="font-semibold text-green-600 dark:text-green-400"
                                x-text="'Rp ' + selectedTransaction?.kembalian.toLocaleString()"></span></div>
                    </div>
                </div>
            </div>

            <!-- 🌟 MODAL REVIEW TRANSAKSI DIGITAL -->
            <div x-show="showDigitalReviewModal" x-cloak x-transition class="pos-modal-overlay">

                <div @click.away="showDigitalReviewModal = false"
                    class="bg-white dark:bg-gray-800 rounded-2xl p-6 md:p-8 w-[95%] max-w-2xl shadow-2xl relative overflow-hidden border border-gray-300 dark:border-gray-700">

                    <!-- Judul -->
                    <div class="border-b border-gray-300 dark:border-gray-700 pb-4 mb-4">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <i class="fa-solid fa-mobile-screen-button text-blue-500"></i>
                            Konfirmasi Pembayaran Digital
                        </h2>
                    </div>

                    <!-- Informasi Produk Digital -->
                    <div class="text-sm md:text-base space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <i class="fa-solid fa-laptop text-gray-400"></i> Device:
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white"
                                x-text="selectedDevice?.name ?? '-'"></span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <i class="fa-solid fa-circle-nodes text-gray-400"></i> Aplikasi:
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white"
                                x-text="selectedApp?.name ?? '-'"></span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <i class="fa-solid fa-layer-group text-gray-400"></i> Kategori:
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white"
                                x-text="selectedCategory?.name ?? '-'"></span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <i class="fa-solid fa-tags text-gray-400"></i> Brand:
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white"
                                x-text="selectedBrand?.name ?? '-'"></span>
                        </div>

                        <div class="flex justify-between items-center pb-3">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <i class="fa-solid fa-box text-gray-400"></i> Produk:
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white"
                                x-text="selectedProduct?.name ?? '-'"></span>
                        </div>
                    </div>

                    <!-- Rangkuman Pembayaran -->
                    <div class="mt-5 pt-4 border-t border-gray-300 dark:border-gray-700 space-y-3 text-base">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300 flex items-center gap-2">
                                <svg class="pos-ico w-4 h-4">
                                    <use href="#pos-i-cash"></use>
                                </svg> Total:
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white"
                                x-text="'Rp ' + payment.total.toLocaleString()"></span>
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="flex justify-end gap-3 mt-6 pt-4">
                        <button @click="showDigitalReviewModal = false"
                            class="px-5 py-2.5 rounded-lg bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-100 font-medium hover:bg-gray-400 dark:hover:bg-gray-600 transition">
                            <i class="fa-solid fa-times mr-1"></i> Batalkan
                        </button>
                        <button @click="showDigitalReviewModal = false; confirmDigitalTransaction()"
                            class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow">
                            <i class="fa-solid fa-check mr-1"></i> Konfirmasi Pembayaran
                        </button>
                    </div>

                </div>
            </div>

            {{-- Modal Riwayat Transaksi Digital --}}

            <div x-show="showHistoryDigital" @click.self="showHistoryDigital = false"
                @keydown.escape.window="showHistoryDigital = false" class="pos-modal-overlay" x-cloak x-transition>
                <div class="pos-modal-shell pos-modal-shell-surface pos-modal-dig-hist w-[95%] max-w-[960px]">

                    {{-- Header --}}
                    <div class="pos-modal-head">
                        <h2 class="text-2xl font-bold flex items-center gap-2 text-gray-800 dark:text-gray-100">
                            <svg class="pos-ico w-6 h-6">
                                <use href="#pos-i-bolt"></use>
                            </svg>
                            Riwayat Transaksi Produk Digital
                        </h2>
                        <button @click="showHistoryDigital=false" class="pos-modal-close">
                            <svg class="pos-ico w-5 h-5">
                                <use href="#pos-i-x"></use>
                            </svg>
                        </button>
                    </div>
                    <div class="pos-modal-body">

                        {{-- Step 1: Pilih Aplikasi --}}
                        <template x-if="!selectedAppFilter">
                            <div x-transition>
                                <h3 class="text-lg font-semibold mb-3 text-gray-700 dark:text-gray-300">Pilih Aplikasi
                                </h3>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                    <template x-for="app in apps" :key="app.id">
                                        <div @click="selectedAppFilter = app.id; loadDigitalTransactions();"
                                            class="pos-dig-hist-card flex flex-col items-center justify-center p-4 cursor-pointer
                        hover:bg-blue-50 dark:hover:bg-blue-900/30
                        transition-all duration-200">

                                            {{-- Logo --}}
                                            <div
                                                class="w-16 h-16 rounded-full flex items-center justify-center bg-white dark:bg-gray-800 overflow-hidden mb-2 border border-gray-300 dark:border-gray-700 pos-thumb">
                                                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none">
                                                    <rect x="3" y="3" width="18" height="18" rx="3"
                                                        stroke="currentColor" stroke-width="1.5" />
                                                </svg>
                                                <template x-if="app.logo">
                                                    <img :src="(app.logo.startsWith('http') || app.logo.startsWith('/')) ? app
                                                        .logo: `/storage/${app.logo}`"
                                                        alt="" onerror="this.remove()">
                                                </template>
                                            </div>

                                            {{-- Nama App --}}
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 text-center"
                                                x-text="app.name"></p>

                                            {{-- Total & Jumlah Transaksi --}}
                                            <template x-if="digitalAppSummary && digitalAppSummary[app.id]">
                                                <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-0.5">
                                                    (
                                                    <span class="font-medium text-blue-600 dark:text-blue-400">
                                                        Rp <span
                                                            x-text="digitalAppSummary[app.id].total.toLocaleString()"></span>
                                                    </span>
                                                    •
                                                    <span x-text="digitalAppSummary[app.id].count"></span> trx
                                                    )
                                                </p>
                                            </template>
                                            <template x-if="!digitalAppSummary || !digitalAppSummary[app.id]">
                                                <p class="text-xs text-gray-400 italic">(Belum ada transaksi)</p>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        {{-- Step 2: Riwayat Transaksi Aplikasi Terpilih --}}
                        <template x-if="selectedAppFilter">
                            <div x-transition>
                                {{-- Header + Tombol Kembali --}}
                                <div class="flex items-center justify-between mb-4">
                                    <button @click="selectedAppFilter=''; digitalTransactions=[]; digitalSummary=null"
                                        class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600 transition">
                                        <svg class="pos-ico w-4 h-4">
                                            <use href="#pos-i-arrow-left"></use>
                                        </svg>
                                        Kembali
                                    </button>
                                    <h3
                                        class="font-semibold text-lg text-gray-800 dark:text-gray-100 flex items-center gap-2">
                                        <svg class="pos-ico w-5 h-5">
                                            <use href="#pos-i-clock"></use>
                                        </svg>
                                        Riwayat Transaksi
                                    </h3>
                                </div>

                                {{-- Ringkasan Total Hari Ini --}}
                                <template x-if="digitalAppSummary">
                                    <div
                                        class="bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-5 text-center shadow-sm">

                                        <p class="text-sm text-blue-600 dark:text-blue-300 font-medium mb-1">
                                            Total <span class="font-semibold"
                                                x-text="apps.find(a => a.id == selectedAppFilter)?.name || 'Aplikasi'"></span>
                                            Hari
                                            Ini
                                        </p>

                                        <h2 class="text-3xl font-bold text-blue-700 dark:text-blue-400">
                                            Rp <span x-text="digitalSummary.toLocaleString()"></span>
                                        </h2>
                                    </div>
                                </template>

                                {{-- Loading --}}
                                <template x-if="loadingDigitalTransactions">
                                    <p class="text-center py-8 text-gray-500 dark:text-gray-400 animate-pulse">Memuat
                                        data...
                                    </p>
                                </template>

                                {{-- Tidak Ada Data --}}
                                <template x-if="!loadingDigitalTransactions && digitalTransactions.length === 0">
                                    <p class="text-gray-500 text-center py-8">Belum ada transaksi hari ini untuk aplikasi
                                        ini.
                                    </p>
                                </template>

                                {{-- Daftar Transaksi --}}
                                <div class="pos-hist-list" x-show="digitalTransactions.length > 0">
                                    <template x-for="trx in digitalTransactions" :key="trx.id">
                                        <div class="pos-hist-row">

                                            {{-- Header --}}
                                            <div class="flex justify-between items-center mb-1">
                                                <div>
                                                    <h3 class="font-semibold text-gray-800 dark:text-gray-100"
                                                        x-text="trx.nomor_nota"></h3>
                                                </div>

                                                <div class="flex items-center gap-3">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400"
                                                        x-text="trx.created_at + ' WITA'"></span>

                                                    {{-- Menu Tiga Titik --}}
                                                    <div x-data="{ open: false }" class="relative">
                                                        <button @click="open = !open"
                                                            class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="w-5 h-5 text-gray-500 dark:text-gray-300"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M12 6h.01M12 12h.01M12 18h.01" />
                                                            </svg>
                                                        </button>

                                                        <div x-show="open" @click.away="open = false" x-transition
                                                            class="absolute right-0 mt-2 w-32 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg sh76adow-lg overflow-hidden z-50">
                                                            <button @click="confirmDeleteDigital(trx); open=false"
                                                                class="w-full flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 dark:text-red-400
                               hover:bg-red-50 dark:hover:bg-red-900/40 transition">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                                                    fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                                Hapus
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Daftar Detail Produk --}}
                                            <template x-if="trx.details && trx.details.length > 0">
                                                <div class="text-sm text-gray-600 dark:text-gray-300 mt-1 space-y-0.5">
                                                    <template x-for="item in trx.details.slice(0, 3)"
                                                        :key="item.product">
                                                        <div class="flex justify-between">
                                                            <span>
                                                                <span x-text="item.product"></span>
                                                                × <span x-text="item.qty"></span> pcs
                                                            </span>
                                                            <span x-text="'Rp ' + item.subtotal.toLocaleString()"></span>
                                                        </div>
                                                    </template>
                                                    <template x-if="trx.details.length > 3">
                                                        <p class="text-xs text-gray-400 italic">+ <span
                                                                x-text="trx.details.length - 3"></span> produk lainnya...
                                                        </p>
                                                    </template>
                                                </div>
                                            </template>

                                            {{-- Total --}}
                                            <div class="text-right font-bold text-blue-600 dark:text-blue-400 mt-2">
                                                Rp <span x-text="trx.subtotal.toLocaleString()"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Modal Konfirmasi Hapus Digital -->
            <div x-show="showDeleteConfirmDigital" x-cloak x-transition.opacity.duration.300ms class="pos-modal-overlay">
                <div x-show="showDeleteConfirmDigital" x-transition.scale.duration.300ms
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-[90%] max-w-sm text-center">
                    <h3 class="text-lg font-semibold mb-2 text-gray-800 dark:text-gray-100">
                        Hapus Transaksi Digital?
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-5">
                        Nomor nota:
                        <span class="font-semibold text-red-600 dark:text-red-400"
                            x-text="digitalToDelete?.nomor_nota || '-'"></span>
                    </p>
                    <div class="flex justify-center gap-3">
                        <button @click="showDeleteConfirmDigital = false"
                            class="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100
                           hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                            Batal
                        </button>
                        <button @click="deleteDigitalTransaction(digitalToDelete)"
                            class="px-4 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700
                           active:scale-[0.97] transition shadow-md">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>

            <nav class="pos-bottom-nav" aria-label="Menu POS">
                <button type="button" @click="activeTab = 'physical'"
                    :class="activeTab === 'physical' ? 'is-on' : ''">
                    <svg class="pos-ico">
                        <use href="#pos-i-bag"></use>
                    </svg>
                    <span>Fisik</span>
                </button>
                <button type="button" @click="activeTab = 'digital'"
                    :class="activeTab === 'digital' ? 'is-on' : ''">
                    <svg class="pos-ico">
                        <use href="#pos-i-bolt"></use>
                    </svg>
                    <span>Digital</span>
                </button>
                <button type="button" @click="activeTab = 'manual'"
                    :class="activeTab === 'manual' ? 'is-on' : ''">
                    <svg class="pos-ico">
                        <use href="#pos-i-pencil"></use>
                    </svg>
                    <span>Manual</span>
                </button>
                <button type="button" class="is-book" @click="handleCloseBook()">
                    <svg class="pos-ico">
                        <use href="#pos-i-book"></use>
                    </svg>
                    <span>Tutup</span>
                </button>
            </nav>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function posApp() {
            return {
                // ======== STATE GLOBAL ========
                activeTab: 'physical', // 'physical' | 'digital'
                transitioning: false,

                // ======== WIZARD PRODUK DIGITAL ========
                step: 1,
                devices: [],
                apps: [],
                digitalCategories: [],
                digitalBrands: [],
                digitalProducts: [],
                digitalRules: [],
                selectedDevice: null,
                selectedApp: null,
                selectedCategory: null,
                selectedBrand: null,
                selectedProduct: null,
                payment: {
                    customer: '',
                    total: 0,
                    paid: 0,
                    editingPaid: false
                },
                customers: @json($customers ?? []),

                // ======== PRODUK FISIK ========
                _rawProducts: @json($products),
                categories: @json($categories),
                products: [],
                cart: [],
                selectedCategoryPhysical: null,
                showToast: false,
                toastMsg: '',
                showReview: false,
                showSuccess: false,
                showHistory: false,
                showDetailModal: false,
                lastTransaction: {
                    total: 0,
                    dibayar: 0,
                    kembalian: 0
                },
                selectedCustomer: '',
                transactionsToday: [],
                selectedTransaction: null,
                summary: {
                    total_penjualan: 0,
                    jumlah_transaksi: 0,
                    total_produk_terjual: 0
                },
                showDigitalReviewModal: false,
                editingTotal: false,
                showHistoryDigital: false,
                selectedAppFilter: '',
                digitalTransactions: [],
                loadingDigitalTransactions: false,
                digitalAppSummary: {},
                showDeleteConfirm: false,
                transactionToDelete: null,
                showDeleteConfirmDigital: false,
                digitalToDelete: null,
                digitalSummary: null,
                showOptionModal: false,
                selectedProduct: null,
                selectedProductOptions: [],
                showCloseBookModal: false,
                closeBookData: null,
                lebih: 0,
                copied: false,
                searchQuery: '',
                customerSearch: "",
                isScanning: false,
                manualName: '',
                manualPrice: 0,
                manualPriceDisplay: '',
                manualPaid: 0,
                showManualConfirm: false,
                showConfirmClose: false,
                grandTotalAkhir: 0,
                activeCategoryId: null,
                isCategoryLoading: false,

                // ======== INIT UTAMA ========
                async init() {
                    this.bindPosSegPill();

                    // 🔁 Load keranjang
                    this.loadCart();

                    // === AUTO BARCODE SCANNER ===
                    let buffer = "";
                    let lastTime = Date.now();

                    document.addEventListener("keydown", (e) => {
                        const target = e.target;
                        const tag = (target && target.tagName ? target.tagName : '').toLowerCase();
                        const typingElsewhere = target &&
                            target.id !== 'barcodeInput' &&
                            (tag === 'input' || tag === 'textarea' || tag === 'select' || target
                                .isContentEditable);

                        if (typingElsewhere) return;

                        const now = Date.now();
                        const diff = now - lastTime;
                        if (diff > 50) buffer = "";
                        if (e.key.length === 1) buffer += e.key;

                        if (e.key === "Enter" && buffer.length > 3) {
                            if (this.isScanning) return; // 🔥 cegah double scan

                            this.isScanning = true;
                            setTimeout(() => this.isScanning = false, 80); // cooldown 80ms

                            e.preventDefault();
                            const code = buffer.trim();
                            buffer = "";

                            this.handleBarcodeInput({
                                target: {
                                    value: code
                                }
                            });
                            this.focusScanner();
                        }
                        lastTime = now;
                    });
                    // === END AUTO BARCODE ===

                    // 🧱 Inisialisasi Produk Pertama
                    this.products = @json($products ?? []);
                    this.hasMore = true;
                    this.loadingMore = false;
                    console.log("✅ First 15 products loaded:", this.products.length);

                    // 🔁 Data digital
                    await this.loadDigitalData();

                    // 📜 Infinite Scroll — hanya di grid produk, bukan scroll halaman
                    const scrollContainer = document.querySelector('#productScrollArea');
                    if (scrollContainer) {
                        scrollContainer.addEventListener('scroll', async () => {
                            if (this.activeTab !== 'physical') return;
                            if (this.loadingMore || !this.hasMore) return;
                            if (scrollContainer.scrollHeight <= scrollContainer.clientHeight + 8) return;

                            const nearBottom = scrollContainer.scrollTop + scrollContainer.clientHeight >=
                                scrollContainer.scrollHeight - 80;
                            if (!nearBottom) return;

                            this.loadingMore = true;
                            await this.loadProducts();
                            this.loadingMore = false;
                        }, {
                            passive: true
                        });
                    }

                    // WATCHER
                    this.$watch('showHistoryDigital', value => {
                        if (value) this.loadAppSummaries();
                    });

                    // 🎯 Watcher Search Produk
                    this.$watch('searchQuery', Alpine.debounce(async (q) => {
                        q = q.trim();

                        if (q === '') {
                            // Search clear → balik ke kategori yang aktif
                            this.products = [];
                            this.hasMore = true;
                            await this.loadProducts();
                            return;
                        }

                        // Search override kategori
                        this.activeCategoryId = null;

                        const res = await fetch(`/pos/search-products?q=${encodeURIComponent(q)}`);
                        const data = await res.json();

                        if (data.success && Array.isArray(data.data)) {
                            this.products = data.data;
                            this.hasMore = false;
                        } else {
                            this.products = [];
                        }

                    }, 400));


                    this.$watch('activeTab', (tab) => {
                        this._posSegLock = true;
                        this.$nextTick(() => {
                            requestAnimationFrame(() => {
                                requestAnimationFrame(() => {
                                    this.syncPosSegPill();
                                    this._posSegLock = false;
                                });
                            });
                        });
                        if (tab === 'physical') this.focusScanner();
                    });

                    this.$watch('showOptionModal', (open) => {
                        if (!open) this.focusScanner();
                    });

                    this.$watch('showReview', (open) => {
                        if (!open) this.focusScanner();
                    });

                    this.$watch('showHistory', (open) => {
                        if (!open) this.focusScanner();
                    });

                    this.$watch('editingTotal', (value) => {
                        if (value === true) {
                            this.$nextTick(() => {
                                if (this.$refs.totalInput) {
                                    this.$refs.totalInput.value =
                                        new Intl.NumberFormat("id-ID").format(this.payment.total);

                                    this.$refs.totalInput.focus();
                                    this.$refs.totalInput.select();
                                }
                            });
                        }
                    });

                    this.focusScanner();

                    // sync paid dengan total() ketika cart berubah, kecuali saat sedang edit manual
                    this.$watch(() => this.total(), (newTotal) => {
                        // HANYA sync otomatis kalau sedang di tab PHYSICAL
                        if (this.activeTab === 'physical' && !this.editingTotal) {
                            this.payment.total = newTotal;
                        }

                        // Dibayar tetap ikut total kecuali sedang edit
                        if (!this.payment.editingPaid) {
                            this.payment.paid = this.payment.total;
                        }
                    });

                    this.$watch('payment.total', (newTotal) => {
                        if (!this.payment.editingPaid) {
                            this.payment.paid = newTotal;
                        }
                    });

                    this.$watch('showSuccess', (open) => {
                        if (!open) return;
                        this.$nextTick(() => {
                            const el = this.$refs.posOk;
                            if (el) el.classList.remove('is-pos-ok-out');
                        });
                        if (this._okHide) clearTimeout(this._okHide);
                        if (this._okOff) clearTimeout(this._okOff);
                        this._okHide = setTimeout(() => {
                            const el = this.$refs.posOk;
                            if (el) el.classList.add('is-pos-ok-out');
                            this._okOff = setTimeout(() => {
                                this.showSuccess = false;
                                this.focusScanner();
                            }, 220);
                        }, 1680);
                    });

                },

                focusScanner() {
                    if (this.activeTab !== 'physical') return;
                    if (this.showOptionModal || this.showReview || this.showSuccess || this.showHistory || this
                        .showCloseBookModal) return;

                    this.$nextTick(() => {
                        const el = document.getElementById('barcodeInput');
                        if (!el) return;

                        const active = document.activeElement;
                        if (active && active !== el && (
                                active.id === 'searchInput' ||
                                active.id === 'totalInput' ||
                                active.getAttribute?.('data-pos-keep-focus') ||
                                (this.$refs.totalInput && active === this.$refs.totalInput)
                            )) {
                            return;
                        }

                        el.focus();
                    });
                },

                productIconKey(product) {
                    const n = String(product?.category_name || '').toLowerCase();
                    if (/gores|tempered|screen/.test(n)) return 'shield';
                    if (/kabel|cable|usb/.test(n)) return 'cable';
                    if (/charger|casan|adaptor|batok/.test(n)) return 'plug';
                    if (/baterai|battery|batre/.test(n)) return 'battery';
                    if (/casing|case|softcase|hardcase/.test(n)) return 'phone';
                    if (/card.?reader|nfc/.test(n)) return 'card';
                    if (/headset|earphone|headphone/.test(n)) return 'headphones';
                    return 'box';
                },

                cartItemIconKey(item) {
                    const product = this.products.find(p => p.id === item.id);
                    return this.productIconKey(product || {});
                },

                removeCartItem(index) {
                    this.cart.splice(index, 1);
                    this.saveCart();
                    this.updatePaymentTotals();
                },

                formatPaidInput(event) {
                    // ambil hanya angka
                    let raw = String(event.target.value).replace(/\D/g, '');
                    if (raw === '') raw = '0';

                    // simpan nilai numerik ke model (dipakai untuk perhitungan)
                    this.payment.paid = Number(raw);

                    // tampilkan format rupiah (tanpa prefix "Rp ")
                    event.target.value = new Intl.NumberFormat('id-ID').format(raw);
                },

                async loadProducts() {
                    try {
                        const q = this.searchQuery.trim();

                        // 🔍 SEARCH MODE (prioritas tertinggi)
                        if (q !== '') {
                            console.log("🔍 Mencari produk:", q);

                            // search override kategori
                            this.activeCategoryId = null;

                            const res = await fetch(`/pos/search-products?q=${encodeURIComponent(q)}`);
                            const data = await res.json();

                            if (data.success && Array.isArray(data.data)) {
                                this.products = data.data;
                                this.hasMore = false; // disable infinite scroll saat search
                                console.log(`✅ Ditemukan ${data.data.length} produk`);
                            } else {
                                this.products = [];
                                this.hasMore = false;
                                console.log("⚠️ Tidak ada hasil ditemukan");
                            }

                            return;
                        }

                        // 📂 KATEGORI MODE
                        const offset = this.products.length;
                        const limit = 15;

                        console.log("📦 Fetching products offset:", offset);

                        let url = `/pos/load-more?offset=${offset}&limit=${limit}`;

                        // kalau kategori sedang dipilih → load by category
                        if (this.activeCategoryId) {
                            url += `&category_id=${this.activeCategoryId}`;
                        }

                        const res = await fetch(url);
                        const data = await res.json();

                        if (data.success && Array.isArray(data.data) && data.data.length > 0) {

                            const newItems = data.data.filter(
                                newP => !this.products.some(p => p.id === newP.id)
                            );

                            this.products.push(...newItems);
                            console.log(`✅ Loaded ${newItems.length} new items`);

                        } else {
                            console.log("⚠️ No more products to load.");
                            this.hasMore = false;
                        }

                    } catch (err) {
                        console.error("❌ Gagal memuat produk:", err);
                        this.hasMore = false;
                    }
                },

                // ======== MUAT DATA DIGITAL ========
                async loadDigitalData() {
                    try {
                        const res = await fetch("{{ route('pos.digital.data') }}");
                        const data = await res.json();
                        if (data.success) {
                            this.devices = data.devices;
                            this.apps = data.apps;
                            this.digitalCategories = data.categories;
                            this.digitalBrands = data.brands || [];
                            this.digitalProducts = data.products;
                            this.digitalRules = data.rules;
                            console.log('%c✅ Digital data loaded successfully', 'color:#10b981', data);
                        } else {
                            console.error('⚠️ Gagal memuat data digital:', data);
                        }
                    } catch (err) {
                        console.error('❌ Kesalahan jaringan saat memuat data digital:', err);
                    }
                },

                formatTotalInput(event) {
                    let raw = event.target.value.replace(/\D/g, ""); // hanya angka

                    if (raw === "") raw = "0";

                    // simpan angka asli ke model (payment.total)
                    this.payment.total = Number(raw);

                    // format tampilan input
                    event.target.value = new Intl.NumberFormat("id-ID").format(raw);
                },

                // ======== GETTER (Dynamic Filtering) ========
                get categoriesForSelectedApp() {
                    if (!this.selectedApp) return [];
                    const catIds = [...new Set(this.digitalProducts
                        .filter(p => Number(p.app_id) === Number(this.selectedApp.id))
                        .map(p => Number(p.digital_category_id)))];
                    return this.digitalCategories.filter(c => catIds.includes(Number(c.id)));
                },

                updatePaymentTotals() {
                    // 🔥 Hanya produk fisik yang boleh sync otomatis
                    if (this.activeTab === 'physical' && !this.editingTotal) {
                        this.payment.total = this.total();
                    }

                    // Dibayar ikut total selama tidak edit dibayar
                    if (!this.payment.editingPaid) {
                        this.payment.paid = this.payment.total;
                    }
                },

                // 🧩 Filter brand berdasarkan kategori & app dari relasi pivot
                get filteredBrandsForSelectedAppAndCategory() {
                    if (!this.selectedApp || !this.selectedCategory) return [];

                    // Ambil produk yang sesuai app & kategori
                    const matchedProducts = this.digitalProducts.filter(p =>
                        Number(p.app_id) === Number(this.selectedApp.id) &&
                        Number(p.digital_category_id) === Number(this.selectedCategory.id)
                    );

                    // Ambil semua brand unik dari produk-produk tersebut
                    const brandMap = new Map();
                    for (const prod of matchedProducts) {
                        if (Array.isArray(prod.digital_brands)) {
                            for (const b of prod.digital_brands) {
                                brandMap.set(b.id, b);
                            }
                        }
                    }
                    return Array.from(brandMap.values());
                },

                // 🧩 Filter Produk berdasarkan app + kategori + brand
                get digitalProductsForSelectedCategoryAndApp() {
                    if (!this.selectedApp || !this.selectedCategory || !this.selectedBrand) return [];

                    return this.digitalProducts.filter(p =>
                        Number(p.app_id) === Number(this.selectedApp.id) &&
                        Number(p.digital_category_id) === Number(this.selectedCategory.id) &&
                        Array.isArray(p.digital_brands) &&
                        p.digital_brands.some(b => Number(b.id) === Number(this.selectedBrand.id))
                    );
                },

                // ======== PRODUK FISIK: KERANJANG ========
                addToCart(p) {
                    const existing = this.cart.find(i => i.id === p.id);
                    const qtyInCart = existing ? existing.qty : 0;

                    if (qtyInCart >= p.stock) {
                        this.toastMsg = `Stok ${p.name} tinggal ${p.stock}`;
                        this.showToast = true;
                        setTimeout(() => this.showToast = false, 2500);
                        return;
                    }

                    if (existing) existing.qty++;
                    else this.cart.push({
                        id: p.id,
                        name: p.name,
                        price: Number(p.price ?? 0),
                        qty: 1
                    });

                    this.saveCart();
                    this.updatePaymentTotals(); // 🔥 WAJIB!!
                },

                formatManualPrice(e) {
                    // Ambil hanya angka
                    let raw = e.target.value.replace(/\D/g, '');
                    if (raw === '') raw = '0';

                    // Simpan nilai numerik asli (untuk checkout)
                    this.manualPrice = parseInt(raw);

                    // Tampilkan format Rp modern
                    this.manualPriceDisplay = new Intl.NumberFormat('id-ID').format(this.manualPrice);
                    this.manualPaid = this.manualPrice;
                },

                manualAddPayment(n) {
                    this.manualPaid += n;
                },

                manualPayExact() {
                    this.manualPaid = this.manualPrice; // harga jasa
                },

                manualHandleKey(b) {
                    if (b === '⌫') {
                        this.manualPaid = Math.floor(this.manualPaid / 10);
                    } else {
                        this.manualPaid = Number(String(this.manualPaid) + b);
                    }
                },

                manualChange() {
                    return this.manualPaid - this.manualPrice;
                },


                displayStock(product) {
                    const item = this.cart.find(i => i.id === product.id);
                    const qty = item ? item.qty : 0;
                    return product.stock - qty;
                },

                increaseQty(i) {
                    const item = this.cart[i];
                    const master = this.products.find(x => x.id === item.id);
                    if (!master) return;

                    if (master.stock < item.qty + 1) {
                        this.toastMsg = `Stok ${master.name} tidak cukup (tersisa ${master.stock}).`;
                        this.showToast = true;
                        setTimeout(() => this.showToast = false, 2500);
                        return;
                    }

                    this.cart[i].qty++;
                    this.saveCart();
                    this.updatePaymentTotals();
                },
                decreaseQty(i) {
                    const item = this.cart[i];

                    if (item.qty > 1) {
                        this.cart[i].qty--;
                    } else {
                        this.cart.splice(i, 1);
                    }

                    this.saveCart();
                    this.updatePaymentTotals();
                },
                total() {
                    return this.cart.reduce((s, i) => s + i.price * i.qty, 0);
                },
                loadCart() {
                    try {
                        this.cart = JSON.parse(localStorage.getItem('cart') || '[]');
                    } catch (e) {
                        this.cart = [];
                    }
                    this.updatePaymentTotals();
                },
                saveCart() {
                    try {
                        localStorage.setItem('cart', JSON.stringify(this.cart));
                    } catch (e) {}
                },
                clearCart() {
                    this.cart = [];
                    this.saveCart();
                },
                // ======== KALKULATOR FISIK ========
                addPayment(n) {
                    this.payment.paid += n;
                },
                payExact() {
                    this.payment.paid = this.payment.total;
                },
                handleKey(b) {
                    b === '⌫' ?
                        this.payment.paid = Math.floor(this.payment.paid / 10) :
                        this.payment.paid = Number(String(this.payment.paid) + b);
                },
                change() {
                    return this.payment.paid - this.payment.total;
                },

                // ======== FILTER PRODUK FISIK ========
                async switchCategory(cat) {
                    this.selectedCategoryPhysical = cat;
                    this.activeCategoryId = cat ? cat.id : null;

                    this.products = [];
                    this.hasMore = true;
                    this.isCategoryLoading = true;

                    // hentikan dulu infinite scroll
                    this.transitioning = true;

                    // delay kecil biar animasi terasa smooth
                    await this.$nextTick();

                    // load ulang dari offset 0
                    await this.loadProducts();

                    this.transitioning = false;
                    this.isCategoryLoading = false;
                },
                get filteredProducts() {
                    if (!this.selectedCategoryPhysical) return this.products;
                    return this.products.filter(p => p.category_id === this.selectedCategoryPhysical.id);
                },

                // ======== MODAL REVIEW FISIK ========
                openReviewModal() {
                    if (this.cart.length === 0) {
                        alert("Keranjang masih kosong.");
                        return;
                    }

                    if ((this.payment.total || 0) === 0) {
                        alert("Total transaksi tidak valid.");
                        return;
                    }

                    // Backend tetap butuh dibayar/kembalian; isi otomatis = total penjualan
                    this.payment.paid = this.payment.total;
                    this.showReview = true;
                },

                // ======== CHECKOUT PRODUK FISIK (sinkron dengan backend) ========
                async confirmCheckout() {
                    try {
                        if (this.cart.length === 0) {
                            alert("Keranjang kosong.");
                            return;
                        }

                        this.payment.paid = this.payment.total;

                        const payload = {
                            cart: this.cart.map(i => ({
                                item_type: "product",
                                id: i.id,
                                qty: i.qty,
                                price: this.payment.total > 0 ?
                                    Math.round((this.payment.total / this.total()) * i.price) : i.price,
                                product_attribute_value_id: i.variant_id ?? null,
                            })),
                            subtotal: this.payment.total || this.total(),
                            dibayar: this.payment.total || this.total(),
                            kembalian: 0,
                            customer_id: this.selectedCustomer || null,
                        };

                        const res = await fetch("{{ route('pos.checkout') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify(payload),
                        });

                        const result = await res.json();

                        if (res.ok && result.success) {

                            // TUTUP MODAL
                            this.showReview = false;
                            this.showSuccess = true;

                            this.lastTransaction = {
                                total: payload.subtotal,
                                dibayar: payload.dibayar,
                                kembalian: payload.kembalian
                            };

                            // 🧹 KOSONGKAN CART
                            this.finalizeCheckout();
                            this.selectedCustomer = '';
                            this.customerSearch = '';

                            // ================================
                            // ⭐ FIX UTAMA:
                            // Reload stok produk secara FULL
                            // ================================
                            if (typeof this.loadProducts === 'function') {
                                this.products = []; // reset list produk
                                this.hasMore = true; // reset infinite scroll
                                await this.loadProducts(); // reload produk dari server
                            }

                            // TOAST
                            this.showToast = true;
                            this.toastMsg = "Transaksi berhasil! Stok diperbarui.";
                            setTimeout(() => this.showToast = false, 3000);
                            this.focusScanner();

                        } else {
                            console.error("❌ Transaksi gagal:", {
                                status: res.status,
                                statusText: res.statusText,
                                result,
                            });

                            if (result.errors) {
                                const firstError = Object.values(result.errors)[0][0];
                                alert(`Validasi gagal: ${firstError}`);
                            } else if (result.message) {
                                alert(`Gagal: ${result.message}`);
                            } else {
                                alert(`Terjadi kesalahan (HTTP ${res.status}): ${res.statusText}`);
                            }
                        }

                    } catch (err) {
                        console.error("🔥 Error confirmCheckout:", err);
                        alert("Kesalahan saat memproses transaksi. Cek console untuk detail.");
                    }
                },

                // ======== CHECKOUT MANUAL / SERVICE ========
                async confirmManualCheckout() {
                    try {

                        if (!this.manualName || this.manualName.trim() === "") {
                            alert("Nama layanan belum diisi.");
                            return;
                        }

                        if (this.manualPrice <= 0) {
                            alert("Harga layanan tidak valid.");
                            return;
                        }

                        if (this.manualPaid < this.manualPrice) {
                            alert("Pembayaran kurang.");
                            return;
                        }

                        const payload = {
                            cart: [{
                                item_type: "service",
                                manual_name: this.manualName,
                                qty: 1,
                                price: this.manualPrice,
                                id: null,
                                product_attribute_value_id: null
                            }],
                            subtotal: this.manualPrice,
                            dibayar: this.manualPrice,
                            kembalian: 0,
                            customer_id: this.selectedCustomer || null,
                        };

                        const res = await fetch("{{ route('pos.checkout') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify(payload),
                        });
                        this.yooo();
                        const result = await res.json();

                        if (res.ok && result.success) {
                            this.showManualConfirm = false;
                            this.showSuccess = true;

                            this.lastTransaction = {
                                total: payload.subtotal,
                                dibayar: payload.dibayar,
                                kembalian: payload.kembalian,
                            };

                            // 🔄 Reset form manual
                            this.manualName = "";
                            this.manualPrice = 0;
                            this.manualPriceDisplay = "";
                            this.manualPaid = 0;
                            this.selectedCustomer = '';
                            this.customerSearch = '';

                            // 🔥 Toast
                            this.toastMsg = "Transaksi jasa berhasil!";
                            this.showToast = true;
                            setTimeout(() => (this.showToast = false), 3000);

                        } else {
                            if (result.errors) {
                                const firstError = Object.values(result.errors)[0][0];
                                alert(`Validasi gagal: ${firstError}`);
                            } else {
                                alert(result.message || "Gagal menyimpan transaksi.");
                            }
                        }

                    } catch (err) {
                        console.error("🔥 Error confirmManualCheckout:", err);
                        alert("Kesalahan jaringan, coba lagi.");
                    }
                },

                async loadTodayTransactions() {
                    try {
                        const res = await fetch("{{ route('pos.today') }}");

                        if (!res.ok) {
                            const text = await res.text(); // 🔍 tampilkan isi error sebenarnya
                            console.error("🚨 Response error:", text);
                            alert("Terjadi kesalahan server: " + res.status + " " + res.statusText);
                            return;
                        }

                        const data = await res.json();
                        if (data.success) {
                            this.transactionsToday = data.transactions;
                            this.summary = data.summary || {
                                total_penjualan: 0,
                                jumlah_transaksi: 0,
                                total_produk_terjual: 0,
                            };
                            this.showHistory = true;
                        } else {
                            console.error("⚠️ Gagal memuat riwayat:", data.message);
                            alert(data.message || "Gagal memuat riwayat transaksi hari ini.");
                        }
                    } catch (err) {
                        console.error("❌ Kesalahan jaringan:", err);
                        alert("Terjadi kesalahan saat memuat data transaksi hari ini: " + err.message);
                    }
                },

                get filteredCustomers() {
                    if (!this.customerSearch) return this.customers;

                    return this.customers.filter(c =>
                        c.name.toLowerCase().includes(this.customerSearch.toLowerCase())
                    );
                },

                // ======== CHECKOUT DIGITAL ========
                async confirmDigitalTransaction() {
                    try {
                        this.yooo();
                        const payload = {
                            device_id: this.selectedDevice?.id,
                            app_id: this.selectedApp?.id,
                            digital_brand_id: this.selectedBrand?.id,
                            digital_product_id: this.selectedProduct?.id,
                            customer_id: this.selectedCustomer || null,
                            nominal: this.selectedProduct?.base_price || 0,
                            harga_jual: this.selectedProduct?.base_price || 0,
                            subtotal: this.payment.total,
                            dibayar: this.payment.total,
                            kembalian: 0,
                            total: this.payment.total,
                        };

                        console.log('%c🚀 Sending Digital Checkout Payload:', 'color:#2563eb;font-weight:bold',
                            payload);

                        const res = await fetch("{{ route('pos.digital.checkout') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify(payload)
                        });

                        console.log('%c📡 Response Status:', 'color:#10b981;font-weight:bold', res.status, res
                            .statusText);

                        let result;
                        try {
                            result = await res.json();
                        } catch (parseError) {
                            const rawText = await res.text();
                            console.error('❌ Gagal parse JSON dari server:', parseError, '\nRaw response:\n', rawText);
                            throw new Error('Response bukan JSON valid.');
                        }

                        console.log('%c🧭 Server Response:', 'color:#9333ea;font-weight:bold', result);

                        if (result.success) {
                            this.showSuccess = true;
                            this.lastTransaction = {
                                total: payload.total,
                                dibayar: payload.dibayar,
                                kembalian: payload.kembalian,
                            };

                            // 🔁 Reset semua state setelah transaksi sukses
                            this.step = 1;
                            this.payment = {
                                customer: '',
                                total: 0,
                                paid: 0
                            };
                            this.selectedDevice = null;
                            this.selectedApp = null;
                            this.selectedCategory = null;
                            this.selectedBrand = null;
                            this.selectedProduct = null;
                            this.selectedCustomer = '';
                            this.customerSearch = '';

                        } else {
                            console.error('%c💥 Server Error:', 'color:#dc2626;font-weight:bold', result.error ||
                                '(no message)');
                            alert(result.message || 'Gagal menyimpan transaksi.');
                        }
                    } catch (err) {
                        console.error('%c🔥 Exception saat mengirim transaksi:', 'color:#ef4444;font-weight:bold', err);
                        alert('Kesalahan jaringan atau error internal. Lihat console untuk detail.');
                    }
                },
                async loadDigitalTransactions() {
                    this.loadingDigitalTransactions = true;
                    try {
                        const res = await fetch(`/digital-transactions?app_id=${this.selectedAppFilter}`);
                        const data = await res.json();
                        this.digitalTransactions = data.transactions;
                        this.digitalSummary = data.summary_total; // 🆕 ambil total subtotal
                    } catch (e) {
                        console.error('Gagal memuat transaksi digital', e);
                    } finally {
                        this.loadingDigitalTransactions = false;
                    }
                },
                async loadAppSummaries() {
                    try {
                        const res = await fetch('/digital-transactions');
                        const data = await res.json();
                        this.digitalAppSummary = data.summary_per_app || {};
                    } catch (err) {
                        console.error('Gagal memuat summary per app:', err);
                    }
                },
                async confirmDelete(trx) {
                    this.transactionToDelete = trx;
                    this.showDeleteConfirm = true;
                },

                async yooo() {
                    const sound = new Audio(@json(asset('sounds/applepay.mp3')));
                    sound.play().catch(() => {});
                },

                async deleteTransaction(trx) {
                    if (!trx) return;

                    try {
                        const res = await fetch(`/pos/transactions/${trx.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });

                        const result = await res.json();
                        console.log("🗑️ Hapus transaksi result:", result);

                        if (result.success) {
                            this.showDeleteConfirm = false;
                            this.showHistory = false;
                            // ✅ Hapus transaksi dari daftar riwayat
                            this.transactionsToday = this.transactionsToday.filter(t => t.id !== trx.id);

                            // ✅ Update summary (jumlah, total, produk terjual)
                            this.summary.jumlah_transaksi = this.transactionsToday.length;
                            this.summary.total_penjualan = this.transactionsToday.reduce(
                                (sum, t) => sum + (t.subtotal || 0),
                                0
                            );
                            this.summary.total_produk_terjual = this.transactionsToday.reduce(
                                (sum, t) => sum + (t.details?.reduce((a, d) => a + (d.qty || 0), 0) || 0),
                                0
                            );

                            // ✅ Rehitung kategori terjual
                            const categoryMap = {};
                            this.transactionsToday.forEach(t => {
                                (t.details || []).forEach(d => {
                                    const catName = d.category_name || 'Lainnya';
                                    categoryMap[catName] = (categoryMap[catName] || 0) + (d.qty || 0);
                                });
                            });

                            this.summary.categories = Object.entries(categoryMap).map(([name, pcs]) => ({
                                name,
                                pcs,
                            }));

                            // ✅ Update stok di UI pakai data dari server (result.details)
                            if (result.details && result.details.length > 0) {
                                result.details.forEach(item => {
                                    const prod = this.products.find(p => p.id === item.product_id);
                                    if (prod) {
                                        prod.stock += Number(item.qty) || 0;

                                        if (item.product_attribute_value_id) {
                                            const attr = prod.attribute_values?.find(
                                                a => a.id === item.product_attribute_value_id
                                            );
                                            if (attr) {
                                                attr.stok += Number(item.qty) || 0;
                                            }
                                        }
                                    }
                                });
                            }

                            // ✅ Tampilkan toast sukses
                            this.toastMsg = result.message || 'Transaksi berhasil dihapus dan stok dikembalikan.';
                            this.showToast = true;
                            setTimeout(() => (this.showToast = false), 2500);

                            // 🔁 Refresh produk dari backend agar stok final konsisten
                            if (typeof this.loadProducts === 'function') {
                                await this.loadProducts();
                            }

                        } else {
                            // ❌ Gagal hapus
                            this.toastMsg = result.message || 'Gagal menghapus transaksi.';
                            this.showToast = true;
                            setTimeout(() => (this.showToast = false), 2500);
                        }
                    } catch (err) {
                        console.error("🔥 Error saat hapus:", err);
                        this.toastMsg = 'Terjadi kesalahan saat menghapus transaksi.';
                        this.showToast = true;
                        setTimeout(() => (this.showToast = false), 2500);
                    }
                },

                confirmDeleteDigital(trx) {
                    this.digitalToDelete = trx;
                    this.showDeleteConfirmDigital = true;
                },

                finalizeCheckout() {
                    // 🔊 play sound
                    const sound = new Audio(@json(asset('sounds/applepay.mp3')));
                    sound.play().catch(() => {});

                    // Kosongkan keranjang TANPA mengubah stok di UI
                    this.cart = [];
                    this.saveCart();
                },

                async deleteDigitalTransaction(trx) {
                    if (!trx) return;

                    try {
                        const res = await fetch(`/digital-transactions/${trx.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        });

                        const result = await res.json();
                        console.log("🗑️ Delete digital transaction:", result);

                        if (result.success) {
                            // 1️⃣ Hapus transaksi dari daftar
                            this.digitalTransactions = this.digitalTransactions.filter(t => t.id !== trx.id);

                            // 2️⃣ Kurangi total summary secara dinamis
                            if (this.digitalSummary && trx.subtotal) {
                                this.digitalSummary -= Number(trx.subtotal);
                            }

                            // 3️⃣ Tutup modal
                            this.showDeleteConfirmDigital = false;

                            // 4️⃣ Tampilkan notifikasi
                            this.toastMsg = result.message || "Transaksi digital berhasil dihapus.";
                            this.toastType = "success";
                            this.showToast = true;
                        } else {
                            this.toastMsg = result.message || "Gagal menghapus transaksi digital.";
                            this.toastType = "error";
                            this.showToast = true;
                        }
                    } catch (err) {
                        console.error("🔥 Error hapus digital:", err);
                        this.toastMsg = "Kesalahan saat menghapus transaksi digital.";
                        this.toastType = "error";
                        this.showToast = true;
                    }
                },

                openProductOptions(product) {
                    const attrs = product.attribute_values || [];

                    // 🚀 Kalau tidak ada varian → langsung masuk keranjang
                    if (attrs.length === 0) {
                        this.addToCart(product);
                        this.toastMsg = `${product.name} ditambahkan!`;
                        this.showToast = true;
                        setTimeout(() => this.showToast = false, 2000);
                        return;
                    }

                    // 🚀 Kalau cuma 1 varian → langsung tambah ke keranjang
                    if (attrs.length === 1) {
                        const opt = attrs[0];
                        const itemName = `${product.name} - ${opt.attribute_value}`;
                        const masterStock = opt.stok ?? 0;

                        const existing = this.cart.find(
                            i => i.id === product.id && i.variant_id === opt.id
                        );

                        if (existing) {
                            if (existing.qty + 1 > masterStock) {
                                this.toastMsg = `Stok ${itemName} tidak cukup (tersisa ${masterStock}).`;
                                this.showToast = true;
                                setTimeout(() => this.showToast = false, 2500);
                                return;
                            }
                            existing.qty++;
                        } else {
                            if (masterStock <= 0) {
                                this.toastMsg = `⚠️ Stok ${itemName} habis.`;
                                this.showToast = true;
                                setTimeout(() => this.showToast = false, 2500);
                                return;
                            }

                            this.cart.push({
                                id: product.id,
                                name: itemName,
                                price: Number(product.price ?? 0),
                                qty: 1,
                                variant: opt.attribute_value,
                                variant_id: opt.id,
                            });
                        }

                        this.saveCart();
                        this.toastMsg = `${itemName} ditambahkan!`;
                        this.showToast = true;
                        setTimeout(() => this.showToast = false, 2000);
                        return;
                    }

                    // 🧩 Kalau varian lebih dari 1 → tampilkan modal
                    this.selectedProduct = product;
                    this.selectedProductOptions = attrs;
                    this.showOptionModal = true;
                },

                // Pilih salah satu varian di modal
                chooseOption(opt) {
                    if (!this.selectedProduct) return;

                    const itemName = `${this.selectedProduct.name} - ${opt.attribute_value}`;
                    const masterStock = opt.stok ?? 0;

                    // ❌ Cek stok habis
                    if (masterStock <= 0) {
                        this.toastMsg = `⚠️ Stok ${itemName} habis.`;
                        this.showToast = true;
                        setTimeout(() => this.showToast = false, 2500);
                        return;
                    }

                    const existing = this.cart.find(
                        i => i.id === this.selectedProduct.id && i.variant_id === opt.id
                    );

                    if (existing) {
                        if (existing.qty + 1 > masterStock) {
                            this.toastMsg = `Stok ${itemName} tidak cukup (tersisa ${masterStock}).`;
                            this.showToast = true;
                            setTimeout(() => this.showToast = false, 2500);
                            return;
                        }
                        existing.qty++;
                    } else {
                        this.cart.push({
                            id: this.selectedProduct.id,
                            name: itemName,
                            price: Number(this.selectedProduct.price),
                            qty: 1,
                            variant: opt.attribute_value,
                            variant_id: opt.id,
                        });
                    }

                    this.saveCart();
                    this.updatePaymentTotals();

                    // Tutup modal
                    this.showOptionModal = false;
                    this.selectedProduct = null;
                    this.selectedProductOptions = [];

                    this.toastMsg = `${itemName} ditambahkan!`;
                    this.showToast = true;
                    setTimeout(() => this.showToast = false, 2000);
                },

                // Update scanner agar mendeteksi produk bervarian
                async handleBarcodeInput(e) {
                    const code = e.target.value.trim();
                    if (!code) {
                        e.target.value = '';
                        return;
                    }

                    // 1️⃣ Cari di produk lokal dulu
                    let found = this.products.find(p => String(p.code) === String(code));

                    // 2️⃣ Kalau belum ketemu, cari ke backend
                    if (!found) {
                        try {
                            const res = await fetch(`/pos/find-product?barcode=${encodeURIComponent(code)}`);
                            const data = await res.json();

                            if (data.success) {
                                found = data.data;
                                console.log('🆕 Produk dimuat dari server:', found);
                            } else {
                                this.toastMsg = `Produk dengan barcode ${code} tidak ditemukan.`;
                                this.showToast = true;
                                setTimeout(() => (this.showToast = false), 2500);
                                e.target.value = '';
                                return;
                            }

                        } catch (err) {
                            console.error('❌ Gagal memuat produk dari server:', err);
                            this.toastMsg = 'Koneksi gagal. Coba lagi.';
                            this.showToast = true;
                            setTimeout(() => (this.showToast = false), 2500);
                            e.target.value = '';
                            return;
                        }
                    }

                    // Normalisasi harga agar tidak NaN
                    found.price = Number(found.price ?? 0);

                    // 3️⃣ Cek varian
                    const attrs = found.attribute_values || [];

                    // ==========================================
                    // CASE: Ada varian
                    // ==========================================
                    if (attrs.length > 0) {

                        // --- VARIAN TUNGGAL ---
                        if (attrs.length === 1) {
                            const opt = attrs[0];
                            const masterStock = Number(opt.stok ?? 0);

                            if (masterStock <= 0) {
                                this.toastMsg = `⚠️ Stok ${found.name} - ${opt.attribute_value} habis.`;
                                this.showToast = true;
                                setTimeout(() => (this.showToast = false), 2500);
                                e.target.value = '';
                                return;
                            }

                            const itemName = `${found.name} - ${opt.attribute_value}`;

                            const existing = this.cart.find(
                                i => i.id === found.id && i.variant_id === opt.id
                            );

                            if (existing) {
                                if (existing.qty + 1 > masterStock) {
                                    this.toastMsg = `Stok ${itemName} tidak cukup (tersisa ${masterStock}).`;
                                    this.showToast = true;
                                    setTimeout(() => this.showToast = false, 2500);
                                    e.target.value = '';
                                    return;
                                }
                                existing.qty++;
                            } else {
                                this.cart.push({
                                    id: found.id,
                                    name: itemName,
                                    price: Number(found.price || 0),
                                    qty: 1,
                                    variant: opt.attribute_value,
                                    variant_id: opt.id,
                                });
                            }

                            this.saveCart();
                            this.updatePaymentTotals();

                            this.toastMsg = `${itemName} ditambahkan!`;
                            this.showToast = true;
                            setTimeout(() => this.showToast = false, 2000);

                            e.target.value = '';
                            return;
                        }

                        // --- VARIAN BANYAK: buka modal ---
                        this.selectedProduct = found;
                        this.selectedProductOptions = attrs;
                        this.showOptionModal = true;

                        e.target.value = '';
                        return;
                    }

                    // ==========================================
                    // CASE: Tidak punya varian
                    // ==========================================

                    const existing = this.cart.find(i => i.id === found.id);
                    const qtyInCart = existing ? existing.qty : 0;

                    if (Number(found.stock) <= qtyInCart) {
                        this.toastMsg = `Stok ${found.name} tidak cukup (tersisa ${found.stock}).`;
                        this.showToast = true;
                        setTimeout(() => (this.showToast = false), 2500);
                        e.target.value = '';
                        return;
                    }

                    // Tambah tanpa varian
                    if (existing) {
                        existing.qty++;
                    } else {
                        this.cart.push({
                            id: found.id,
                            name: found.name,
                            price: Number(found.price || 0),
                            qty: 1
                        });
                    }

                    this.saveCart();
                    this.updatePaymentTotals();

                    this.toastMsg = `${found.name} ditambahkan!`;
                    this.showToast = true;
                    setTimeout(() => (this.showToast = false), 2000);

                    e.target.value = '';
                },


                bindPosSegPill() {
                    this.syncPosSegPill({
                        instant: true,
                        scroll: false
                    });
                    this.$nextTick(() => {
                        requestAnimationFrame(() => {
                            this.syncPosSegPill({
                                instant: true,
                                scroll: false
                            });
                        });
                        if (this._posSegBound) return;
                        this._posSegBound = true;
                        const seg = this.$refs.posSeg;
                        const track = seg ? seg.querySelector('.pos-seg-track') : null;
                        const target = track || seg;
                        if (target && typeof ResizeObserver !== 'undefined') {
                            const ro = new ResizeObserver(() => {
                                if (this._posSegLock) return;
                                this.syncPosSegPill({
                                    instant: true,
                                    scroll: false
                                });
                            });
                            ro.observe(target);
                        }
                        window.addEventListener('resize', () => {
                            this.syncPosSegPill({
                                instant: true,
                                scroll: false
                            });
                        });
                        window.addEventListener('orientationchange', () => {
                            this.syncPosSegPill({
                                instant: true,
                                scroll: false
                            });
                        });
                        if (document.fonts && document.fonts.ready) {
                            document.fonts.ready.then(() => {
                                this.syncPosSegPill({
                                    instant: true,
                                    scroll: false
                                });
                            });
                        }
                    });
                },

                syncPosSegPill(opts = {}) {
                    const seg = this.$refs.posSeg;
                    if (!seg) {
                        return;
                    }
                    const track = seg.querySelector('.pos-seg-track');
                    const btn = seg.querySelector(`[data-pos-tab="${this.activeTab}"]`);
                    if (!track || !btn) return;

                    const pill = track.querySelector('.pos-seg-pill');
                    if (!pill) return;

                    const x = btn.offsetLeft;
                    const w = btn.offsetWidth;
                    const h = btn.offsetHeight;
                    if (w <= 0 || h <= 0) {
                        return;
                    }

                    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    const ready = seg.classList.contains('is-pos-seg-ready');
                    const instant = opts.instant || reduce || !ready;

                    if (instant) {
                        seg.classList.add('pos-seg-noanim');
                    }

                    pill.style.width = `${w}px`;
                    pill.style.height = `${h}px`;
                    pill.style.transform = `translate3d(${x}px, 0, 0)`;

                    if (!ready) {
                        void pill.offsetWidth;
                        seg.classList.add('is-pos-seg-ready');
                    }

                    if (instant) {
                        void pill.offsetWidth;
                        requestAnimationFrame(() => seg.classList.remove('pos-seg-noanim'));
                    }

                    if (opts.scroll !== false && ready) {
                        btn.scrollIntoView({
                            behavior: reduce ? 'auto' : 'smooth',
                            inline: 'nearest',
                            block: 'nearest'
                        });
                    }
                },

                async handleCloseBook() {
                    this.showCloseBookModal = true;
                    this.closeBookData = null;

                    try {
                        const res = await fetch("{{ route('pos.closebook.data') }}");
                        const data = await res.json();

                        // Pastikan semua numeric field dipaksa jadi Number
                        data.barangTotal = Number(data.barangTotal) || 0;
                        data.totalPenjualan = Number(data.totalPenjualan) || 0;
                        data.totalUtang = Number(data.totalUtang) || 0;
                        data.bayarUtang = Number(data.bayarUtang) || 0;
                        data.grandTotal = Number(data.grandTotal) || 0;

                        this.closeBookData = data;

                    } catch (e) {
                        alert("Gagal memuat data tutup buku.");
                    }
                },

                formatRupiah(angka) {
                    angka = Number(angka);
                    if (isNaN(angka)) angka = 0;
                    return 'Rp ' + angka.toLocaleString('id-ID');
                },

                formatLebihInput(event) {
                    let raw = event.target.value.replace(/\D/g, "");
                    if (!raw) raw = "0";

                    this.lebih = Number(raw) || 0;
                    event.target.value = this.lebih.toLocaleString("id-ID");
                },
                async copyCloseBook() {
                    if (!this.closeBookData) return;

                    const d = this.closeBookData;

                    // 🔒 Helper aman angka
                    const toNumber = v => Number.isFinite(Number(v)) ? Number(v) : 0;

                    // =========================
                    // 🧮 HITUNG (SOURCE OF TRUTH)
                    // =========================

                    // Total Utang (belum dibayar)
                    const totalUtang = (d.utangList || [])
                        .reduce((sum, u) => sum + toNumber(u.subtotal), 0);

                    // Total Bayar Utang (dibayar hari ini)
                    const totalBayarUtang = (d.bayarUtangList || [])
                        .reduce((sum, u) => sum + toNumber(u.subtotal), 0);

                    // Total setelah utang (SAMA DENGAN UI)
                    const totalSetelahUtang =
                        toNumber(d.totalPenjualan) +
                        totalBayarUtang -
                        totalUtang;

                    // Total Transfer
                    const totalTF = (d.transferDetail || [])
                        .reduce((sum, i) => sum + toNumber(i.total), 0);

                    // Total Tarik
                    const totalTarik = (d.tarikDetail || [])
                        .reduce((sum, i) => sum + toNumber(i.total), 0);

                    // Total akhir + lebih
                    const totalAkhir = totalSetelahUtang + toNumber(this.lebih || 0);

                    let text = '';

                    // =========================
                    // 🗓️ HEADER
                    // =========================
                    text += `${d.tanggal}\n\n`;

                    // =========================
                    // 📦 BARANG & DIGITAL
                    // =========================
                    text += `Barang : Rp ${toNumber(d.barangTotal).toLocaleString('id-ID')}\n`;
                    (d.digitalPerApp || []).forEach(app => {
                        text += `${app.name} : Rp ${toNumber(app.total).toLocaleString('id-ID')}\n`;
                    });

                    // =========================
                    // 🔸 TOTAL PENJUALAN
                    // =========================
                    text += `---------------------------\n`;
                    text += `Total Penjualan : Rp ${toNumber(d.totalPenjualan).toLocaleString('id-ID')}\n\n`;

                    // =========================
                    // 💸 UTANG
                    // =========================
                    if ((d.utangList || []).length > 0) {
                        text += `Utang :\n`;
                        d.utangList.forEach(u => {
                            text += `- ${u.name}: Rp ${toNumber(u.subtotal).toLocaleString('id-ID')}\n`;
                        });
                        text += `---------------------------\n`;
                    }

                    // =========================
                    // 💵 BAYAR UTANG
                    // =========================
                    if ((d.bayarUtangList || []).length > 0) {
                        text += `Bayar Utang :\n`;
                        d.bayarUtangList.forEach(u => {
                            text += `* ${u.name}: Rp ${toNumber(u.subtotal).toLocaleString('id-ID')}\n`;
                        });
                        text += `---------------------------\n`;
                    }

                    // =========================
                    // 🧾 TOTAL SETELAH UTANG
                    // =========================
                    text += `Total => Rp ${totalSetelahUtang.toLocaleString('id-ID')}\n`;

                    if (toNumber(this.lebih) > 0) {
                        text += `Lebih => Rp ${toNumber(this.lebih).toLocaleString('id-ID')}\n`;
                    }

                    // =========================
                    // 💰 TOTAL AKHIR
                    // =========================
                    text += `Total Akhir => Rp ${totalAkhir.toLocaleString('id-ID')}\n`;
                    text += `---------------------------\n`;

                    // =========================
                    // 🏦 TRANSFER & TARIK
                    // =========================
                    text += `*Total TF : Rp ${totalTF.toLocaleString('id-ID')}*\n`;
                    text += `*Total Tarik : Rp ${totalTarik.toLocaleString('id-ID')}*`;

                    // =========================
                    // 📋 COPY TO CLIPBOARD
                    // =========================
                    await navigator.clipboard.writeText(text);

                    // ✨ FEEDBACK UI
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                },

                async handleFinalCloseBook() {
                    try {
                        this.showConfirmCloseBook = false;
                        this.showCloseBookModal = false;

                        // 🧮 Hitung total akhir (cukup GrandTotal + Lebih)
                        const totalFinal = (this.closeBookData?.grandTotal ?? 0) + (this.lebih || 0);

                        console.log('🚀 Mengirim data tutup buku:', {
                            tanggal: this.closeBookData?.tanggal,
                            total_final: totalFinal,
                        });

                        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                        if (!csrf) {
                            alert('❌ Token CSRF tidak ditemukan.');
                            return;
                        }

                        const response = await fetch('{{ route('cashbook.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                            },
                            body: JSON.stringify({
                                deskripsi: `Penjualan Tanggal ${this.closeBookData?.tanggal}`,
                                type: 'IN',
                                nominal: totalFinal,
                                outlet_id: {{ Auth::user()->outlet_id ?? 1 }},
                                cashbook_category_id: 3,
                                cashbook_wallet_id: 1,
                            }),
                        });

                        console.log('📬 Response Status:', response.status);
                        const rawText = await response.text();
                        console.log('🧾 Response Body:', rawText);

                        let result = {};
                        try {
                            result = JSON.parse(rawText);
                        } catch {
                            console.warn('⚠️ Respon bukan JSON valid (mungkin redirect / HTML error).');
                        }

                        if (result.success) {
                            const formatted = totalFinal.toLocaleString('id-ID');
                            const toast = document.createElement('div');
                            toast.textContent = `✅ Tutup buku berhasil! Rp ${formatted}`;
                            toast.className =
                                'fixed bottom-5 right-5 bg-emerald-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                            document.body.appendChild(toast);
                            setTimeout(() => toast.remove(), 3000);
                            setTimeout(() => window.location.href = '/pembukuan', 1500);
                        } else {
                            alert(`❌ Gagal menyimpan: ${result.message || 'Server tidak merespons.'}`);
                        }

                    } catch (error) {
                        console.error('💥 Gagal mengirim data ke pembukuan:', error);
                        alert('❌ Terjadi kesalahan jaringan.');
                    }
                },
            }
        }

        // ======== HERO ICONS MAP ========
        window.heroicons = {
            'device-phone-mobile': `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 2.25h9a1.5 1.5 0 011.5 1.5v16.5a1.5 1.5 0 01-1.5 1.5h-9a1.5 1.5 0 01-1.5-1.5V3.75a1.5 1.5 0 011.5-1.5zM9 18.75h6" /></svg>`,
            'credit-card': `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.5A2.25 2.25 0 014.5 5.25h15a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 012.25 16.5v-9zM2.25 9h19.5" /></svg>`,
            'computer-desktop': `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h18v10.5H3zM7.5 19.5h9" /></svg>`,
            'wifi': `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 9.75a15.375 15.375 0 0119.5 0M5.25 12.75a10.5 10.5 0 0113.5 0M8.25 15.75a5.625 5.625 0 017.5 0M12 18.75h.008v.008H12v-.008z" /></svg>`,
            'printer': `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 9V4.5h10.5V9m-10.5 0h10.5m-10.5 0v10.5h10.5V9m-10.5 0h-3A2.25 2.25 0 001.5 11.25v5.25A2.25 2.25 0 003.75 18.75H6.75" /></svg>`,
            'server-stack': `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 4.5h15v3H4.5zm0 6h15v3h-15zm0 6h15v3h-15z" /></svg>`,
        };
    </script>
@endsection

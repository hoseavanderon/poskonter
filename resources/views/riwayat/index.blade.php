@extends('layouts.app')

@push('head')
    <!-- FLATPICKR (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
@endpush

@section('content')
    <style>
        .hist-page {
            color: var(--text-primary);
            overflow-x: hidden !important;
        }

        .hist-page .no-scrollbar::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        .hist-page .no-scrollbar {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }

        .hist-page .smooth-scroll {
            scroll-behavior: smooth;
        }

        /* iOS cards */
        .hist-card {
            background: #FFFFFF !important;
            border: 1px solid rgba(0, 0, 0, 0.04) !important;
            border-radius: 24px !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06) !important;
            color: var(--text-primary) !important;
        }

        html.dark .hist-card {
            background: #1C1C1E !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.45) !important;
        }

        /* Nested panel (utang, total) */
        .hist-inset {
            background: #F2F2F7 !important;
            border-radius: 16px !important;
            border: 0 !important;
            color: var(--text-primary) !important;
            box-shadow: none !important;
        }

        html.dark .hist-inset {
            background: #2C2C2E !important;
        }

        /* Elevated list / tile cards inside sections */
        .hist-item {
            background: #FFFFFF !important;
            border-radius: 16px !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04) !important;
            color: var(--text-primary) !important;
            transition: box-shadow 220ms ease, transform 220ms ease, border-color 220ms ease;
        }

        .hist-item:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.07) !important;
            border-color: rgba(0, 0, 0, 0.08) !important;
        }

        html.dark .hist-item {
            background: #2C2C2E !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.35) !important;
        }

        @media (max-width: 767px) {
            .hist-cat-card {
                padding: 16px 12px !important;
            }

            .hist-cat-count {
                margin-top: 4px;
                font-size: 22px !important;
                font-weight: 700 !important;
                letter-spacing: -0.03em;
                line-height: 1.1;
            }

            .hist-product-card {
                padding: 18px 16px !important;
            }

            .hist-product-name {
                font-size: 17px !important;
                line-height: 1.3;
            }

            .hist-product-qty {
                margin-top: 2px;
                font-size: 15px !important;
            }

            .hist-product-amount {
                font-size: 22px !important;
                font-weight: 700 !important;
                letter-spacing: -0.03em;
                line-height: 1.15;
            }
        }

        html.dark .hist-item:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.45) !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        .hist-title {
            color: var(--text-primary) !important;
        }

        .hist-muted {
            color: var(--text-muted) !important;
        }

        .hist-secondary {
            color: var(--text-secondary) !important;
        }

        .hist-accent {
            color: var(--accent) !important;
        }

        .hist-divider {
            border-color: var(--divider) !important;
        }

        .hist-chip {
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            background: #FFFFFF;
            color: var(--text-secondary);
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        html.dark .hist-chip {
            background: #1C1C1E;
            border-color: rgba(255, 255, 255, 0.10);
            box-shadow: none;
            color: #AEAEB2;
        }

        .hist-chip.is-on {
            background: #007AFF;
            color: #fff;
            border-color: transparent;
            box-shadow: none;
        }

        html.dark .hist-chip.is-on {
            background: #007AFF;
            color: #fff;
            border-color: transparent;
            box-shadow: none;
        }

        /* Premium date-range control (Flatpickr target unchanged) */
        .hist-range {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: min(100%, 320px);
            height: 48px;
            padding: 0 14px 0 16px;
            border-radius: 16px;
            background: #FFFFFF;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow:
                0 1px 2px rgba(0, 0, 0, 0.04),
                0 8px 24px rgba(0, 0, 0, 0.06);
            cursor: pointer;
            transition:
                box-shadow 240ms cubic-bezier(0.22, 1, 0.36, 1),
                border-color 240ms ease,
                transform 240ms cubic-bezier(0.22, 1, 0.36, 1);
        }

        .hist-range:hover {
            border-color: rgba(0, 122, 255, 0.22);
            box-shadow:
                0 2px 6px rgba(0, 0, 0, 0.04),
                0 12px 28px rgba(0, 122, 255, 0.10);
            transform: translateY(-1px);
        }

        html.dark .hist-range {
            background: #1C1C1E;
            border-color: rgba(255, 255, 255, 0.10);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        }

        html.dark .hist-range:hover {
            border-color: rgba(10, 132, 255, 0.35);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.5);
        }

        .hist-range-icon {
            flex: 0 0 auto;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 122, 255, 0.10);
            color: var(--accent);
        }

        html.dark .hist-range-icon {
            background: rgba(10, 132, 255, 0.18);
        }

        .hist-page .hist-range input.hist-range-input,
        .hist-page .hist-range input.hist-range-input:focus {
            flex: 1 1 auto;
            min-width: 0;
            width: 100%;
            height: 100%;
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
            outline: none !important;
            box-shadow: none !important;
            background: transparent !important;
            background-color: transparent !important;
            color: var(--text-primary) !important;
            font-size: 15px !important;
            font-weight: 600;
            letter-spacing: -0.01em;
            text-align: left;
            cursor: pointer;
        }

        .hist-range-input::placeholder {
            color: var(--text-muted);
            font-weight: 500;
        }

        .hist-range-chevron {
            flex: 0 0 auto;
            color: var(--text-muted);
            display: inline-flex;
        }

        /* Flatpickr calendar — premium iOS */
        .flatpickr-calendar {
            width: 320px !important;
            padding: 12px 12px 14px !important;
            border: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-radius: 20px !important;
            box-shadow:
                0 4px 12px rgba(0, 0, 0, 0.04),
                0 20px 48px rgba(0, 0, 0, 0.12) !important;
            overflow: hidden;
            font-family: inherit;
            background: #FFFFFF !important;
        }

        html.dark .flatpickr-calendar {
            background: #1C1C1E !important;
            border-color: rgba(255, 255, 255, 0.10) !important;
            box-shadow: 0 20px 48px rgba(0, 0, 0, 0.55) !important;
        }

        .flatpickr-calendar.arrowTop:before,
        .flatpickr-calendar.arrowTop:after,
        .flatpickr-calendar.arrowBottom:before,
        .flatpickr-calendar.arrowBottom:after {
            display: none !important;
        }

        .flatpickr-months {
            padding: 4px 4px 10px !important;
            align-items: center;
        }

        .flatpickr-months .flatpickr-month {
            height: 36px !important;
            color: #1D1D1F !important;
            fill: #1D1D1F !important;
            background: transparent !important;
        }

        html.dark .flatpickr-months .flatpickr-month {
            color: #F5F5F7 !important;
            fill: #F5F5F7 !important;
        }

        .flatpickr-current-month {
            font-size: 15px !important;
            font-weight: 700 !important;
            letter-spacing: -0.02em;
            padding-top: 4px !important;
        }

        .flatpickr-current-month input.cur-year,
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            color: #1D1D1F !important;
            font-weight: 700 !important;
            background: transparent !important;
        }

        html.dark .flatpickr-current-month input.cur-year,
        html.dark .flatpickr-current-month .flatpickr-monthDropdown-months {
            color: #F5F5F7 !important;
        }

        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            width: 32px !important;
            height: 32px !important;
            border-radius: 10px !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            top: 10px !important;
            padding: 0 !important;
            fill: #636366 !important;
            color: #636366 !important;
            transition: background-color 160ms ease;
        }

        .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-months .flatpickr-next-month:hover {
            background: #F2F2F7 !important;
        }

        html.dark .flatpickr-months .flatpickr-prev-month:hover,
        html.dark .flatpickr-months .flatpickr-next-month:hover {
            background: #2C2C2E !important;
        }

        .flatpickr-weekdays {
            height: 32px !important;
            margin-top: 2px;
        }

        span.flatpickr-weekday {
            color: #8E8E93 !important;
            font-size: 12px !important;
            font-weight: 600 !important;
        }

        .flatpickr-days {
            width: 100% !important;
        }

        .dayContainer {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }

        .flatpickr-day {
            max-width: 40px !important;
            height: 40px !important;
            line-height: 40px !important;
            margin: 2px 0 !important;
            border-radius: 12px !important;
            border: 0 !important;
            color: #1D1D1F !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            transition:
                background-color 160ms ease,
                color 160ms ease,
                box-shadow 160ms ease;
        }

        html.dark .flatpickr-day {
            color: #F5F5F7 !important;
        }

        .flatpickr-day:hover,
        .flatpickr-day:focus {
            background: #F2F2F7 !important;
            border-color: transparent !important;
        }

        html.dark .flatpickr-day:hover,
        html.dark .flatpickr-day:focus {
            background: #2C2C2E !important;
        }

        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            color: #C7C7CC !important;
        }

        html.dark .flatpickr-day.prevMonthDay,
        html.dark .flatpickr-day.nextMonthDay {
            color: #636366 !important;
        }

        .flatpickr-day.today {
            background: transparent !important;
            box-shadow: inset 0 0 0 1.5px rgba(0, 122, 255, 0.45) !important;
            color: var(--accent) !important;
            font-weight: 700 !important;
        }

        .flatpickr-day.today:hover {
            background: rgba(0, 122, 255, 0.08) !important;
        }

        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected:hover,
        .flatpickr-day.startRange:hover,
        .flatpickr-day.endRange:hover,
        .flatpickr-day.selected:focus,
        .flatpickr-day.startRange:focus,
        .flatpickr-day.endRange:focus {
            background: var(--accent) !important;
            border-color: var(--accent) !important;
            color: #FFFFFF !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.28) !important;
        }

        .flatpickr-day.startRange {
            border-radius: 12px 4px 4px 12px !important;
        }

        .flatpickr-day.endRange {
            border-radius: 4px 12px 12px 4px !important;
        }

        .flatpickr-day.startRange.endRange {
            border-radius: 12px !important;
        }

        .flatpickr-day.inRange,
        .flatpickr-day.prevMonthDay.inRange,
        .flatpickr-day.nextMonthDay.inRange {
            background: rgba(0, 122, 255, 0.12) !important;
            border-color: transparent !important;
            box-shadow: none !important;
            color: #1D1D1F !important;
            border-radius: 0 !important;
        }

        html.dark .flatpickr-day.inRange {
            background: rgba(10, 132, 255, 0.22) !important;
            color: #F5F5F7 !important;
        }

        .flatpickr-day.inRange:hover {
            background: rgba(0, 122, 255, 0.18) !important;
        }

        /* Tabs with blue underline */
        .hist-tabs {
            display: flex;
            align-items: stretch;
            width: 100%;
            border-bottom: 1px solid var(--divider);
        }

        .hist-tab {
            flex: 1;
            text-align: center;
            padding: 10px 16px 12px;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-muted);
            background: transparent;
            border: 0;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            transition:
                color 200ms ease,
                border-color 200ms ease;
        }

        .hist-tab:hover {
            color: var(--text-primary);
        }

        .hist-tab.is-on {
            color: var(--accent) !important;
            border-bottom-color: var(--accent) !important;
        }

        .hist-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 40px 16px;
            text-align: center;
            color: var(--text-muted);
            font-size: 14px;
        }

        .hist-empty svg {
            width: 28px;
            height: 28px;
            color: var(--text-muted);
            stroke: currentColor;
        }

        .hist-dropdown {
            background: #FFFFFF !important;
            border: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-radius: 18px !important;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12) !important;
            color: var(--text-primary);
        }

        html.dark .hist-dropdown {
            background: #1C1C1E !important;
            border-color: rgba(255, 255, 255, 0.10) !important;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.55) !important;
        }

        .hist-month-btn {
            height: 40px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            background: #F2F2F7;
            color: var(--text-secondary);
            border: 0;
        }

        html.dark .hist-month-btn {
            background: #2C2C2E;
        }

        .hist-month-btn.is-on {
            background: var(--accent);
            color: #fff;
        }

        .hist-modal {
            background: #FFFFFF !important;
            border: 1px solid rgba(0, 0, 0, 0.06) !important;
            border-radius: 24px !important;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.16) !important;
            color: var(--text-primary) !important;
        }

        html.dark .hist-modal {
            background: #1C1C1E !important;
            border-color: rgba(255, 255, 255, 0.10) !important;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6) !important;
        }

        .hist-debt {
            color: #FF3B30 !important;
        }

        .hist-pay {
            color: #34C759 !important;
        }

        html.dark .hist-debt {
            color: #FF453A !important;
        }

        html.dark .hist-pay {
            color: #30D158 !important;
        }

        .hist-btn-danger {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #FF3B30 !important;
            color: #FFFFFF !important;
            border: 0;
            font-weight: 600;
            transition: background-color 200ms ease, transform 160ms ease;
        }

        .hist-btn-danger:hover {
            background: #E0352B !important;
            color: #FFFFFF !important;
        }

        .hist-btn-danger:active {
            transform: scale(0.98);
        }

        html.dark .hist-btn-danger {
            background: #FF3B30 !important;
            color: #FFFFFF !important;
        }

        html.dark .hist-btn-danger:hover {
            background: #E0352B !important;
        }

        body.no-scroll {
            overflow: hidden !important;
        }

        /* Entrance — soft fade (setelah overlay) */
        @keyframes histSoftIn {
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

        @keyframes histChipSlide {
            from {
                opacity: 0;
                transform: translateX(16px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes histTabIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes histCopyPop {
            0% {
                transform: scale(1);
            }
            40% {
                transform: scale(0.88);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes histCopyCheck {
            0% {
                transform: scale(0.4);
                opacity: 0;
            }
            60% {
                transform: scale(1.12);
                opacity: 1;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .hist-page.is-waiting .hist-enter-head,
        .hist-page.is-waiting .hist-range,
        .hist-page.is-waiting .hist-chip,
        .hist-page.is-waiting .hist-enter-card,
        .hist-page.is-waiting .hist-item,
        .hist-page.is-waiting .hist-tabs {
            opacity: 0;
        }

        .hist-page.is-booting .hist-enter-head {
            animation: histSoftIn 640ms cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .hist-page.is-booting .hist-range {
            animation: histSoftIn 680ms cubic-bezier(0.16, 1, 0.3, 1) 90ms both;
        }

        .hist-page.is-booting .hist-chip {
            opacity: 0;
            animation: histChipSlide 480ms cubic-bezier(0.16, 1, 0.3, 1) both;
            animation-delay: calc(160ms + (var(--i, 0) * 22ms));
        }

        .hist-page.is-booting .hist-tabs {
            animation: histTabIn 560ms cubic-bezier(0.16, 1, 0.3, 1) 240ms both;
        }

        .hist-page.is-booting .hist-enter-card {
            opacity: 0;
            animation: histSoftIn 700ms cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .hist-page.is-booting .hist-enter-card[data-enter="1"] {
            animation-delay: 260ms;
        }

        .hist-page.is-booting .hist-enter-card[data-enter="2"] {
            animation-delay: 340ms;
        }

        .hist-page.is-booting .hist-enter-card[data-enter="3"] {
            animation-delay: 420ms;
        }

        .hist-page.is-booting .hist-enter-card[data-enter="4"] {
            animation-delay: 500ms;
        }

        .hist-page.is-booting .hist-item {
            opacity: 0;
            animation: histSoftIn 620ms cubic-bezier(0.16, 1, 0.3, 1) both;
            animation-delay: calc(480ms + (var(--i, 0) * 40ms));
        }

        @media (prefers-reduced-motion: reduce) {
            .hist-page.is-waiting .hist-enter-head,
            .hist-page.is-waiting .hist-range,
            .hist-page.is-waiting .hist-chip,
            .hist-page.is-waiting .hist-enter-card,
            .hist-page.is-waiting .hist-item,
            .hist-page.is-waiting .hist-tabs,
            .hist-page.is-booting .hist-enter-head,
            .hist-page.is-booting .hist-range,
            .hist-page.is-booting .hist-chip,
            .hist-page.is-booting .hist-enter-card,
            .hist-page.is-booting .hist-item,
            .hist-page.is-booting .hist-tabs {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
                filter: none !important;
            }
        }

        /* Round copy button */
        .hist-copy-btn {
            position: relative;
            width: 36px;
            height: 36px;
            border-radius: 999px;
            border: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #F2F2F7;
            color: #636366;
            cursor: pointer;
            flex-shrink: 0;
            transition:
                background-color 220ms ease,
                color 220ms ease,
                box-shadow 220ms ease,
                transform 220ms cubic-bezier(0.22, 1, 0.36, 1);
        }

        .hist-copy-btn:hover {
            background: #E5E5EA;
            color: #1D1D1F;
        }

        .hist-copy-btn:active {
            transform: scale(0.94);
        }

        html.dark .hist-copy-btn {
            background: #2C2C2E;
            color: #AEAEB2;
        }

        html.dark .hist-copy-btn:hover {
            background: #3A3A3C;
            color: #F5F5F7;
        }

        .hist-copy-btn .hist-copy-ico {
            position: absolute;
            width: 18px;
            height: 18px;
            transition:
                opacity 220ms ease,
                transform 320ms cubic-bezier(0.22, 1, 0.36, 1);
        }

        .hist-copy-btn .hist-copy-ico.is-clip {
            opacity: 1;
            transform: scale(1);
        }

        .hist-copy-btn .hist-copy-ico.is-check {
            opacity: 0;
            transform: scale(0.45);
            color: #34C759;
        }

        .hist-copy-btn.is-done {
            background: rgba(52, 199, 89, 0.14);
            color: #34C759;
            animation: histCopyPop 360ms cubic-bezier(0.22, 1, 0.36, 1);
        }

        .hist-copy-btn.is-done .hist-copy-ico.is-clip {
            opacity: 0;
            transform: scale(0.45);
        }

        .hist-copy-btn.is-done .hist-copy-ico.is-check {
            opacity: 1;
            animation: histCopyCheck 380ms cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        html.dark .hist-copy-btn.is-done {
            background: rgba(48, 209, 88, 0.18);
            color: #30D158;
        }

        html.dark .hist-copy-btn .hist-copy-ico.is-check {
            color: #30D158;
        }

        .hist-tab-panel {
            width: 100%;
        }
    </style>

    <div x-data="transactionHistory()" x-init="init()"
        class="hist-page p-5 sm:p-6 w-full h-full overflow-x-hidden relative"
        :class="{ 'is-waiting': isWaiting, 'is-booting': isBooting }">

        <!-- HEADER -->
        <div class="hist-enter-head flex items-center justify-between border-b hist-divider pb-3 mb-5 relative">
            <h1 class="text-2xl font-semibold hist-title">Riwayat Transaksi</h1>

            <!-- DROPDOWN BUTTON -->
            <button @click="toggleDropdown"
                class="p-2 rounded-md hover:bg-[color:var(--surface-secondary)] transition-all duration-300 z-30 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 transform transition-transform duration-300"
                    :class="showDropdown ? 'rotate-180 text-[color:var(--accent)]' : 'rotate-0 text-[color:var(--text-muted)]'" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- FLOATING DROPDOWN (ganti yang lama) -->
            <div x-show="showDropdown" @click.outside="showDropdown=false"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                class="hist-dropdown absolute top-[52px] right-0 p-5 w-72 z-50 space-y-4">

                <template x-for="year in availableYears" :key="year">
                    <div>
                        <p class="text-lg font-semibold hist-title mb-3" x-text="year"></p>
                        <div class="grid grid-cols-4 gap-3">
                            <template x-for="m in months" :key="m">
                                <button @click="selectMonth(m, year)"
                                    class="hist-month-btn transition-all duration-300"
                                    :class="(selectedYear === year && selectedMonth === m) ? 'is-on' : ''">
                                    <span x-text="m"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- DATE RANGE FILTER (FLATPICKR) -->
        <div class="flex justify-center items-center mb-6">
            <label class="hist-range" for="dateRangePicker">
                <span class="hist-range-icon" aria-hidden="true">
                    <x-heroicon-o-calendar-days class="w-4 h-4" />
                </span>
                <input id="dateRangePicker" type="text"
                    class="hist-range-input"
                    readonly placeholder="Pilih rentang tanggal" />
                <span class="hist-range-chevron" aria-hidden="true">
                    <x-heroicon-o-chevron-down class="w-4 h-4" />
                </span>
            </label>
        </div>

        <!-- DATE SLIDER -->
        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-4 mb-4 smooth-scroll">
            <template x-for="day in days" :key="day">
                <button @click="selectedDate = day; fetchData();"
                    class="hist-chip transition-all duration-300 ease-out"
                    :style="'--i:' + (day - 1)"
                    :class="Number(selectedDate) === Number(day) ? 'is-on' : ''">
                    <span x-text="day + '/' + selectedMonthNumber"></span>
                </button>
            </template>
        </div>

        <!-- MAIN GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_2fr] gap-5">

            <div id="summaryBox"
                class="hist-card hist-enter-card p-6 text-[15px] space-y-6 leading-normal" data-enter="1">

                <!-- HEADER -->
                <div class="flex justify-between items-center mb-1">
                    <div>
                        <p class="text-[12px] uppercase tracking-wider hist-muted">Rincian Transaksi</p>

                        <h2 class="text-[20px] font-semibold hist-title mt-1">
                            <template x-if="isRangeActive">
                                <span x-text="formatRangeTanggal(fromDate, toDate)"></span>
                            </template>
                            <template x-if="!isRangeActive">
                                <span x-text="formatTanggal(selectedDate, selectedMonthNumber, selectedYear)"></span>
                            </template>
                        </h2>
                    </div>

                    <button type="button"
                        @click="copySummary()"
                        class="hist-copy-btn"
                        :class="{ 'is-done': copied }"
                        :disabled="copied"
                        :aria-label="copied ? 'Disalin' : 'Salin rincian'">
                        <svg class="hist-copy-ico is-clip" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <svg class="hist-copy-ico is-check" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>

                </div>

                <hr class="hist-divider">

                <!-- BARANG + DIGITAL -->
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="hist-secondary">Barang</span>
                        <span class="font-semibold hist-accent" x-text="formatCurrency(barangTotal)"></span>
                    </div>

                    <template x-for="d in digitalPerApp" :key="d.name">
                        <div class="flex justify-between">
                            <span class="hist-secondary" x-text="d.name"></span>
                            <span class="font-semibold hist-accent" x-text="formatCurrency(d.total)"></span>
                        </div>
                    </template>
                </div>

                <hr class="hist-divider">

                <!-- SUBTOTAL -->
                <div class="flex justify-between pt-1">
                    <span class="hist-secondary">Subtotal ( Belum Utang )</span>
                    <span class="font-semibold hist-title" x-text="formatCurrency(totalPenjualanSebelumUtang)"></span>
                </div>

                <!-- DEBT -->
                <template x-if="utangList.length > 0">
                    <div class="hist-inset p-4 space-y-2">
                        <p class="uppercase text-[12px] hist-muted font-semibold">Utang : </p>

                        <template x-for="u in utangList" :key="u.name">
                            <div class="flex justify-between">
                                <span class="hist-title" x-text="u.name"></span>
                                <span class="hist-debt font-semibold"
                                    x-text="'(' + formatCurrency(u.subtotal) + ')'"></span>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- PAYMENT -->
                <template x-if="pembayaranUtang.length > 0">
                    <div class="hist-inset p-4 space-y-2">
                        <p class="uppercase text-[12px] hist-muted font-semibold">Bayar Utang : </p>

                        <template x-for="(u, index) in pembayaranUtang" :key="u.name + '-' + index">
                            <div class="flex justify-between">
                                <span class="hist-pay" x-text="u.name"></span>
                                <span class="hist-pay font-semibold" x-text="formatCurrency(u.subtotal)"></span>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- TOTAL SALES -->
                <div class="hist-inset px-4 py-3 flex justify-between font-semibold hist-title">
                    <span>Total Penjualan</span>
                    <span x-text="formatCurrency(computedTotalPenjualan())"></span>
                </div>

                <!-- TRANSFERS -->
                <div>
                    <p class="uppercase text-[12px] hist-muted font-semibold mb-2">Transfer</p>

                    <div class="space-y-2">

                        <!-- Brilink -->
                        <template x-if="tfTarikByApp[7]?.tf > 0">
                            <div class="flex justify-between">
                                <span class="hist-secondary">Brilink TF</span>
                                <span class="hist-accent" x-text="formatCurrency(tfTarikByApp[7]?.tf)">
                                </span>
                            </div>
                        </template>

                        <template x-if="tfTarikByApp[7]?.tarik > 0">
                            <div class="flex justify-between">
                                <span class="hist-debt">Brilink Tarik</span>
                                <span class="hist-debt" x-text="formatCurrency(tfTarikByApp[7]?.tarik)">
                                </span>
                            </div>
                        </template>

                        <!-- Seabank -->
                        <template x-if="tfTarikByApp[6]?.tf > 0">
                            <div class="flex justify-between">
                                <span class="hist-secondary">Seabank TF</span>
                                <span class="hist-accent" x-text="formatCurrency(tfTarikByApp[6]?.tf)">
                                </span>
                            </div>
                        </template>

                        <!-- MyBCA -->
                        <template x-if="tfTarikByApp[9]?.tf > 0">
                            <div class="flex justify-between">
                                <span class="hist-secondary">MyBCA TF</span>
                                <span class="hist-accent" x-text="formatCurrency(tfTarikByApp[9]?.tf)">
                                </span>
                            </div>
                        </template>

                        <template x-if="tfTarikByApp[9]?.tarik > 0">
                            <div class="flex justify-between">
                                <span class="hist-debt">MyBCA Tarik</span>
                                <span class="hist-debt" x-text="formatCurrency(tfTarikByApp[9]?.tarik)">
                                </span>
                            </div>
                        </template>

                        <!-- Shopee -->
                        <template x-if="tfTarikByApp[10]?.tf > 0">
                            <div class="flex justify-between">
                                <span class="hist-secondary">SHP Pay TF</span>
                                <span class="hist-accent" x-text="formatCurrency(tfTarikByApp[10]?.tf)">
                                </span>
                            </div>
                        </template>

                        <template x-if="tfTarikByApp[12]?.tf > 0">
                            <div class="flex justify-between">
                                <span class="hist-secondary">ShopeePay TF</span>
                                <span class="hist-accent" x-text="formatCurrency(tfTarikByApp[12]?.tf)">
                                </span>
                            </div>
                        </template>

                    </div>
                </div>


                @if (Auth::user()->outlet_id == 3)
                    <div class="flex justify-between items-center font-semibold hist-title text-lg mt-4">
                        <span>Grand Total</span>
                        <span x-text="formatCurrency(computedGrandTotal())"></span>
                    </div>
                @endif

            </div>

            <!-- RIGHT SIDE -->
            <div class="flex flex-col gap-5 overflow-y-auto no-scrollbar" x-ref="rightSide"
                style="max-height: calc(110vh - 100px);">

                <!-- TABS -->
                <div class="hist-tabs">
                    <button type="button" @click="activeTab = 'produk'"
                        class="hist-tab"
                        :class="{ 'is-on': activeTab === 'produk' }">
                        Produk Fisik
                    </button>

                    <button type="button" @click="activeTab = 'digital'"
                        class="hist-tab"
                        :class="{ 'is-on': activeTab === 'digital' }">
                        Produk Digital
                    </button>
                </div>

                <!-- TAB PRODUK FISIK -->
                <div x-show="activeTab === 'produk'"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="hist-tab-panel space-y-5">

                    <!-- CATEGORY SUMMARY -->
                    <div class="hist-card hist-enter-card p-5" data-enter="2">
                        <h3 class="text-[15px] font-semibold hist-title mb-3">Ringkasan Kategori Produk</h3>

                        <template x-if="categories.length > 0">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                                <template x-for="(c, index) in categories" :key="c.name + index">
                                    <div class="hist-inset hist-cat-card py-3 px-1">
                                        <p class="text-[15px] font-semibold hist-title" x-text="c.name"></p>
                                        <p class="hist-cat-count text-[13px] hist-muted" x-text="c.total_pcs + ' pcs'"></p>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="categories.length === 0">
                            <div class="hist-empty">
                                <x-heroicon-o-rectangle-stack />
                                <p>Tidak ada data kategori produk.</p>
                            </div>
                        </template>
                    </div>

                    <!-- PRODUCT HISTORY -->
                    <div class="hist-card hist-enter-card p-5" data-enter="3">
                        <h3 class="text-[15px] font-semibold hist-title mb-3">Riwayat Transaksi Produk</h3>

                        <template x-if="productTransactions.length > 0">
                            <div class="space-y-4">
                                <template x-for="(t, idx) in productTransactions" :key="t.transaction_id">

                                    <div x-data="{ openMenu: false, confirmDelete: false }"
                                        class="hist-item hist-product-card p-4 space-y-3"
                                        :style="'--i:' + idx"
                                        @keydown.escape.window="confirmDelete = false">

                                        <!-- MODAL BACKDROP -->
                                        <div x-show="confirmDelete" x-transition.opacity @click="confirmDelete = false"
                                            class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[999]">

                                            <!-- MODAL BOX -->
                                            <div x-show="confirmDelete" x-transition.scale @click.stop
                                                class="hist-modal w-[420px] p-6 space-y-5">

                                                <h2 class="hist-title text-xl font-semibold text-center leading-snug">
                                                    Yakin retur <span class="hist-accent"
                                                        x-text="t.details[0].name"></span>?
                                                </h2>

                                                <p class="hist-secondary text-[15px] text-center leading-relaxed">
                                                    Transaksi ini akan dihapus dan stok akan dikembalikan.
                                                </p>

                                                <div class="flex justify-end gap-4 mt-4">

                                                    <!-- BATAL -->
                                                    <button @click="confirmDelete = false"
                                                        class="px-5 py-2 text-sm rounded-lg bg-[color:var(--surface-secondary)] hist-title transition">
                                                        Batal
                                                    </button>

                                                    <!-- YA, RETUR -->
                                                    <button
                                                        @click="confirmDelete = false; deleteTransaction(t.transaction_id)"
                                                        class="hist-btn-danger px-5 py-2 text-sm rounded-lg">
                                                        Ya, Retur
                                                    </button>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- HEADER: TANGGAL/JAM + MENU 3 TITIK -->
                                        <div class="flex justify-between items-center mb-1">

                                            <!-- TANGGAL & JAM -->
                                            <div class="flex flex-col leading-tight">
                                                <span class="text-[13px] hist-muted"
                                                    x-text="formatPrettyDate(t.datetime || t.date || t.created_at)">
                                                </span>
                                            </div>

                                            <!-- MENU BUTTON -->
                                            <div x-data="{ openMenu: false }" class="relative">

                                                <button @click="openMenu = !openMenu"
                                                    class="p-1 rounded hover:bg-[color:var(--surface)] transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hist-secondary"
                                                        fill="currentColor" viewBox="0 0 24 24">
                                                        <circle cx="12" cy="5" r="2"></circle>
                                                        <circle cx="12" cy="12" r="2"></circle>
                                                        <circle cx="12" cy="19" r="2"></circle>
                                                    </svg>
                                                </button>

                                                <!-- DROPDOWN -->
                                                <div x-show="openMenu"
                                                    x-transition:enter="transition ease-out duration-150"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-100"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95"
                                                    @click.outside="openMenu = false"
                                                    class="hist-dropdown absolute right-0 mt-2 w-36 z-30 overflow-hidden">

                                                    <button @click="confirmDelete = true"
                                                        class="hist-btn-danger w-full px-4 py-2.5 text-[14px] rounded-none">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-4 w-4" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V5a1 1 0 00-1-1h-4a1 1 0 00-1 1v2m-5 0h16" />
                                                        </svg>
                                                        Hapus
                                                    </button>

                                                </div>
                                            </div>

                                        </div>

                                        <!-- LIST PRODUK -->
                                        <template x-for="d in t.details" :key="d.name">

                                            <div class="flex justify-between items-start py-1">

                                                <!-- NAMA PRODUK -->
                                                <div class="flex flex-col leading-tight">
                                                    <span class="hist-product-name hist-title font-semibold text-[15px]"
                                                        x-text="d.name"></span>
                                                    <span class="hist-product-qty text-[13px] hist-muted"
                                                        x-text="d.qty + ' pcs'"></span>
                                                </div>

                                                <!-- HARGA -->
                                                <div class="flex flex-col text-right">
                                                    <span class="hist-product-amount hist-accent font-semibold text-[15px]"
                                                        x-text="formatCurrency(d.amount)">
                                                    </span>
                                                </div>

                                            </div>

                                        </template>

                                    </div>

                                </template>
                            </div>
                        </template>

                        <template x-if="productTransactions.length === 0">
                            <div class="hist-empty">
                                <x-heroicon-o-document-text />
                                <p>Tidak ada transaksi produk pada tanggal ini.</p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- TOAST SUKSES -->
                <div x-show="toastSuccess" x-transition.duration.300ms
                    class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-3 rounded-xl shadow-lg 
            text-sm font-medium z-[9999] flex items-center gap-2">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>

                    <span x-text="toastMessage"></span>
                </div>

                <!-- TAB PRODUK DIGITAL -->
                <div x-show="activeTab === 'digital'"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="hist-tab-panel space-y-5">

                    <div class="hist-card hist-enter-card p-5" data-enter="2">
                        <h3 class="text-[15px] font-semibold hist-title mb-3">Riwayat Produk Digital</h3>

                        <template x-if="Object.keys(digitalTransactions).length > 0">
                            <div class="space-y-6">

                                <!-- DEVICE LIST -->
                                <template x-for="(apps, deviceName) in digitalTransactions" :key="deviceName">
                                    <div>
                                        <h4 class="text-[15px] font-semibold hist-title mb-3 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hist-muted"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 2h6a2 2 0 012 2v16a2 2 0 01-2 2H9a2 2 0 01-2-2V4a2 2 0 012-2z" />
                                            </svg>
                                            <span x-text="deviceName"></span>
                                        </h4>

                                        <!-- APPS -->
                                        <div class="space-y-3">
                                            <template x-for="(app, appName) in apps" :key="appName">
                                                <div class="hist-item p-3">

                                                    <!-- APP HEADER -->
                                                    <button @click="app.open = !app.open"
                                                        class="w-full flex justify-between items-center px-2 py-1 text-left text-[15px] font-semibold hist-title hover:text-[color:var(--accent)]">

                                                        <div class="flex items-center gap-2">
                                                            <span x-text="appName"></span>

                                                            <span class="text-[13px] hist-muted"
                                                                x-text="app.transactions.length + ' Trx'">
                                                            </span>
                                                        </div>

                                                        <div class="flex items-center gap-2">
                                                            <span class="hist-accent text-[13px] font-medium"
                                                                x-text="formatCurrency(app.total)">
                                                            </span>

                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="w-4 h-4 transition-transform hist-muted"
                                                                :class="app.open ? 'rotate-180' : ''" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M19 9l-7 7-7-7" />
                                                            </svg>
                                                        </div>

                                                    </button>

                                                    <!-- DETAIL -->
                                                    <div x-show="app.open" x-collapse class="mt-3 space-y-2">
                                                        <template x-for="t in app.transactions">
                                                            <div
                                                                class="hist-card p-3 flex justify-between items-center !shadow-none">

                                                                <div>
                                                                    <p class="text-[15px] font-semibold hist-title"
                                                                        x-text="t.name"></p>

                                                                    <p class="text-[13px] hist-muted"
                                                                        x-text="t.datetime"></p>
                                                                </div>

                                                                <span class="text-[15px] font-semibold"
                                                                    :class="{
                                                                        'hist-pay': t.category_id == 8,
                                                                        'hist-debt': t.category_id == 9,
                                                                        'hist-accent': ![8, 9].includes(t
                                                                            .category_id),
                                                                    }"
                                                                    x-text="formatCurrency(t.amount)">
                                                                </span>

                                                            </div>
                                                        </template>
                                                    </div>

                                                </div>
                                            </template>
                                        </div>

                                    </div>
                                </template>

                            </div>
                        </template>

                        <template x-if="Object.keys(digitalTransactions).length === 0">
                            <div class="hist-empty">
                                <x-heroicon-o-bolt />
                                <p>Tidak ada transaksi digital.</p>
                            </div>
                        </template>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <script>
        function transactionHistory() {
            return {
                fromDate: null,
                toDate: null,
                rangeDisplay: '',
                showDropdown: false,
                availableYears: [], // tahun dinamis dari backend
                currentYear: new Date().getFullYear(),
                currentMonthIndex: new Date().getMonth(),
                selectedYear: new Date().getFullYear(),
                selectedMonth: '',
                selectedDate: new Date().getDate(),
                emptyData: false, // tampilkan pesan jika kosong

                months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                days: [],
                categories: @json($defaultData['categories'] ?? []),
                productTransactions: @json($defaultData['productTransactions'] ?? []),
                digitalTransactions: @json($defaultData['digitalTransactions'] ?? []),
                total: @json($defaultData['total'] ?? 0),
                extra: @json($defaultData['extra'] ?? 0),
                barangTotal: @json($defaultData['barangTotal'] ?? 0),
                digitalPerApp: @json($defaultData['digitalPerApp'] ?? []),
                totalTarik: @json($defaultData['totalTarik'] ?? 0),
                utangList: @json($defaultData['utangList'] ?? []),
                pembayaranUtang: @json($defaultData['pembayaranUtang'] ?? []),
                totalPembayaranUtang: @json($defaultData['totalPembayaranUtang'] ?? 0),
                totalTransfer: @json($defaultData['totalTransfer'] ?? 0),
                productTransactions: @json($defaultData['productTransactions'] ?? []),
                groupedDigitalTransactions: @json($defaultData['digitalTransactions'] ?? []),
                copied: false,
                tfTarikByApp: @json($defaultData['tfTarikByApp'] ?? []),
                isRangeActive: false,
                outletId: {{ Auth::user()->outlet_id }},
                activeTab: 'produk',
                toastSuccess: false,
                toastMessage: "",
                isWaiting: true,
                isBooting: false,

                async deleteTransaction(id) {
                    try {
                        const res = await fetch(`/riwayat/transaction/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });

                        const data = await res.json();

                        if (!data.success) {
                            console.error("Gagal hapus:", data);
                            return;
                        }

                        // 🔥 Hapus card dari UI
                        this.productTransactions = this.productTransactions.filter(t => t.transaction_id !== id);

                        // 🔄 Update summary kiri
                        await this.fetchData();

                        // ✅ Tampilkan toast sukses
                        this.showToast("Transaksi berhasil diretur");

                    } catch (e) {
                        console.error("Error:", e);
                    }
                },

                showToast(message) {
                    this.toastMessage = message;
                    this.toastSuccess = true;

                    setTimeout(() => {
                        this.toastSuccess = false;
                    }, 2000);
                },

                get totalPenjualan() {
                    // Hitung total barang + semua digital apps
                    const totalDigital = this.digitalPerApp.reduce((sum, d) => sum + Number(d.total || 0), 0);
                    return Number(this.barangTotal || 0) + totalDigital;
                },

                get grandTotal() {
                    const outletId = {{ Auth::user()->outlet_id }};

                    if (outletId == 3) {
                        return Number(this.totalPenjualan || 0) +
                            this.totalTransferFix -
                            this.totalTarikFix;
                    }

                    // Outlet lain → rumus lama
                    const totalUtang = this.utangList.reduce((sum, u) => sum + Number(u.subtotal || 0), 0);
                    return this.totalPenjualan - totalUtang;
                },

                toggleDropdown() {
                    this.showDropdown = !this.showDropdown;
                },

                selectMonth(m, year) {
                    this.selectedMonth = m;
                    this.selectedYear = year;
                    this.showDropdown = false;

                    const idx = this.months.indexOf(m);
                    const month = idx + 1;
                    const lastDay = new Date(year, month, 0).getDate();
                    this.days = Array.from({
                        length: lastDay
                    }, (_, i) => i + 1);

                    this.selectedDate = 1;
                    this.fetchData();
                },

                get selectedMonthNumber() {
                    const idx = this.months.indexOf(this.selectedMonth);
                    return idx >= 0 ? String(idx + 1).padStart(2, '0') : '';
                },

                formatTanggal(day, monthNumber, year) {
                    const namaBulan = [
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];
                    const bulanIndex = parseInt(monthNumber, 10) - 1;
                    const bulanNama = namaBulan[bulanIndex] || '-';
                    return `${day} ${bulanNama} ${year}`;
                },

                formatCurrency(v) {
                    return 'Rp ' + Number(v).toLocaleString('id-ID');
                },

                // helper to produce ISO YYYY-MM-DD
                formatDate(date) {
                    if (!date) return '';
                    if (typeof date === 'string') return date;
                    const y = date.getFullYear();
                    const m = String(date.getMonth() + 1).padStart(2, '0');
                    const d = String(date.getDate()).padStart(2, '0');
                    return `${y}-${m}-${d}`;
                },

                formatPrettyDate(dateValue) {
                    if (!dateValue) return '-';

                    if (typeof dateValue === 'string' && dateValue.includes(' ')) {
                        const [datePart, timePart] = dateValue.split(' ');
                        const cleanTime = timePart?.substring(0, 5) || '';
                        return `${this.formatIndo(datePart)} ${cleanTime}`;
                    }

                    if (/^\d{4}-\d{2}-\d{2}$/.test(dateValue)) {
                        return this.formatIndo(dateValue);
                    }

                    return dateValue;
                },

                getTransactionTime(t) {
                    if (!t) return '';

                    // Cari jam dari field transaksi langsung
                    const dt = t.datetime || t.created_at || t.date || null;
                    if (!dt) return '';

                    const d = new Date(dt);
                    if (isNaN(d)) return '';

                    const jam = String(d.getHours()).padStart(2, '0');
                    const menit = String(d.getMinutes()).padStart(2, '0');

                    return `${jam}:${menit}`;
                },

                // helper to produce pretty Indonesian date
                formatIndo(dateInput) {
                    let date = (typeof dateInput === 'string') ? new Date(dateInput) : dateInput;
                    if (!(date instanceof Date) || isNaN(date)) return '-';
                    const bulan = [
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];
                    return `${date.getDate()} ${bulan[date.getMonth()]} ${date.getFullYear()}`;
                },

                formatRangeTanggal(fromIso, toIso) {
                    if (!fromIso || !toIso) return this.formatIndo(fromIso || toIso);
                    if (fromIso === toIso) return this.formatIndo(fromIso);
                    return `${this.formatIndo(fromIso)} - ${this.formatIndo(toIso)}`;
                },

                setSelectedFromIso(iso) {
                    if (!iso) return;
                    const parts = iso.split('-');
                    if (parts.length !== 3) return;
                    const y = parseInt(parts[0], 10);
                    const m = parseInt(parts[1], 10);
                    const d = parseInt(parts[2], 10);
                    this.selectedYear = y;
                    this.selectedMonth = this.months[m - 1];
                    const lastDay = new Date(y, m, 0).getDate();
                    this.days = Array.from({
                        length: lastDay
                    }, (_, i) => i + 1);
                    this.selectedDate = d;
                },

                async fetchData() {
                    this.isRangeActive = false;

                    const monthNum = this.selectedMonthNumber;
                    const day = String(this.selectedDate).padStart(2, '0');
                    const date = `${this.selectedYear}-${monthNum}-${day}`;

                    try {
                        const res = await fetch(`/riwayat/data?tanggal=${date}`);
                        const data = await res.json();

                        if (data.empty) {
                            this.emptyData = true;

                            this.categories = [];
                            this.productTransactions = [];
                            this.digitalTransactions = [];
                            this.groupedDigitalTransactions = [];

                            this.total = 0;
                            this.extra = 0;
                            this.barangTotal = 0;

                            this.digitalPerApp = [];
                            this.totalTransfer = 0;
                            this.totalTarik = 0;

                            this.utangList = [];
                            this.pembayaranUtang = [];
                            this.totalPembayaranUtang = 0;

                            this.tfTarikByApp = {};
                        } else {
                            this.emptyData = false;

                            this.categories = data.categories;

                            this.productTransactions = data.productTransactions.map(t => ({
                                ...t,
                                open: false
                            }));

                            this.digitalTransactions = data.digitalTransactions || [];
                            this.groupedDigitalTransactions = data.digitalTransactions || [];

                            this.total = data.total;
                            this.extra = data.extra;
                            this.barangTotal = data.barangTotal;

                            this.digitalPerApp = data.digitalPerApp || [];

                            this.totalTransfer = data.totalTransfer || 0;
                            this.totalTarik = data.totalTarik || 0;

                            this.utangList = data.utangList || [];
                            this.tfTarikByApp = data.tfTarikByApp || {};

                            // 🔥 WAJIB
                            this.pembayaranUtang = data.pembayaranUtang || [];
                            this.totalPembayaranUtang = data.totalPembayaranUtang || 0;
                        }

                    } catch (error) {
                        console.error("⚠️ Gagal memuat data transaksi:", error);

                        this.emptyData = true;

                        this.categories = [];
                        this.productTransactions = [];
                        this.digitalTransactions = [];
                        this.groupedDigitalTransactions = [];

                        this.total = 0;
                        this.extra = 0;
                        this.barangTotal = 0;

                        this.digitalPerApp = [];
                        this.totalTransfer = 0;
                        this.totalTarik = 0;

                        this.utangList = [];
                        this.pembayaranUtang = [];
                        this.totalPembayaranUtang = 0;

                        this.tfTarikByApp = {};
                    }
                },

                async fetchDataRange() {
                    if (!this.fromDate || !this.toDate) return;
                    this.isRangeActive = true;

                    try {
                        const res = await fetch(`/riwayat/data-range?from=${this.fromDate}&to=${this.toDate}`);
                        const data = await res.json();

                        if (data.empty) {
                            this.emptyData = true;

                            this.categories = [];
                            this.productTransactions = [];
                            this.digitalTransactions = [];
                            this.groupedDigitalTransactions = [];

                            this.barangTotal = 0;
                            this.digitalPerApp = [];

                            this.totalTransfer = 0;
                            this.totalTarik = 0;
                            this.tfTarikByApp = {}; // 🔥 INI WAJIB

                            this.utangList = [];
                            this.pembayaranUtang = [];
                            this.totalPembayaranUtang = 0;

                        } else {
                            this.emptyData = false;

                            this.categories = data.categories || [];
                            this.productTransactions = (data.productTransactions || []).map(t => ({
                                ...t,
                                open: false
                            }));
                            this.digitalTransactions = data.digitalTransactions || [];
                            this.groupedDigitalTransactions = data.digitalTransactions || [];

                            this.barangTotal = data.barangTotal || 0;
                            this.digitalPerApp = data.digitalPerApp || [];

                            this.totalTransfer = data.totalTransfer || 0;
                            this.totalTarik = data.totalTarik || 0;

                            this.tfTarikByApp = data.tfTarikByApp || {}; // 🔥 INI PENENTU

                            this.utangList = data.utangList || [];
                            this.pembayaranUtang = data.pembayaranUtang || [];
                            this.totalPembayaranUtang = data.totalPembayaranUtang || 0;
                        }

                    } catch (err) {
                        console.error("Gagal memuat data rentang tanggal:", err);
                    }
                },

                async loadAvailableYears() {
                    const res = await fetch('/riwayat/years');
                    this.availableYears = await res.json();
                },

                async copySummary() {
                    try {
                        const tanggal = this.isRangeActive ?
                            this.formatRangeTanggal(this.fromDate, this.toDate) :
                            this.formatTanggal(this.selectedDate, this.selectedMonthNumber, this.selectedYear);

                        let lines = [];
                        lines.push(`Rincian Transaksi ${tanggal}`);
                        lines.push('');

                        // ============================
                        // BARANG + DIGITAL
                        // ============================
                        lines.push(`Barang : ${this.formatCurrency(this.barangTotal)}`);

                        this.digitalPerApp.forEach(d => {
                            lines.push(`${d.name} : ${this.formatCurrency(d.total)}`);
                        });

                        lines.push('');

                        // ============================
                        // TOTAL SEBELUM UTANG
                        // ============================
                        lines.push(
                            `Total Penjualan (Sebelum Utang) : ${this.formatCurrency(this.totalPenjualanSebelumUtang)}`
                        );
                        lines.push('');

                        // ============================
                        // UTANG
                        // ============================
                        if (this.utangList.length > 0) {
                            lines.push('Utang :');
                            this.utangList.forEach(u => {
                                lines.push(`- ${u.name} (${this.formatCurrency(u.subtotal)})`);
                            });
                            lines.push('');
                        }

                        // ============================
                        // PEMBAYARAN UTANG (BARU)
                        // ============================
                        if (this.pembayaranUtang.length > 0) {
                            lines.push('Pembayaran Utang :');
                            this.pembayaranUtang.forEach(u => {
                                lines.push(`+ ${u.name} (${this.formatCurrency(u.subtotal)})`);
                            });
                            lines.push('');
                        }

                        // ============================
                        // TOTAL SESUDAH UTANG
                        // ============================
                        lines.push(
                            `Total Penjualan : ${this.formatCurrency(this.computedTotalPenjualan())}`
                        );
                        lines.push('');

                        // ============================
                        // TF & TARIK per APP
                        // ============================
                        const APP_LABELS = {
                            5: "Brimo",
                            6: "Seabank",
                            7: "Brilink",
                            9: "MyBCA",
                            10: "SHP Pay",
                            12: "ShopeePay"
                        };

                        let totalTF = 0;
                        let totalTarik = 0;

                        Object.keys(APP_LABELS).forEach(appId => {
                            const item = this.tfTarikByApp[appId];
                            if (!item) return;

                            const name = APP_LABELS[appId];

                            if (item.tf > 0) {
                                lines.push(`${name} TF : ${this.formatCurrency(item.tf)}`);
                                totalTF += Number(item.tf);
                            }

                            if (item.tarik > 0) {
                                lines.push(`${name} Tarik : ${this.formatCurrency(item.tarik)}`);
                                totalTarik += Number(item.tarik);
                            }
                        });

                        lines.push('');

                        // ============================
                        // GRAND TOTAL (OUTLET 3)
                        // ============================
                        if (this.outletId == 3) {
                            lines.push(
                                `Grand Total : ${this.formatCurrency(this.computedGrandTotal())}`
                            );
                        }

                        // ============================
                        // TOTAL TF/TARIK (OUTLET BUKAN 3)
                        // ============================
                        if (this.outletId != 3) {
                            lines.push(`TOTAL TF : ${this.formatCurrency(totalTF)}`);
                            lines.push(`TOTAL TARIK : ${this.formatCurrency(totalTarik)}`);
                        }

                        await navigator.clipboard.writeText(lines.join('\n'));

                        this.copied = true;
                        setTimeout(() => (this.copied = false), 2000);

                    } catch (err) {
                        console.error('❌ Copy gagal:', err);
                    }
                },

                computedTotalPenjualan() {
                    const barang = Number(this.barangTotal || 0);
                    const digital = this.digitalPerApp.reduce((s, d) => s + Number(d.total || 0), 0);

                    // ini FIXED
                    const utang = this.utangList.reduce((s, d) => s + Number(d.subtotal || 0), 0);

                    const bayarUtang = Number(this.totalPembayaranUtang || 0);

                    return (barang + digital) - utang + bayarUtang;
                },

                computedGrandTotal() {
                    if (this.outletId !== 3) return 0;

                    const totalPenjualan = this.computedTotalPenjualan();
                    const totalTF = this.totalTransferFix;
                    const totalTarik = this.totalTarikFix;

                    return totalPenjualan + totalTF - totalTarik;
                },

                get totalPenjualanSebelumUtang() {
                    const barang = Number(this.barangTotal || 0);
                    const digital = this.digitalPerApp.reduce((s, d) => s + Number(d.total || 0), 0);
                    return barang + digital;
                },

                get totalTransferFix() {
                    return Object.values(this.tfTarikByApp || {}).reduce((sum, a) => sum + (a.tf || 0), 0);
                },

                get totalTarikFix() {
                    return Object.values(this.tfTarikByApp || {}).reduce((sum, a) => sum + (a.tarik || 0), 0);
                },

                init() {
                    const now = new Date();
                    this.currentYear = now.getFullYear();
                    this.currentMonthIndex = now.getMonth();
                    this.selectedYear = now.getFullYear();
                    this.selectedMonth = this.months[now.getMonth()];
                    this.selectedDate = now.getDate();

                    const year = this.selectedYear;
                    const month = this.currentMonthIndex + 1;
                    const lastDay = new Date(year, month, 0).getDate();
                    if (this.selectedDate > lastDay) this.selectedDate = lastDay;

                    this.days = Array.from({
                        length: lastDay
                    }, (_, i) => i + 1);

                    this.loadAvailableYears();
                    this.fetchData();

                    this.$nextTick(() => {
                        // Inisialisasi Flatpickr untuk rentang tanggal
                        flatpickr("#dateRangePicker", {
                            mode: "range",
                            dateFormat: "Y-m-d",
                            locale: "id",
                            animate: true,
                            defaultDate: [this.fromDate, this.toDate],
                            onChange: (selectedDates, dateStr, instance) => {
                                if (selectedDates.length === 2) {
                                    this.fromDate = this.formatDate(selectedDates[0]);
                                    this.toDate = this.formatDate(selectedDates[1]);
                                    instance.input.value = this.formatRangeTanggal(this.fromDate, this
                                        .toDate);
                                    this.fetchDataRange();
                                }
                            },
                        });

                        this.scrollActiveDateIntoView();
                        // retry singkat jika chip belum ter-render
                        setTimeout(() => this.scrollActiveDateIntoView(), 120);
                        setTimeout(() => this.scrollActiveDateIntoView(), 320);

                        this.playEnterAfterOverlay();
                    });
                },

                playEnterAfterOverlay() {
                    const start = () => {
                        if (this._enterStarted) return;
                        this._enterStarted = true;

                        this.isWaiting = false;
                        // double rAF supaya browser apply state → lalu trigger anim
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                this.isBooting = true;
                                setTimeout(() => {
                                    this.isBooting = false;
                                }, 1500);
                            });
                        });
                    };

                    const html = document.documentElement;

                    // Overlay masih aktif → tunggu class is-boot hilang / event reveal
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

                        // pantau overlay DOM juga
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

                        // fallback jika observer gagal
                        setTimeout(() => {
                            obs.disconnect();
                            window.removeEventListener('app-boot:reveal', onReveal);
                            if (this.isWaiting) start();
                        }, 2800);
                        return;
                    }

                    // Tidak ada overlay → langsung animasi
                    start();
                },

                scrollActiveDateIntoView() {
                    const container = this.$el.querySelector('.smooth-scroll');
                    const activeBtn = container?.querySelector('.hist-chip.is-on');
                    if (!activeBtn || !container) return;
                    const offsetLeft = activeBtn.offsetLeft - container.clientWidth / 2 + activeBtn
                        .clientWidth / 2;
                    container.scrollTo({
                        left: Math.max(0, offsetLeft),
                        behavior: 'smooth'
                    });
                },
            };
        }
    </script>
@endsection

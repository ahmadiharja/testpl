<style>
    .mobile-scope-screen {
        display: grid;
        gap: 1rem;
        padding-bottom: 0.2rem;
    }

    .mobile-scope-hero {
        display: grid;
        gap: 0.82rem;
    }

    .mobile-scope-kicker {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .mobile-scope-title {
        margin-top: 0.36rem;
        font-size: 1.22rem;
        font-weight: 700;
        line-height: 1.08;
        letter-spacing: -0.035em;
        color: #0f172a;
    }

    .mobile-scope-copy {
        margin-top: 0.24rem;
        max-width: 19rem;
        font-size: 12.5px;
        line-height: 1.5;
        color: #64748b;
    }

    .mobile-scope-card {
        position: relative;
        overflow: hidden;
        border-radius: 1.45rem;
        background:
            radial-gradient(circle at top left, rgba(125, 211, 252, 0.28), transparent 28%),
            radial-gradient(circle at bottom right, rgba(34, 197, 94, 0.22), transparent 36%),
            linear-gradient(145deg, #082f49 0%, #0f172a 42%, #0f766e 100%);
        padding: 1rem;
        box-shadow: 0 20px 32px rgba(8, 47, 73, 0.22);
        color: #f8fafc;
    }

    .mobile-scope-card::before {
        content: '';
        position: absolute;
        top: -1.4rem;
        right: -1.2rem;
        height: 5.8rem;
        width: 5.8rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        filter: blur(6px);
        pointer-events: none;
    }

    .mobile-scope-card::after {
        content: '';
        position: absolute;
        inset: auto auto 0.8rem 0.8rem;
        width: 4.5rem;
        height: 1px;
        background: rgba(255, 255, 255, 0.16);
        pointer-events: none;
    }

    .mobile-scope-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.7rem;
    }

    .mobile-scope-card-badge {
        display: inline-flex;
        align-items: center;
        min-height: 1.7rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        padding: 0.28rem 0.66rem;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(248, 250, 252, 0.95);
        backdrop-filter: blur(10px);
    }

    .mobile-scope-card-context {
        min-width: 0;
        font-size: 11px;
        font-weight: 600;
        color: rgba(226, 232, 240, 0.82);
        text-align: right;
    }

    .mobile-scope-screen.scope-facility .mobile-scope-card {
        background:
            radial-gradient(circle at top left, rgba(125, 211, 252, 0.24), transparent 30%),
            radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.18), transparent 36%),
            linear-gradient(145deg, #082f49 0%, #0f172a 42%, #0f766e 100%);
    }

    .mobile-scope-screen.scope-workgroup .mobile-scope-card {
        background:
            radial-gradient(circle at top left, rgba(191, 219, 254, 0.22), transparent 28%),
            radial-gradient(circle at bottom right, rgba(251, 146, 60, 0.18), transparent 34%),
            linear-gradient(145deg, #172554 0%, #1e293b 44%, #9a3412 100%);
    }

    .mobile-scope-screen.scope-workstation .mobile-scope-card {
        background:
            radial-gradient(circle at top left, rgba(196, 181, 253, 0.18), transparent 28%),
            radial-gradient(circle at bottom right, rgba(250, 204, 21, 0.2), transparent 36%),
            linear-gradient(145deg, #111827 0%, #0f172a 48%, #166534 100%);
    }

    .mobile-scope-card-title {
        position: relative;
        z-index: 1;
        margin-top: 1rem;
        max-width: 12rem;
        font-size: 1.16rem;
        font-weight: 700;
        line-height: 1.1;
        letter-spacing: -0.03em;
        color: #fff;
    }

    .mobile-scope-card-value {
        position: relative;
        z-index: 1;
        margin-top: 0.52rem;
        font-size: 2rem;
        font-weight: 700;
        line-height: 0.95;
        letter-spacing: -0.05em;
        color: #fff;
    }

    .mobile-scope-card-caption {
        position: relative;
        z-index: 1;
        margin-top: 0.28rem;
        max-width: 14rem;
        font-size: 12px;
        line-height: 1.48;
        color: rgba(226, 232, 240, 0.82);
    }

    .mobile-scope-card-grid {
        position: relative;
        z-index: 1;
        margin-top: 1rem;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.7rem;
    }

    .mobile-scope-card-stat {
        border-top: 1px solid rgba(255, 255, 255, 0.14);
        padding-top: 0.56rem;
    }

    .mobile-scope-card-stat-label {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(226, 232, 240, 0.68);
    }

    .mobile-scope-card-stat-value {
        margin-top: 0.18rem;
        font-size: 13.5px;
        font-weight: 700;
        line-height: 1.16;
        letter-spacing: -0.02em;
        color: #fff;
    }

    .mobile-scope-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.44rem;
    }

    .mobile-scope-chip {
        display: inline-flex;
        align-items: center;
        min-height: 1.72rem;
        border-radius: 999px;
        border: 1px solid rgba(186, 230, 253, 0.9);
        background: rgba(255, 255, 255, 0.92);
        padding: 0.24rem 0.62rem;
        font-size: 10.5px;
        font-weight: 700;
        color: #0c4a6e;
    }

    .mobile-scope-loading {
        display: grid;
        gap: 0.9rem;
    }

    .mobile-scope-loading-card {
        position: relative;
        overflow: hidden;
        border-radius: 1.18rem;
        border: 1px solid rgba(226, 232, 240, 0.9);
        background: rgba(255, 255, 255, 0.97);
        padding: 0.96rem;
        box-shadow: 0 10px 18px rgba(15, 23, 42, 0.03);
    }

    .mobile-scope-loading-card.hero {
        padding: 1rem;
    }

    .mobile-scope-loading-card::after {
        content: '';
        position: absolute;
        inset: 0;
        transform: translateX(-100%);
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.72), transparent);
        animation: mobileScopeLoadingSweep 1.12s ease-in-out infinite;
    }

    .mobile-scope-loading-line,
    .mobile-scope-loading-chip,
    .mobile-scope-loading-grid-cell {
        background: linear-gradient(90deg, rgba(226, 232, 240, 0.84), rgba(241, 245, 249, 0.96), rgba(226, 232, 240, 0.84));
        background-size: 200% 100%;
        animation: mobileScopeLoadingPulse 1.35s ease-in-out infinite;
    }

    .mobile-scope-loading-line,
    .mobile-scope-loading-chip,
    .mobile-scope-loading-grid-cell {
        border-radius: 999px;
    }

    .mobile-scope-loading-chip {
        height: 1.5rem;
        width: 5rem;
    }

    .mobile-scope-loading-line {
        height: 0.84rem;
    }

    .mobile-scope-loading-line.title {
        margin-top: 0.65rem;
        width: 8.6rem;
        height: 1.16rem;
    }

    .mobile-scope-loading-line.body {
        margin-top: 0.45rem;
        width: 12.8rem;
    }

    .mobile-scope-loading-line.body.short {
        width: 9.6rem;
    }

    .mobile-scope-loading-grid {
        margin-top: 0.92rem;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.62rem;
    }

    .mobile-scope-loading-grid-cell {
        height: 2.6rem;
        border-radius: 0.88rem;
    }

    .mobile-scope-loading-metrics {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.66rem;
    }

    @keyframes mobileScopeLoadingSweep {
        100% {
            transform: translateX(100%);
        }
    }

    @keyframes mobileScopeLoadingPulse {
        0%,
        100% {
            background-position: 100% 50%;
        }

        50% {
            background-position: 0% 50%;
        }
    }

    .mobile-scope-metrics {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.66rem;
    }

    .mobile-scope-metric {
        border-radius: 1.02rem;
        border: 1px solid rgba(226, 232, 240, 0.9);
        background: rgba(255, 255, 255, 0.97);
        padding: 0.82rem 0.86rem;
        box-shadow: 0 10px 18px rgba(15, 23, 42, 0.03);
    }

    .mobile-scope-metric-label {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .mobile-scope-metric-value {
        margin-top: 0.26rem;
        font-size: 1.18rem;
        font-weight: 700;
        line-height: 1;
        letter-spacing: -0.03em;
        color: #0f172a;
    }

    .mobile-scope-metric-note {
        margin-top: 0.24rem;
        font-size: 11.3px;
        line-height: 1.42;
        color: #64748b;
    }

    .mobile-scope-actions {
        display: flex;
        gap: 0.62rem;
        overflow-x: auto;
        overflow-y: hidden;
        padding-bottom: 0.1rem;
        scroll-snap-type: x proximity;
        scroll-behavior: auto;
        overscroll-behavior-x: contain;
        -webkit-overflow-scrolling: touch;
        touch-action: auto;
        cursor: grab;
        scrollbar-width: none;
    }

    .mobile-scope-actions::-webkit-scrollbar {
        display: none;
    }

    .mobile-scope-actions.is-dragging,
    .mobile-scope-actions.is-pointer-down {
        cursor: grabbing;
        scroll-snap-type: none;
    }

    .mobile-scope-actions.is-dragging .mobile-scope-action,
    .mobile-scope-actions.is-pointer-down .mobile-scope-action {
        scroll-snap-align: none;
    }

    .mobile-scope-actions.is-dragging * {
        user-select: none;
    }

    .mobile-scope-action {
        user-select: none;
        -webkit-user-drag: none;
    }

    .mobile-scope-action {
        min-width: 11.2rem;
        max-width: 12rem;
        flex: 0 0 74%;
        scroll-snap-align: start;
        border-radius: 1.08rem;
        border: 1px solid rgba(125, 211, 252, 0.42);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(239, 246, 255, 0.99));
        padding: 0.86rem;
        box-shadow: 0 14px 24px rgba(15, 23, 42, 0.06);
    }

    .mobile-scope-screen.scope-facility .mobile-scope-action {
        border-color: rgba(56, 189, 248, 0.34);
        background: linear-gradient(180deg, rgba(248, 252, 255, 1), rgba(232, 245, 255, 1));
    }

    .mobile-scope-screen.scope-workgroup .mobile-scope-action {
        border-color: rgba(96, 165, 250, 0.3);
        background: linear-gradient(180deg, rgba(250, 251, 255, 1), rgba(245, 247, 255, 1));
    }

    .mobile-scope-screen.scope-workstation .mobile-scope-action {
        border-color: rgba(74, 222, 128, 0.26);
        background: linear-gradient(180deg, rgba(249, 254, 250, 1), rgba(241, 250, 244, 1));
    }

    .mobile-scope-action-title {
        margin-top: 0.5rem;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.25;
        color: #0f172a;
    }

    .mobile-scope-action-copy {
        margin-top: 0.22rem;
        font-size: 11.5px;
        line-height: 1.45;
        color: #64748b;
    }

    .mobile-scope-section {
        border-radius: 1.18rem;
        border: 1px solid rgba(226, 232, 240, 0.92);
        background: rgba(255, 255, 255, 0.97);
        padding: 0.94rem;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.03);
    }

    .mobile-scope-section-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.8rem;
    }

    .mobile-scope-section-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .mobile-scope-section-title {
        margin-top: 0.24rem;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: -0.02em;
        color: #0f172a;
    }

    .mobile-scope-section-copy {
        margin-top: 0.18rem;
        font-size: 11.8px;
        line-height: 1.5;
        color: #64748b;
    }

    .mobile-scope-section-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 1.96rem;
        border-radius: 999px;
        border: 1px solid rgba(191, 219, 254, 0.95);
        background: rgba(248, 250, 252, 0.95);
        padding: 0.34rem 0.7rem;
        font-size: 10.5px;
        font-weight: 700;
        color: #0c4a6e;
        white-space: nowrap;
    }

    .mobile-scope-preview-list {
        display: grid;
        gap: 0.62rem;
        margin-top: 0.88rem;
    }

    .mobile-scope-preview-item {
        display: block;
        border-radius: 1rem;
        border: 1px solid rgba(226, 232, 240, 0.9);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.9));
        padding: 0.8rem 0.82rem;
        transition: transform 140ms ease, box-shadow 140ms ease, border-color 140ms ease;
    }

    .mobile-scope-preview-item:active {
        transform: scale(0.994);
    }

    .mobile-scope-preview-item.attention {
        border-color: rgba(251, 113, 133, 0.2);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255, 241, 242, 0.58));
    }

    .mobile-scope-preview-item.featured {
        padding: 0.96rem;
        border-color: rgba(125, 211, 252, 0.34);
        background:
            radial-gradient(circle at top right, rgba(186, 230, 253, 0.18), transparent 32%),
            linear-gradient(180deg, rgba(255, 255, 255, 1), rgba(241, 245, 249, 0.98));
        box-shadow: 0 14px 24px rgba(15, 23, 42, 0.04);
    }

    .mobile-scope-screen.scope-facility .mobile-scope-preview-item.featured {
        border-color: rgba(14, 165, 233, 0.24);
    }

    .mobile-scope-screen.scope-workgroup .mobile-scope-preview-item.featured {
        border-color: rgba(59, 130, 246, 0.22);
    }

    .mobile-scope-screen.scope-workstation .mobile-scope-preview-item.featured {
        border-color: rgba(34, 197, 94, 0.22);
    }

    .mobile-scope-preview-meta {
        display: flex;
        align-items: center;
        gap: 0.42rem;
        flex-wrap: wrap;
    }

    .mobile-scope-preview-title {
        margin-top: 0.26rem;
        font-size: 13.5px;
        font-weight: 700;
        line-height: 1.28;
        letter-spacing: -0.02em;
        color: #0f172a;
    }

    .mobile-scope-preview-subtitle {
        margin-top: 0.18rem;
        font-size: 11.6px;
        line-height: 1.45;
        color: #64748b;
    }

    .mobile-scope-preview-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.72rem;
    }

    .mobile-scope-preview-arrow {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 1.82rem;
        width: 1.82rem;
        flex: 0 0 auto;
        border-radius: 999px;
        border: 1px solid rgba(226, 232, 240, 0.95);
        background: rgba(255, 255, 255, 0.96);
        font-size: 16px;
        line-height: 1;
        color: #64748b;
    }

    .mobile-scope-preview-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 0.34rem;
        margin-top: 0.58rem;
    }

    .mobile-scope-stat-pill {
        display: inline-flex;
        align-items: center;
        min-height: 1.56rem;
        border-radius: 999px;
        border: 1px solid rgba(226, 232, 240, 0.95);
        background: rgba(255, 255, 255, 0.94);
        padding: 0.2rem 0.52rem;
        font-size: 10px;
        font-weight: 700;
        color: #475569;
    }

    .mobile-scope-empty {
        margin-top: 0.88rem;
        border-radius: 1rem;
        border: 1px dashed rgba(191, 219, 254, 0.9);
        background: rgba(248, 250, 252, 0.75);
        padding: 0.9rem;
        font-size: 12px;
        line-height: 1.5;
        color: #64748b;
        text-align: center;
    }

    .mobile-scope-pulse-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.66rem;
    }

    .mobile-scope-pulse {
        border-radius: 1.08rem;
        border: 1px solid rgba(226, 232, 240, 0.92);
        background: rgba(255, 255, 255, 0.98);
        padding: 0.86rem;
        box-shadow: 0 10px 18px rgba(15, 23, 42, 0.03);
    }

    .mobile-scope-pulse-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .mobile-scope-pulse-title {
        margin-top: 0.34rem;
        font-size: 13.5px;
        font-weight: 700;
        line-height: 1.28;
        color: #0f172a;
    }

    .mobile-scope-pulse-copy {
        margin-top: 0.18rem;
        font-size: 11.6px;
        line-height: 1.46;
        color: #64748b;
    }

    .mobile-scope-pulse-pill {
        display: inline-flex;
        align-items: center;
        min-height: 1.62rem;
        margin-top: 0.62rem;
        border-radius: 999px;
        padding: 0.22rem 0.58rem;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .mobile-scope-pulse-pill.alert {
        background: rgba(244, 63, 94, 0.1);
        color: #be123c;
    }

    .mobile-scope-pulse-pill.activity {
        background: rgba(14, 165, 233, 0.1);
        color: #0369a1;
    }
</style>

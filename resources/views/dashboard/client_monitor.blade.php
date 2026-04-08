@include('common.navigations.header')

<style>
    .terminal-monitor-wrap {
        height: calc(100vh - 112px);
        min-height: 620px;
        overflow: hidden;
        border: 1px solid #0f291f;
        border-radius: 18px;
        background: #020704;
        box-shadow: inset 0 0 0 1px rgba(16, 185, 129, 0.1);
        color: #86efac;
        font-family: Consolas, "Cascadia Mono", "Liberation Mono", Menlo, monospace;
        display: flex;
        flex-direction: column;
    }
    .terminal-topline {
        border-bottom: 1px solid #0f291f;
        padding: 10px 14px;
        background: #03110a;
        color: #bbf7d0;
        font-size: 12px;
        line-height: 1.4;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .terminal-screen {
        flex: 1;
        margin: 0;
        padding: 12px 14px;
        background:
            linear-gradient(rgba(16, 185, 129, 0.05) 1px, transparent 1px);
        background-size: 100% 22px;
        color: #dcfce7;
        font-size: 12px;
        line-height: 1.8;
        overflow: hidden;
        white-space: pre;
        text-shadow: 0 0 6px rgba(74, 222, 128, 0.22);
        user-select: text;
    }
    @media (max-width: 1024px) {
        .terminal-monitor-wrap {
            height: calc(100vh - 132px);
            min-height: 560px;
        }
        .terminal-screen {
            font-size: 11px;
            line-height: 1.7;
        }
    }
</style>

<section class="terminal-monitor-wrap">
    <div id="terminal-topline" class="terminal-topline">
        initializing realtime stream...
    </div>
    <pre id="terminal-screen" class="terminal-screen">loading latest lines...</pre>
</section>

<script>
    (function () {
        const endpoint = @json(url('api/client-monitor'));
        const topLine = document.getElementById('terminal-topline');
        const screen = document.getElementById('terminal-screen');
        let timer = null;

        function computeLineLimit() {
            const h = screen.clientHeight || 640;
            const lineHeight = 22;
            const reserve = 2;
            return Math.max(24, Math.min(80, Math.floor(h / lineHeight) - reserve));
        }

        function fmtSummary(summary) {
            return [
                `WS online/offline ${summary.workstations_online ?? 0}/${summary.workstations_offline ?? 0}`,
                `DISP connected/disconnected ${summary.displays_connected ?? 0}/${summary.displays_disconnected ?? 0}`,
                `DISP failed ${summary.displays_failed ?? 0}`,
                `SYNC 10m ok/fail ${summary.sync_last_10m_ok ?? 0}/${summary.sync_last_10m_fail ?? 0}`,
            ].join(' | ');
        }

        async function pull() {
            try {
                const lineLimit = computeLineLimit();
                const res = await fetch(`${endpoint}?line_limit=${lineLimit}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                    cache: 'no-store'
                });
                if (!res.ok) {
                    throw new Error(`http ${res.status}`);
                }
                const payload = await res.json();
                const lines = Array.isArray(payload.terminal_lines) ? payload.terminal_lines : [];
                screen.textContent = lines.length ? lines.join('\n') : 'no recent logs';
                topLine.textContent = `${new Date().toLocaleTimeString()} | ${fmtSummary(payload.summary || {})} | mode=terminal | latest-only`;
            } catch (err) {
                topLine.textContent = `${new Date().toLocaleTimeString()} | stream error: ${err.message}`;
                screen.textContent = 'failed to load terminal lines';
            }
        }

        function stopClientMonitorTimer() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        }

        function initClientMonitorPage() {
            stopClientMonitorTimer();
            pull();
            timer = window.setInterval(pull, 5000);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initClientMonitorPage, { once: true });
        } else {
            initClientMonitorPage();
        }

        window.addEventListener('resize', () => {
            pull();
        });

        window.addEventListener('beforeunload', () => {
            stopClientMonitorTimer();
        });

        window.clientMonitorPageCleanup = stopClientMonitorTimer;
    })();
</script>

@include('common.navigations.footer')

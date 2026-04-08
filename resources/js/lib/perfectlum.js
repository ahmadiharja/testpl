import Alpine from 'alpinejs';
import { Grid, html } from 'gridjs';
import 'gridjs/dist/theme/mermaid.css';

window.Alpine = Alpine;
window.gridjs = { Grid, html };
window.__perfectlumPageBoot = window.__perfectlumPageBoot || [];

const toneClasses = {
    danger: 'bg-rose-100 text-rose-700',
    warning: 'bg-amber-100 text-amber-700',
    success: 'bg-emerald-100 text-emerald-700',
    info: 'bg-sky-100 text-sky-700',
    neutral: 'bg-slate-100 text-slate-600',
};

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function badge(label, tone = 'neutral') {
    return `<span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold ${toneClasses[tone] || toneClasses.neutral}">${escapeHtml(label)}</span>`;
}

function buildServerUrl(baseUrl, params = {}) {
    const url = new URL(baseUrl, window.location.origin);
    Object.entries(params).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
            url.searchParams.set(key, value);
        }
    });
    return `${url.pathname}${url.search}`;
}

function createGrid(elementId, config) {
    const element = typeof elementId === 'string' ? document.getElementById(elementId) : elementId;
    if (!element) {
        return null;
    }

    const grid = new Grid({
        search: {
            enabled: true,
            debounceTimeout: 250,
            ...(config.search || {}),
        },
        sort: config.sort ?? { multiColumn: false },
        pagination: config.pagination ?? {
            enabled: true,
            limit: 10,
        },
        language: {
            search: {
                placeholder: 'Search records...',
            },
            pagination: {
                previous: 'Prev',
                next: 'Next',
                showing: 'Showing',
                results: () => 'results',
            },
            loading: 'Loading...',
            noRecordsFound: 'No matching records found',
            error: 'Unable to load data',
            ...(config.language || {}),
        },
        className: {
            table: 'min-w-full',
            ...(config.className || {}),
        },
        ...config,
    });

    grid.render(element);
    return grid;
}

async function request(url, options = {}) {
    const response = await fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            ...(options.headers || {}),
        },
        ...options,
    });

    const contentType = response.headers.get('content-type') || '';
    const payload = contentType.includes('application/json') ? await response.json() : await response.text();

    if (!response.ok) {
        throw new Error(typeof payload === 'string' ? payload : payload.message || 'Request failed');
    }

    return payload;
}

async function postForm(url, formData) {
    return request(url, {
        method: 'POST',
        body: formData,
    });
}

function remountGrid(elementId, init) {
    const element = document.getElementById(elementId);
    if (!element) {
        return null;
    }

    const freshElement = element.cloneNode(false);
    freshElement.id = elementId;
    element.parentNode?.replaceChild(freshElement, element);

    if (typeof init === 'function') {
        init(freshElement);
    }

    return freshElement;
}

let structureMapModulePromise = null;
let schedulerCalendarModulePromise = null;
let currentMobilePageCleanup = null;
const dragScrollBindings = new WeakMap();
const dragScrollBindingCleanups = new Set();
let mobileShellBooted = false;
let desktopShellBooted = false;
let desktopNavigationPolishBooted = false;
let mobileDynamicHeadNodes = [];
const mobilePageCache = new Map();
const mobilePagePrefetchCache = new Map();
const mobilePagePrefetchInflight = new Map();
const desktopPrefetchedUrls = new Set();
let desktopNavigateInFlight = null;
let desktopNavigationProgressTimer = null;
let currentMobileSwapCleanup = null;
let idleWatcherBooted = false;
let desktopPageRuntimeCleanups = [];
let desktopActionMenuTarget = null;
const desktopSpaPilotRoutes = new Set([
    '/dashboard',
    '/facilities-management',
    '/workgroups',
    '/workstations',
    '/displays',
    '/histories-reports',
    '/users-management',
    '/display-calibration',
    '/scheduler',
    '/client-monitor',
    '/site-settings',
    '/global-settings',
    '/alert-settings',
    '/profile-settings',
    '/facility-info',
]);
const desktopSpaPilotRoutePrefixes = [
    '/facility-info/',
];

async function createStructureMapGraph(config) {
    if (!structureMapModulePromise) {
        structureMapModulePromise = import('./structure-map');
    }

    const module = await structureMapModulePromise;
    return module.createStructureMapGraph(config);
}

async function createSchedulerCalendar(config) {
    if (!schedulerCalendarModulePromise) {
        schedulerCalendarModulePromise = import('./scheduler-calendar');
    }

    const module = await schedulerCalendarModulePromise;
    return module.createSchedulerCalendar(config);
}

function cleanupMobilePage() {
    dragScrollBindingCleanups.forEach((cleanup) => {
        try {
            cleanup();
        } catch (error) {
            console.error('Drag scroll cleanup failed', error);
        }
    });
    dragScrollBindingCleanups.clear();

    if (typeof currentMobilePageCleanup === 'function') {
        try {
            currentMobilePageCleanup();
        } catch (error) {
            console.error('Mobile page cleanup failed', error);
        }
    }

    currentMobilePageCleanup = null;
}

function mountMobilePage(_key, init) {
    cleanupMobilePage();
    window.__perfectlumMobileInitTriggered = true;

    const cleanup = typeof init === 'function' ? init() : null;
    if (typeof cleanup === 'function') {
        currentMobilePageCleanup = cleanup;
    }

    if (window.lucide?.createIcons) {
        window.lucide.createIcons();
    }

    bindMobileDragScroll();
}

function bindMobileDragScroll(root = document) {
    root.querySelectorAll?.('[data-mobile-drag-scroll]').forEach((container) => {
        if (!container || dragScrollBindings.has(container)) {
            return;
        }

        let isPointerDown = false;
        let isDragging = false;
        let startX = 0;
        let startScrollLeft = 0;
        let suppressClick = false;
        let snapRestoreTimer = null;

        const reset = () => {
            isPointerDown = false;
            isDragging = false;
            container.classList.remove('is-dragging', 'is-pointer-down');
        };

        const beginDrag = (clientX) => {
            if (container.scrollWidth <= container.clientWidth + 2) {
                return;
            }

            window.clearTimeout(snapRestoreTimer);
            isPointerDown = true;
            isDragging = false;
            startX = clientX;
            startScrollLeft = container.scrollLeft;
            suppressClick = false;
            container.classList.add('is-pointer-down');
        };

        const moveDrag = (event) => {
            if (!isPointerDown) {
                return;
            }

            const deltaX = event.clientX - startX;
            if (!isDragging && Math.abs(deltaX) > 4) {
                isDragging = true;
                suppressClick = true;
                container.classList.add('is-dragging');
            }

            if (!isDragging) {
                return;
            }

            event.preventDefault();
            container.scrollLeft = startScrollLeft - deltaX;
        };

        const endDrag = () => {
            if (!isPointerDown) {
                return;
            }

            const didDrag = isDragging;
            reset();

            if (didDrag) {
                snapRestoreTimer = window.setTimeout(() => {
                    container.classList.remove('is-dragging');
                }, 120);
            }

            window.setTimeout(() => {
                suppressClick = false;
            }, 40);
        };

        const onMouseDown = (event) => {
            if (event.button !== 0) {
                return;
            }

            beginDrag(event.clientX);
        };

        const onMouseMove = (event) => {
            moveDrag(event);
        };

        const onMouseUp = () => {
            endDrag();
        };

        const onClickCapture = (event) => {
            if (!suppressClick) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
        };

        const onDragStart = (event) => {
            event.preventDefault();
        };

        const onSelectStart = (event) => {
            if (!isDragging) {
                return;
            }

            event.preventDefault();
        };

        container.addEventListener('mousedown', onMouseDown);
        document.addEventListener('mousemove', onMouseMove, { passive: false });
        document.addEventListener('mouseup', onMouseUp);
        container.addEventListener('click', onClickCapture, true);
        container.addEventListener('dragstart', onDragStart);
        container.addEventListener('selectstart', onSelectStart);

        const cleanup = () => {
            window.clearTimeout(snapRestoreTimer);
            container.removeEventListener('mousedown', onMouseDown);
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
            container.removeEventListener('click', onClickCapture, true);
            container.removeEventListener('dragstart', onDragStart);
            container.removeEventListener('selectstart', onSelectStart);
            reset();
            dragScrollBindings.delete(container);
            dragScrollBindingCleanups.delete(cleanup);
        };

        dragScrollBindings.set(container, cleanup);
        dragScrollBindingCleanups.add(cleanup);
    });
}

function registerAlpineData(name, factory) {
    const register = () => {
        if (window.Alpine) {
            window.Alpine.data(name, factory);
        }
    };

    if (window.Alpine) {
        register();
        return;
    }

    document.addEventListener('alpine:init', register, { once: true });
}

function bootIdleWatcher() {
    if (idleWatcherBooted) {
        return;
    }

    const body = document.body;
    if (!body) {
        return;
    }

    const timeoutMinutes = Number(body.dataset.idleLogoutMinutes || 0);
    const heartbeatSeconds = Number(body.dataset.idleHeartbeatSeconds || 0);
    const heartbeatUrl = body.dataset.idleHeartbeatUrl || '';
    const logoutUrl = body.dataset.idleLogoutUrl || '';
    const loginUrl = body.dataset.idleLoginUrl || logoutUrl;

    if (timeoutMinutes <= 0 || !heartbeatUrl || !logoutUrl) {
        return;
    }

    idleWatcherBooted = true;

    let logoutTimer = null;
    let lastHeartbeatAt = 0;
    let heartbeatInflight = false;
    let logoutTriggered = false;

    const timeoutMs = timeoutMinutes * 60 * 1000;
    const heartbeatMs = Math.max(15, heartbeatSeconds) * 1000;

    const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const redirectTo = (targetUrl) => {
        if (logoutTriggered) {
            return;
        }

        logoutTriggered = true;
        window.location.assign(targetUrl || logoutUrl || loginUrl || '/logout');
    };

    const scheduleLogout = () => {
        window.clearTimeout(logoutTimer);
        logoutTimer = window.setTimeout(() => {
            redirectTo(logoutUrl);
        }, timeoutMs);
    };

    const sendHeartbeat = async (force = false) => {
        if (logoutTriggered || heartbeatInflight || document.visibilityState === 'hidden') {
            return;
        }

        const now = Date.now();
        if (!force && now - lastHeartbeatAt < heartbeatMs) {
            return;
        }

        heartbeatInflight = true;

        try {
            const response = await fetch(heartbeatUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ ping: true }),
            });

            if (response.status === 401) {
                const payload = await response.json().catch(() => null);
                redirectTo(payload?.redirect || `${loginUrl}${loginUrl.includes('?') ? '&' : '?'}reason=inactive`);
                return;
            }

            if (response.ok) {
                lastHeartbeatAt = now;
            }
        } catch (_error) {
            // Ignore transient heartbeat failures; server-side expiry still enforces logout.
        } finally {
            heartbeatInflight = false;
        }
    };

    const markActivity = (forceHeartbeat = false) => {
        if (logoutTriggered) {
            return;
        }

        scheduleLogout();
        void sendHeartbeat(forceHeartbeat);
    };

    ['pointerdown', 'keydown', 'scroll', 'touchstart'].forEach((eventName) => {
        window.addEventListener(eventName, () => markActivity(false), { passive: true });
    });

    window.addEventListener('focus', () => markActivity(true));
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            markActivity(true);
        }
    });

    scheduleLogout();
    lastHeartbeatAt = Date.now();
    void sendHeartbeat(true);
}

function createNodeFromMarkup(markup) {
    const template = document.createElement('template');
    template.innerHTML = markup.trim();
    return template.content.firstChild;
}

function serializeMarkupNodes(nodes) {
    return nodes
        .map((node) => {
            if (!node) {
                return null;
            }

            if (node.nodeType === Node.ELEMENT_NODE) {
                return node.outerHTML;
            }

            if (node.nodeType === Node.TEXT_NODE && node.textContent?.trim()) {
                return node.textContent;
            }

            return null;
        })
        .filter(Boolean);
}

function setBottomNavActive(activeKey = '') {
    const currentNav = document.getElementById('mobile-bottom-nav');
    const currentSurface = document.getElementById('mobile-bottom-nav-surface');

    if (!currentNav) {
        return;
    }

    currentNav.hidden = false;
    currentNav.style.removeProperty('display');
    currentNav.style.removeProperty('visibility');
    currentNav.style.removeProperty('opacity');

    if (currentSurface) {
        currentSurface.classList.remove('opacity-0', 'pointer-events-none', 'invisible');
        currentSurface.classList.add('opacity-100');
    }

    currentNav.querySelectorAll('[data-mobile-bottom-key]').forEach((item) => {
        item.classList.toggle('active', item.dataset.mobileBottomKey === activeKey);
    });
}

function captureCurrentPageSnapshot() {
    const appbarShell = document.getElementById('mobile-appbar-shell');
    const mainContent = document.getElementById('mobile-main-content');
    const modalRoot = document.getElementById('mobile-modal-root');
    const runtimeScripts = document.getElementById('mobile-page-runtime-scripts');

    if (!appbarShell || !mainContent || !modalRoot || !runtimeScripts) {
        return null;
    }

    return {
        url: window.location.href,
        title: document.title,
        appbarClassName: appbarShell.className,
        appbarHtml: appbarShell.innerHTML,
        mainClassName: mainContent.className,
        mainHtml: mainContent.innerHTML,
        modalHtml: modalRoot.innerHTML,
        activeBottomKey: document.querySelector('#mobile-bottom-nav [data-mobile-bottom-key].active')?.dataset.mobileBottomKey || '',
        headMarkup: serializeMarkupNodes(mobileDynamicHeadNodes),
        scriptMarkup: serializeMarkupNodes(Array.from(runtimeScripts.children)),
        scrollY: window.scrollY || 0,
    };
}

function storeCurrentPageSnapshot(url = window.location.href) {
    const snapshot = captureCurrentPageSnapshot();
    if (!snapshot) {
        return;
    }

    snapshot.url = url;
    mobilePageCache.set(url, snapshot);
}

function mountSnapshotHead(headMarkup = []) {
    mobileDynamicHeadNodes.forEach((node) => node.remove());
    mobileDynamicHeadNodes = [];

    headMarkup.forEach((markup) => {
        const node = createNodeFromMarkup(markup);
        if (!node) {
            return;
        }

        if (node.nodeType === Node.ELEMENT_NODE) {
            node.dataset.mobileDynamicHead = '1';
        }

        document.head.appendChild(node);
        mobileDynamicHeadNodes.push(node);
    });
}

function appendDynamicHeadMarkup(headMarkup = []) {
    const appendedNodes = [];

    headMarkup.forEach((markup) => {
        const node = createNodeFromMarkup(markup);
        if (!node) {
            return;
        }

        if (node.nodeType === Node.ELEMENT_NODE) {
            node.dataset.mobileDynamicHead = '1';
        }

        document.head.appendChild(node);
        appendedNodes.push(node);
    });

    return appendedNodes;
}

function mountRuntimeScripts(scriptMarkup = [], runtimeScripts) {
    runtimeScripts.innerHTML = '';

    scriptMarkup.forEach((markup) => {
        const node = createNodeFromMarkup(markup);
        if (!node || node.nodeName !== 'SCRIPT') {
            return;
        }

        const script = document.createElement('script');
        Array.from(node.attributes || []).forEach((attribute) => {
            script.setAttribute(attribute.name, attribute.value);
        });
        script.textContent = node.textContent || '';
        runtimeScripts.appendChild(script);
    });
}

function isCommentNode(node, value) {
    return node?.nodeType === Node.COMMENT_NODE && node.nodeValue?.trim() === value;
}

function markedNodes(root, startMarker, endMarker) {
    const nodes = [];
    let collecting = false;

    for (const node of root.childNodes) {
        if (isCommentNode(node, startMarker)) {
            collecting = true;
            continue;
        }

        if (isCommentNode(node, endMarker)) {
            break;
        }

        if (collecting) {
            nodes.push(node);
        }
    }

    return nodes;
}

function resetBottomNav(doc) {
    const nextActive = doc.querySelector('#mobile-bottom-nav [data-mobile-bottom-key].active')?.dataset.mobileBottomKey || '';
    setBottomNavActive(nextActive);
}

function syncElementAttributes(currentElement, nextElement, { preserve = ['id'] } = {}) {
    if (!currentElement || !nextElement) {
        return;
    }

    Array.from(currentElement.attributes).forEach((attribute) => {
        if (preserve.includes(attribute.name)) {
            return;
        }

        if (!nextElement.hasAttribute(attribute.name)) {
            currentElement.removeAttribute(attribute.name);
        }
    });

    Array.from(nextElement.attributes).forEach((attribute) => {
        if (preserve.includes(attribute.name)) {
            return;
        }

        currentElement.setAttribute(attribute.name, attribute.value);
    });
}

function syncAppbar(doc) {
    const currentShell = document.getElementById('mobile-appbar-shell');
    const currentBack = document.getElementById('mobile-appbar-back');
    const currentTitle = document.getElementById('mobile-appbar-title');
    const currentProfile = document.getElementById('mobile-appbar-profile');

    const nextShell = doc.getElementById('mobile-appbar-shell');
    const nextBack = doc.getElementById('mobile-appbar-back');
    const nextTitle = doc.getElementById('mobile-appbar-title');
    const nextProfile = doc.getElementById('mobile-appbar-profile');

    if (!currentShell || !nextShell || !currentBack || !nextBack || !currentTitle || !nextTitle || !currentProfile || !nextProfile) {
        return false;
    }

    syncElementAttributes(currentShell, nextShell, { preserve: ['id'] });
    syncElementAttributes(currentBack, nextBack, { preserve: ['id'] });
    syncElementAttributes(currentTitle, nextTitle, { preserve: ['id'] });
    syncElementAttributes(currentProfile, nextProfile, { preserve: ['id'] });

    currentTitle.textContent = nextTitle.textContent || '';
    currentBack.innerHTML = nextBack.innerHTML;
    currentProfile.innerHTML = nextProfile.innerHTML;

    return true;
}

function applyDynamicHead(doc, runtimeScripts) {
    mobileDynamicHeadNodes.forEach((node) => node.remove());
    mobileDynamicHeadNodes = [];

    const nextHeadNodes = markedNodes(doc.head, 'mobile-page-head-start', 'mobile-page-head-end');

    nextHeadNodes.forEach((node) => {
        if (node.nodeType === Node.TEXT_NODE && !node.textContent?.trim()) {
            return;
        }

        if (node.nodeName === 'SCRIPT') {
            const script = document.createElement('script');
            Array.from(node.attributes || []).forEach((attribute) => {
                script.setAttribute(attribute.name, attribute.value);
            });
            script.textContent = node.textContent || '';
            runtimeScripts.appendChild(script);
            return;
        }

        const clone = node.cloneNode(true);
        clone.dataset.mobileDynamicHead = '1';
        document.head.appendChild(clone);
        mobileDynamicHeadNodes.push(clone);
    });
}

function runPageScripts(doc, runtimeScripts) {
    runtimeScripts.innerHTML = '';

    const scriptNodes = markedNodes(doc.body, 'mobile-page-scripts-start', 'mobile-page-scripts-end');
    scriptNodes.forEach((node) => {
        if (node.nodeType === Node.TEXT_NODE && !node.textContent?.trim()) {
            return;
        }

        if (node.nodeName !== 'SCRIPT') {
            return;
        }

        const script = document.createElement('script');
        Array.from(node.attributes || []).forEach((attribute) => {
            script.setAttribute(attribute.name, attribute.value);
        });
        script.textContent = node.textContent || '';
        runtimeScripts.appendChild(script);
    });
}

function stripCloneIdentifiers(root) {
    if (!root || root.nodeType !== Node.ELEMENT_NODE) {
        return;
    }

    root.removeAttribute('id');
    root.querySelectorAll('[id]').forEach((node) => node.removeAttribute('id'));
}

function animateMobileSwap(direction, animatedRoot, render, options = {}) {
    if (!animatedRoot || typeof render !== 'function') {
        render?.();
        return;
    }

    if (typeof currentMobileSwapCleanup === 'function') {
        currentMobileSwapCleanup();
    }

    const parent = animatedRoot.parentElement;

    if (!parent) {
        render();
        return;
    }

    const computedParentPosition = window.getComputedStyle(parent).position;
    const shouldSetParentRelative = computedParentPosition === 'static';
    const previousParentPosition = parent.style.position;
    const previousParentMinHeight = parent.style.minHeight;
    const previousParentOverflow = parent.style.overflow;
    const previousRootPosition = animatedRoot.style.position;
    const previousRootZIndex = animatedRoot.style.zIndex;
    const previousRootWidth = animatedRoot.style.width;
    const previousRootPointerEvents = animatedRoot.style.pointerEvents;
    const previousRootTransform = animatedRoot.style.transform;
    const previousRootOpacity = animatedRoot.style.opacity;
    const previousRootTransition = animatedRoot.style.transition;
    const previousRootBackground = animatedRoot.style.background;
    const previousRootMinHeight = animatedRoot.style.minHeight;
    const currentHeight = animatedRoot.offsetHeight || 0;
    const currentWidth = animatedRoot.offsetWidth || parent.clientWidth || 0;
    const snapshotLayer = animatedRoot.cloneNode(true);
    const usesBlankForwardBackdrop = direction === 'forward';
    const enterFromX = direction === 'back' ? '0%' : '100%';
    const exitToX = direction === 'back' ? '100%' : '-18%';
    const durationMs = Number(options.durationMs || 320);
    const onAfterCleanup = typeof options.onAfterCleanup === 'function' ? options.onAfterCleanup : null;
    const easing = 'cubic-bezier(0.22, 1, 0.36, 1)';

    stripCloneIdentifiers(snapshotLayer);
    snapshotLayer.setAttribute('aria-hidden', 'true');
    snapshotLayer.dataset.mobileTransitionLayer = '1';
    if (usesBlankForwardBackdrop) {
        snapshotLayer.innerHTML = '';
    }

    if (shouldSetParentRelative) {
        parent.style.position = 'relative';
    }
    parent.style.overflow = 'hidden';

    if (currentHeight) {
        parent.style.minHeight = `${currentHeight}px`;
    }

    Object.assign(snapshotLayer.style, {
        position: 'absolute',
        inset: '0',
        width: currentWidth ? `${currentWidth}px` : '100%',
        minHeight: currentHeight ? `${currentHeight}px` : '100%',
        pointerEvents: 'none',
        zIndex: direction === 'back' ? '30' : '20',
        background: 'var(--mobile-bg, #f6f8fc)',
        overflow: 'hidden',
        transform: 'translateX(0)',
        opacity: '1',
        transition: direction === 'back'
            ? `transform ${durationMs}ms ${easing}`
            : `transform ${durationMs}ms ${easing}, opacity ${durationMs}ms ${easing}`,
        boxShadow: direction === 'back' ? '-14px 0 32px rgba(15, 23, 42, 0.10)' : 'none',
    });

    Object.assign(animatedRoot.style, {
        position: 'relative',
        zIndex: direction === 'back' ? '20' : '30',
        width: '100%',
        pointerEvents: 'none',
        transform: `translateX(${enterFromX})`,
        opacity: '1',
        transition: 'none',
        background: 'var(--mobile-bg, #f6f8fc)',
        minHeight: currentHeight ? `${currentHeight}px` : previousRootMinHeight,
    });

    parent.appendChild(snapshotLayer);

    render();

    const nextHeight = animatedRoot.offsetHeight || currentHeight;
    const targetMinHeight = Math.max(currentHeight, nextHeight);
    if (nextHeight) {
        parent.style.minHeight = `${targetMinHeight}px`;
    }
    if (targetMinHeight) {
        snapshotLayer.style.minHeight = `${targetMinHeight}px`;
        animatedRoot.style.minHeight = `${targetMinHeight}px`;
    }

    animatedRoot.style.transition = direction === 'back'
        ? 'none'
        : `transform ${durationMs}ms ${easing}`;

    window.requestAnimationFrame(() => {
        window.requestAnimationFrame(() => {
            if (!usesBlankForwardBackdrop) {
                snapshotLayer.style.transform = `translateX(${exitToX})`;
            }
            if (direction !== 'back') {
                animatedRoot.style.transform = 'translateX(0)';
            }
        });
    });

    const cleanup = () => {
        if (currentMobileSwapCleanup === cleanup) {
            currentMobileSwapCleanup = null;
        }

        snapshotLayer.remove();
        animatedRoot.style.position = previousRootPosition;
        animatedRoot.style.zIndex = previousRootZIndex;
        animatedRoot.style.width = previousRootWidth;
        animatedRoot.style.pointerEvents = previousRootPointerEvents;
        animatedRoot.style.transform = previousRootTransform;
        animatedRoot.style.opacity = previousRootOpacity;
        animatedRoot.style.transition = previousRootTransition;
        animatedRoot.style.background = previousRootBackground;
        animatedRoot.style.minHeight = previousRootMinHeight;
        parent.style.minHeight = previousParentMinHeight;
        parent.style.overflow = previousParentOverflow;
        if (shouldSetParentRelative) {
            parent.style.position = previousParentPosition;
        }

        onAfterCleanup?.();
    };

    currentMobileSwapCleanup = cleanup;
    window.setTimeout(cleanup, durationMs + 40);
}

function restorePageSnapshot(snapshot, { direction = 'back', durationMs = null } = {}) {
    const stage = document.getElementById('mobile-page-stage');
    const appbarShell = document.getElementById('mobile-appbar-shell');
    const mainContent = document.getElementById('mobile-main-content');
    const modalRoot = document.getElementById('mobile-modal-root');
    const runtimeScripts = document.getElementById('mobile-page-runtime-scripts');

    if (!snapshot || !stage || !appbarShell || !mainContent || !modalRoot || !runtimeScripts) {
        return false;
    }

    let previousHeadNodes = null;
    let appendedHeadNodes = null;

    if (direction === 'back') {
        previousHeadNodes = [...mobileDynamicHeadNodes];
        appendedHeadNodes = appendDynamicHeadMarkup(snapshot.headMarkup || []);
    }

    animateMobileSwap(direction, stage, () => {
        cleanupMobilePage();
        window.__perfectlumMobileInitTriggered = false;

        if (direction !== 'back') {
            mountSnapshotHead(snapshot.headMarkup || []);
        }
        appbarShell.className = snapshot.appbarClassName || appbarShell.className;
        appbarShell.innerHTML = snapshot.appbarHtml || appbarShell.innerHTML;
        mainContent.className = snapshot.mainClassName || mainContent.className;
        mainContent.innerHTML = snapshot.mainHtml || '';
        modalRoot.innerHTML = snapshot.modalHtml || '';
        document.title = snapshot.title || document.title;
        setBottomNavActive(snapshot.activeBottomKey || '');
        mountRuntimeScripts(snapshot.scriptMarkup || [], runtimeScripts);

        if (!window.__perfectlumMobileInitTriggered && window.Alpine) {
            window.Alpine.initTree(mainContent);
        }

        if (window.lucide?.createIcons) {
            window.lucide.createIcons();
        }

        window.scrollTo(0, snapshot.scrollY || 0);
    }, {
        durationMs,
        onAfterCleanup: () => {
            if (direction === 'back') {
                previousHeadNodes?.forEach((node) => node.remove());
                mobileDynamicHeadNodes = appendedHeadNodes || [];
            }
        },
    });

    return true;
}

function getMobileTransitionDuration(direction, fromUrl, toUrl) {
    if (!fromUrl || !toUrl) {
        return 320;
    }

    return 320;
}

function shouldHandleMobileUrl(url) {
    return url.origin === window.location.origin && /^\/m(\/|$)/.test(url.pathname);
}

async function fetchMobilePagePayload(targetUrl) {
    const url = new URL(targetUrl, window.location.href);
    const response = await fetch(url.toString(), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-Mobile-SPA': '1',
            'Accept': 'text/html,application/xhtml+xml',
        },
        credentials: 'same-origin',
    });

    const htmlText = await response.text();
    const contentType = response.headers.get('content-type') || '';

    if (!response.ok || !/text\/html|application\/xhtml\+xml/i.test(contentType)) {
        return null;
    }

    return {
        htmlText,
        responseUrl: response.url || url.toString(),
    };
}

async function prefetchMobilePage(targetUrl) {
    const url = new URL(targetUrl, window.location.href);
    if (!shouldHandleMobileUrl(url)) {
        return null;
    }

    const cacheKey = url.toString();
    if (mobilePagePrefetchCache.has(cacheKey)) {
        return mobilePagePrefetchCache.get(cacheKey);
    }

    if (mobilePagePrefetchInflight.has(cacheKey)) {
        return mobilePagePrefetchInflight.get(cacheKey);
    }

    const request = fetchMobilePagePayload(cacheKey)
        .then((payload) => {
            if (payload) {
                mobilePagePrefetchCache.set(cacheKey, payload);
            }

            return payload;
        })
        .finally(() => {
            mobilePagePrefetchInflight.delete(cacheKey);
        });

    mobilePagePrefetchInflight.set(cacheKey, request);
    return request;
}

function bootMobileShell() {
    if (mobileShellBooted) {
        return;
    }

    const stage = document.getElementById('mobile-page-stage');
    const appbarShell = document.getElementById('mobile-appbar-shell');
    const mainContent = document.getElementById('mobile-main-content');
    const modalRoot = document.getElementById('mobile-modal-root');
    const runtimeScripts = document.getElementById('mobile-page-runtime-scripts');

    if (!stage || !appbarShell || !mainContent || !modalRoot || !runtimeScripts) {
        return;
    }

    mobileShellBooted = true;
    history.replaceState({ mobileShell: true }, '', window.location.href);

    const navigate = async (targetUrl, options = {}) => {
        const {
            push = true,
            direction = 'forward',
            durationMs = 320,
        } = options;

        const url = new URL(targetUrl, window.location.href);
        if (!shouldHandleMobileUrl(url)) {
            window.location.href = url.toString();
            return;
        }

        if (push) {
            storeCurrentPageSnapshot(window.location.href);
        }

        const payload = mobilePagePrefetchCache.get(url.toString()) || await prefetchMobilePage(url.toString());
        if (!payload?.htmlText) {
            window.location.href = url.toString();
            return;
        }

        const doc = new DOMParser().parseFromString(payload.htmlText, 'text/html');
        const nextMain = doc.getElementById('mobile-main-content');
        const nextModalRoot = doc.getElementById('mobile-modal-root');

        if (!nextMain) {
            window.location.href = url.toString();
            return;
        }

        animateMobileSwap(direction, stage, () => {
            cleanupMobilePage();
            window.__perfectlumMobileInitTriggered = false;

            applyDynamicHead(doc, runtimeScripts);
            if (!syncAppbar(doc)) {
                const nextAppbar = doc.getElementById('mobile-appbar-shell');
                if (nextAppbar) {
                    appbarShell.className = nextAppbar.className;
                    appbarShell.innerHTML = nextAppbar.innerHTML;
                }
            }
            mainContent.className = nextMain.className;
            mainContent.innerHTML = nextMain.innerHTML;
            modalRoot.innerHTML = nextModalRoot?.innerHTML || '';
            document.title = doc.title || document.title;
            resetBottomNav(doc);
            runPageScripts(doc, runtimeScripts);

            if (!window.__perfectlumMobileInitTriggered && window.Alpine) {
                window.Alpine.initTree(mainContent);
            }

            if (window.lucide?.createIcons) {
                window.lucide.createIcons();
            }

            window.scrollTo(0, 0);
        }, { durationMs });

        if (push) {
            history.pushState({ mobileShell: true }, '', payload.responseUrl || url.toString());
        }
    };

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link) {
            return;
        }

        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        if (link.target === '_blank' || link.hasAttribute('download') || link.dataset.mobileSpa === 'off') {
            return;
        }

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#')) {
            return;
        }

        const url = new URL(href, window.location.href);
        if (!shouldHandleMobileUrl(url)) {
            return;
        }

        const isBackNav = link.dataset.mobileNav === 'back';
        const durationMs = getMobileTransitionDuration(
            isBackNav ? 'back' : 'forward',
            window.location.href,
            url.toString()
        );

        event.preventDefault();

        if (isBackNav) {
            const cachedSnapshot = mobilePageCache.get(url.toString());
            if (cachedSnapshot) {
                restorePageSnapshot(cachedSnapshot, { direction: 'back', durationMs });
                history.replaceState({ mobileShell: true }, '', url.toString());
                return;
            }

            navigate(url.toString(), {
                push: false,
                direction: 'back',
                durationMs,
            }).catch(() => {
                window.location.href = url.toString();
            });
            return;
        }

        navigate(url.toString(), {
            push: true,
            direction: 'forward',
            durationMs,
        }).catch(() => {
            window.location.href = url.toString();
        });
    });

    window.addEventListener('popstate', (event) => {
        if (window.__perfectlumIgnoreNextShellPopstate) {
            window.__perfectlumIgnoreNextShellPopstate = false;
            return;
        }

        const customPopstateHandler = window.__perfectlumMobileShellPopstateHandler;
        if (typeof customPopstateHandler === 'function') {
            try {
                if (customPopstateHandler(event) === true) {
                    return;
                }
            } catch (error) {
                console.error('Custom mobile popstate handler failed', error);
            }
        }

        if (event.state && event.state.perfectlumClientHierarchy) {
            return;
        }

        if (!shouldHandleMobileUrl(new URL(window.location.href))) {
            return;
        }

        const cachedSnapshot = mobilePageCache.get(window.location.href);
        if (cachedSnapshot) {
            restorePageSnapshot(cachedSnapshot, {
                direction: 'back',
                durationMs: getMobileTransitionDuration('back', document.referrer || window.location.href, window.location.href),
            });
            return;
        }

        navigate(window.location.href, {
            push: false,
            direction: 'back',
            durationMs: getMobileTransitionDuration('back', document.referrer || window.location.href, window.location.href),
        }).catch(() => {
            window.location.reload();
        });
    });
}

function shouldHandleDesktopUrl(url) {
    return url.origin === window.location.origin && !/^\/m(\/|$)/.test(url.pathname);
}

function shouldHandleDesktopSpaUrl(url) {
    return shouldHandleDesktopUrl(url)
        && (
            desktopSpaPilotRoutes.has(url.pathname)
            || desktopSpaPilotRoutePrefixes.some((prefix) => url.pathname.startsWith(prefix))
        );
}

async function fetchDesktopPagePayload(targetUrl) {
    const url = new URL(targetUrl, window.location.href);
    const response = await fetch(url.toString(), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-Desktop-SPA': '1',
            'Accept': 'text/html,application/xhtml+xml',
        },
        credentials: 'same-origin',
    });

    const htmlText = await response.text();
    const contentType = response.headers.get('content-type') || '';

    if (!response.ok || !/text\/html|application\/xhtml\+xml/i.test(contentType)) {
        return null;
    }

    return {
        htmlText,
        responseUrl: response.url || url.toString(),
    };
}

function cleanupDesktopPageRuntime() {
    [
        'schedulerPageCleanup',
        'calibrationPageCleanup',
        'clientMonitorPageCleanup',
        'dashboardPageCleanup',
        'historyPageCleanup',
        'usersPageCleanup',
    ].forEach((key) => {
        if (typeof window[key] === 'function') {
            try {
                window[key]();
            } catch (_) {
                // Ignore page-specific cleanup errors so navigation remains resilient.
            }
        }
    });

    desktopPageRuntimeCleanups.forEach((cleanup) => {
        try {
            cleanup();
        } catch (_) {
            // Keep navigation resilient if an old page listener was already removed.
        }
    });
    desktopPageRuntimeCleanups = [];
}

function executeDesktopScript(runtimeScripts, node) {
    const script = document.createElement('script');
    Array.from(node.attributes || []).forEach((attribute) => {
        script.setAttribute(attribute.name, attribute.value);
    });
    script.textContent = node.textContent || '';
    runtimeScripts.appendChild(script);
}

function runDesktopPageScripts(doc, runtimeScripts, contentScripts = []) {
    if (!runtimeScripts) {
        return;
    }

    cleanupDesktopPageRuntime();
    runtimeScripts.innerHTML = '';
    [
        'facilitiesPage',
        'workgroupsPage',
        'workstationsPage',
        'displaysPage',
        'usersPage',
        'dashboardPageMount',
        'dashboardPageCleanup',
        'schedulerPageMount',
        'calibrationPageMount',
        'schedulerPageCleanup',
        'calibrationPageCleanup',
        'clientMonitorPageCleanup',
        'historyPageCleanup',
        'usersPageCleanup',
        'fetch_schedule_workgroups',
        'fetch_schedule_workstations',
        'fetch_schedule_displays',
        'refreshDashboardGrids',
        'openDashboardStatModal',
        'closeDashboardStatModal',
        'changeDashboardNativeModalPage',
        'closeAlertPanel',
        'alert_form',
        'delete_record',
        'update_alert',
        'generate_password',
        'remove_image',
        'closeWorkgroupPanel',
        'openFacilityWorkgroupForm',
        'deleteFacilityWorkgroup',
        'openHierarchyEntity',
        'confirmDelete',
        'openDisplayModal',
        '__historyTableInitialized',
        'fetch_workgroups',
        'fetch_workstations',
        'fetch_displays_checklist',
    ].forEach((key) => {
        try {
            delete window[key];
        } catch (_) {
            window[key] = undefined;
        }
    });

    const sourceContainer = doc.getElementById('desktop-page-runtime-scripts');
    const stackedScripts = sourceContainer
        ? markedNodes(sourceContainer, 'desktop-page-scripts-start', 'desktop-page-scripts-end')
            .filter((node) => !(node.nodeType === Node.TEXT_NODE && !node.textContent?.trim()) && node.nodeName === 'SCRIPT')
        : [];
    const scriptNodes = [...contentScripts, ...stackedScripts];
    const originalAddEventListener = EventTarget.prototype.addEventListener;

    EventTarget.prototype.addEventListener = function (type, listener, options) {
        desktopPageRuntimeCleanups.push(() => this.removeEventListener(type, listener, options));
        return originalAddEventListener.call(this, type, listener, options);
    };

    try {
        scriptNodes.forEach((node) => executeDesktopScript(runtimeScripts, node));
    } finally {
        EventTarget.prototype.addEventListener = originalAddEventListener;
    }
}

function getDesktopActionConfigFromButton(button) {
    const configs = [
        {
            type: 'facility',
            idKey: 'facilityId',
            nameKey: 'facilityName',
            pageKey: 'facilitiesPage',
            editButtonId: 'facility-action-edit',
            deleteButtonId: 'facility-action-delete',
            editLabel: 'Edit Facility',
            deleteLabel: 'Delete Facility',
        },
        {
            type: 'workgroup',
            idKey: 'workgroupId',
            nameKey: 'workgroupName',
            pageKey: 'workgroupsPage',
            editButtonId: 'workgroup-action-edit',
            deleteButtonId: 'workgroup-action-delete',
            editLabel: 'Edit Workgroup',
            deleteLabel: 'Delete Workgroup',
        },
        {
            type: 'workstation',
            idKey: 'workstationId',
            nameKey: 'workstationName',
            pageKey: 'workstationsPage',
            editButtonId: 'workstation-action-edit',
            deleteButtonId: 'workstation-action-delete',
            editLabel: 'Edit Workstation',
            deleteLabel: 'Delete Workstation',
        },
        {
            type: 'display',
            idKey: 'displayId',
            nameKey: 'displayName',
            pageKey: 'displaysPage',
            editButtonId: 'display-action-edit',
            deleteButtonId: 'display-action-delete',
            editLabel: 'Edit Display',
            deleteLabel: 'Delete Display',
        },
        {
            type: 'user',
            idKey: 'userId',
            nameKey: 'userName',
            pageKey: 'usersPage',
            editButtonId: 'user-action-edit',
            deleteButtonId: 'user-action-delete',
            editLabel: 'Edit User',
            deleteLabel: 'Delete User',
        },
    ];

    return configs.find((item) => button?.dataset?.[item.idKey]) || null;
}

function safeDecodeDesktopDatasetValue(value) {
    const text = value || '';
    try {
        return decodeURIComponent(text);
    } catch (_) {
        return text;
    }
}

function getHierarchyModalData() {
    const modal = document.querySelector('[x-data="hierarchyModal()"]');
    try {
        return modal && window.Alpine?.$data ? window.Alpine.$data(modal) : null;
    } catch (_) {
        return null;
    }
}

function closeDesktopPersistentOverlays() {
    const hierarchy = getHierarchyModalData();
    if (hierarchy?.isOpen && typeof hierarchy.close === 'function') {
        hierarchy.close();
    }
    if (hierarchy) {
        hierarchy.displayStructureMapOpen = false;
        hierarchy.workgroupStructureMapOpen = false;
        hierarchy.workstationStructureMapOpen = false;
    }
}

function closeLegacyDesktopActionMenus() {
    ['facility', 'workgroup', 'workstation', 'display', 'user'].forEach((prefix) => {
        const overlay = document.getElementById(`${prefix}-action-overlay`);
        const menu = document.getElementById(`${prefix}-action-menu`);
        overlay?.classList.add('hidden');
        menu?.classList.add('hidden');
    });
}

function closeDesktopGlobalActionMenu() {
    desktopActionMenuTarget = null;
    const menu = document.getElementById('desktop-global-action-menu');
    menu?.classList.add('hidden');
}

function ensureDesktopGlobalActionMenu() {
    let menu = document.getElementById('desktop-global-action-menu');
    if (menu) {
        return menu;
    }

    menu = document.createElement('div');
    menu.id = 'desktop-global-action-menu';
    menu.className = 'hidden';
    menu.style.position = 'fixed';
    menu.style.zIndex = '50000';
    menu.style.width = '14rem';
    menu.style.borderRadius = '1rem';
    menu.style.border = '1px solid rgb(226 232 240)';
    menu.style.background = '#fff';
    menu.style.padding = '0.5rem';
    menu.style.boxShadow = '0 24px 80px -36px rgba(15, 23, 42, 0.55)';
    menu.innerHTML = `
        <button type="button" data-desktop-global-action="edit" class="flex w-full items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">
            <span aria-hidden="true">✎</span>
            <span data-desktop-global-action-label="edit">Edit</span>
        </button>
        <button type="button" data-desktop-global-action="delete" class="mt-1 flex w-full items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50">
            <span aria-hidden="true">×</span>
            <span data-desktop-global-action-label="delete">Delete</span>
        </button>
    `;
    document.body.appendChild(menu);

    menu.addEventListener('click', (event) => {
        const actionButton = event.target.closest('[data-desktop-global-action]');
        if (!actionButton || !desktopActionMenuTarget) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        const { config, id, name } = desktopActionMenuTarget;
        const page = window[config.pageKey];
        closeDesktopGlobalActionMenu();

        if (actionButton.dataset.desktopGlobalAction === 'edit') {
            page?.openEditModal?.(id, name);
            return;
        }

        if (actionButton.dataset.desktopGlobalAction === 'delete') {
            page?.openDeleteModal?.(id, name);
        }
    });

    return menu;
}

function openDesktopGlobalActionMenu(event, button, config) {
    const page = window[config.pageKey];
    if (typeof page?.openEditModal !== 'function' && typeof page?.openDeleteModal !== 'function') {
        return false;
    }

    const canEdit = !!document.getElementById(config.editButtonId) && typeof page.openEditModal === 'function';
    const canDelete = !!document.getElementById(config.deleteButtonId) && typeof page.openDeleteModal === 'function';
    if (!canEdit && !canDelete) {
        return false;
    }

    closeDesktopPersistentOverlays();
    closeLegacyDesktopActionMenus();

    const id = Number(button.dataset[config.idKey] || 0);
    const name = safeDecodeDesktopDatasetValue(button.dataset[config.nameKey] || '');
    desktopActionMenuTarget = { config, id, name };

    const menu = ensureDesktopGlobalActionMenu();
    const editButton = menu.querySelector('[data-desktop-global-action="edit"]');
    const deleteButton = menu.querySelector('[data-desktop-global-action="delete"]');
    menu.querySelector('[data-desktop-global-action-label="edit"]').textContent = config.editLabel;
    menu.querySelector('[data-desktop-global-action-label="delete"]').textContent = config.deleteLabel;
    editButton.classList.toggle('hidden', !canEdit);
    deleteButton.classList.toggle('hidden', !canDelete);

    const rect = button.getBoundingClientRect();
    const width = 224;
    const left = Math.min(Math.max(16, rect.right - width), window.innerWidth - width - 16);
    const top = Math.min(rect.bottom + 10, window.innerHeight - 120);
    menu.style.left = `${left}px`;
    menu.style.top = `${top}px`;
    menu.classList.remove('hidden');

    return true;
}

function rescueDesktopActionMenuClick(event) {
    const button = event.target?.closest?.('[data-action="menu"], [data-user-action]');
    if (!button || !document.getElementById('desktop-main-content')?.contains(button)) {
        return;
    }

    const config = getDesktopActionConfigFromButton(button);
    if (!config) {
        return;
    }

    if (openDesktopGlobalActionMenu(event, button, config)) {
        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation?.();
    }
}

function resolveDesktopActiveMenu(doc, pathname) {
    const title = (doc?.title || '').split('|')[0].trim();
    const titleMap = {
        'User Management': 'Users',
        'All Facilities': 'Facilities',
        'All Workgroups': 'Workgroups',
        'All Workstations': 'Workstations',
        'All Displays': 'Displays',
        'Facility Management': 'Facilities',
        'Histories & Reports': 'History & Reports',
        'History and Reports': 'History & Reports',
        'Displays Not Ok': 'Dashboard',
        'Displays Ok': 'Dashboard',
        'Search': 'Dashboard',
        'Schedule Tasks': 'Scheduler',
        'Global Settings': 'Application Settings',
        'Display Calibration': 'Calibrate Display',
    };

    if (title && titleMap[title]) {
        return titleMap[title];
    }

    if (title) {
        return title;
    }

    const pathMap = {
        '/dashboard': 'Dashboard',
        '/facilities-management': 'Facilities',
        '/workgroups': 'Workgroups',
        '/workstations': 'Workstations',
        '/displays': 'Displays',
        '/display-calibration': 'Calibrate Display',
        '/scheduler': 'Scheduler',
        '/histories-reports': 'History & Reports',
        '/users-management': 'Users',
        '/site-settings': 'Site Settings',
        '/global-settings': 'Application Settings',
        '/alert-settings': 'Alert Settings',
        '/client-monitor': 'Client Monitor',
    };

    return pathMap[pathname] || '';
}

function syncDesktopMenuState(doc, pathname) {
    const appRoot = document.querySelector('body[x-data]');
    const alpineData = window.Alpine?.$data ? window.Alpine.$data(appRoot) : appRoot?.__x?.$data;
    if (!alpineData) {
        return;
    }

    const nextMenu = resolveDesktopActiveMenu(doc, pathname);
    if (nextMenu) {
        alpineData.activeMenu = nextMenu;
    }

    const settingsMenus = ['Site Settings', 'Application Settings', 'Alert Settings', 'Client Monitor'];
    alpineData.settingsExpanded = settingsMenus.includes(alpineData.activeMenu);
}

function replaceDesktopShellFragment(selector, doc) {
    const current = document.querySelector(selector);
    const next = doc.querySelector(selector);

    if (!current || !next) {
        return false;
    }

    const currentParent = current.parentElement;
    if (!currentParent) {
        return false;
    }

    if (window.Alpine?.destroyTree) {
        window.Alpine.destroyTree(current);
    }

    const template = document.createElement('template');
    template.innerHTML = next.outerHTML.trim();
    const replacement = template.content.firstElementChild;

    if (!replacement) {
        return false;
    }

    current.replaceWith(replacement);

    if (window.Alpine?.initTree) {
        window.Alpine.initTree(replacement);
    }

    return true;
}

function syncDesktopShell(doc) {
    const currentSignature = document.body?.dataset?.desktopShellSignature || '';
    const nextSignature = doc.body?.dataset?.desktopShellSignature || '';

    if (!nextSignature || currentSignature === nextSignature) {
        return;
    }

    const sidebarUpdated = replaceDesktopShellFragment('#desktop-sidebar-shell', doc);
    const headerUpdated = replaceDesktopShellFragment('#desktop-top-header', doc);

    if (sidebarUpdated || headerUpdated) {
        document.body.dataset.desktopShellSignature = nextSignature;

        if (window.lucide?.createIcons) {
            window.lucide.createIcons();
        }
    }
}

function setDesktopLoadingState(isLoading) {
    const stage = document.getElementById('desktop-page-stage');
    const scrollArea = document.getElementById('desktop-scroll-area');
    const content = document.getElementById('desktop-main-content');
    if (!stage || !scrollArea) {
        return;
    }

    content?.classList.toggle('opacity-80', !!isLoading);
    scrollArea.classList.toggle('cursor-progress', !!isLoading);
    stage.setAttribute('aria-busy', isLoading ? 'true' : 'false');
}

function isDesktopNavigationPolishContext() {
    return !/^\/m(\/|$)/.test(window.location.pathname)
        && window.matchMedia('(min-width: 1024px)').matches;
}

function getSafeDesktopNavigationUrl(link) {
    if (!link || link.dataset.desktopPolish === 'off' || link.dataset.desktopSpa === 'off') {
        return null;
    }

    if (link.target && link.target !== '_self') {
        return null;
    }

    if (link.hasAttribute('download')) {
        return null;
    }

    const href = link.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:')) {
        return null;
    }

    const url = new URL(href, window.location.href);
    if (!shouldHandleDesktopUrl(url)) {
        return null;
    }

    if (url.pathname === '/logout' || url.pathname.startsWith('/api')) {
        return null;
    }

    if (url.pathname === window.location.pathname && url.search === window.location.search) {
        return null;
    }

    return url;
}

function injectDesktopNavigationPolish() {
    if (!document.getElementById('perfectlum-navigation-polish-style')) {
        const style = document.createElement('style');
        style.id = 'perfectlum-navigation-polish-style';
        style.textContent = `
        #perfectlum-navigation-progress {
            position: fixed;
            inset: 0 0 auto 0;
            z-index: 99999;
            height: 3px;
            pointer-events: none;
            background: linear-gradient(90deg, #0284c7, #38bdf8, #0ea5e9);
            box-shadow: 0 0 18px rgba(14, 165, 233, 0.35);
            opacity: 0;
            transform: scaleX(0);
            transform-origin: left center;
            transition: transform 520ms cubic-bezier(.2, .8, .2, 1), opacity 180ms ease;
        }

        html.perfectlum-navigation-warming #perfectlum-navigation-progress {
            opacity: .72;
            transform: scaleX(.24);
        }

        html.perfectlum-navigation-pending #perfectlum-navigation-progress {
            opacity: 1;
            transform: scaleX(.78);
            transition-duration: 760ms, 120ms;
        }

        html.perfectlum-navigation-pending body {
            cursor: progress;
        }

        @media (prefers-reduced-motion: reduce) {
            #perfectlum-navigation-progress {
                transition: none;
            }
        }
    `;
        document.head.appendChild(style);
    }

    if (!document.getElementById('perfectlum-navigation-progress')) {
        const progress = document.createElement('div');
        progress.id = 'perfectlum-navigation-progress';
        progress.setAttribute('aria-hidden', 'true');
        document.body.appendChild(progress);
    }
}

function startDesktopNavigationProgress({ pending = false } = {}) {
    injectDesktopNavigationPolish();
    window.clearTimeout(desktopNavigationProgressTimer);

    document.documentElement.classList.toggle('perfectlum-navigation-warming', !pending);
    document.documentElement.classList.toggle('perfectlum-navigation-pending', !!pending);

    if (!pending) {
        desktopNavigationProgressTimer = window.setTimeout(() => {
            document.documentElement.classList.remove('perfectlum-navigation-warming');
        }, 900);
    }
}

function stopDesktopNavigationProgress() {
    window.clearTimeout(desktopNavigationProgressTimer);
    document.documentElement.classList.remove('perfectlum-navigation-warming', 'perfectlum-navigation-pending');
}

function prefetchDesktopNavigationUrl(url) {
    const href = url.toString();
    if (desktopPrefetchedUrls.has(href)) {
        return;
    }

    desktopPrefetchedUrls.add(href);
    startDesktopNavigationProgress();

    const link = document.createElement('link');
    link.rel = 'prefetch';
    link.href = href;
    link.as = 'document';
    document.head.appendChild(link);
}

function bootDesktopNavigationPolish() {
    if (desktopNavigationPolishBooted || !isDesktopNavigationPolishContext()) {
        return;
    }

    desktopNavigationPolishBooted = true;
    injectDesktopNavigationPolish();

    const prefetchFromEvent = (event) => {
        const link = event.target.closest?.('a[href]');
        const url = getSafeDesktopNavigationUrl(link);
        if (!url) {
            return;
        }

        window.setTimeout(() => {
            if (link.matches(':hover') || document.activeElement === link) {
                prefetchDesktopNavigationUrl(url);
            }
        }, 80);
    };

    document.addEventListener('pointerover', prefetchFromEvent, { passive: true });
    document.addEventListener('focusin', prefetchFromEvent, { passive: true });

    document.addEventListener('click', (event) => {
        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        const link = event.target.closest?.('a[href]');
        const url = getSafeDesktopNavigationUrl(link);
        if (!url) {
            return;
        }

        startDesktopNavigationProgress({ pending: true });
    }, { capture: true });

    window.addEventListener('pageshow', stopDesktopNavigationProgress);
    window.addEventListener('pagehide', () => startDesktopNavigationProgress({ pending: true }));
}

function bootDesktopShell() {
    if (desktopShellBooted) {
        return;
    }

    const currentStage = document.getElementById('desktop-page-stage');
    const currentContent = document.getElementById('desktop-main-content');
    const runtimeScripts = document.getElementById('desktop-page-runtime-scripts');
    if (!currentStage || !currentContent || !runtimeScripts) {
        return;
    }

    if (!isDesktopNavigationPolishContext()) {
        return;
    }

    desktopShellBooted = true;

    const currentState = history.state || {};
    history.replaceState({ ...currentState, desktopShell: true }, '', window.location.href);

    document.addEventListener('click', rescueDesktopActionMenuClick, { capture: true });
    document.addEventListener('click', (event) => {
        if (!event.target.closest?.('#desktop-global-action-menu') && !event.target.closest?.('[data-action="menu"], [data-user-action]')) {
            closeDesktopGlobalActionMenu();
        }
    });

    const navigateDesktop = async (targetUrl, options = {}) => {
        const { push = true } = options;
        const target = new URL(targetUrl, window.location.href);

        if (!shouldHandleDesktopSpaUrl(target)) {
            window.location.href = target.toString();
            return;
        }

        if (desktopNavigateInFlight) {
            return;
        }

        desktopNavigateInFlight = target.toString();
        startDesktopNavigationProgress({ pending: true });
        setDesktopLoadingState(true);
        closeDesktopGlobalActionMenu();
        closeDesktopPersistentOverlays();

        try {
            const payload = await fetchDesktopPagePayload(target.toString());
            if (!payload?.htmlText) {
                window.location.href = target.toString();
                return;
            }

            const doc = new DOMParser().parseFromString(payload.htmlText, 'text/html');
            const nextContent = doc.getElementById('desktop-main-content');
            const nextRuntimeScripts = doc.getElementById('desktop-page-runtime-scripts');
            const content = document.getElementById('desktop-main-content');
            const scrollArea = document.getElementById('desktop-scroll-area');

            if (!nextContent || !nextRuntimeScripts || !content || !scrollArea) {
                window.location.href = target.toString();
                return;
            }

            const nextContentScripts = Array.from(nextContent.querySelectorAll('script'));
            nextContentScripts.forEach((script) => script.remove());

            if (window.Alpine?.destroyTree) {
                window.Alpine.destroyTree(content);
            }

            syncElementAttributes(content, nextContent, { preserve: ['id'] });
            content.innerHTML = nextContent.innerHTML;
            document.title = doc.title || document.title;

            runDesktopPageScripts(doc, runtimeScripts, nextContentScripts);

            if (window.Alpine?.initTree) {
                window.Alpine.initTree(content);
            }

            if (window.lucide?.createIcons) {
                window.lucide.createIcons();
            }

            [
                'dashboardPageMount',
                'schedulerPageMount',
                'calibrationPageMount',
            ].forEach((key) => {
                if (typeof window[key] === 'function') {
                    try {
                        window[key]();
                    } catch (_) {
                        // Allow the page to fallback gracefully if a mount hook fails.
                    }
                }
            });

            syncDesktopShell(doc);
            syncDesktopMenuState(doc, target.pathname);

            scrollArea.scrollTo({ top: 0, left: 0, behavior: 'auto' });

            if (push) {
                history.pushState({ desktopShell: true }, '', payload.responseUrl || target.toString());
            }
        } finally {
            desktopNavigateInFlight = null;
            setDesktopLoadingState(false);
            stopDesktopNavigationProgress();
        }
    };

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link) {
            return;
        }

        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        if (link.target === '_blank' || link.hasAttribute('download') || link.dataset.desktopSpa === 'off') {
            return;
        }

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript:')) {
            return;
        }

        const url = new URL(href, window.location.href);
        if (url.pathname === '/logout') {
            return;
        }

        if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash === window.location.hash) {
            return;
        }

        if (!shouldHandleDesktopSpaUrl(url)) {
            return;
        }

        event.preventDefault();
        navigateDesktop(url.toString(), { push: true }).catch(() => {
            window.location.href = url.toString();
        });
    });

    window.addEventListener('popstate', () => {
        if (!desktopSpaPilotRoutes.has(window.location.pathname)) {
            window.location.reload();
            return;
        }

        navigateDesktop(window.location.href, { push: false }).catch(() => {
            window.location.reload();
        });
    });
}

window.Perfectlum = {
    Alpine,
    Grid,
    html,
    escapeHtml,
    badge,
    buildServerUrl,
    createGrid,
    createSchedulerCalendar,
    createStructureMapGraph,
    request,
    postForm,
    remountGrid,
    cleanupMobilePage,
    mountMobilePage,
    registerAlpineData,
    bindMobileDragScroll,
    bootMobileShell,
    bootDesktopShell,
    bootDesktopNavigationPolish,
    bootIdleWatcher,
    animateMobileSwap,
    prefetchMobilePage,
};

if (Array.isArray(window.__perfectlumPageBoot) && window.__perfectlumPageBoot.length) {
    const pendingBoots = [...window.__perfectlumPageBoot];
    window.__perfectlumPageBoot.length = 0;

    pendingBoots.forEach((boot) => {
        try {
            if (typeof boot === 'function') {
                boot();
            }
        } catch (error) {
            console.error('Deferred mobile page boot failed', error);
        }
    });
}

Alpine.start();

const initMobileShell = () => {
    try {
        bootIdleWatcher();
        bootMobileShell();
        bootDesktopNavigationPolish();
        bootDesktopShell();
    } catch (error) {
        console.error('Shell boot failed', error);
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMobileShell, { once: true });
} else {
    initMobileShell();
}

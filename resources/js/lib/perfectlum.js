import Alpine from 'alpinejs';
import { Grid, html } from 'gridjs';
import 'gridjs/dist/theme/mermaid.css';

window.Alpine = Alpine;
window.gridjs = { Grid, html };

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
        return;
    }

    element.innerHTML = '';
    element._gi = false;
    init();
}

let structureMapModulePromise = null;
let schedulerCalendarModulePromise = null;

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
};

Alpine.start();

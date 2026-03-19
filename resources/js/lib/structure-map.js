import { Graph } from '@antv/x6/es/graph/graph';
import { HTML } from '@antv/x6/es/shape/html';

let registered = false;

function el(tag, className, text) {
    const node = document.createElement(tag);
    if (className) {
        node.className = className;
    }
    if (text !== undefined) {
        node.textContent = text;
    }
    return node;
}

function makeNonDraggable(node) {
    node.setAttribute('draggable', 'false');
    node.style.userSelect = 'none';
    node.style.webkitUserDrag = 'none';
    node.addEventListener('dragstart', (event) => {
        event.preventDefault();
    });
    return node;
}

function finalizeNodeHtml(node) {
    makeNonDraggable(node);
    node.querySelectorAll('*').forEach((child) => {
        child.setAttribute('draggable', 'false');
        child.style.userSelect = 'none';
        child.style.webkitUserDrag = 'none';
    });
    return node;
}

function stageHtml(cell) {
    const data = cell.getData() || {};
    const wrap = makeNonDraggable(el('div', 'h-full w-full rounded-2xl border border-slate-200 bg-white px-5 py-4'));
    wrap.append(
        el('p', 'text-[11px] font-bold uppercase tracking-[0.16em] text-slate-400', data.kind || ''),
        el('p', 'mt-2 text-sm font-extrabold leading-snug text-slate-900', data.label || '')
    );
    return finalizeNodeHtml(wrap);
}

function displayHtml(cell) {
    const data = cell.getData() || {};
    const attention = data.statusTone === 'danger';
    const wrap = el(
        'div',
        `h-full w-full rounded-xl border px-3 py-2.5 ${
            data.active
                ? 'border-sky-300 bg-sky-50'
                : attention
                    ? 'border-rose-200 bg-rose-50/60'
                    : 'border-slate-200 bg-white'
        }`,
    );
    makeNonDraggable(wrap);

    const row = el('div', 'flex items-center gap-2');
    const icon = el(
        'div',
        `flex h-7 w-7 shrink-0 items-center justify-center rounded-lg ${
            data.active ? 'bg-sky-500 text-white' : attention ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 text-slate-500'
        }`,
    );
    icon.innerHTML = `
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-3 w-3">
            <rect x="3" y="4" width="18" height="12" rx="2"></rect>
            <path d="M8 20h8"></path>
            <path d="M12 16v4"></path>
        </svg>
    `;

    const body = el('div', 'min-w-0 flex-1');
    const top = el('div', 'flex items-start justify-between gap-2');
    const info = el('div', 'min-w-0 flex-1');
    info.append(el('p', `truncate text-[12px] font-bold leading-tight ${data.active ? 'text-sky-700' : attention ? 'text-rose-700' : 'text-slate-900'}`, data.label || 'Display'));

    const meta = el('div', 'mt-1 flex items-center gap-1 whitespace-nowrap');
    const health = el(
        'span',
        `inline-flex items-center gap-1 rounded-full px-1.5 py-0.5 text-[8px] font-bold uppercase tracking-[0.08em] ${
            data.statusTone === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'
        }`,
    );
    const healthDot = el('span', `inline-flex h-1.5 w-1.5 rounded-full ${data.statusTone === 'success' ? 'bg-emerald-500' : 'bg-rose-500'}`);
    health.append(healthDot, document.createTextNode(data.statusLabel || 'Unknown'));

    const connected = el('span', 'inline-flex items-center gap-1 rounded-full bg-slate-100 px-1.5 py-0.5 text-[8px] font-bold uppercase tracking-[0.08em] text-slate-500');
    const connectedDot = el('span', `inline-flex h-1.5 w-1.5 rounded-full ${data.connectedLabel === 'Online' ? 'bg-emerald-500' : 'bg-amber-500'}`);
    connected.append(connectedDot, document.createTextNode(data.connectedLabel || 'Offline'));

    const role = el('span', `text-[8px] font-semibold uppercase tracking-[0.08em] ${data.active ? 'text-sky-500' : 'text-slate-400'}`, data.active ? 'Current' : 'Sibling');
    meta.append(health, connected, role);
    info.append(meta);
    top.append(info);

    if (!data.active) {
        const button = el('button', 'inline-flex h-6 shrink-0 items-center gap-1 rounded-md border border-slate-200 bg-white px-1.5 py-0 text-[9px] font-semibold text-slate-600 transition hover:border-sky-200 hover:text-sky-700');
        button.type = 'button';
        button.dataset.action = 'open-display';
        button.dataset.displayId = String(data.id || '');
        button.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-2.5 w-2.5">
                <path d="M7 17L17 7"></path>
                <path d="M9 7h8v8"></path>
            </svg>
            Open
        `;
        top.append(button);
    }

    body.append(top);
    row.append(icon, body);
    wrap.append(row);
    return finalizeNodeHtml(wrap);
}

function wrapNodeLabel(text, limit = 16) {
    const value = String(text || '').trim();
    if (!value) {
        return '-';
    }
    if (value.length <= limit) {
        return value;
    }

    const parts = [];
    let current = '';
    value.split(/\s+/).forEach((word) => {
        if ((`${current} ${word}`).trim().length <= limit && parts.length < 1) {
            current = `${current} ${word}`.trim();
            return;
        }
        if (parts.length < 1) {
            parts.push(current || word);
            current = word === current ? '' : word;
            return;
        }
        current = `${current} ${word}`.trim();
    });

    if (current) {
        parts.push(current.length > limit + 4 ? `${current.slice(0, limit + 1)}...` : current);
    }

    return parts.slice(0, 2).join('\n');
}

function stageNodeAttrs(kind, label) {
    return {
        body: {
            fill: '#ffffff',
            stroke: '#e2e8f0',
            strokeWidth: 1.2,
            rx: 18,
            ry: 18,
            cursor: 'move',
        },
        kind: {
            text: String(kind || '').toUpperCase(),
            fill: '#94a3b8',
            fontSize: 10,
            fontWeight: 700,
            letterSpacing: 1.6,
            refX: 16,
            refY: 18,
            textAnchor: 'left',
            pointerEvents: 'none',
        },
        label: {
            text: wrapNodeLabel(label, 18),
            fill: '#0f172a',
            fontSize: 13,
            fontWeight: 800,
            refX: 16,
            refY: 36,
            textAnchor: 'left',
            whiteSpace: 'pre',
            pointerEvents: 'none',
        },
    };
}

function workstationNodeAttrs(data) {
    const attention = Number(data.attentionCount || 0) > 0;
    const active = !!data.active;
    const eyeStroke = '#64748b';
    return {
        body: {
            fill: active ? '#f0f9ff' : attention ? '#fff1f2' : '#ffffff',
            stroke: active ? '#7dd3fc' : attention ? '#fecdd3' : '#e2e8f0',
            strokeWidth: 1.2,
            rx: 20,
            ry: 20,
            cursor: 'move',
        },
        kind: {
            text: 'WORKSTATION',
            fill: '#94a3b8',
            fontSize: 10,
            fontWeight: 700,
            letterSpacing: 1.6,
            refX: 16,
            refY: 18,
            textAnchor: 'left',
            pointerEvents: 'none',
        },
        label: {
            text: wrapNodeLabel(data.label || 'Workstation', 18),
            fill: active ? '#0369a1' : '#0f172a',
            fontSize: 13,
            fontWeight: 800,
            refX: 16,
            refY: 36,
            textAnchor: 'left',
            whiteSpace: 'pre',
            pointerEvents: 'none',
        },
        toggleEyeOutline: {
            d: 'M239 20C241.8 16.2 245.3 14 249 14C252.7 14 256.2 16.2 259 20C256.2 23.8 252.7 26 249 26C245.3 26 241.8 23.8 239 20Z',
            fill: 'none',
            stroke: eyeStroke,
            strokeWidth: 1.5,
            strokeLinecap: 'round',
            strokeLinejoin: 'round',
            cursor: 'pointer',
        },
        toggleEyePupil: {
            cx: 249,
            cy: 20,
            r: 2.2,
            fill: eyeStroke,
            opacity: data.expanded ? 1 : 0.85,
            cursor: 'pointer',
        },
        toggleEyeSlash: {
            d: 'M241 26L257 14',
            fill: 'none',
            stroke: eyeStroke,
            strokeWidth: 1.6,
            strokeLinecap: 'round',
            strokeLinejoin: 'round',
            opacity: data.expanded ? 0 : 1,
            cursor: 'pointer',
        },
        healthyText: {
            text: `${data.healthyCount || 0} HEALTHY`,
            fill: '#10b981',
            fontSize: 9,
            fontWeight: 700,
            refX: 16,
            refY: 72,
            textAnchor: 'left',
            pointerEvents: 'none',
        },
        attentionText: {
            text: `${data.attentionCount || 0} ATTENTION`,
            fill: attention ? '#f43f5e' : '#94a3b8',
            fontSize: 9,
            fontWeight: 700,
            refX: 92,
            refY: 72,
            textAnchor: 'left',
            pointerEvents: 'none',
        },
        displaysText: {
            text: `${data.displayCount || 0} DISPLAYS`,
            fill: '#94a3b8',
            fontSize: 9,
            fontWeight: 700,
            refX: 188,
            refY: 72,
            textAnchor: 'left',
            pointerEvents: 'none',
        },
    };
}

function workgroupNodeAttrs(data) {
    const attention = Number(data.attentionCount || 0) > 0;
    const active = !!data.active;
    const eyeStroke = '#64748b';
    return {
        body: {
            fill: active ? '#f0f9ff' : attention ? '#fff1f2' : '#ffffff',
            stroke: active ? '#7dd3fc' : attention ? '#fecdd3' : '#e2e8f0',
            strokeWidth: 1.2,
            rx: 20,
            ry: 20,
            cursor: 'move',
        },
        kind: {
            text: 'WORKGROUP',
            fill: '#94a3b8',
            fontSize: 10,
            fontWeight: 700,
            letterSpacing: 1.6,
            refX: 16,
            refY: 18,
            textAnchor: 'left',
            pointerEvents: 'none',
        },
        label: {
            text: wrapNodeLabel(data.label || 'Workgroup', 18),
            fill: active ? '#0369a1' : '#0f172a',
            fontSize: 13,
            fontWeight: 800,
            refX: 16,
            refY: 36,
            textAnchor: 'left',
            whiteSpace: 'pre',
            pointerEvents: 'none',
        },
        toggleEyeOutline: {
            d: 'M259 20C261.8 16.2 265.3 14 269 14C272.7 14 276.2 16.2 279 20C276.2 23.8 272.7 26 269 26C265.3 26 261.8 23.8 259 20Z',
            fill: 'none',
            stroke: eyeStroke,
            strokeWidth: 1.5,
            strokeLinecap: 'round',
            strokeLinejoin: 'round',
            cursor: 'pointer',
        },
        toggleEyePupil: {
            cx: 269,
            cy: 20,
            r: 2.2,
            fill: eyeStroke,
            opacity: data.expanded ? 1 : 0.85,
            cursor: 'pointer',
        },
        toggleEyeSlash: {
            d: 'M261 26L277 14',
            fill: 'none',
            stroke: eyeStroke,
            strokeWidth: 1.6,
            strokeLinecap: 'round',
            strokeLinejoin: 'round',
            opacity: data.expanded ? 0 : 1,
            cursor: 'pointer',
        },
        workstationsText: {
            text: `${data.workstationCount || 0} WORKSTATIONS`,
            fill: '#0f172a',
            fontSize: 9,
            fontWeight: 700,
            refX: 16,
            refY: 72,
            textAnchor: 'left',
            pointerEvents: 'none',
        },
        healthyText: {
            text: `${data.healthyCount || 0} HEALTHY`,
            fill: '#10b981',
            fontSize: 9,
            fontWeight: 700,
            refX: 118,
            refY: 72,
            textAnchor: 'left',
            pointerEvents: 'none',
        },
        attentionText: {
            text: `${data.attentionCount || 0} ATTENTION`,
            fill: attention ? '#f43f5e' : '#94a3b8',
            fontSize: 9,
            fontWeight: 700,
            refX: 194,
            refY: 72,
            textAnchor: 'left',
            pointerEvents: 'none',
        },
    };
}

function registerNodes() {
    if (registered) {
        return;
    }

    HTML.register({ shape: 'pl-display-node', width: 320, height: 64, html: displayHtml });
    Graph.registerNode('pl-stage-node-native', {
        inherit: 'rect',
        width: 210,
        height: 88,
        markup: [
            { tagName: 'rect', selector: 'body' },
            { tagName: 'text', selector: 'kind' },
            { tagName: 'text', selector: 'label' },
        ],
    }, true);
    Graph.registerNode('pl-workstation-node-native', {
        inherit: 'rect',
        width: 280,
        height: 88,
        markup: [
            { tagName: 'rect', selector: 'body' },
            { tagName: 'text', selector: 'kind' },
            { tagName: 'text', selector: 'label' },
            { tagName: 'path', selector: 'toggleEyeOutline' },
            { tagName: 'circle', selector: 'toggleEyePupil' },
            { tagName: 'path', selector: 'toggleEyeSlash' },
            { tagName: 'text', selector: 'healthyText' },
            { tagName: 'text', selector: 'attentionText' },
            { tagName: 'text', selector: 'displaysText' },
        ],
    }, true);
    Graph.registerNode('pl-workgroup-node-native', {
        inherit: 'rect',
        width: 300,
        height: 88,
        markup: [
            { tagName: 'rect', selector: 'body' },
            { tagName: 'text', selector: 'kind' },
            { tagName: 'text', selector: 'label' },
            { tagName: 'path', selector: 'toggleEyeOutline' },
            { tagName: 'circle', selector: 'toggleEyePupil' },
            { tagName: 'path', selector: 'toggleEyeSlash' },
            { tagName: 'text', selector: 'workstationsText' },
            { tagName: 'text', selector: 'healthyText' },
            { tagName: 'text', selector: 'attentionText' },
        ],
    }, true);

    registered = true;
}

function edgeStyle({ active = false, attention = false } = {}) {
    return {
        line: {
            stroke: active ? '#0ea5e9' : attention ? '#f43f5e' : '#94a3b8',
            strokeWidth: active ? 3 : attention ? 2.4 : 2,
            targetMarker: null,
            sourceMarker: null,
        },
    };
}

function addEdge(graph, source, target, options = {}) {
    graph.addEdge({
        source,
        target,
        attrs: edgeStyle(options),
        connector: { name: 'smooth' },
        router: { name: 'normal' },
        zIndex: 0,
    });
}

function buildDisplayLayout(structure) {
    const displays = structure?.displays || [];
    const columns = displays.length <= 10 ? 1 : displays.length <= 20 ? 2 : 3;
    const rows = Math.max(1, Math.ceil(displays.length / columns));
    const colGap = 360;
    const rowGap = 82;
    const startX = 1110;
    const stageTopY = 96;
    const stageHeight = 88;
    const nodeHeight = 64;
    const columnHeight = Math.max(0, ((rows - 1) * rowGap) + nodeHeight);
    const baseCenterY = stageTopY + (stageHeight / 2);
    const stageY = baseCenterY - (stageHeight / 2);
    const startY = baseCenterY - (columnHeight / 2);

    const nodes = {
        facility: { x: 80, y: stageY, width: 210, height: stageHeight },
        workgroup: { x: 390, y: stageY, width: 210, height: stageHeight },
        workstation: { x: 700, y: stageY, width: 210, height: stageHeight },
        displays: [],
        width: startX + (Math.max(columns, 1) * colGap) + 240,
        height: Math.max(520, startY + rows * rowGap + 100),
    };

    displays.forEach((display, index) => {
        const column = Math.floor(index / rows);
        const row = index % rows;
        nodes.displays.push({
            id: display.id,
            x: startX + column * colGap,
            y: startY + row * rowGap,
            width: 320,
            height: 64,
        });
    });

    return nodes;
}

function buildWorkstationLayout(structure, expandedWorkstationId, workstationPositionOverrides = {}, stagePositionOverrides = {}) {
    const workstations = structure?.workstations || [];
    const expanded = workstations.find((item) => String(item.id) === String(expandedWorkstationId));
    const displays = expanded?.displays || [];
    const workstationWidth = 280;
    const workstationHeight = 88;
    const workstationColGap = 340;
    const workstationCount = Math.max(workstations.length, 1);
    const workstationCols = workstationCount <= 8
        ? workstationCount
        : Math.max(3, Math.min(8, Math.ceil(Math.sqrt(workstationCount))));
    const workstationRows = Math.max(1, Math.ceil(workstationCount / workstationCols));
    const workstationRowGap = Math.max(140, Math.min(220, 140 + workstationRows * 8));
    const displayWidth = 320;
    const displayHeight = 64;
    const displayGap = 360;
    const stageWidth = 210;
    const canvasCenterX = 760;
    const facilityX = canvasCenterX - (stageWidth / 2);
    const workgroupX = canvasCenterX - (stageWidth / 2);
    const facilityY = 80;
    const workgroupY = 210;
    const workstationY = 380;
    const stageHeight = 88;
    const expandedIndex = expanded
        ? workstations.findIndex((item) => String(item.id) === String(expanded.id))
        : -1;
    const workstationGridWidth = ((workstationCols - 1) * workstationColGap) + workstationWidth;
    const workstationGridHeight = ((workstationRows - 1) * workstationRowGap) + workstationHeight;
    const workstationStartX = canvasCenterX - (workstationGridWidth / 2);
    const expandedCol = expandedIndex >= 0 ? (expandedIndex % workstationCols) : Math.floor(workstationCols / 2);
    const expandedRow = expandedIndex >= 0 ? Math.floor(expandedIndex / workstationCols) : 0;
    const expandedOverride = expanded ? workstationPositionOverrides[String(expanded.id)] : null;
    const expandedCenterX = expandedOverride
        ? expandedOverride.x + (workstationWidth / 2)
        : workstationStartX + (expandedCol * workstationColGap) + (workstationWidth / 2);
    const expandedCenterY = expandedOverride
        ? expandedOverride.y + (workstationHeight / 2)
        : workstationY + (expandedRow * workstationRowGap) + (workstationHeight / 2);
    const displayY = workstationY + workstationGridHeight + 140;
    const displayCount = Math.max(displays.length, 1);
    const displayStartX = expandedCenterX - ((((displayCount - 1) * displayGap) + displayWidth) / 2);
    const lastWorkstationX = workstationStartX + ((workstationCols - 1) * workstationColGap);
    const lastDisplayX = displayStartX + (Math.max(displays.length, 1) - 1) * displayGap;

    const nodes = {
        facility: {
            x: stagePositionOverrides.facility?.x ?? facilityX,
            y: stagePositionOverrides.facility?.y ?? facilityY,
            width: stageWidth,
            height: stageHeight,
        },
        workgroup: {
            x: stagePositionOverrides.workgroup?.x ?? workgroupX,
            y: stagePositionOverrides.workgroup?.y ?? workgroupY,
            width: stageWidth,
            height: stageHeight,
        },
        workstations: [],
        displays: [],
        width: Math.max(1520, lastWorkstationX + workstationWidth + 180, lastDisplayX + displayWidth + 180),
        height: Math.max(680, displayY + (displays.length ? displayHeight + 140 : 0), workstationY + workstationGridHeight + 160),
    };

    workstations.forEach((item, index) => {
        const col = index % workstationCols;
        const row = Math.floor(index / workstationCols);
        const override = workstationPositionOverrides[String(item.id)];
        nodes.workstations.push({
            id: item.id,
            x: override?.x ?? (workstationStartX + col * workstationColGap),
            y: override?.y ?? (workstationY + row * workstationRowGap),
            width: workstationWidth,
            height: workstationHeight,
        });
    });

    displays.forEach((display, index) => {
        nodes.displays.push({
            id: display.id,
            workstationId: expanded?.id,
            x: displayStartX + index * displayGap,
            y: displayY,
            width: displayWidth,
            height: displayHeight,
        });
    });

    return nodes;
}

function buildWorkgroupLayout(structure, expandedWorkgroupId, expandedWorkstationId, groupPositionOverrides = {}, workstationPositionOverrides = {}, stagePositionOverrides = {}) {
    const workgroups = structure?.workgroups || [];
    const workgroupWidth = 300;
    const workgroupHeight = 88;
    const colGap = 360;
    const count = Math.max(workgroups.length, 1);
    const cols = count <= 8 ? count : Math.max(3, Math.min(8, Math.ceil(Math.sqrt(count))));
    const rows = Math.max(1, Math.ceil(count / cols));
    const rowGap = Math.max(150, Math.min(240, 150 + rows * 10));
    const stageWidth = 210;
    const stageHeight = 88;
    const workstationWidth = 280;
    const workstationHeight = 88;
    const workstationColGap = 340;
    const displayWidth = 320;
    const displayHeight = 64;
    const displayGap = 360;
    const canvasCenterX = 760;
    const facilityY = 80;
    const siblingsY = 260;
    const gridWidth = ((cols - 1) * colGap) + workgroupWidth;
    const startX = canvasCenterX - (gridWidth / 2);
    const gridHeight = ((rows - 1) * rowGap) + workgroupHeight;
    const lastX = startX + ((cols - 1) * colGap);
    const expandedGroup = workgroups.find((item) => String(item.id) === String(expandedWorkgroupId)) || null;
    const childWorkstations = expandedGroup?.workstations || [];
    const workstationCount = childWorkstations.length;
    const workstationGridWidth = workstationCount > 0
        ? ((Math.max(workstationCount, 1) - 1) * workstationColGap) + workstationWidth
        : workstationWidth;
    const expandedGroupNode = expandedGroup
        ? {
            x: groupPositionOverrides[String(expandedGroup.id)]?.x ?? (startX + ((workgroups.findIndex((item) => String(item.id) === String(expandedGroup.id)) % cols) * colGap)),
            y: groupPositionOverrides[String(expandedGroup.id)]?.y ?? (siblingsY + (Math.floor(workgroups.findIndex((item) => String(item.id) === String(expandedGroup.id)) / cols) * rowGap)),
        }
        : null;
    const workstationStartX = expandedGroupNode
        ? (expandedGroupNode.x + (workgroupWidth / 2) - (workstationGridWidth / 2))
        : 0;
    const workstationsY = siblingsY + gridHeight + 150;
    const expandedWorkstation = childWorkstations.find((item) => String(item.id) === String(expandedWorkstationId)) || null;
    const displays = expandedWorkstation?.displays || [];
    const displayGridWidth = displays.length > 0
        ? ((displays.length - 1) * displayGap) + displayWidth
        : displayWidth;
    const expandedWorkstationIndex = childWorkstations.findIndex((item) => String(item.id) === String(expandedWorkstationId));
    const expandedWorkstationNode = expandedWorkstationIndex >= 0
        ? {
            x: workstationPositionOverrides[String(expandedWorkstation.id)]?.x ?? (workstationStartX + (expandedWorkstationIndex * workstationColGap)),
            y: workstationPositionOverrides[String(expandedWorkstation.id)]?.y ?? workstationsY,
        }
        : null;
    const displayStartX = expandedWorkstationNode
        ? (expandedWorkstationNode.x + (workstationWidth / 2) - (displayGridWidth / 2))
        : 0;
    const displayY = workstationsY + workstationHeight + 130;

    const nodes = {
        facility: {
            x: stagePositionOverrides.facility?.x ?? (canvasCenterX - (stageWidth / 2)),
            y: stagePositionOverrides.facility?.y ?? facilityY,
            width: stageWidth,
            height: stageHeight,
        },
        workgroups: [],
        workstations: [],
        displays: [],
        width: Math.max(1520, lastX + workgroupWidth + 180),
        height: Math.max(560, displayY + displayHeight + 140, workstationsY + workstationHeight + 160, siblingsY + gridHeight + 140),
    };

    workgroups.forEach((item, index) => {
        const col = index % cols;
        const row = Math.floor(index / cols);
        const override = groupPositionOverrides[String(item.id)];
        nodes.workgroups.push({
            id: item.id,
            x: override?.x ?? (startX + col * colGap),
            y: override?.y ?? (siblingsY + row * rowGap),
            width: workgroupWidth,
            height: workgroupHeight,
        });
    });

    childWorkstations.forEach((item, index) => {
        const override = workstationPositionOverrides[String(item.id)];
        nodes.workstations.push({
            id: item.id,
            x: override?.x ?? (workstationStartX + index * workstationColGap),
            y: override?.y ?? workstationsY,
            width: workstationWidth,
            height: workstationHeight,
        });
    });

    displays.forEach((display, index) => {
        nodes.displays.push({
            id: display.id,
            workstationId: expandedWorkstation?.id,
            x: displayStartX + index * displayGap,
            y: displayY,
            width: displayWidth,
            height: displayHeight,
        });
    });

    return nodes;
}

function fit(graph) {
    graph.centerContent();
    graph.zoomToFit({ padding: 48, maxScale: 1 });
}

function getViewport(graph) {
    return {
        zoom: graph.zoom(),
        translate: graph.translate(),
    };
}

function restoreViewport(graph, viewport) {
    if (!viewport) {
        return;
    }

    graph.zoomTo(viewport.zoom || 1);
    graph.translate(viewport.translate?.tx || 0, viewport.translate?.ty || 0);
}

function focusWorkstation(graph, workstationId) {
    if (!workstationId) {
        fit(graph);
        return;
    }

    const cell = graph.getCellById(`workstation-${workstationId}`);
    if (!cell) {
        fit(graph);
        return;
    }

    graph.zoomTo(1.25);
    graph.centerCell(cell);
}

function createDisplayGraph(graph, structure) {
    const layout = buildDisplayLayout(structure || {});
    const stageNodes = {
        facility: graph.addNode({
            id: 'facility',
            shape: 'pl-stage-node-native',
            x: layout.facility.x,
            y: layout.facility.y,
            width: layout.facility.width,
            height: layout.facility.height,
            attrs: stageNodeAttrs('Facility', structure?.facility?.name || '-'),
        }),
        workgroup: graph.addNode({
            id: 'workgroup',
            shape: 'pl-stage-node-native',
            x: layout.workgroup.x,
            y: layout.workgroup.y,
            width: layout.workgroup.width,
            height: layout.workgroup.height,
            attrs: stageNodeAttrs('Workgroup', structure?.workgroup?.name || '-'),
        }),
        workstation: graph.addNode({
            id: 'workstation',
            shape: 'pl-stage-node-native',
            x: layout.workstation.x,
            y: layout.workstation.y,
            width: layout.workstation.width,
            height: layout.workstation.height,
            attrs: stageNodeAttrs('Workstation', structure?.workstation?.name || '-'),
        }),
    };

    addEdge(graph, { cell: stageNodes.facility.id, anchor: 'right' }, { cell: stageNodes.workgroup.id, anchor: 'left' }, {});
    addEdge(graph, { cell: stageNodes.workgroup.id, anchor: 'right' }, { cell: stageNodes.workstation.id, anchor: 'left' }, {});

    (structure?.displays || []).forEach((display, index) => {
        const position = layout.displays[index];
        graph.addNode({
            id: `display-${display.id}`,
            shape: 'pl-display-node',
            x: position.x,
            y: position.y,
            width: position.width,
            height: position.height,
            data: {
                id: display.id,
                label: display.name,
                active: !!display.active,
                statusTone: display.statusTone,
                statusLabel: display.statusLabel,
                connectedLabel: display.connectedLabel,
            },
        });

        const total = Math.max((structure?.displays || []).length - 1, 1);
        const lane = ((index - total / 2) * 9);
        addEdge(
            graph,
            { cell: stageNodes.workstation.id, anchor: { name: 'right', args: { dy: lane } } },
            { cell: `display-${display.id}`, anchor: 'left' },
            { active: !!display.active, attention: display.statusTone === 'danger' },
        );
    });
}

function createWorkstationGraph(graph, structure, expandedWorkstationId, workstationPositionOverrides = {}, stagePositionOverrides = {}) {
    const layout = buildWorkstationLayout(structure || {}, expandedWorkstationId, workstationPositionOverrides, stagePositionOverrides);
    const stageNodes = {
        facility: graph.addNode({
            id: 'facility',
            shape: 'pl-stage-node-native',
            x: layout.facility.x,
            y: layout.facility.y,
            width: layout.facility.width,
            height: layout.facility.height,
            attrs: stageNodeAttrs('Facility', structure?.facility?.name || '-'),
        }),
        workgroup: graph.addNode({
            id: 'workgroup',
            shape: 'pl-stage-node-native',
            x: layout.workgroup.x,
            y: layout.workgroup.y,
            width: layout.workgroup.width,
            height: layout.workgroup.height,
            attrs: stageNodeAttrs('Workgroup', structure?.workgroup?.name || '-'),
        }),
    };

    addEdge(graph, { cell: stageNodes.facility.id, anchor: 'bottom' }, { cell: stageNodes.workgroup.id, anchor: 'top' }, {});

    (structure?.workstations || []).forEach((item, index) => {
        const position = layout.workstations[index];
        graph.addNode({
            id: `workstation-${item.id}`,
            shape: 'pl-workstation-node-native',
            x: position.x,
            y: position.y,
            width: position.width,
            height: position.height,
            attrs: workstationNodeAttrs({
                id: item.id,
                label: item.name,
                active: !!item.active,
                expanded: String(item.id) === String(expandedWorkstationId),
                healthyCount: item.healthyCount || 0,
                attentionCount: item.attentionCount || 0,
                displayCount: item.displayCount || 0,
            }),
        });

        addEdge(
            graph,
            { cell: stageNodes.workgroup.id, anchor: 'bottom' },
            { cell: `workstation-${item.id}`, anchor: 'top' },
            { active: !!item.active, attention: Number(item.attentionCount || 0) > 0 },
        );
    });

    const expanded = (structure?.workstations || []).find((item) => String(item.id) === String(expandedWorkstationId));
    const displays = expanded?.displays || [];
    displays.forEach((display, index) => {
        const position = layout.displays[index];
        graph.addNode({
            id: `display-${display.id}`,
            shape: 'pl-display-node',
            x: position.x,
            y: position.y,
            width: position.width,
            height: position.height,
            data: {
                id: display.id,
                label: display.name,
                active: false,
                statusTone: display.statusTone,
                statusLabel: display.statusLabel,
                connectedLabel: display.connectedLabel,
            },
        });

        addEdge(
            graph,
            { cell: `workstation-${expanded.id}`, anchor: 'bottom' },
            { cell: `display-${display.id}`, anchor: 'top' },
            { attention: display.statusTone === 'danger' },
        );
    });
}

function createWorkgroupGraph(graph, structure, expandedWorkgroupId, expandedWorkstationId, workgroupPositionOverrides = {}, workstationPositionOverrides = {}, stagePositionOverrides = {}) {
    const layout = buildWorkgroupLayout(structure || {}, expandedWorkgroupId, expandedWorkstationId, workgroupPositionOverrides, workstationPositionOverrides, stagePositionOverrides);
    const stageNodes = {
        facility: graph.addNode({
            id: 'facility',
            shape: 'pl-stage-node-native',
            x: layout.facility.x,
            y: layout.facility.y,
            width: layout.facility.width,
            height: layout.facility.height,
            attrs: stageNodeAttrs('Facility', structure?.facility?.name || '-'),
        }),
    };

    (structure?.workgroups || []).forEach((item, index) => {
        const position = layout.workgroups[index];
        graph.addNode({
            id: `workgroup-sibling-${item.id}`,
            shape: 'pl-workgroup-node-native',
            x: position.x,
            y: position.y,
            width: position.width,
            height: position.height,
            attrs: workgroupNodeAttrs({
                id: item.id,
                label: item.name,
                active: !!item.active,
                expanded: String(item.id) === String(expandedWorkgroupId),
                workstationCount: item.workstationCount || 0,
                healthyCount: item.healthyCount || 0,
                attentionCount: item.attentionCount || 0,
            }),
        });

        addEdge(
            graph,
            { cell: stageNodes.facility.id, anchor: 'bottom' },
            { cell: `workgroup-sibling-${item.id}`, anchor: 'top' },
            { active: !!item.active, attention: Number(item.attentionCount || 0) > 0 },
        );
    });

    const expandedGroup = (structure?.workgroups || []).find((item) => String(item.id) === String(expandedWorkgroupId));
    const childWorkstations = expandedGroup?.workstations || [];

    childWorkstations.forEach((item, index) => {
        const position = layout.workstations[index];
        graph.addNode({
            id: `workstation-${item.id}`,
            shape: 'pl-workstation-node-native',
            x: position.x,
            y: position.y,
            width: position.width,
            height: position.height,
            attrs: workstationNodeAttrs({
                id: item.id,
                label: item.name,
                active: false,
                expanded: String(item.id) === String(expandedWorkstationId),
                healthyCount: item.healthyCount || 0,
                attentionCount: item.attentionCount || 0,
                displayCount: item.displayCount || 0,
            }),
        });

        addEdge(
            graph,
            { cell: `workgroup-sibling-${expandedGroup.id}`, anchor: 'bottom' },
            { cell: `workstation-${item.id}`, anchor: 'top' },
            { attention: Number(item.attentionCount || 0) > 0 },
        );
    });

    const expandedWorkstation = childWorkstations.find((item) => String(item.id) === String(expandedWorkstationId));
    const displays = expandedWorkstation?.displays || [];
    displays.forEach((display, index) => {
        const position = layout.displays[index];
        graph.addNode({
            id: `display-${display.id}`,
            shape: 'pl-display-node',
            x: position.x,
            y: position.y,
            width: position.width,
            height: position.height,
            data: {
                id: display.id,
                label: display.name,
                active: false,
                statusTone: display.statusTone,
                statusLabel: display.statusLabel,
                connectedLabel: display.connectedLabel,
            },
        });

        addEdge(
            graph,
            { cell: `workstation-${expandedWorkstation.id}`, anchor: 'bottom' },
            { cell: `display-${display.id}`, anchor: 'top' },
            { attention: display.statusTone === 'danger' },
        );
    });
}

export function createStructureMapGraph({ container, structure, onOpenDisplay, onZoomChange, expandedWorkstationId: initialExpandedWorkstationId = null, expandedWorkgroupId: initialExpandedWorkgroupId = null, onExpandedWorkstationChange, onExpandedWorkgroupChange }) {
    registerNodes();
    container.innerHTML = '';
    container.style.userSelect = 'none';
    container.style.webkitUserSelect = 'none';
    container.style.webkitUserDrag = 'none';

    const workstationMode = Array.isArray(structure?.workstations);
    const workgroupMode = Array.isArray(structure?.workgroups);
    let expandedWorkstationId = workstationMode
        ? (
            initialExpandedWorkstationId !== null && initialExpandedWorkstationId !== undefined
                ? ((structure.workstations || []).some((item) => String(item.id) === String(initialExpandedWorkstationId))
                    ? initialExpandedWorkstationId
                    : null)
                : (structure.workstations.find((item) => item.active)?.id ?? structure.workstations[0]?.id ?? null)
        )
        : null;
    let expandedWorkgroupId = workgroupMode
        ? (
            initialExpandedWorkgroupId !== null && initialExpandedWorkgroupId !== undefined
                ? ((structure.workgroups || []).some((item) => String(item.id) === String(initialExpandedWorkgroupId))
                    ? initialExpandedWorkgroupId
                    : null)
                : null
        )
        : null;
    let hasMounted = false;
    const workstationPositionOverrides = {};
    const workgroupPositionOverrides = {};
    const stagePositionOverrides = {};

    const graph = new Graph({
        container,
        width: container.clientWidth,
        height: container.clientHeight,
        async: false,
        background: { color: '#f8fafc' },
        grid: {
            visible: true,
            type: 'dot',
            size: 24,
            args: { color: 'rgba(148,163,184,0.18)', thickness: 1 },
        },
        panning: true,
        preventDefaultMouseDown: true,
        mousewheel: {
            enabled: true,
            minScale: 0.5,
            maxScale: 2.2,
        },
        interacting: {
            edgeMovable: false,
            vertexMovable: false,
            arrowheadMovable: false,
        },
    });

    container.addEventListener('dragstart', (event) => {
        event.preventDefault();
    });
    container.addEventListener('selectstart', (event) => {
        event.preventDefault();
    });

    const render = ({ preserveViewport = false } = {}) => {
        const viewport = preserveViewport ? getViewport(graph) : null;
        graph.clearCells();
        if (workstationMode) {
            createWorkstationGraph(graph, structure, expandedWorkstationId, workstationPositionOverrides, stagePositionOverrides);
        } else if (workgroupMode) {
            createWorkgroupGraph(graph, structure, expandedWorkgroupId, expandedWorkstationId, workgroupPositionOverrides, workstationPositionOverrides, stagePositionOverrides);
        } else {
            createDisplayGraph(graph, structure);
        }
        if (preserveViewport) {
            restoreViewport(graph, viewport);
        } else if (workstationMode) {
            focusWorkstation(graph, expandedWorkstationId);
        } else if (workgroupMode) {
            fit(graph);
        } else {
            fit(graph);
        }
    };

    graph.on('scale', ({ sx }) => {
        onZoomChange?.(sx);
    });

    graph.on('node:click', ({ e, node, x, y }) => {
        const id = String(node.id || '');
        const position = node.getPosition();
        const localX = x - position.x;
        const localY = y - position.y;
        if (id.startsWith('workstation-')) {
            const dx = localX - 249;
            const dy = localY - 20;
            const insideToggle = (dx * dx) + (dy * dy) <= (16 * 16);
            if (!insideToggle) {
                return;
            }

            e.preventDefault?.();
            e.stopPropagation?.();
            const nextId = id.replace('workstation-', '');
            expandedWorkstationId = String(expandedWorkstationId) === String(nextId) ? null : nextId;
            onExpandedWorkstationChange?.(expandedWorkstationId);
            render({ preserveViewport: true });
            return;
        }

        if (id.startsWith('workgroup-sibling-')) {
            const dx = localX - 269;
            const dy = localY - 20;
            const insideToggle = (dx * dx) + (dy * dy) <= (16 * 16);
            if (!insideToggle) {
                return;
            }

            e.preventDefault?.();
            e.stopPropagation?.();
            const nextId = id.replace('workgroup-sibling-', '');
            expandedWorkgroupId = String(expandedWorkgroupId) === String(nextId) ? null : nextId;
            expandedWorkstationId = null;
            onExpandedWorkgroupChange?.(expandedWorkgroupId);
            onExpandedWorkstationChange?.(expandedWorkstationId);
            render({ preserveViewport: true });
        }
    });

    graph.on('node:change:position', ({ node }) => {
        if (!workstationMode && !workgroupMode) {
            return;
        }

        const id = String(node.id || '');
        const position = node.getPosition();
        if (id.startsWith('workstation-')) {
            const workstationId = id.replace('workstation-', '');
            workstationPositionOverrides[workstationId] = {
                x: position.x,
                y: position.y,
            };
            return;
        }

        if (id.startsWith('workgroup-sibling-')) {
            const workgroupId = id.replace('workgroup-sibling-', '');
            workgroupPositionOverrides[workgroupId] = {
                x: position.x,
                y: position.y,
            };
            return;
        }

        if (id === 'facility' || id === 'workgroup') {
            stagePositionOverrides[id] = {
                x: position.x,
                y: position.y,
            };
        }
    });

    const clickHandler = (event) => {
        const openButton = event.target.closest('[data-action="open-display"]');
        if (openButton) {
            event.preventDefault();
            event.stopPropagation();
            onOpenDisplay?.(openButton.dataset.displayId);
            return;
        }

    };

    container.addEventListener('click', clickHandler);

    const resizeObserver = new ResizeObserver(() => {
        graph.resize(container.clientWidth, container.clientHeight);
        if (hasMounted) {
            return;
        }

        if (workstationMode) {
            focusWorkstation(graph, expandedWorkstationId);
        } else {
            fit(graph);
        }
    });
    resizeObserver.observe(container);

    render();
    hasMounted = true;

    return {
        graph,
        fit: () => fit(graph),
        getViewport: () => getViewport(graph),
        setViewport: (viewport) => restoreViewport(graph, viewport),
        zoomIn() {
            graph.zoom(0.15);
            onZoomChange?.(graph.zoom());
        },
        zoomOut() {
            graph.zoom(-0.15);
            onZoomChange?.(graph.zoom());
        },
        destroy() {
            resizeObserver.disconnect();
            container.removeEventListener('click', clickHandler);
            graph.dispose();
        },
    };
}

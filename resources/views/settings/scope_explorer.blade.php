@include('common.navigations.header')

<style>
    .scope-explorer { --blue:#0ea5e9; --ink:#0f172a; --muted:#64748b; --line:#dbe7f3; }
    .scope-card { border:1px solid var(--line); border-radius:24px; background:linear-gradient(180deg,#fff,#f8fbff); box-shadow:0 22px 56px rgba(15,23,42,.07); overflow:hidden; }
    .scope-toolbar { display:flex; gap:12px; align-items:center; padding:10px 14px; min-height:64px; border-bottom:1px solid var(--line); background:rgba(247,251,255,.86); }
    .scope-breadcrumb { display:flex; gap:8px; align-items:center; min-width:0; color:var(--muted); font-size:13px; font-weight:800; }
    .scope-breadcrumb button { border:0; background:transparent; color:inherit; padding:7px 8px; border-radius:12px; font:inherit; cursor:pointer; }
    .scope-breadcrumb button:hover { background:#eaf5ff; color:#0369a1; }
    .scope-toolbar-controls { display:flex; align-items:center; justify-content:flex-end; gap:8px; margin-left:auto; min-width:0; }
    .scope-search { position:relative; display:block; width:300px; flex:0 0 min(300px,30vw); }
    .scope-search input { width:100%; height:40px; border:1px solid var(--line); border-radius:14px; padding:9px 14px 9px 38px; outline:0; background:#fff; color:var(--ink); box-shadow:0 8px 22px rgba(15,23,42,.03); }
    .scope-search input:focus { border-color:#8bd3ff; box-shadow:0 0 0 4px rgba(14,165,233,.10); }
    .scope-search i, .scope-search svg { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#8aa0b6; width:17px; height:17px; pointer-events:none; }
    .scope-btn { width:40px; height:40px; display:inline-grid; place-items:center; border:1px solid var(--line); border-radius:13px; background:#fff; color:#475569; }
    .scope-btn:hover { color:#0369a1; border-color:#b9dff8; background:#f4fbff; }
    .scope-layout { display:grid; grid-template-columns:300px minmax(0,1fr) 320px; min-height:620px; transition:grid-template-columns .22s ease; }
    .scope-pane { min-width:0; background:rgba(255,255,255,.92); }
    .scope-tree { border-right:1px solid var(--line); padding:18px 16px; }
    .scope-main { padding:22px; }
    .scope-inspector { border-left:1px solid var(--line); padding:20px; background:rgba(249,252,255,.94); overflow:hidden; transition:opacity .18s ease,padding .22s ease,border-color .18s ease; }
    .scope-card.inspector-collapsed .scope-layout { grid-template-columns:300px minmax(0,1fr) 0px; }
    .scope-card.inspector-collapsed .scope-inspector { opacity:0; padding:0; border-left-color:transparent; }
    .scope-card.inspector-collapsed [data-scope-toggle-inspector] { color:#0369a1; background:#eaf5ff; border-color:#b9dff8; }
    .scope-kicker { color:#8aa0b6; font-size:12px; font-weight:900; letter-spacing:.26em; text-transform:uppercase; margin-bottom:12px; }
    .scope-tree-list { max-height:565px; overflow:auto; display:grid; gap:4px; }
    .scope-tree-row { width:100%; display:flex; align-items:center; gap:9px; border:0; border-radius:13px; padding:8px 9px; background:transparent; text-align:left; color:#334155; cursor:pointer; }
    .scope-tree-row:hover, .scope-tree-row.active { background:#eaf5ff; color:#0369a1; }
    .scope-tree-toggle { width:24px; height:24px; flex:0 0 24px; display:inline-grid; place-items:center; border-radius:9px; }
    .scope-tree-toggle:hover { background:rgba(14,165,233,.12); color:#0369a1; }
    .scope-tree-toggle svg { pointer-events:none; }
    .scope-tree-name { min-width:0; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:13px; font-weight:800; }
    .scope-tree-count { color:#8aa0b6; font-size:11px; font-weight:800; }
    .scope-tree-children { margin-left:18px; padding-left:8px; border-left:1px solid #e4edf6; }
    .scope-summary { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:18px; padding:8px; border:1px solid #e7eff8; border-radius:16px; background:rgba(248,251,255,.7); }
    .scope-summary-card { display:inline-flex; align-items:center; gap:8px; border:0; border-radius:999px; background:transparent; padding:8px 10px; }
    .scope-summary-card span { display:block; color:#8aa0b6; font-size:10px; font-weight:900; letter-spacing:.14em; text-transform:uppercase; }
    .scope-summary-card strong { display:block; color:var(--ink); font-size:14px; line-height:1; }
    .scope-head { display:flex; justify-content:space-between; align-items:end; gap:16px; margin-bottom:16px; }
    .scope-head h2 { margin:0; color:var(--ink); font-size:24px; letter-spacing:-.04em; }
    .scope-head p { margin:4px 0 0; color:var(--muted); font-size:13px; }
    .scope-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(190px,1fr)); gap:8px 16px; max-height:440px; overflow:auto; padding:6px 4px 8px; align-items:start; }
    .scope-item { display:grid; grid-template-columns:52px minmax(0,1fr); align-items:center; gap:10px; border:1px solid transparent; border-radius:12px; background:transparent; padding:8px 10px; min-height:62px; text-align:left; cursor:pointer; box-shadow:none; transition:background .14s ease,border-color .14s ease; }
    .scope-item:hover { background:#f3f8fd; border-color:#dbeafe; }
    .scope-item.active { background:#eaf5ff; border-color:#8bd3ff; }
    .scope-item-icon { width:48px; height:42px; display:inline-grid; place-items:center; border-radius:10px; background:transparent; color:#0ea5e9; }
    .scope-item-icon svg { width:42px; height:42px; stroke-width:1.7; }
    .scope-item[data-scope-type="facility"] .scope-item-icon { color:#0ea5e9; }
    .scope-item[data-scope-type="workgroup"] .scope-item-icon { color:#14b8a6; }
    .scope-item[data-scope-type="workstation"] .scope-item-icon { color:#64748b; }
    .scope-item[data-scope-type="display"] .scope-item-icon { color:#475569; }
    .scope-item-copy { min-width:0; }
    .scope-item-name { display:block; color:var(--ink); font-weight:850; font-size:14px; line-height:1.25; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-bottom:0; }
    .scope-empty { display:grid; place-items:center; min-height:260px; border:1px dashed var(--line); border-radius:24px; color:var(--muted); text-align:center; background:rgba(255,255,255,.72); }
    .scope-detail { border:1px solid var(--line); border-radius:24px; background:#fff; padding:18px; box-shadow:0 18px 44px rgba(15,23,42,.06); }
    .scope-detail-icon { width:58px; height:52px; display:grid; place-items:center; border-radius:18px; background:linear-gradient(135deg,#e0f2fe,#f8fafc); color:#0369a1; margin-bottom:14px; }
    .scope-detail h3 { margin:0; color:var(--ink); font-size:21px; letter-spacing:-.03em; }
    .scope-detail p { margin:8px 0 0; color:var(--muted); line-height:1.55; font-size:13px; }
    .scope-detail-row { display:flex; justify-content:space-between; gap:14px; border-top:1px solid #edf3f9; padding-top:9px; margin-top:9px; color:#475569; font-size:12px; }
    .scope-detail-row strong { color:var(--ink); text-align:right; }
    .scope-actions { display:grid; gap:10px; margin-top:18px; }
    .scope-action { display:flex; align-items:center; justify-content:center; gap:8px; min-height:42px; border-radius:14px; border:1px solid var(--line); background:#fff; color:#334155; font-size:12px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; text-decoration:none; }
    .scope-action.danger { color:#e11d48; background:#fff7f8; border-color:#fecdd3; }
    .scope-menu { position:fixed; z-index:10020; min-width:220px; padding:8px; border:1px solid var(--line); border-radius:18px; background:rgba(255,255,255,.97); box-shadow:0 26px 60px rgba(15,23,42,.18); display:none; }
    .scope-menu.open { display:block; }
    .scope-menu button, .scope-menu a { width:100%; display:flex; align-items:center; gap:10px; border:0; border-radius:12px; background:transparent; padding:10px 11px; color:#334155; font-size:13px; font-weight:800; text-align:left; text-decoration:none; }
    .scope-menu button:hover, .scope-menu a:hover { background:#eef7ff; color:#0369a1; }
    .scope-menu .danger { color:#e11d48; }
    .scope-sep { height:1px; background:#edf3f9; margin:6px 4px; }
    .scope-confirm { position:fixed; inset:0; z-index:10030; display:none; align-items:center; justify-content:center; background:rgba(15,23,42,.48); backdrop-filter:blur(8px); padding:24px; }
    .scope-confirm.open { display:flex; }
    .scope-dialog { width:min(520px,100%); border-radius:26px; background:#fff; box-shadow:0 28px 70px rgba(15,23,42,.26); overflow:hidden; }
    .scope-dialog-body { padding:26px; }
    .scope-dialog h3 { margin:0; color:var(--ink); font-size:24px; }
    .scope-dialog p { color:var(--muted); line-height:1.6; }
    .scope-dialog-actions { display:flex; justify-content:flex-end; gap:12px; border-top:1px solid var(--line); padding:16px 22px; background:#f8fbff; }
    .scope-cancel, .scope-delete { border:0; border-radius:14px; padding:12px 18px; font-weight:900; }
    .scope-cancel { background:#edf3f9; color:#475569; }
    .scope-delete { background:#e11d48; color:#fff; }
    .scope-toast { position:fixed; right:22px; bottom:24px; z-index:10040; display:none; max-width:360px; border:1px solid var(--line); border-radius:18px; background:#fff; color:#334155; box-shadow:0 20px 50px rgba(15,23,42,.18); padding:14px 16px; font-size:13px; font-weight:800; }
    .scope-toast.open { display:block; }
    @media (max-width:1240px){ .scope-layout,.scope-card.inspector-collapsed .scope-layout{grid-template-columns:260px minmax(0,1fr)} .scope-inspector{display:none} }
    @media (max-width:900px){ .scope-toolbar{flex-wrap:wrap} .scope-toolbar-controls{width:100%;margin-left:0;justify-content:flex-start} .scope-search{flex:1 1 240px;width:auto} .scope-layout,.scope-card.inspector-collapsed .scope-layout{grid-template-columns:1fr} .scope-tree{border-right:0;border-bottom:1px solid var(--line)} .scope-summary{grid-template-columns:repeat(2,minmax(0,1fr))} }
</style>

<div class="scope-explorer">
    <section id="scope-explorer-root" class="scope-card"
        data-facilities-url="{{ url('api/scope-explorer/facilities') }}"
        data-children-template="{{ url('api/scope-explorer/__TYPE__/__ID__/children') }}"
        data-csrf="{{ csrf_token() }}">
        <div class="scope-toolbar">
            <nav class="scope-breadcrumb" data-scope-breadcrumb></nav>
            <div class="scope-toolbar-controls">
                <label class="scope-search">
                    <i data-lucide="search"></i>
                    <input type="search" data-scope-search placeholder="Search current folder..." autocomplete="off">
                </label>
                <button type="button" class="scope-btn" data-scope-refresh title="Refresh"><i data-lucide="refresh-cw"></i></button>
                <button type="button" class="scope-btn" data-scope-open-selected title="Open selected"><i data-lucide="external-link"></i></button>
                <button type="button" class="scope-btn" data-scope-toggle-inspector title="Hide inspector"><i data-lucide="panel-right-close"></i></button>
            </div>
        </div>

        <div class="scope-layout">
            <aside class="scope-pane scope-tree">
                <div class="scope-kicker">Hierarchy</div>
                <div class="scope-tree-list" data-scope-tree><div class="scope-empty">Loading hierarchy...</div></div>
            </aside>

            <main class="scope-pane scope-main">
                <div class="scope-summary" data-scope-summary></div>
                <div class="scope-head">
                    <div>
                        <div class="scope-kicker" data-scope-type-label>Workspace</div>
                        <h2 data-scope-current-title>Remote Workspace</h2>
                        <p data-scope-current-subtitle>Open a folder to browse the operational scope.</p>
                    </div>
                </div>
                <div class="scope-grid" data-scope-content><div class="scope-empty">Loading folders...</div></div>
            </main>

            <aside class="scope-pane scope-inspector">
                <div class="scope-kicker">Inspector</div>
                <div data-scope-detail></div>
            </aside>
        </div>
    </section>
</div>

<div class="scope-menu" data-scope-context-menu></div>
<div class="scope-confirm" data-scope-confirm>
    <div class="scope-dialog" role="dialog" aria-modal="true">
        <div class="scope-dialog-body">
            <div class="scope-detail-icon" style="background:#fff1f2;color:#e11d48;"><i data-lucide="trash-2"></i></div>
            <h3>Delete selected scope?</h3>
            <p data-scope-confirm-copy>This action will remove the selected item.</p>
        </div>
        <div class="scope-dialog-actions">
            <button type="button" class="scope-cancel" data-scope-cancel-delete>Cancel</button>
            <button type="button" class="scope-delete" data-scope-confirm-delete>Delete</button>
        </div>
    </div>
</div>
<div class="scope-toast" data-scope-toast></div>

@push('scripts')
<script>
(() => {
    const root = document.getElementById('scope-explorer-root');
    if (!root || root.dataset.ready === '1') return;
    root.dataset.ready = '1';

    const state = {
        nodes: new Map(),
        current: 'root',
        selected: 'root',
        query: '',
        deleting: null,
        summary: { facilities: 0, workgroups: 0, workstations: 0, displays: 0 },
    };

    const els = {
        tree: root.querySelector('[data-scope-tree]'),
        content: root.querySelector('[data-scope-content]'),
        summary: root.querySelector('[data-scope-summary]'),
        detail: root.querySelector('[data-scope-detail]'),
        breadcrumb: root.querySelector('[data-scope-breadcrumb]'),
        search: root.querySelector('[data-scope-search]'),
        title: root.querySelector('[data-scope-current-title]'),
        subtitle: root.querySelector('[data-scope-current-subtitle]'),
        typeLabel: root.querySelector('[data-scope-type-label]'),
        menu: document.querySelector('[data-scope-context-menu]'),
        confirm: document.querySelector('[data-scope-confirm]'),
        confirmCopy: document.querySelector('[data-scope-confirm-copy]'),
        toast: document.querySelector('[data-scope-toast]'),
    };

    const urls = {
        root: root.dataset.facilitiesUrl,
        children: root.dataset.childrenTemplate,
        csrf: root.dataset.csrf,
    };

    const rootNode = {
        id: 'root', key: 'root', type: 'root', name: 'Remote Workspace',
        subtitle: 'Facilities are the first level of this explorer.', meta: 'Root',
        childLabel: 'facilities', childCount: 0, children: [], parentKey: null,
        loaded: false, expanded: true, urls: {},
    };
    state.nodes.set('root', rootNode);

    const iconMap = { root:'folder-tree', facility:'folder', workgroup:'folder', workstation:'folder', display:'monitor' };
    const keyOf = (type, id) => `${type}:${id}`;
    const node = (key) => state.nodes.get(key);
    const icon = (type) => iconMap[type] || 'folder';
    const h = (value) => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
    const metaText = (meta) => {
        if (!meta) return '-';
        if (typeof meta !== 'object') return String(meta);
        return Object.entries(meta).map(([key, value]) => `${key}: ${value}`).join(' - ');
    };
    const childUrl = (type, id) => urls.children.replace('__TYPE__', encodeURIComponent(type)).replace('__ID__', encodeURIComponent(id));
    const makeIcons = () => window.lucide?.createIcons?.();

    function normalize(item, parentKey) {
        const itemNode = {
            ...item,
            id: String(item.id),
            key: keyOf(item.type, item.id),
            parentKey,
            children: [],
            loaded: item.type === 'display' || Number(item.childCount || 0) === 0,
            expanded: false,
            urls: item.urls || {},
            deleteEndpoint: item.deleteEndpoint || null,
        };
        state.nodes.set(itemNode.key, itemNode);
        return itemNode;
    }

    function toast(message) {
        if (!els.toast) return;
        els.toast.textContent = message;
        els.toast.classList.add('open');
        clearTimeout(els.toastTimer);
        els.toastTimer = setTimeout(() => els.toast.classList.remove('open'), 2600);
    }

    function setInspectorCollapsed(collapsed, remember = true) {
        root.classList.toggle('inspector-collapsed', collapsed);
        const button = root.querySelector('[data-scope-toggle-inspector]');
        if (button) {
            button.title = collapsed ? 'Show inspector' : 'Hide inspector';
            button.innerHTML = `<i data-lucide="${collapsed ? 'panel-right-open' : 'panel-right-close'}"></i>`;
        }
        if (remember) {
            try { localStorage.setItem('scopeExplorerInspectorCollapsed', collapsed ? '1' : '0'); } catch (error) {}
        }
        makeIcons();
    }

    function renderSummary() {
        els.summary.innerHTML = [
            ['Facilities', state.summary.facilities],
            ['Workgroups', state.summary.workgroups],
            ['Workstations', state.summary.workstations],
            ['Displays', state.summary.displays],
        ].map(([label, value]) => `<div class="scope-summary-card"><span>${h(label)}</span><strong>${Number(value || 0).toLocaleString()}</strong></div>`).join('');
    }

    function renderTreeNode(item) {
        const treeChildren = item.children.filter(child => child.type !== 'display');
        const hasKids = item.type === 'root' || (['facility', 'workgroup'].includes(item.type) && (Number(item.childCount || 0) > 0 || treeChildren.length > 0));
        const kids = item.expanded && treeChildren.length ? `<div class="scope-tree-children">${treeChildren.map(renderTreeNode).join('')}</div>` : '';
        return `<div>
            <button type="button" class="scope-tree-row ${state.selected === item.key ? 'active' : ''}" data-scope-key="${h(item.key)}" data-tree-row>
                <span class="scope-tree-toggle" ${hasKids ? `data-tree-toggle="${h(item.key)}"` : ''}>
                    <i data-lucide="${hasKids && item.expanded ? 'chevron-down' : hasKids ? 'chevron-right' : 'minus'}"></i>
                </span>
                <i data-lucide="${icon(item.type)}"></i>
                <span class="scope-tree-name">${h(item.name)}</span>
                <span class="scope-tree-count">${item.type === 'root' ? '' : h(item.childCount || 0)}</span>
            </button>${kids}
        </div>`;
    }

    function renderTree() {
        els.tree.innerHTML = renderTreeNode(rootNode);
    }

    function renderBreadcrumb() {
        const chain = [];
        let cursor = node(state.current) || rootNode;
        while (cursor) {
            chain.unshift(cursor);
            cursor = cursor.parentKey ? node(cursor.parentKey) : null;
        }
        els.breadcrumb.innerHTML = chain.map((item, index) => `${index ? '<i data-lucide="chevron-right"></i>' : ''}<button type="button" data-breadcrumb="${h(item.key)}">${h(item.name)}</button>`).join('');
    }

    function renderContent() {
        const current = node(state.current) || rootNode;
        const query = state.query.trim().toLowerCase();
        const items = current.children.filter(item => !query || [item.name, item.subtitle, item.meta, item.childLabel].filter(Boolean).some(v => String(v).toLowerCase().includes(query)));
        els.title.textContent = current.name;
        els.subtitle.textContent = current.subtitle || 'Open a folder to browse the next scope level.';
        els.typeLabel.textContent = current.type === 'root' ? 'Workspace' : current.type;
        if (!items.length) {
            els.content.innerHTML = '<div class="scope-empty">No items found in this folder.</div>';
            return;
        }
        els.content.innerHTML = items.map(item => `<button type="button" class="scope-item ${state.selected === item.key ? 'active' : ''}" data-scope-key="${h(item.key)}" data-scope-type="${h(item.type)}" data-card>
            <span class="scope-item-icon"><i data-lucide="${icon(item.type)}"></i></span>
            <span class="scope-item-copy">
                <span class="scope-item-name">${h(item.name)}</span>
            </span>
        </button>`).join('');
    }

    function renderDetail() {
        const item = node(state.selected) || node(state.current) || rootNode;
        const canDelete = item.type !== 'root' && item.deleteEndpoint;
        els.detail.innerHTML = `<div class="scope-detail">
            <div class="scope-detail-icon"><i data-lucide="${icon(item.type)}"></i></div>
            <h3>${h(item.name)}</h3>
            <p>${h(item.subtitle || 'Select a folder or display to inspect its scope information.')}</p>
            <div class="scope-detail-row"><span>Type</span><strong>${h(item.type)}</strong></div>
            <div class="scope-detail-row"><span>Contains</span><strong>${h(item.childCount || 0)} ${h(item.childLabel || 'items')}</strong></div>
            <div class="scope-detail-row"><span>Info</span><strong>${h(metaText(item.meta))}</strong></div>
            <div class="scope-actions">
                ${item.urls?.open ? `<a class="scope-action" href="${h(item.urls.open)}"><i data-lucide="external-link"></i> Open</a>` : ''}
                ${item.urls?.edit ? `<a class="scope-action" href="${h(item.urls.edit)}"><i data-lucide="pencil"></i> Edit</a>` : ''}
                ${item.urls?.settings ? `<a class="scope-action" href="${h(item.urls.settings)}"><i data-lucide="settings"></i> Settings</a>` : ''}
                ${canDelete ? `<button type="button" class="scope-action danger" data-detail-delete="${h(item.key)}"><i data-lucide="trash-2"></i> Delete</button>` : ''}
            </div>
        </div>`;
    }

    function render() {
        renderSummary();
        renderTree();
        renderBreadcrumb();
        renderContent();
        renderDetail();
        makeIcons();
    }

    async function getJson(url) {
        const response = await fetch(url, { headers:{ Accept:'application/json', 'X-Requested-With':'XMLHttpRequest' }, credentials:'same-origin' });
        if (!response.ok) throw new Error(`Request failed ${response.status}`);
        return response.json();
    }

    async function loadRoot() {
        state.nodes.clear();
        state.nodes.set('root', rootNode);
        state.current = 'root';
        state.selected = 'root';
        rootNode.children = [];
        rootNode.loaded = false;
        const payload = await getJson(urls.root);
        state.summary = payload.summary || state.summary;
        rootNode.childCount = payload.items?.length || 0;
        rootNode.children = (payload.items || []).map(item => normalize(item, 'root'));
        rootNode.loaded = true;
        rootNode.expanded = true;
        render();
    }

    async function loadChildren(item) {
        if (!item || item.loaded || item.type === 'display') return;
        const payload = await getJson(childUrl(item.type, item.id));
        item.children = (payload.items || []).map(child => normalize(child, item.key));
        item.loaded = true;
    }

    async function openItem(key) {
        const item = node(key);
        if (!item) return;
        state.selected = key;
        if (item.type === 'display') {
            window.dispatchEvent(new CustomEvent('open-hierarchy', {
                detail: { type: 'display', id: Number(item.id || 0) },
            }));
            return;
        }
        try {
            await loadChildren(item);
            item.expanded = true;
            state.current = key;
            state.query = '';
            if (els.search) els.search.value = '';
            render();
        } catch (error) {
            console.error(error);
            toast('Unable to load this folder.');
        }
    }

    async function toggleTree(key) {
        const item = node(key);
        if (!item || item.type === 'display') return;
        state.selected = key;
        if (item.expanded) {
            item.expanded = false;
            render();
            return;
        }
        try {
            await loadChildren(item);
            item.expanded = true;
            render();
        } catch (error) {
            console.error(error);
            toast('Unable to load this folder.');
        }
    }

    function showMenu(event, key) {
        const item = node(key);
        if (!item || !els.menu) return;
        event.preventDefault();
        state.selected = key;
        els.menu.innerHTML = `
            <button type="button" data-menu-open="${h(item.key)}"><i data-lucide="folder-open"></i> Open</button>
            ${item.urls?.edit ? `<a href="${h(item.urls.edit)}"><i data-lucide="pencil"></i> Edit</a>` : ''}
            ${item.urls?.settings ? `<a href="${h(item.urls.settings)}"><i data-lucide="settings"></i> Settings</a>` : ''}
            ${item.type !== 'root' && item.deleteEndpoint ? `<div class="scope-sep"></div><button type="button" class="danger" data-menu-delete="${h(item.key)}"><i data-lucide="trash-2"></i> Delete</button>` : ''}
        `;
        els.menu.style.left = `${Math.max(12, Math.min(event.clientX, window.innerWidth - 240))}px`;
        els.menu.style.top = `${Math.max(12, Math.min(event.clientY, window.innerHeight - 230))}px`;
        els.menu.classList.add('open');
        render();
    }

    function askDelete(key) {
        const item = node(key);
        if (!item?.deleteEndpoint) return;
        state.deleting = key;
        els.confirmCopy.textContent = `Delete "${item.name}" from the ${item.type} scope.`;
        els.confirm.classList.add('open');
        makeIcons();
    }

    async function deleteItem() {
        const item = node(state.deleting);
        if (!item?.deleteEndpoint) return;
        const form = new FormData();
        form.append('_token', urls.csrf);
        form.append('id', item.id);
        try {
            const response = await fetch(item.deleteEndpoint, { method:'POST', body:form, headers:{ 'X-Requested-With':'XMLHttpRequest' }, credentials:'same-origin' });
            if (!response.ok) throw new Error(`Delete failed ${response.status}`);
            const parent = node(item.parentKey);
            if (parent) parent.children = parent.children.filter(child => child.key !== item.key);
            state.nodes.delete(item.key);
            state.selected = parent?.key || 'root';
            els.confirm.classList.remove('open');
            toast('Scope item deleted.');
            render();
        } catch (error) {
            console.error(error);
            toast('Delete failed. Check permissions or related child records.');
        }
    }

    root.addEventListener('click', event => {
        const toggle = event.target.closest('[data-tree-toggle]');
        const card = event.target.closest('[data-card]');
        const row = event.target.closest('[data-tree-row]');
        const crumb = event.target.closest('[data-breadcrumb]');
        const del = event.target.closest('[data-detail-delete]');
        if (toggle) {
            event.preventDefault();
            event.stopPropagation();
            toggleTree(toggle.dataset.treeToggle);
        }
        else if (card) openItem(card.dataset.scopeKey);
        else if (row) openItem(row.dataset.scopeKey);
        else if (crumb) openItem(crumb.dataset.breadcrumb);
        else if (del) askDelete(del.dataset.detailDelete);
    });

    root.addEventListener('contextmenu', event => {
        const target = event.target.closest('[data-scope-key]');
        if (target) showMenu(event, target.dataset.scopeKey);
    });

    els.menu?.addEventListener('click', event => {
        const open = event.target.closest('[data-menu-open]');
        const del = event.target.closest('[data-menu-delete]');
        if (open) { els.menu.classList.remove('open'); openItem(open.dataset.menuOpen); }
        if (del) { els.menu.classList.remove('open'); askDelete(del.dataset.menuDelete); }
    });

    document.addEventListener('click', event => {
        if (!event.target.closest('[data-scope-context-menu]')) els.menu?.classList.remove('open');
    });
    els.search?.addEventListener('input', event => { state.query = event.target.value; renderContent(); makeIcons(); });
    root.querySelector('[data-scope-refresh]')?.addEventListener('click', () => loadRoot().catch(error => { console.error(error); toast('Unable to refresh explorer.'); }));
    root.querySelector('[data-scope-open-selected]')?.addEventListener('click', () => openItem(state.selected));
    root.querySelector('[data-scope-toggle-inspector]')?.addEventListener('click', () => setInspectorCollapsed(!root.classList.contains('inspector-collapsed')));
    document.querySelector('[data-scope-cancel-delete]')?.addEventListener('click', () => els.confirm.classList.remove('open'));
    document.querySelector('[data-scope-confirm-delete]')?.addEventListener('click', deleteItem);
    els.confirm?.addEventListener('click', event => { if (event.target === els.confirm) els.confirm.classList.remove('open'); });

    try { setInspectorCollapsed(localStorage.getItem('scopeExplorerInspectorCollapsed') === '1', false); } catch (error) {}

    loadRoot().catch(error => {
        console.error(error);
        els.tree.innerHTML = '<div class="scope-empty">Unable to load hierarchy.</div>';
        els.content.innerHTML = '<div class="scope-empty">Unable to load folders.</div>';
        toast('Scope Explorer failed to load.');
    });
})();
</script>
@endpush

@include('common.navigations.footer')

/**
 * renderBadge — JS helper companion for <x-badge> Blade component
 *
 * Use this inside DataTable column render functions where Blade syntax
 * cannot be used. Mirrors the same color palette as badge.blade.php.
 *
 * USAGE (in DataTable columns):
 *   { "data": "role", "render": (data) => renderBadge('amber', data) }
 *   { "data": "facility", "render": (data) => renderBadge('emerald', data) }
 *
 * Available colors: sky | violet | amber | emerald | rose | gray | indigo | green | red | blue
 */

const BADGE_STYLES = {
    sky:     'background:rgba(14,165,233,0.1);color:#38bdf8;',
    violet:  'background:rgba(139,92,246,0.1);color:#a78bfa;',
    amber:   'background:rgba(251,191,36,0.1);color:#fbbf24;',
    emerald: 'background:rgba(52,211,153,0.1);color:#34d399;',
    rose:    'background:rgba(251,113,133,0.1);color:#fb7185;',
    gray:    'background:rgba(156,163,175,0.1);color:#9ca3af;',
    indigo:  'background:rgba(99,102,241,0.1);color:#818cf8;',
    green:   'background:rgba(74,222,128,0.1);color:#4ade80;',
    red:     'background:rgba(248,113,113,0.1);color:#f87171;',
    blue:    'background:rgba(96,165,250,0.1);color:#60a5fa;',
};

/**
 * renderBadge(color, text)
 * @param {string} color - color key from BADGE_STYLES
 * @param {string} text  - label text
 * @returns {string} HTML string
 */
function renderBadge(color, text) {
    if (!text) return '<span style="opacity:0.3;">—</span>';
    const style = BADGE_STYLES[color] || BADGE_STYLES['sky'];
    return `<span style="${style}padding:2px 8px;border-radius:9999px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;">${text}</span>`;
}

/**
 * renderRoleBadge(role)
 * Convenience: auto-selects color based on role value.
 * super → amber, admin → sky, user → gray, others → gray
 */
function renderRoleBadge(role) {
    const map = { super: 'amber', admin: 'sky', user: 'gray' };
    return renderBadge(map[role] || 'gray', role);
}

/**
 * renderResultBadge(result)
 * Convenience: pass, fail, warning auto-coloring.
 */
function renderResultBadge(result) {
    const map = { pass: 'emerald', fail: 'rose', warning: 'amber', ok: 'emerald' };
    const key = (result || '').toLowerCase();
    return renderBadge(map[key] || 'gray', result);
}

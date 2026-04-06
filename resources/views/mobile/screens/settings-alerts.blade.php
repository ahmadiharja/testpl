@extends('mobile.layouts.app')

@php
    $role = $mobileAlertRole ?? ($mobileRole ?? 'user');
    $canManage = in_array($role, ['super', 'admin'], true);
    $canSeeSmtp = $role === 'super';
    $smtp = $smtpDetails;
    $facilityOptions = $alertFacilities ?? collect();
    $defaultFacilityId = $alertDefaultFacilityId ?? '';
@endphp

@push('head')
<style>
.mas-shell{display:grid;gap:1rem}.mas-card{border-radius:1.3rem;border:1px solid rgba(148,163,184,.14);background:#fff;box-shadow:0 12px 26px rgba(15,23,42,.04);padding:1rem}.mas-k{font-size:10px;font-weight:700;letter-spacing:.22em;text-transform:uppercase;color:#94a3b8}.mas-h{margin-top:.28rem;font-size:1.45rem;font-weight:700;line-height:1.08;letter-spacing:-.04em;color:#0f172a}.mas-p{margin-top:.55rem;font-size:12.5px;line-height:1.65;color:#64748b}.mas-tabs{display:grid;gap:.4rem;grid-template-columns:repeat({{ $canSeeSmtp ? 3 : 2 }},minmax(0,1fr));padding:.4rem;border-radius:999px;background:#f8fafc;border:1px solid #e2e8f0}.mas-tab{display:inline-flex;align-items:center;justify-content:center;min-height:2.45rem;border-radius:999px;padding:.45rem .7rem;font-size:12px;font-weight:700;color:#64748b;border:1px solid transparent}.mas-tab.on{border-color:#bfdbfe;background:#eff6ff;color:#0369a1}.mas-row{display:flex;align-items:flex-end;justify-content:space-between;gap:.8rem;margin-bottom:.9rem}.mas-row h3{font-size:1.02rem;font-weight:700;color:#0f172a}.mas-row p{margin-top:.2rem;font-size:12px;line-height:1.55;color:#64748b}.mas-primary{display:inline-flex;align-items:center;justify-content:center;min-height:2.3rem;border-radius:999px;background:linear-gradient(135deg,#0ea5e9,#0284c7);padding:.45rem .9rem;font-size:12px;font-weight:700;color:#fff}.mas-search{position:relative;margin-bottom:.85rem}.mas-search svg{position:absolute;left:.88rem;top:50%;width:.92rem;height:.92rem;transform:translateY(-50%);color:#94a3b8}.mas-search input,.mas-input,.mas-select{width:100%;min-height:2.9rem;border-radius:999px;border:1px solid rgba(148,163,184,.2);background:#fff;padding:.8rem .95rem;font-size:12.5px;color:#0f172a;outline:none}.mas-search input{padding-left:2.5rem}.mas-list{display:grid;gap:.8rem}.mas-item{border-radius:1.05rem;border:1px solid #e2e8f0;background:linear-gradient(180deg,#fff,#f8fafc);padding:.9rem}.mas-top{display:flex;align-items:flex-start;justify-content:space-between;gap:.8rem}.mas-title{font-size:14px;font-weight:700;line-height:1.45;color:#0f172a;word-break:break-word}.mas-meta{margin-top:.15rem;font-size:11.5px;color:#64748b}.mas-badge,.mas-chip,.mas-info-pill{display:inline-flex;align-items:center;min-height:1.7rem;border-radius:999px;padding:.25rem .58rem;font-size:10.5px;font-weight:700}.mas-badge{border:1px solid #e2e8f0;background:#fff;color:#64748b}.mas-badge.on{border-color:#86efac;background:#f0fdf4;color:#15803d}.mas-tags{display:flex;flex-wrap:wrap;gap:.45rem;margin-top:.72rem}.mas-chip{border:1px solid #e2e8f0;background:#fff;color:#475569}.mas-chip.daily{border-color:#bfdbfe;background:#eff6ff;color:#0369a1}.mas-foot{display:flex;align-items:center;justify-content:space-between;gap:.8rem;margin-top:.8rem;padding-top:.75rem;border-top:1px solid #e2e8f0}.mas-info-rail{display:flex;flex-wrap:wrap;gap:.45rem}.mas-info-pill{border:1px solid #dbeafe;background:#f8fbff;color:#475569}.mas-info-pill strong{margin-right:.28rem;color:#0f172a}.mas-actions,.mas-toolbar-actions{display:flex;gap:.45rem;align-items:center}.mas-btn,.mas-secondary{display:inline-flex;align-items:center;justify-content:center;min-height:2rem;border-radius:999px;border:1px solid #e2e8f0;background:#fff;padding:.36rem .78rem;font-size:11.5px;font-weight:700;color:#334155}.mas-btn.danger{border-color:#fda4af;background:#fff1f2;color:#be123c}.mas-secondary{background:#f8fafc}.mas-secondary[disabled],.mas-submit[disabled],.mas-limit-save[disabled]{opacity:.46;pointer-events:none}.mas-form{display:grid;gap:.8rem}.mas-field{display:grid;gap:.35rem}.mas-field label{font-size:10px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#94a3b8}.mas-submit{display:inline-flex;align-items:center;justify-content:center;width:100%;min-height:2.85rem;border-radius:999px;background:linear-gradient(135deg,#0ea5e9,#0284c7);padding:.78rem 1rem;font-size:13px;font-weight:700;color:#fff}.mas-empty{border-radius:1rem;border:1px dashed rgba(148,163,184,.26);background:#f8fafc;padding:.85rem;font-size:12px;color:#64748b}.mas-pager{display:flex;align-items:center;justify-content:space-between;gap:.7rem;margin-top:.9rem;border-top:1px solid #e2e8f0;padding-top:.85rem}.mas-pager small{font-size:11px;font-weight:600;color:#64748b}.mas-pager div{display:flex;gap:.45rem}.mas-pager button{min-height:2rem;border-radius:999px;border:1px solid #e2e8f0;background:#f8fafc;padding:.34rem .72rem;font-size:11px;font-weight:700;color:#334155}.mas-limit-shell{display:grid;gap:.9rem}.mas-limit-intro{display:flex;align-items:flex-start;justify-content:space-between;gap:.8rem;border-bottom:1px solid #e2e8f0;padding-bottom:.9rem}.mas-limit-intro h3{font-size:1.05rem;font-weight:700;color:#0f172a}.mas-limit-intro p{margin-top:.2rem;font-size:12px;line-height:1.55;color:#64748b;max-width:18rem}.mas-limit-pill{display:inline-flex;align-items:center;justify-content:center;min-height:1.9rem;border-radius:999px;border:1px solid #dbeafe;background:#f8fbff;padding:.3rem .65rem;font-size:10.5px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:#0369a1}.mas-limit-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.72rem}.mas-limit-card{display:grid;gap:.5rem;border-radius:1rem;border:1px solid rgba(148,163,184,.16);background:linear-gradient(180deg,#fff,#f8fafc);padding:.78rem .8rem;box-shadow:inset 0 1px 0 rgba(255,255,255,.65)}.mas-limit-card.readonly{background:linear-gradient(180deg,#fcfdff,#f8fafc)}.mas-limit-head{display:flex;align-items:flex-start;justify-content:space-between;gap:.65rem}.mas-limit-name{font-size:10px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#94a3b8;line-height:1.45}.mas-limit-hint{font-size:10.5px;line-height:1.4;color:#94a3b8;text-align:right}.mas-limit-inputwrap{display:flex;align-items:center;gap:.55rem;border-radius:.9rem;border:1px solid rgba(148,163,184,.18);background:#fff;padding:.12rem .16rem .12rem .78rem;transition:border-color .18s ease,box-shadow .18s ease,transform .18s ease}.mas-limit-inputwrap:focus-within{border-color:rgba(56,189,248,.7);box-shadow:0 0 0 3px rgba(56,189,248,.12);transform:translateY(-1px)}.mas-limit-card.readonly .mas-limit-inputwrap{background:#f8fafc;border-color:rgba(148,163,184,.12);box-shadow:none}.mas-limit-input{width:100%;min-width:0;border:0;background:transparent;padding:.58rem 0;font-size:15px;font-weight:700;color:#0f172a;outline:none}.mas-limit-input[readonly]{color:#334155;cursor:default}.mas-limit-unit{display:inline-flex;align-items:center;justify-content:center;min-width:2.4rem;min-height:2.15rem;border-radius:.78rem;background:#f8fafc;padding:0 .6rem;font-size:10.5px;font-weight:700;color:#64748b}.mas-limit-footer{display:flex;align-items:center;justify-content:space-between;gap:.8rem;border-top:1px solid #e2e8f0;padding-top:.95rem}.mas-limit-save{display:inline-flex;align-items:center;justify-content:center;min-height:2.65rem;border-radius:999px;background:linear-gradient(135deg,#0ea5e9,#0284c7);padding:.72rem 1rem;font-size:12px;font-weight:700;color:#fff;min-width:10rem}.mas-smtp-shell{display:grid;gap:.95rem}.mas-smtp-intro{display:flex;align-items:flex-start;justify-content:space-between;gap:.8rem;border-bottom:1px solid #e2e8f0;padding-bottom:.9rem}.mas-smtp-intro h3{font-size:1.05rem;font-weight:700;color:#0f172a}.mas-smtp-intro p{margin-top:.2rem;font-size:12px;line-height:1.55;color:#64748b;max-width:18rem}.mas-smtp-grid{display:grid;gap:.72rem}.mas-smtp-card{display:grid;gap:.42rem;border-radius:1rem;border:1px solid rgba(148,163,184,.16);background:linear-gradient(180deg,#fff,#f8fafc);padding:.78rem .82rem}.mas-smtp-card.readonly{background:linear-gradient(180deg,#fcfdff,#f8fafc)}.mas-smtp-label{font-size:10px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#94a3b8}.mas-smtp-input{width:100%;border:0;background:transparent;padding:0;font-size:14px;font-weight:650;line-height:1.45;color:#0f172a;outline:none}.mas-smtp-input[readonly]{color:#334155;cursor:default}.mas-smtp-toggle{display:flex;align-items:center;justify-content:space-between;gap:.7rem;border-radius:1rem;border:1px solid rgba(148,163,184,.16);background:#fff;padding:.82rem .9rem}.mas-smtp-toggle-copy strong{display:block;font-size:12.5px;color:#0f172a}.mas-smtp-toggle-copy span{display:block;margin-top:.12rem;font-size:11px;color:#64748b}.mas-smtp-toggle-state{display:inline-flex;align-items:center;justify-content:center;min-height:1.8rem;border-radius:999px;border:1px solid #dbeafe;background:#f8fbff;padding:.25rem .68rem;font-size:10.5px;font-weight:700;color:#0369a1}.mas-smtp-field{display:grid;gap:.38rem}.mas-smtp-readonly{padding:.15rem 0;font-size:14px;font-weight:650;line-height:1.45;color:#334155}.mas-smtp-actions{display:flex;gap:.55rem;flex-wrap:wrap}.mas-sheet{position:fixed;inset:0;z-index:140;display:flex;align-items:flex-end;justify-content:center;padding:.75rem}.mas-backdrop{position:absolute;inset:0;background:rgba(15,23,42,.46);backdrop-filter:blur(4px)}.mas-panel{position:relative;width:min(100%,440px);max-height:calc(100vh - 1.5rem);overflow-y:auto;border-radius:1.35rem;background:#fff;box-shadow:0 24px 50px rgba(15,23,42,.22)}.mas-head{position:sticky;top:0;display:flex;align-items:flex-start;justify-content:space-between;gap:.85rem;padding:1rem .95rem .8rem;border-bottom:1px solid #e2e8f0;background:#fff}.mas-head h4{font-size:1rem;font-weight:700;color:#0f172a}.mas-head p{margin-top:.2rem;font-size:12px;line-height:1.5;color:#64748b}.mas-close{display:inline-flex;align-items:center;justify-content:center;width:2.3rem;height:2.3rem;border-radius:999px;border:1px solid #e2e8f0;background:#f8fafc;color:#64748b}.mas-body{padding:1rem}@media (max-width:380px){.mas-limit-grid{grid-template-columns:1fr}.mas-limit-footer,.mas-smtp-intro,.mas-limit-intro{flex-direction:column;align-items:stretch}.mas-limit-save{width:100%}}
</style>
@endpush

@section('content')
<div class="mas-shell" x-data="mobileAlertSettingsPage()" x-init="init()">
    <section class="mas-card"><p class="mas-k">Alert settings</p><h2 class="mas-h">Recipients, limits, delivery</h2><p class="mas-p">This mobile workspace mirrors desktop alert settings. Manage recipients, adjust thresholds, and configure SMTP delivery based on your role.</p></section>
    <section class="mas-card"><div class="mas-tabs"><button type="button" class="mas-tab" :class="{ 'on': activeTab==='alerts' }" @click="activeTab='alerts'">Alerts</button><button type="button" class="mas-tab" :class="{ 'on': activeTab==='limits' }" @click="activeTab='limits'">Limits</button>@if($canSeeSmtp)<button type="button" class="mas-tab" :class="{ 'on': activeTab==='smtp' }" @click="activeTab='smtp'">SMTP</button>@endif</div></section>

    <section class="mas-card" x-show="activeTab==='alerts'" x-cloak>
        <div class="mas-row"><div><h3>Alert recipients</h3><p>Email rules for alert notifications and daily reports.</p></div>@if($canManage)<button type="button" class="mas-primary" @click="openForm()">Add alert</button>@endif</div>
        <div class="mas-search"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg><input type="text" placeholder="Search alert recipients..." x-model.debounce.350ms="search" @input.debounce.350ms="loadAlerts(1)"></div>
        <div class="mas-list">
            <template x-if="loading"><div class="mas-empty">Loading alert recipients...</div></template>
            <template x-if="!loading && items.length===0"><div class="mas-empty">No alert recipients match the current search.</div></template>
            <template x-for="item in items" :key="item.id">
                <article class="mas-item">
                    <div class="mas-top"><div class="min-w-0 flex-1"><p class="mas-title" x-text="item.email"></p><p class="mas-meta" x-text="item.facName || 'All facilities'"></p></div><span class="mas-badge" :class="{ 'on': item.active }" x-text="item.active ? 'Active' : 'Disabled'"></span></div>
                    <div class="mas-tags"><span class="mas-chip daily" x-show="item.dailyReport">Daily report</span><span class="mas-chip" x-show="!item.dailyReport">No daily report</span><span class="mas-chip" x-text="item.facName ? 'Scoped facility' : 'All facilities'"></span></div>
                    <div class="mas-foot">
                        <div class="mas-info-rail">
                            <span class="mas-info-pill"><strong>Alert:</strong> <span x-text="item.active ? 'Enabled' : 'Disabled'"></span></span>
                            <span class="mas-info-pill"><strong>Send daily report:</strong> <span x-text="item.dailyReport ? 'Enabled' : 'Disabled'"></span></span>
                        </div>
                        <div class="mas-actions"><button type="button" class="mas-btn" @click="openForm(item)">Edit</button>@if($canManage)<button type="button" class="mas-btn danger" @click="askDelete(item)">Delete</button>@endif</div>
                    </div>
                </article>
            </template>
        </div>
        <div class="mas-pager"><small x-text="paginationLabel"></small><div><button type="button" @click="loadAlerts(page-1)" :disabled="page<=1">Prev</button><button type="button" @click="loadAlerts(page+1)" :disabled="page>=totalPages">Next</button></div></div>
    </section>

    <section class="mas-card" x-show="activeTab==='limits'" x-cloak>
        <form id="mobile-limit-form" class="mas-limit-shell" @submit.prevent="submitLimits()">@csrf
            <div class="mas-limit-intro">
                <div>
                    <h3>Error limits</h3>
                    <p>Threshold values used to determine alert conditions.</p>
                </div>
                <span class="mas-limit-pill">{{ $errorLimits->count() }} rules</span>
            </div>
            <div class="mas-limit-grid">
                @foreach($errorLimits as $error)
                    @php
                        $hint = trim((string) ($error->suffix ?? ''));
                    @endphp
                    <label class="mas-limit-card" :class="{ 'readonly': !editingLimits }">
                        <div class="mas-limit-head">
                            <span class="mas-limit-name">{{ $error->name }}</span>
                            @if($hint !== '')
                                <span class="mas-limit-hint">{{ $hint }}</span>
                            @endif
                        </div>
                        <span class="mas-limit-inputwrap">
                            <input
                                class="mas-limit-input"
                                type="text"
                                inputmode="decimal"
                                name="{{ $error->id }}"
                                value="{{ $error->value }}"
                                placeholder="{{ $error->suffix }}"
                                :readonly="!editingLimits"
                            >
                            <span class="mas-limit-unit">value</span>
                        </span>
                    </label>
                @endforeach
            </div>
            <div class="mas-limit-footer">
                <div class="mas-toolbar-actions">
                    <button type="button" class="mas-secondary" @click="editingLimits = true" :disabled="editingLimits">Edit limits</button>
                    <button type="submit" class="mas-limit-save" :disabled="!editingLimits || savingLimits" x-text="savingLimits ? 'Saving...' : 'Save limits'"></button>
                </div>
            </div>
        </form>
    </section>

    @if($canSeeSmtp)
    <section class="mas-card" x-show="activeTab==='smtp'" x-cloak>
        <form id="mobile-smtp-form" class="mas-smtp-shell" @submit.prevent="submitSmtp()">@csrf
            <input type="hidden" name="smtp_id" value="{{ $smtp?->id }}"><input type="hidden" name="ajax" value="0">
            <div class="mas-smtp-intro">
                <div>
                    <h3>SMTP delivery</h3>
                    <p>Outbound mail transport for alert notifications and test email.</p>
                </div>
            </div>
            <div class="mas-smtp-grid">
                <label class="mas-smtp-card" :class="{ 'readonly': !editingSmtp }">
                    <span class="mas-smtp-label">Sender email</span>
                    <input class="mas-smtp-input" type="text" name="senderemail" value="{{ $smtp?->senderemail }}" :readonly="!editingSmtp">
                </label>
                <label class="mas-smtp-card" :class="{ 'readonly': !editingSmtp }">
                    <span class="mas-smtp-label">Sender name</span>
                    <input class="mas-smtp-input" type="text" name="sendername" value="{{ $smtp?->sendername }}" :readonly="!editingSmtp">
                </label>
                <label class="mas-smtp-card" :class="{ 'readonly': !editingSmtp }">
                    <span class="mas-smtp-label">SMTP server</span>
                    <input class="mas-smtp-input" type="text" name="smtpserver" value="{{ $smtp?->host }}" :readonly="!editingSmtp">
                </label>
                <label class="mas-smtp-card" :class="{ 'readonly': !editingSmtp }">
                    <span class="mas-smtp-label">SMTP port</span>
                    <input class="mas-smtp-input" type="text" name="smtpport" value="{{ $smtp?->port }}" :readonly="!editingSmtp">
                </label>
                <label class="mas-smtp-card" :class="{ 'readonly': !editingSmtp }">
                    <span class="mas-smtp-label">SMTP user</span>
                    <input class="mas-smtp-input" type="text" name="smtpuser" value="{{ $smtp?->username }}" :readonly="!editingSmtp">
                </label>
                <label class="mas-smtp-card" :class="{ 'readonly': !editingSmtp }">
                    <span class="mas-smtp-label">SMTP password</span>
                    <input class="mas-smtp-input" type="text" name="smtppassword" value="{{ $smtp?->password }}" :readonly="!editingSmtp">
                </label>
                <label class="mas-smtp-toggle">
                    <span class="mas-smtp-toggle-copy">
                        <strong>TLS encryption</strong>
                        <span>Delivery security mode</span>
                    </span>
                    <template x-if="!editingSmtp">
                        <span class="mas-smtp-toggle-state">{{ (($smtp?->encryption ?? '') === 'tls') ? 'Enabled' : 'Disabled' }}</span>
                    </template>
                    <template x-if="editingSmtp">
                        <span class="mas-switch"><input type="checkbox" name="usetls" value="1" @checked(($smtp?->encryption ?? '') === 'tls')><span class="mas-track"></span></span>
                    </template>
                </label>
                <div class="mas-smtp-card">
                    <div class="mas-smtp-field">
                        <span class="mas-smtp-label">Send test email to</span>
                        <input class="mas-smtp-input" type="email" x-model="testEmail" placeholder="name@example.com">
                    </div>
                </div>
            </div>
            <div class="mas-smtp-actions">
                <button type="button" class="mas-btn" @click="sendTestEmail()" :disabled="sendingTest" x-text="sendingTest ? 'Sending...' : 'Send test email'"></button>
            </div>
            <template x-if="testResult"><div class="mas-empty" :style="testResultType==='error' ? 'color:#be123c;border-color:#fda4af;background:#fff1f2' : 'color:#166534;border-color:#86efac;background:#f0fdf4'" x-text="testResult"></div></template>
            <div class="mas-limit-footer">
                <div class="mas-toolbar-actions">
                    <button type="button" class="mas-secondary" @click="editingSmtp = true" :disabled="editingSmtp">Edit SMTP</button>
                    <button type="submit" class="mas-limit-save" :disabled="!editingSmtp || savingSmtp" x-text="savingSmtp ? 'Saving...' : 'Save SMTP'"></button>
                </div>
            </div>
        </form>
    </section>
    @endif

    <template x-teleport="body">
        <div class="mas-sheet" x-show="formOpen" x-cloak>
            <div class="mas-backdrop" @click="closeForm()"></div>
            <div class="mas-panel" @click.stop>
                <div class="mas-head">
                    <div>
                        <p class="mas-k">Alert rule</p>
                        <h4 x-text="formState.id ? 'Edit recipient' : 'Add recipient'"></h4>
                        <p>Set the recipient, activation state, daily report preference, and facility scope.</p>
                    </div>
                    <button type="button" class="mas-close" @click="closeForm()">&times;</button>
                </div>
                <div class="mas-body">
                    <form class="mas-form" @submit.prevent="saveForm()">
                        <div class="mas-field"><label>Recipient email</label><input class="mas-input" type="email" x-model="formState.email" required placeholder="name@example.com"></div>
                        <div class="mas-field" x-show="showFacilitySelect">
                            <label>Facility scope</label>
                            <select class="mas-select" x-model="formState.facility_id">
                                <option value="">All facilities</option>
                                @foreach($facilityOptions as $facility)
                                    <option value="{{ $facility['id'] }}">{{ $facility['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <template x-if="!showFacilitySelect && formFacilityName"><div class="mas-empty" x-text="'Facility scope: ' + formFacilityName"></div></template>
                        <label class="mas-switch"><span>Enable alert</span><input type="checkbox" x-model="formState.actived"><span class="mas-track"></span></label>
                        <label class="mas-switch"><span>Send daily report</span><input type="checkbox" x-model="formState.daily_report"><span class="mas-track"></span></label>
                        <button type="submit" class="mas-submit" :disabled="savingForm" x-text="savingForm ? 'Saving...' : 'Save alert'"></button>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <template x-teleport="body">
        <div class="mas-sheet" x-show="confirmDeleteOpen" x-cloak>
            <div class="mas-backdrop" @click="closeDeleteConfirm()"></div>
            <div class="mas-panel" @click.stop>
                <div class="mas-head">
                    <div>
                        <p class="mas-k">Delete alert</p>
                        <h4>Remove recipient rule?</h4>
                        <p x-text="deleteTarget ? deleteTarget.email : ''"></p>
                    </div>
                    <button type="button" class="mas-close" @click="closeDeleteConfirm()">&times;</button>
                </div>
                <div class="mas-body">
                    <div class="mas-empty">This deletes the saved alert recipient rule.</div>
                    <div class="mas-actions" style="margin-top:1rem;justify-content:flex-end;">
                        <button type="button" class="mas-btn" @click="closeDeleteConfirm()">Cancel</button>
                        <button type="button" class="mas-btn danger" @click="confirmDelete()" :disabled="deleting">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
function mobileAlertSettingsPage(){return{activeTab:'alerts',items:[],loading:false,search:'',page:1,limit:10,total:0,editingLimits:false,savingLimits:false,editingSmtp:false,savingSmtp:false,sendingTest:false,testEmail:'',testResult:'',testResultType:'success',formOpen:false,savingForm:false,confirmDeleteOpen:false,deleting:false,deleteTarget:null,facilities:@json($facilityOptions->values()),defaultFacilityId:@json($defaultFacilityId),formState:{id:0,email:'',facility_id:@json($defaultFacilityId),actived:true,daily_report:true},get totalPages(){return Math.max(1,Math.ceil(this.total/this.limit))},get paginationLabel(){if(!this.total)return'0 results';const s=((this.page-1)*this.limit)+1,e=Math.min(this.total,this.page*this.limit);return`${s}-${e} of ${this.total}`},get showFacilitySelect(){return @json($role==='super')},get formFacilityName(){const m=this.facilities.find(i=>String(i.id)===String(this.formState.facility_id));return m?.name||'Current facility'},init(){this.loadAlerts(1)},resetForm(){this.formState={id:0,email:'',facility_id:this.defaultFacilityId,actived:true,daily_report:true}},async loadAlerts(page=1){this.page=Math.max(1,page);this.loading=true;try{const url=new URL(@json(url('/api/alerts')),window.location.origin);url.searchParams.set('page',this.page);url.searchParams.set('limit',this.limit);if(this.search.trim()!=='')url.searchParams.set('search',this.search.trim());const r=await fetch(url.toString(),{headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},credentials:'same-origin'});if(!r.ok)throw new Error('Failed to load alerts');const p=await r.json();this.items=Array.isArray(p.data)?p.data:[];this.total=Number(p.total||0);if(this.page>this.totalPages)this.page=this.totalPages}catch(e){this.items=[];this.total=0;window.notify&&window.notify('error','Failed to load alert recipients.')}finally{this.loading=false}},openForm(item=null){this.resetForm();if(item&&typeof item==='object'){this.formState={id:item.id,email:item.email||'',facility_id:item.facilityId?String(item.facilityId):this.defaultFacilityId,actived:!!item.active,daily_report:!!item.dailyReport}}this.formOpen=true},closeForm(){this.formOpen=false;this.savingForm=false;this.resetForm()},async saveForm(){this.savingForm=true;try{const fd=new FormData();fd.append('_token',@json(csrf_token()));fd.append('id',this.formState.id||0);fd.append('email',this.formState.email||'');fd.append('actived',this.formState.actived?'1':'');fd.append('daily_report',this.formState.daily_report?'1':'');fd.append('facility_id',this.formState.facility_id||'');const p=await window.Perfectlum.postForm(@json(url('alert-settings')),fd);if(p&&p.success===0)throw new Error(p.msg||'Failed to save alert.');this.closeForm();await this.loadAlerts(this.page);window.notify&&window.notify('success','Alert saved successfully.')}catch(e){window.notify&&window.notify('error',e.message||'Failed to save alert.')}finally{this.savingForm=false}},askDelete(item){this.deleteTarget=item;this.confirmDeleteOpen=true},closeDeleteConfirm(){this.confirmDeleteOpen=false;this.deleting=false;this.deleteTarget=null},async confirmDelete(){if(!this.deleteTarget)return;this.deleting=true;try{const fd=new FormData();fd.append('_token',@json(csrf_token()));fd.append('id',this.deleteTarget.id);const p=await window.Perfectlum.postForm(@json(url('delete-alert')),fd);if(!p.success)throw new Error(p.msg||'Failed to delete alert.');this.closeDeleteConfirm();await this.loadAlerts(this.page);window.notify&&window.notify('success',p.msg||'Alert deleted successfully.')}catch(e){window.notify&&window.notify('error',e.message||'Failed to delete alert.')}finally{this.deleting=false}},async submitLimits(){if(!this.editingLimits)return;this.savingLimits=true;try{const p=await window.Perfectlum.postForm(@json(url('errorlimit-update')),new FormData(document.getElementById('mobile-limit-form')));window.notify&&window.notify(p.success?'success':'error',p.msg||'Failed to update error limits.');if(p.success){this.editingLimits=false}}catch(e){window.notify&&window.notify('error','Failed to update error limits.')}finally{this.savingLimits=false}},async submitSmtp(){if(!this.editingSmtp)return;this.savingSmtp=true;try{const p=await window.Perfectlum.postForm(@json(url('errorsmtp-update')),new FormData(document.getElementById('mobile-smtp-form')));window.notify&&window.notify(p.success?'success':'error',p.msg||'Failed to update SMTP settings.');if(p.success){this.editingSmtp=false}}catch(e){window.notify&&window.notify('error','Failed to update SMTP settings.')}finally{this.savingSmtp=false}},async sendTestEmail(){if(!this.testEmail){this.testResult='Enter a recipient email address first.';this.testResultType='error';return}this.sendingTest=true;this.testResult='';try{const fd=new FormData();fd.append('_token',@json(csrf_token()));fd.append('email',this.testEmail);const r=await fetch(@json(url('sendtestmail')),{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest'},body:fd,credentials:'same-origin'});const t=await r.text();if(!r.ok)throw new Error(t||'Failed to send test email.');this.testResult=t;this.testResultType='success'}catch(e){this.testResult=e.message||'Failed to send test email.';this.testResultType='error'}finally{this.sendingTest=false}}}}
</script>
@endpush

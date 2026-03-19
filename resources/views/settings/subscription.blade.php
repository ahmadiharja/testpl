@include('common.navigations.header')

<main class="main-vertical-layout">
    <div class="container-fluid">
        <section class="py-4" x-data="{ open: false, plan: null, workstations: '' }">
            <x-page-header
                title="Subscription"
                description="Pilih paket langganan tanpa modal Bootstrap. Semua interaksi memakai Alpine dan Tailwind."
            />

            <div class="grid gap-6 lg:grid-cols-2">
                <article class="rounded-[2rem] border border-sky-200 bg-white p-8 shadow-sm">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-600">Monthly</p>
                    <h2 class="mt-4 text-3xl font-semibold text-slate-900">Monthly Plan</h2>
                    <p class="mt-3 text-sm text-slate-500">$2 USD per connected workstation per month.</p>
                    <div class="mt-8 flex items-end gap-2">
                        <span class="text-4xl font-semibold text-slate-900">$2</span>
                        <span class="pb-1 text-sm text-slate-500">/ month</span>
                    </div>
                    <button
                        type="button"
                        class="mt-8 inline-flex rounded-full bg-sky-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-600"
                        @click="plan = 'Monthly Plan'; open = true"
                    >
                        Choose Plan
                    </button>
                </article>

                <article class="relative overflow-hidden rounded-[2rem] border border-emerald-200 bg-slate-950 p-8 text-white shadow-sm">
                    <div class="absolute right-5 top-5 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-200">
                        Most Popular
                    </div>
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-300">5 Years</p>
                    <h2 class="mt-4 text-3xl font-semibold">5 Years Plan</h2>
                    <p class="mt-3 text-sm text-slate-300">$24 USD per connected workstation per year. Save 2 months.</p>
                    <div class="mt-8 flex items-end gap-2">
                        <span class="text-4xl font-semibold">$24</span>
                        <span class="pb-1 text-sm text-slate-300">/ year</span>
                    </div>
                    <button
                        type="button"
                        class="mt-8 inline-flex rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-emerald-100"
                        @click="plan = '5 Years Plan'; open = true"
                    >
                        Choose Plan
                    </button>
                </article>
            </div>

            <div
                x-cloak
                x-show="open"
                class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 px-4"
                x-transition.opacity
                @keydown.escape.window="open = false"
            >
                <div
                    class="w-full max-w-lg rounded-[2rem] bg-white p-8 shadow-2xl"
                    x-transition:enter="transition duration-200 ease-out"
                    x-transition:enter-start="translate-y-4 opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                    x-transition:leave="transition duration-150 ease-in"
                    x-transition:leave-start="translate-y-0 opacity-100"
                    x-transition:leave-end="translate-y-4 opacity-0"
                    @click.outside="open = false"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-500">Subscribe</p>
                            <h3 class="mt-2 text-2xl font-semibold text-slate-900" x-text="plan"></h3>
                        </div>
                        <button type="button" class="rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" @click="open = false">
                            <span class="sr-only">Close</span>
                            &times;
                        </button>
                    </div>

                    <form method="post" class="mt-8 space-y-5">
                        {{ csrf_field() }}
                        <input type="hidden" name="plan" :value="plan">
                        <label class="block space-y-2">
                            <span class="text-sm font-medium text-slate-700">No. of Workstations</span>
                            <input
                                type="number"
                                name="workstations"
                                min="1"
                                x-model="workstations"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-400 focus:ring-4 focus:ring-sky-100"
                                placeholder="Enter quantity"
                            >
                        </label>
                        <div class="flex justify-end gap-3">
                            <button type="button" class="rounded-full border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900" @click="open = false">
                                Cancel
                            </button>
                            <button type="submit" class="rounded-full bg-sky-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-600">
                                Subscribe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</main>

@include('common.navigations.footer')

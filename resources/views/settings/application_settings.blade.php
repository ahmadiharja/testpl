@include('common.navigations.header')

<main class="main-vertical-layout">
    <div class="container-fluid">
        <x-workstation-settings-editor
            title="Application Settings"
            description="Editor workstation settings terpusat dengan Alpine tabs, Tailwind styling, dan save ke endpoint Laravel yang sama."
            :load-ws-id="$load_ws_id"
            :multiple="false"
            workstation-app="ALL"
            leaf="di"
        />
    </div>
</main>

@include('common.navigations.footer')

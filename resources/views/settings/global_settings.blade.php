@include('common.navigations.header')

<main class="main-vertical-layout">
    <div class="container-fluid">
        <x-workstation-settings-editor
            title="Bulk Workstation Settings"
            description="Choose target facilities, workgroups, or workstations, then open a bulk configuration workspace for the affected workstations."
            :multiple="true"
            workstation-app="ALL"
            leaf="di"
            :tree-data="$treeData ?? []"
            :option-catalog="$optionCatalog ?? []"
        />
    </div>
</main>

@include('common.navigations.footer')

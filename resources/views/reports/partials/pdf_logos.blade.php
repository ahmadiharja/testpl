@php
    $embedPdfLogo = function (array $paths) {
        foreach ($paths as $path) {
            if (! is_string($path) || trim($path) === '') {
                continue;
            }

            $candidate = $path;
            if (! is_file($candidate)) {
                $candidate = public_path(ltrim($path, '/\\'));
            }

            if (! is_file($candidate)) {
                continue;
            }

            $type = pathinfo($candidate, PATHINFO_EXTENSION) ?: 'png';
            return 'data:image/' . $type . ';base64,' . base64_encode((string) file_get_contents($candidate));
        }

        return null;
    };

    $logo = $embedPdfLogo([
        $site['Site logo'] ?? null,
        public_path('assets/images/perfectlum-logo.png'),
    ]);

    $logo_qubyx = $embedPdfLogo([
        public_path('assets/images/qubyx-black.png'),
        public_path('assets/images/qubyx-logo.png'),
        public_path('images/qubyx_logo.png'),
    ]);
@endphp

@if($logo_qubyx)
    <img src="{{ $logo_qubyx }}" style="max-width:150px; margin-bottom:20px; margin-right:30px;">
@endif
@if($logo)
    <img src="{{ $logo }}" style="max-width:200px; margin-bottom:20px;">
@endif

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qubyx - Choose Platform</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #4A4A58; 
        }
        
        .modal-bg {
            background-color: #262534;
        }

        /* Hide scrollbar for clean look */
        ::-webkit-scrollbar {
            width: 5px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #494858;
            border-radius: 10px;
        }
        
        .platform-card {
            background: #353444;
            border: 1px solid #494858;
            transition: all 0.3s ease;
        }
        .platform-card:hover {
            border-color: #7C5CBF;
            box-shadow: 0 10px 25px -5px rgba(124, 92, 191, 0.3);
            transform: translateY(-5px);
        }

    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 text-[#E2E1E6]">

    <!-- Main Modal Container -->
    <div class="modal-bg w-full max-w-[900px] min-h-[500px] rounded-3xl shadow-2xl flex flex-col relative overflow-hidden">
        
        <!-- Top Banner -->
        <div class="w-full h-48 relative">
            <div class="absolute inset-0 bg-cover bg-center" 
                 style="background-image: url('{{ asset('assets/images/dune_background.png') }}'); background-position: center bottom;">
                <!-- Overlay gradient -->
                <div class="absolute inset-0 bg-gradient-to-t from-[#262534] via-[#262534]/80 to-transparent"></div>
            </div>
            
            <div class="absolute inset-0 p-8 flex justify-center items-start z-10 w-full mt-4">
                <img src="{{ asset('assets/images/qubyx-logo.png') }}" alt="Qubyx" class="h-10 w-auto">
            </div>
        </div>

        <!-- Content Section -->
        <div class="w-full p-8 md:p-12 flex flex-col items-center justify-center relative -mt-20 z-20">
            
            <h1 class="text-3xl font-semibold text-white mb-2 text-center">Choose your workspace</h1>
            <p class="text-[#A19FAD] text-sm mb-10 text-center max-w-md">
                Your account is linked to multiple platforms. Please select the environment you wish to enter.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-2xl">
                
                <!-- PerfectLum Option -->
                <a href="{{ url('select-platform/perfectlum') }}" class="platform-card rounded-2xl p-6 flex flex-col items-center text-center group cursor-pointer">
                    <div class="h-20 flex items-center justify-center mb-4">
                        <img src="{{ asset('assets/images/perfectlum-logo.png') }}" alt="PerfectLum" class="h-10 w-auto opacity-90 group-hover:opacity-100 transition-opacity">
                    </div>
                    <p class="text-[#A19FAD] text-sm mt-1 leading-relaxed">Tailored for healthcare professionals. Calibrates displays to strict DICOM standards and ensures compliance with medical QA protocols like AAPM.</p>
                </a>
                
                <!-- PerfectChroma Option -->
                <a href="{{ url('select-platform/perfectchroma') }}" class="platform-card rounded-2xl p-6 flex flex-col items-center text-center group cursor-pointer">
                    <div class="h-20 flex items-center justify-center mb-4">
                        <img src="{{ asset('assets/images/perfectchroma-logo.png') }}" alt="PerfectChroma" class="h-10 w-auto opacity-90 group-hover:opacity-100 transition-opacity">
                    </div>
                    <p class="text-[#A19FAD] text-sm mt-1 leading-relaxed">Designed for photographers and creative artists. Delivers precise gamma curves and wider color gamut calibration for flawless visual reproduction.</p>
                </a>

            </div>

            <div class="mt-10">
                <a href="{{ url('logout') }}" class="text-sm text-[#A19FAD] hover:text-white transition-colors underline decoration-transparent hover:decoration-white underline-offset-2">
                    Sign out
                </a>
            </div>

        </div>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
</body>
</html>

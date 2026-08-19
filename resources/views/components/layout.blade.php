@props([
    'title' => 'MadingBoard',
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title }}</title>

    {{-- Manrope + JetBrains Mono fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">

    {{-- Material Symbols Outlined icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="min-h-screen flex flex-col bg-background text-on-surface font-sans antialiased relative overflow-x-hidden">
    {{-- Particle animation canvas --}}
    <canvas id="particle-canvas" class="fixed inset-0 w-full h-full pointer-events-none z-0"></canvas>

    <div class="relative z-10 flex flex-col min-h-screen">
        {{ $slot }}
    </div>

    @stack('scripts')

    <script>
    (function () {
        const canvas = document.getElementById('particle-canvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');

        // Neumorphic pastel palette — matches project accent colors (subtle)
        const colors = [
            'rgba(163, 222, 254, 0.18)', // event
            'rgba(181, 234, 215, 0.18)', // academic
            'rgba(255, 218, 193, 0.18)', // alert
            'rgba(226, 240, 203, 0.18)', // club
            'rgba(186, 230, 253, 0.18)', // announcement
            'rgba(187, 247, 208, 0.18)', // news
            'rgba(199, 206, 234, 0.18)', // finance
            'rgba(254, 240, 138, 0.18)', // hr
        ];

        let w, h, particles = [];
        const COUNT = 20;
        const MAX_R = 4;
        const MIN_R = 1.5;
        const SPEED = 0.15;

        function resize() {
            w = canvas.width = window.innerWidth;
            h = canvas.height = window.innerHeight;
        }

        function createParticle() {
            return {
                x: Math.random() * w,
                y: Math.random() * h,
                r: MIN_R + Math.random() * (MAX_R - MIN_R),
                color: colors[Math.floor(Math.random() * colors.length)],
                vx: (Math.random() - 0.5) * SPEED,
                vy: (Math.random() - 0.5) * SPEED,
                pulse: Math.random() * Math.PI * 2,
                pulseSpeed: 0.005 + Math.random() * 0.01,
            };
        }

        function init() {
            resize();
            particles = [];
            for (let i = 0; i < COUNT; i++) particles.push(createParticle());
        }

        function draw() {
            ctx.clearRect(0, 0, w, h);
            for (const p of particles) {
                p.x += p.vx;
                p.y += p.vy;
                p.pulse += p.pulseSpeed;

                if (p.x < -10) p.x = w + 10;
                if (p.x > w + 10) p.x = -10;
                if (p.y < -10) p.y = h + 10;
                if (p.y > h + 10) p.y = -10;

                const scale = 0.8 + 0.2 * Math.sin(p.pulse);
                const r = p.r * scale;

                ctx.beginPath();
                ctx.arc(p.x, p.y, r, 0, Math.PI * 2);
                ctx.fillStyle = p.color;
                ctx.fill();
            }
            requestAnimationFrame(draw);
        }

        window.addEventListener('resize', resize);
        init();
        draw();
    })();
    </script>
</body>
</html>

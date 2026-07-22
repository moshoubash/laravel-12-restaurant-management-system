<div>
    {{-- Hero --}}
    <section class="relative flex items-center justify-center min-h-screen overflow-hidden" style="background: radial-gradient(ellipse 80% 60% at 50% -10%, rgb(var(--color-primary-400)) 0%, rgb(var(--color-primary-700)) 50%, rgb(var(--color-primary-900)) 100%);">
        {{-- Decorative dot grid --}}
        <div class="absolute inset-0 opacity-[0.06]" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 40px 40px;"></div>

        {{-- Blurry accent orbs --}}
        <div class="absolute -top-40 -left-40 w-[600px] h-[600px] rounded-full opacity-20 blur-[120px]" style="background: radial-gradient(circle, rgb(var(--color-primary-300)) 0%, transparent 70%);"></div>
        <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] rounded-full opacity-20 blur-[120px]" style="background: radial-gradient(circle, rgb(var(--color-primary-200)) 0%, transparent 70%);"></div>

        {{-- Dark overlay at bottom --}}
        <div class="absolute bottom-0 left-0 right-0 h-48" style="background: linear-gradient(to top, rgb(var(--color-surface-50)) 0%, transparent 100%);"></div>

        <div class="relative z-10 max-w-5xl px-6 mx-auto text-center">
            {{-- Logo badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-8 rounded-full text-sm font-medium backdrop-blur-sm" style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.9);">
                <span class="w-2 h-2 bg-white rounded-full"></span>
                Restaurant Management Platform
            </div>

            {{-- Main heading --}}
            <h1 class="text-5xl font-extrabold tracking-tight text-white sm:text-6xl md:text-7xl lg:text-8xl leading-[1.1]">
                Run your<br>
                <span class="relative">
                    restaurant
                    <span class="absolute left-0 right-0 h-3 rounded-full -bottom-2 opacity-30 blur-sm" style="background: rgb(var(--color-primary-300));"></span>
                </span>
                smarter.
            </h1>

            <p class="max-w-2xl mx-auto mt-8 text-lg leading-relaxed sm:text-xl" style="color: rgba(255,255,255,0.7);">
                ReSaaS gives you everything to manage your restaurant — menus, reservations,<br class="hidden sm:inline">
                orders, floor plans, inventory, and real-time reports. No complexity, just results.
            </p>

            {{-- CTAs --}}
            <div class="flex flex-wrap items-center justify-center gap-4 mt-10">
                <a href="{{ route('tenant.menu') }}"
                   class="inline-flex items-center gap-2 px-8 py-3.5 text-base font-bold rounded-xl text-primary-700 shadow-2xl hover:shadow-3xl transition-all duration-200"
                   style="background: white;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    Browse Menu
                </a>
                <a href="{{ route('tenant.reserve') }}"
                   class="inline-flex items-center gap-2 px-8 py-3.5 text-base font-bold rounded-xl text-white ring-1 transition-all duration-200 hover:-translate-y-0.5"
                   style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.25);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Book a Table
                </a>
            </div>

            <p class="mt-8 text-sm" style="color: rgba(255,255,255,0.4);">
                Staff? <a href="/dashboard" class="font-semibold underline underline-offset-2" style="color: rgba(255,255,255,0.7); hover-color: white;">Sign in to the dashboard</a>
            </p>
        </div>
    </section>
</div>

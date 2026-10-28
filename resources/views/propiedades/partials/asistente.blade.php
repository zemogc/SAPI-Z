@php $usaIA = app(\App\Services\IA\AsistenteInmobiliario::class)->usaIA(); @endphp

<div class="card overflow-hidden"
     x-data="asistentePropiedad({ urlDescribir: @js(route('asistente.describir')), urlEstimar: @js(route('asistente.estimar')), propiedadId: @js($propiedad->id) })">
    <div class="bg-gradient-to-br from-violet-600 to-fuchsia-500 px-6 py-4 text-white">
        <div class="flex items-center gap-2">
            <x-icon name="sparkles" class="h-5 w-5" />
            <h2 class="font-semibold">Asistente IA</h2>
        </div>
        <p class="mt-1 text-xs text-violet-100">
            {{ $usaIA ? 'Usa los datos del formulario para ayudarte.' : 'Modo local: activa la IA con ANTHROPIC_API_KEY.' }}
        </p>
    </div>

    <div class="space-y-3 p-5">
        <button type="button" @click="describir" :disabled="cargando" class="btn-ai w-full">
            <svg x-show="cargando === 'descripcion'" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".3" stroke-width="3"/><path d="M22 12a10 10 0 0 0-10-10" stroke="currentColor" stroke-width="3"/></svg>
            <span x-text="cargando === 'descripcion' ? 'Redactando…' : 'Redactar descripción'"></span>
        </button>
        <button type="button" @click="estimar" :disabled="cargando" class="btn-secondary w-full">
            <svg x-show="cargando === 'precio'" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".3" stroke-width="3"/><path d="M22 12a10 10 0 0 0-10-10" stroke="currentColor" stroke-width="3"/></svg>
            <span x-text="cargando === 'precio' ? 'Analizando comparables…' : 'Estimar precio'"></span>
        </button>

        <p x-show="error" x-cloak x-text="error" class="rounded-lg bg-rose-50 px-3 py-2 text-xs font-medium text-rose-700"></p>

        <div x-show="estimacion" x-cloak x-transition class="rounded-xl border border-violet-100 bg-violet-50/60 p-4">
            <template x-if="estimacion && estimacion.sugerido > 0">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-violet-700">Precio sugerido</p>
                    <p class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900" x-text="pesos(estimacion.sugerido)"></p>
                    <p class="text-xs text-slate-500">
                        Rango <span class="font-semibold" x-text="pesos(estimacion.minimo)"></span> – <span class="font-semibold" x-text="pesos(estimacion.maximo)"></span>
                    </p>
                    <p class="mt-3 text-xs leading-relaxed text-slate-600" x-text="estimacion.explicacion"></p>
                    <p class="mt-2 text-[11px] text-slate-400">
                        <span x-text="estimacion.comparables"></span> comparables · <span x-text="estimacion.fuente === 'ia' ? 'análisis con IA' : 'cálculo estadístico'"></span>
                    </p>
                    <button type="button" @click="usarPrecio" class="mt-3 text-xs font-semibold text-violet-700 hover:text-violet-900">Usar precio sugerido →</button>
                </div>
            </template>
            <template x-if="estimacion && estimacion.sugerido === 0">
                <p class="text-xs text-slate-600" x-text="estimacion.explicacion"></p>
            </template>
        </div>
    </div>
</div>

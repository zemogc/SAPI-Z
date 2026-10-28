<x-app-layout title="Asistente IA">
    <x-page-header title="Asistente IA" subtitle="Pregunta lo que necesites sobre tus propiedades, clientes y negocios.">
        <x-slot name="actions">
            <span @class([
                'inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset',
                'bg-violet-50 text-violet-700 ring-violet-200' => $usaIA,
                'bg-amber-50 text-amber-700 ring-amber-200' => ! $usaIA,
            ])>
                <span @class(['h-2 w-2 rounded-full', 'bg-violet-500' => $usaIA, 'bg-amber-500' => ! $usaIA])></span>
                {{ $usaIA ? 'Conectado a Claude' : 'Modo local (sin API key)' }}
            </span>
        </x-slot>
    </x-page-header>

    <div x-data="chatAsistente({ url: @js(route('asistente.preguntar')) })" class="card flex h-[calc(100vh-15rem)] min-h-[32rem] flex-col overflow-hidden">
        <div class="flex-1 space-y-6 overflow-y-auto p-6">
            {{-- Estado inicial --}}
            <div x-show="mensajes.length === 0" class="mx-auto flex max-w-2xl flex-col items-center pt-8 text-center">
                <div class="rounded-2xl bg-gradient-to-br from-violet-600 to-fuchsia-500 p-4 text-white shadow-lg shadow-violet-500/30">
                    <x-icon name="sparkles" class="h-8 w-8" />
                </div>
                <h2 class="mt-5 text-xl font-bold text-slate-900">¿En qué te ayudo hoy?</h2>
                <p class="mt-2 text-sm text-slate-500">El asistente lee tu inventario, clientes y transacciones para responder con tus datos reales.</p>
                <div class="mt-8 grid w-full gap-3 sm:grid-cols-2">
                    @foreach ($sugerencias as $sugerencia)
                        <button type="button" @click="enviar(@js($sugerencia))"
                                class="rounded-xl border border-slate-200 px-4 py-3 text-left text-sm text-slate-700 transition hover:border-violet-300 hover:bg-violet-50/50">
                            {{ $sugerencia }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Conversación --}}
            <template x-for="(mensaje, i) in mensajes" :key="i">
                <div class="flex gap-3" :class="mensaje.role === 'user' ? 'justify-end' : ''">
                    <div x-show="mensaje.role === 'assistant'" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-violet-600 to-fuchsia-500 text-white">
                        <x-icon name="sparkles" class="h-4 w-4" />
                    </div>
                    <div class="max-w-[80%] rounded-2xl px-4 py-3 text-sm leading-relaxed"
                         :class="mensaje.role === 'user' ? 'bg-brand-600 text-white rounded-br-md' : (mensaje.error ? 'bg-rose-50 text-rose-800' : 'bg-slate-100 text-slate-800 rounded-tl-md')">
                        <template x-if="mensaje.html"><div class="respuesta-ia" x-html="mensaje.html"></div></template>
                        <template x-if="! mensaje.html"><p class="whitespace-pre-line" x-text="mensaje.content"></p></template>
                    </div>
                </div>
            </template>

            <div x-show="cargando" x-cloak class="flex gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-violet-600 to-fuchsia-500 text-white">
                    <x-icon name="sparkles" class="h-4 w-4" />
                </div>
                <div class="flex items-center gap-1 rounded-2xl bg-slate-100 px-4 py-3">
                    <span class="h-2 w-2 animate-bounce rounded-full bg-slate-400"></span>
                    <span class="h-2 w-2 animate-bounce rounded-full bg-slate-400 [animation-delay:150ms]"></span>
                    <span class="h-2 w-2 animate-bounce rounded-full bg-slate-400 [animation-delay:300ms]"></span>
                </div>
            </div>
            <div x-ref="final"></div>
        </div>

        <form @submit.prevent="enviar()" class="border-t border-slate-100 bg-slate-50/60 p-4">
            <div class="flex items-end gap-3">
                <textarea x-model="pregunta" rows="1" maxlength="1000" placeholder="Escribe tu pregunta…"
                          @keydown.enter.prevent="if (! $event.shiftKey) enviar()"
                          class="input max-h-40 resize-none" aria-label="Pregunta"></textarea>
                <button type="submit" :disabled="cargando || ! pregunta.trim()" class="btn-ai h-[42px] shrink-0" aria-label="Enviar">
                    <x-icon name="send" class="h-4 w-4" />
                </button>
            </div>
            <p class="mt-2 text-center text-[11px] text-slate-400">Las respuestas se basan en los datos de tu cartera. Verifica las cifras importantes.</p>
        </form>
    </div>
</x-app-layout>

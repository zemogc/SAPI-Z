import axios from 'axios';

const pesos = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 });

function mensajeDeError(error) {
    const datos = error.response?.data;
    if (datos?.errors) {
        return Object.values(datos.errors).flat().join(' ');
    }
    return datos?.message || 'No fue posible contactar al asistente. Intenta de nuevo.';
}

/**
 * Panel de IA dentro del formulario de propiedad: redacta la descripción
 * y estima el precio con los datos que el usuario ya escribió.
 */
export function asistentePropiedad({ urlDescribir, urlEstimar, propiedadId = null }) {
    return {
        cargando: null,
        error: '',
        estimacion: null,
        pesos: (v) => pesos.format(v),

        datos() {
            const form = new FormData(this.$root.closest('form'));
            // _method (PATCH en edición) haría que Laravel rechace el POST al asistente.
            form.delete('_method');
            form.delete('_token');
            return { ...Object.fromEntries(form.entries()), propiedad_id: propiedadId };
        },

        async describir() {
            await this.ejecutar('descripcion', async () => {
                const { data } = await axios.post(urlDescribir, this.datos());
                const campo = document.getElementById('descripcion');
                campo.value = data.descripcion;
                campo.dispatchEvent(new Event('input'));
                campo.classList.add('ring-2', 'ring-violet-300');
                setTimeout(() => campo.classList.remove('ring-2', 'ring-violet-300'), 1500);
            });
        },

        async estimar() {
            await this.ejecutar('precio', async () => {
                const { data } = await axios.post(urlEstimar, this.datos());
                this.estimacion = data;
            });
        },

        usarPrecio() {
            document.getElementById('precio').value = new Intl.NumberFormat('es-CO').format(this.estimacion.sugerido);
        },

        async ejecutar(tarea, accion) {
            this.cargando = tarea;
            this.error = '';
            try {
                await accion();
            } catch (error) {
                this.error = mensajeDeError(error);
            } finally {
                this.cargando = null;
            }
        },
    };
}

/**
 * Chat con el asistente sobre la cartera.
 */
export function chatAsistente({ url }) {
    return {
        pregunta: '',
        mensajes: [],
        cargando: false,

        async enviar(texto = null) {
            const pregunta = (texto ?? this.pregunta).trim();
            if (!pregunta || this.cargando) return;

            const historial = this.mensajes
                .filter((m) => !m.error)
                .map(({ role, content }) => ({ role, content }));

            this.mensajes.push({ role: 'user', content: pregunta });
            this.pregunta = '';
            this.cargando = true;
            this.bajar();

            try {
                const { data } = await axios.post(url, { pregunta, historial });
                this.mensajes.push({ role: 'assistant', content: data.respuesta, html: data.html });
            } catch (error) {
                this.mensajes.push({ role: 'assistant', content: mensajeDeError(error), error: true });
            } finally {
                this.cargando = false;
                this.bajar();
            }
        },

        bajar() {
            this.$nextTick(() => this.$refs.final?.scrollIntoView({ behavior: 'smooth', block: 'end' }));
        },
    };
}

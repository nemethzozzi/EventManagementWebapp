import { createApp } from 'vue'
import App from './App.vue'

import PrimeVue from 'primevue/config'
import ToastService from 'primevue/toastservice'
import { i18n } from './plugins/i18n'

import Aura from '@primevue/themes/aura'
import 'primeicons/primeicons.css'

import router from './router'
import './plugins/echo'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

const app = createApp(App)

app.use(router)
app.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: {
            darkModeSelector: false // TODO - később darkmode
        }
    }
})
app.use(ToastService)
app.use(i18n)

app.mount('#app')


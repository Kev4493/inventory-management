import './assets/Main.scss'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import i18n from '@/plugins/i18n.ts'
import PrimeVue from 'primevue/config'
import Aura from '@primeuix/themes/aura'
import 'primeicons/primeicons.css'

const app = createApp(App)

app.use(router)
app.use(i18n)
app.use(PrimeVue, {
  theme: {
    preset: Aura,
  },
})
app.mount('#app')

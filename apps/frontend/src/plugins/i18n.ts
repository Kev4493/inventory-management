import { createI18n } from 'vue-i18n'
import de from '@/locales/de'

const i18n = createI18n({
  legacy: false, // composition API aktivieren
  locale: 'de',
  fallbackLocale: 'de',
  messages: {
    de,
  },
})

export default i18n

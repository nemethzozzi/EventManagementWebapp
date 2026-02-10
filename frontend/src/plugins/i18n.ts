import { createI18n } from 'vue-i18n'
import hu from '../locales/hu.json'
import en from '../locales/en.json'

const savedLocale = localStorage.getItem('locale')

export const i18n = createI18n({
  legacy: false,
  globalInjection: true,
  locale: savedLocale ?? 'hu',
  fallbackLocale: 'en',
  messages: { hu, en },
})

export type MessageSchema = typeof hu

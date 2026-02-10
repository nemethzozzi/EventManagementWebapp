import { i18n } from '../plugins/i18n.ts'
import { LOCAL, type LocalType } from '../types/Local.ts'

export function t(key: string, params?: Record<string, unknown>): string {
  return i18n.global.t(key, params) as string
}

export function getLocale(): LocalType {
  const saved = localStorage.getItem('locale')
  if (saved === LOCAL.HU || saved === LOCAL.ENG) return saved
  return (i18n.global.locale.value as LocalType) || LOCAL.HU
}

/**
 * Nyelv beállítása
 *
 * @param locale
 */
export function setLocale(locale: LocalType) {
  i18n.global.locale.value = locale
  localStorage.setItem('locale', locale)
  document.documentElement.setAttribute('lang', locale)
}

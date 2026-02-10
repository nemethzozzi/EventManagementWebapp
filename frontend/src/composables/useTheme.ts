import { ref } from 'vue'
import { THEME, type ThemeMode } from '../types/Theme.ts'

const STORAGE_KEY = 'theme' as const

const getInitial = (): ThemeMode => {
  const saved = localStorage.getItem(STORAGE_KEY) as ThemeMode | null
  if (saved === THEME.LIGHT || saved === THEME.DARK) return saved

  // ha nincs mentve, akkor rendszer alapján
  return window.matchMedia?.('(prefers-color-scheme: dark)').matches ? THEME.DARK : THEME.LIGHT
}

const mode = ref<ThemeMode>(getInitial())

const apply = (m: ThemeMode) => {
  const html = document.documentElement
  html.classList.toggle(THEME.DARK, m === THEME.DARK)
  localStorage.setItem(STORAGE_KEY, m)
}

apply(mode.value)

export function useTheme() {
  const setMode = (m: ThemeMode) => {
    mode.value = m
    apply(m)
  }

  const toggle = () => setMode(mode.value === THEME.DARK ? THEME.LIGHT : THEME.DARK)

  return {
    mode,
    isDark: () => mode.value === THEME.DARK,
    toggle,
    setMode,
  }
}

/**
 * Témák
 */
export const THEME = {
  DARK: 'dark',
  LIGHT: 'light',
} as const

/**
 * Téma típus
 */
export type ThemeMode = (typeof THEME)[keyof typeof THEME]

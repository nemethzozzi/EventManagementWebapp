/**
 * Nyelvek
 */
export const LOCAL = {
  HU: 'hu',
  ENG: 'en',
} as const

export type LocalType = (typeof LOCAL)[keyof typeof LOCAL]

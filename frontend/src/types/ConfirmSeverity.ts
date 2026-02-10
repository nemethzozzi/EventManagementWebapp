/**
 * Popup típusok
 */
export const CONFIRMSEVERITY = {
    DANGER: 'danger',
    WARNING: 'warning',
    INFO: 'info',
    SUCCESS: 'success',
} as const

/**
 * Popup típusok típusa
 */
export type ConfirmSeverityType = typeof CONFIRMSEVERITY[keyof typeof CONFIRMSEVERITY]
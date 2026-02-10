/**
 * Értesítések
 */
export const NOTIFY = {
    SUCCESS: 'success',
    INFO: 'info',
    WARNING: 'warn',
    ERROR: 'error',
} as const

/**
 * Értesítés típusok
 */
export type NotifyType = typeof NOTIFY[keyof typeof NOTIFY]

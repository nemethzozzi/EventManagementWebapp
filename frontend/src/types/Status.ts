/**
 * Ügyintézés státuszok
 */
export const STATUS = {
    OPEN: 'open',
    ASSIGNED: 'assigned',
    PENDING: 'pending',
    CLOSED: 'closed',
} as const

/**
 * Státusz típusok
 */
export type StatusType = typeof STATUS[keyof typeof STATUS]
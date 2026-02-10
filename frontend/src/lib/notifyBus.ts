import mitt from 'mitt'
import type {NotifyType} from "../types/Notify.ts";

export type NotifyKeyPayload = {
    severity: NotifyType
    summaryKey?: string
    detailKey: string
    detailParams?: Record<string, unknown>
    life?: number
}

type Events = {
    notify: NotifyKeyPayload
}

export const notifyBus = mitt<Events>()

export const notify = {
    show(payload: NotifyKeyPayload) {
        notifyBus.emit('notify', payload)
    },

    success(detailKey: string, opts: Partial<Omit<NotifyKeyPayload, 'severity' | 'detailKey'>> = {}) {
        notifyBus.emit('notify', { severity: 'success', detailKey, ...opts })
    },

    info(detailKey: string, opts: Partial<Omit<NotifyKeyPayload, 'severity' | 'detailKey'>> = {}) {
        notifyBus.emit('notify', { severity: 'info', detailKey, ...opts })
    },

    warn(detailKey: string, opts: Partial<Omit<NotifyKeyPayload, 'severity' | 'detailKey'>> = {}) {
        notifyBus.emit('notify', { severity: 'warn', detailKey, ...opts })
    },

    error(detailKey: string, opts: Partial<Omit<NotifyKeyPayload, 'severity' | 'detailKey'>> = {}) {
        notifyBus.emit('notify', { severity: 'error', detailKey, ...opts })
    },
}

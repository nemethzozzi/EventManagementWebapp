import mitt from 'mitt'
import { NOTIFY, type NotifyType } from '../types/Notify.ts'

export type NotifyKeyPayload = {
  severity: NotifyType
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
    notifyBus.emit('notify', { severity: NOTIFY.SUCCESS, detailKey, ...opts })
  },

  info(detailKey: string, opts: Partial<Omit<NotifyKeyPayload, 'severity' | 'detailKey'>> = {}) {
    notifyBus.emit('notify', { severity: NOTIFY.INFO, detailKey, ...opts })
  },

  warn(detailKey: string, opts: Partial<Omit<NotifyKeyPayload, 'severity' | 'detailKey'>> = {}) {
    notifyBus.emit('notify', { severity: NOTIFY.WARNING, detailKey, ...opts })
  },

  error(detailKey: string, opts: Partial<Omit<NotifyKeyPayload, 'severity' | 'detailKey'>> = {}) {
    notifyBus.emit('notify', { severity: NOTIFY.ERROR, detailKey, ...opts })
  },
}

import { reactive } from 'vue'
import type {ConfirmSeverityType} from "../types/ConfirmSeverity.ts";

type ConfirmState = {
    visible: boolean
    title: string
    message: string
    severity: ConfirmSeverityType
    confirmLabel: string
    cancelLabel: string
    resolve?: (value: boolean) => void
}

const state = reactive<ConfirmState>({
    visible: false,
    title: '',
    message: '',
    severity: 'danger',
    confirmLabel: 'OK',
    cancelLabel: 'Cancel',
})

export const confirm = {
    state,

    open(options: {
        title?: string
        message: string
        severity?: ConfirmSeverityType
        confirmLabel?: string
        cancelLabel?: string
    }): Promise<boolean> {
        state.title = options.title ?? ''
        state.message = options.message
        state.confirmLabel = options.confirmLabel ?? 'OK'
        state.cancelLabel = options.cancelLabel ?? 'Cancel'
        state.visible = true

        return new Promise<boolean>((resolve) => {
            state.resolve = resolve
        })
    },

    accept() {
        state.visible = false
        state.resolve?.(true)
        state.resolve = undefined
    },

    reject() {
        state.visible = false
        state.resolve?.(false)
        state.resolve = undefined
    },
}

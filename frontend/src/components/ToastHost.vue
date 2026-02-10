<template>
  <Toast />
</template>

<script setup lang="ts">
import { onMounted, onBeforeUnmount } from 'vue'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import { notifyBus, type NotifyKeyPayload} from '../lib/notifyBus'
import {NOTIFY} from "../types/Notify.ts";
import {t} from "../lib/i18n.ts";

const toast = useToast()

const handler = (payload: NotifyKeyPayload) => {
  // TODO ide még a nyelvi kulcsokat megírni
  const summary = payload.summaryKey
      ? t(payload.summaryKey)
      : t(`toast.summary.${payload.severity}`)

  const detail = t(payload.detailKey, payload.detailParams)

  toast.add({
    severity: payload.severity,
    summary,
    detail,
    life: payload.life ?? defaultLife(payload.severity),
  })
}

onMounted(() => {
  notifyBus.on('notify', handler)
})

onBeforeUnmount(() => {
  notifyBus.off('notify', handler)
})

/**
 * Default üzenet élettartam
 *
 * @param severity
 */
function defaultLife(severity: NotifyKeyPayload['severity']) {
  switch (severity) {
    case NOTIFY.SUCCESS: return 2500
    case NOTIFY.INFO: return 3000
    case NOTIFY.WARNING: return 3500
    case NOTIFY.ERROR: return 4000
  }
}
</script>

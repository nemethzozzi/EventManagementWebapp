<template>
  <Dialog
    v-model:visible="confirm.state.visible"
    modal
    :closable="false"
    :draggable="false"
    class="confirm-dialog"
    :style="{ width: 'min(520px, 92vw)' }"
  >
    <template #header>
      <div class="header">
        <span class="title">{{ confirm.state.title }}</span>
      </div>
    </template>

    <div class="content">
      <p class="message">{{ confirm.state.message }}</p>
    </div>

    <template #footer>
      <div class="actions">
        <Button
          severity="secondary"
          outlined
          :label="confirm.state.cancelLabel"
          icon="pi pi-times"
          @click="confirm.reject()"
        />
        <Button
          :severity="buttonSeverity"
          :label="confirm.state.confirmLabel"
          icon="pi pi-check"
          @click="confirm.accept()"
        />
      </div>
    </template>
  </Dialog>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import { confirm } from '../lib/confirmBus'
import { CONFIRMSEVERITY } from '../types/ConfirmSeverity.ts'

const buttonSeverity = computed(() => {
  return confirm.state.severity === CONFIRMSEVERITY.DANGER
    ? CONFIRMSEVERITY.DANGER
    : confirm.state.severity
})
</script>

<style scoped>
.confirm-dialog :deep(.p-dialog-header) {
  padding: 16px 18px;
}

.confirm-dialog :deep(.p-dialog-content) {
  padding: 14px 18px 6px;
}

.confirm-dialog :deep(.p-dialog-footer) {
  padding: 12px 18px 16px;
}

.header {
  display: flex;
  align-items: center;
  gap: 10px;
}

.header i {
  font-size: 1.15rem;
}

.title {
  font-weight: 800;
}

.message {
  margin: 0;
  line-height: 1.5;
  opacity: 0.9;
}

.actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  flex-wrap: wrap;
}
</style>

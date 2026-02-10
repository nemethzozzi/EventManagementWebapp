<template>
  <Card class="card-shell">
    <template #title>
      <div class="title-row">
        <i class="pi pi-users" />
        <span>{{ $t('admin_users_table.title') }}</span>
      </div>
    </template>

    <template #content>
      <DataTable
        :value="users"
        dataKey="id"
        stripedRows
        responsiveLayout="scroll"
        class="users-table"
      >
        <Column :header="$t('admin_users_table.name')" field="name" />
        <Column :header="$t('admin_users_table.email')" field="email" />

        <Column :header="$t('admin_users_table.role')">
          <template #body="{ data }">
            <Select
              :modelValue="data.role"
              :options="roleOptions"
              optionLabel="label"
              optionValue="value"
              class="role-select"
              @update:modelValue="(v) => emitRoleChange(data.id, v)"
            />
          </template>
        </Column>

        <Column :header="$t('admin_users_table.actions')" style="width: 140px">
          <template #body="{ data }">
            <Button
              icon="pi pi-trash"
              severity="danger"
              :outlined="true"
              size="small"
              :disabled="data.id === currentUserId"
              @click="emitDelete(data.id)"
            />
          </template>
        </Column>
      </DataTable>
    </template>
  </Card>
</template>

<script setup lang="ts">
import { type PropType } from 'vue'
import Card from 'primevue/card'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Select from 'primevue/select'
import type { User } from '../types/User.ts'
import { useRoleOptions } from '../constants/roleOptions.ts'

defineProps({
  users: {
    type: Array as PropType<User[]>,
    required: true,
  },
  currentUserId: {
    type: Number as PropType<number | null>,
    required: false,
    default: null,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits<{
  (e: 'role-change', payload: { userId: number; role: string }): void
  (e: 'delete', userId: number): void
}>()

/**
 * Szerepkör opciók
 */
const { roleOptions } = useRoleOptions()

const emitRoleChange = (userId: number, role: string) => {
  emit('role-change', { userId, role })
}

const emitDelete = (userId: number) => {
  emit('delete', userId)
}
</script>

<style scoped>
.card-shell {
  border-radius: 18px;
}

.title-row {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 800;
}

.role-select {
  width: 220px;
  max-width: 100%;
}
</style>

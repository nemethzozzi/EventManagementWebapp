<template>
  <Card class="card-shell">
    <template #title>
      <div class="title-row">
        <i class="pi pi-users" />
        <span>{{ $t('admin_users_table.title') }}</span>
      </div>
    </template>

    <template #content>
      <div v-if="loading" class="center">
        <ProgressSpinner
          style="width: 50px; height: 50px"
          strokeWidth="8"
          fill="transparent"
          animationDuration=".5s"
        />
      </div>
      <div v-else>
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
      </div>
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
import ProgressSpinner from 'primevue/progressspinner'

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

.center {
  display: grid;
  place-items: center;
  padding: 28px 0;
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

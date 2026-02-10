import { computed } from 'vue'
import { ROLE } from '../types/Role.ts'
import { t } from '../lib/i18n.ts'

/**
 * Szerepkör opciók
 */
export function useRoleOptions() {
  const roleOptions = computed(() => [
    { label: t('role_option.user'), value: ROLE.USER },
    { label: t('role_option.helpdesk_agent'), value: ROLE.HELPDESK_AGENT },
    { label: t('role_option.admin'), value: ROLE.ADMIN },
  ])

  return { roleOptions }
}

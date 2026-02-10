/**
 * Különböző role-ok
 */
export const ROLE = {
  USER: 'user',
  HELPDESK_AGENT: 'helpdesk_agent',
  ADMIN: 'admin',
} as const

/**
 * Felhasználó típusok
 */
export type RoleType = (typeof ROLE)[keyof typeof ROLE]

export const ROUTES = {
  LOGIN: '/login',
  FORGOT_PASSWORD: '/forgot-password',
  RESET_PASSWORD: '/reset-password/:token',
  EVENTS: '/events',
  AGENT_DASHBOARD: '/agent-dashboard',
  ADMIN: '/admin',
  PAGE_NOT_FOUND: '/:pathMatch(.*)*',
} as const

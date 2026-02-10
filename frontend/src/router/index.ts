import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import LoginPage from '../views/LoginPage.vue'
import ForgotPasswordPage from '../views/ForgotPasswordPage.vue'
import ResetPasswordPage from '../views/ResetPasswordPage.vue'
import EventsPage from '../views/EventsPage.vue'
import AgentChatDashboard from '../views/AgentChatDashboard.vue'
import AdminUsersPage from '../views/AdminUsersPage.vue'
import {ROUTES} from "../routes/routes.ts";
import NotFoundPage from "../views/NotFoundPage.vue";
import {ROLE} from "../types/Role.ts";


const routes: Array<RouteRecordRaw> = [
  {
    path: ROUTES.LOGIN,
    name: 'Login',
    component: LoginPage,
    meta: { guest: true }
  },
  {
    path: ROUTES.FORGOT_PASSWORD,
    name: 'ForgotPassword',
    component: ForgotPasswordPage,
    meta: { guest: true }
  },
  {
    path: ROUTES.RESET_PASSWORD,
    name: 'ResetPassword',
    component: ResetPasswordPage,
    meta: { guest: true }
  },
  {
    path: '/',
    redirect: ROUTES.EVENTS
  },
  {
    path: ROUTES.EVENTS,
    name: 'Events',
    component: EventsPage,
    meta: { requiresAuth: true }
  },
  {
    path: ROUTES.AGENT_DASHBOARD,
    name: 'AgentDashboard',
    component: AgentChatDashboard,
    meta: { requiresAuth: true, roles: [ROLE.HELPDESK_AGENT, ROLE.ADMIN] }
  },
  {
    path: ROUTES.ADMIN,
    name: 'Admin',
    component: AdminUsersPage,
    meta: { requiresAuth: true, roles: [ROLE.ADMIN] }
  },
  {
    path: ROUTES.PAGE_NOT_FOUND,
    name: 'NotFound',
    component: NotFoundPage,
    meta: { guest: true }
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Navigation guard
router.beforeEach((to, from, next) => {
  const { isAuthenticated, userRole, loadUser } = useAuth()
  
  // User betöltése ha van token
  if (isAuthenticated.value && !userRole.value) {
    loadUser()
  }

  // Guest oldal (login) - ha be van jelentkezve, redirect
  if (to.meta.guest && isAuthenticated.value) {
    return next(ROUTES.EVENTS)
  }

  // Auth szükséges
  if (to.meta.requiresAuth && !isAuthenticated.value) {
    return next(ROUTES.LOGIN)
  }

  // Role ellenőrzés
  if (to.meta.roles && userRole.value) {
    const allowedRoles = to.meta.roles as string[]
    if (!allowedRoles.includes(userRole.value)) {
      return next(ROUTES.EVENTS) // Nincs jogosultság
    }
  }

  next()
})

export default router
import { ref, computed } from 'vue'
import apiClient from '../api/axios.ts'
import { useRouter } from 'vue-router'
import {ROUTES} from "../routes/routes.ts";

interface User {
  id: number
  name: string
  email: string
  role: 'user' | 'helpdesk_agent' | 'admin' // TODO - backendben enum legyen, itt string union type
}

const user = ref<User | null>(null)
const token = ref<string | null>(localStorage.getItem('access_token'))

export function useAuth() {
  const router = useRouter()

  const isAuthenticated = computed(() => !!token.value)
  const userRole = computed(() => user.value?.role)

  /**
   * Felhasználó bejelentkeztetése
   *
   * @param email
   * @param password
   */
  const login = async (email: string, password: string) => {
    const response = await apiClient.post('/login', { email, password })
    token.value = response.data.access_token
    user.value = response.data.user
    localStorage.setItem('access_token', response.data.access_token)
    localStorage.setItem('user', JSON.stringify(response.data.user))
  }

  /**
   * Felhasználó kijelentkeztetése
   */
  const logout = async () => {
    try {
      await apiClient.post('/logout')
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('access_token')
      localStorage.removeItem('user')
      router.push(ROUTES.LOGIN)
    }
  }

  /**
   * Felhasználó betöltése localStorage-ból
   */
  const loadUser = () => {
    const storedUser = localStorage.getItem('user')
    if (storedUser) {
      user.value = JSON.parse(storedUser)
    }
  }

  /**
   * Felhasználó adatainak lekérése
   */
  const fetchUser = async () => {
    try {
      const response = await apiClient.get('/me')
      user.value = response.data
      localStorage.setItem('user', JSON.stringify(response.data))
    } catch (error) {
      console.error('Fetch user error:', error)
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    userRole,
    login,
    logout,
    loadUser,
    fetchUser,
  }
}
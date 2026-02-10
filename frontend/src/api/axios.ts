import axios from 'axios'
import { notify } from '../lib/notifyBus.ts'
import { ROUTES } from '../routes/routes.ts'
import { t } from '../lib/i18n.ts'

const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

/**
 * Request interceptor - JWT token hozzáadása
 */
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('access_token')
    if (token) {
      config.headers = config.headers ?? {}
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  },
)

let isRefreshing = false
let refreshPromise: Promise<string> | null = null

/**
 * Token refresh helper (egyszer fut párhuzamosan is)
 */
async function refreshAccessToken(): Promise<string> {
  if (refreshPromise) return refreshPromise

  refreshPromise = (async () => {
    const refreshToken = localStorage.getItem('access_token')
    if (!refreshToken) throw new Error('No token')
    const res = await axios.post(
      `${import.meta.env.VITE_API_BASE_URL}/refresh`,
      {},
      { headers: { Authorization: `Bearer ${refreshToken}` } },
    )

    const newToken = res.data.access_token as string
    localStorage.setItem('access_token', newToken)
    return newToken
  })()

  try {
    return await refreshPromise
  } finally {
    refreshPromise = null
  }
}

/**
 * Response interceptor - token lejárat kezelése
 */
apiClient.interceptors.response.use(
  (response) => response,
  async (error) => {
    // hálózati / CORS / backend down
    if (!error.response) {
      notify.error(t('axios.error.server_unavailable'))
      return Promise.reject(error)
    }

    const status = error.response.status
    const originalRequest = error.config

    // backend üzenet (ha van)
    const backendKey = error.response.data.message || error.response.data.error

    // 401 -> próbálunk refresh-t, de ne a login/refresh endpointokra
    const isLoginRequest = originalRequest?.url?.includes('/login')
    const isRefreshRequest = originalRequest?.url?.includes('/refresh')

    if (status === 401 && !originalRequest._retry && !isLoginRequest && !isRefreshRequest) {
      originalRequest._retry = true

      try {
        if (!isRefreshing) {
          isRefreshing = true
        }

        const newToken = await refreshAccessToken()

        // eredeti kérés újrapróbálása új tokennel
        originalRequest.headers = originalRequest.headers ?? {}
        originalRequest.headers.Authorization = `Bearer ${newToken}`

        return apiClient(originalRequest)
      } catch (refreshError) {
        // Refresh sikertelen: logout
        notify.error(t('axios.error.expired_session'))

        localStorage.removeItem('access_token')
        localStorage.removeItem('user')

        // hogy ne loopoljon: teljes reload a loginra
        window.location.href = ROUTES.LOGIN
        return Promise.reject(refreshError)
      } finally {
        isRefreshing = false
      }
    }

    // Ha a login request 401 (rossz jelszó), azt NE itt toastold duplán,
    // hanem a komponensben (LoginPage) kezeld.
    if (status === 401 && isLoginRequest) {
      return Promise.reject(error)
    }

    // 403 jogosultság
    if (status === 403) {
      notify.error(t('axios.error.forbidden'))
      return Promise.reject(error)
    }

    // 5xx szerverhiba
    if (status >= 500) {
      notify.error(t('axios.error.server_error'))
      return Promise.reject(error)
    }

    // Egyéb 4xx hibák: ha van backend üzenet, mutassuk
    if (backendKey) {
      notify.error(String(backendKey))
    }

    return Promise.reject(error)
  },
)

export default apiClient

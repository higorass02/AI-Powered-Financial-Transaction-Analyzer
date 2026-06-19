import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'

export function useAuth() {
  const authStore = useAuthStore()
  const uiStore = useUiStore()
  const router = useRouter()

  const user = computed(() => authStore.user)
  const isAuthenticated = computed(() => authStore.isAuthenticated)

  async function login(email: string, password: string) {
    try {
      await authStore.login(email, password)
      router.push('/dashboard')
      uiStore.addToast('success', 'Login realizado com sucesso!')
    } catch {
      uiStore.addToast('error', 'Email ou senha inválidos.')
      throw new Error('Login failed')
    }
  }

  async function logout() {
    await authStore.logout()
    router.push('/login')
  }

  return { user, isAuthenticated, login, logout }
}

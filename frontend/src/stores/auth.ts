import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User } from '@/types'
import { authService } from '@/services/auth'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))

  const isAuthenticated = computed(() => !!token.value && !!user.value)

  async function login(email: string, password: string) {
    const result = await authService.login(email, password)
    token.value = result.token
    user.value = result.user
    localStorage.setItem('auth_token', result.token)
  }

  async function register(data: { name: string; email: string; password: string; password_confirmation: string }) {
    const result = await authService.register(data)
    token.value = result.token
    user.value = result.user
    localStorage.setItem('auth_token', result.token)
  }

  async function logout() {
    try {
      await authService.logout()
    } finally {
      clearAuth()
    }
  }

  async function fetchUser() {
    if (!token.value) return
    try {
      user.value = await authService.me()
    } catch {
      clearAuth()
    }
  }

  function clearAuth() {
    user.value = null
    token.value = null
    localStorage.removeItem('auth_token')
  }

  return { user, token, isAuthenticated, login, register, logout, fetchUser, clearAuth }
})

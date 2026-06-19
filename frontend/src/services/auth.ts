import api from './api'
import type { User } from '@/types'

export const authService = {
  async register(data: { name: string; email: string; password: string; password_confirmation: string }) {
    const response = await api.post<{ token: string; user: User }>('/auth/register', data)
    return response.data
  },

  async login(email: string, password: string) {
    const response = await api.post<{ token: string; user: User }>('/auth/login', { email, password })
    return response.data
  },

  async logout() {
    await api.post('/auth/logout')
  },

  async me() {
    const response = await api.get<{ data: User }>('/auth/me')
    return response.data.data
  },

  async refresh() {
    const response = await api.post<{ token: string }>('/auth/refresh')
    return response.data
  },
}

import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '@/stores/auth'

vi.mock('@/services/auth', () => ({
  authService: {
    login: vi.fn().mockResolvedValue({ token: 'test-token', user: { id: 1, name: 'Test', email: 'test@test.com' } }),
    logout: vi.fn().mockResolvedValue(undefined),
    me: vi.fn().mockResolvedValue({ id: 1, name: 'Test', email: 'test@test.com' }),
  },
}))

vi.mock('@/router', () => ({
  default: { push: vi.fn() },
}))

describe('useAuthStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    localStorage.clear()
    vi.clearAllMocks()
  })

  it('initializes unauthenticated', () => {
    const store = useAuthStore()
    expect(store.isAuthenticated).toBe(false)
    expect(store.user).toBeNull()
  })

  it('logs in and stores token', async () => {
    const store = useAuthStore()
    await store.login('test@test.com', 'password')

    expect(store.token).toBe('test-token')
    expect(store.user?.name).toBe('Test')
    expect(store.isAuthenticated).toBe(true)
  })

  it('clears auth on logout', async () => {
    const store = useAuthStore()
    await store.login('test@test.com', 'password')
    await store.logout()

    expect(store.token).toBeNull()
    expect(store.user).toBeNull()
    expect(store.isAuthenticated).toBe(false)
  })
})

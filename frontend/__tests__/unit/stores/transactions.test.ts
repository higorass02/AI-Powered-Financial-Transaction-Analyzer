import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useTransactionStore } from '@/stores/transactions'
import * as transactionService from '@/services/transactions'

vi.mock('@/services/transactions')

describe('useTransactionStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    vi.clearAllMocks()
  })

  it('initializes with empty state', () => {
    const store = useTransactionStore()
    expect(store.transactions).toEqual([])
    expect(store.loading).toBe(false)
    expect(store.error).toBeNull()
  })

  it('fetches transactions', async () => {
    const mockData = {
      data: [{ id: 1, description: 'UBER', amount: 45.50, type: 'debit' }],
      meta: { current_page: 1, per_page: 20, total: 1, last_page: 1, from: 1, to: 1 },
      links: { first: '', last: '', prev: null, next: null },
    }
    vi.mocked(transactionService.transactionService.list).mockResolvedValue(mockData as never)

    const store = useTransactionStore()
    await store.fetch()

    expect(store.transactions).toHaveLength(1)
    expect(store.loading).toBe(false)
  })

  it('computes total debit amount', () => {
    const store = useTransactionStore()
    store.transactions = [
      { id: 1, amount: 100, type: 'debit' } as never,
      { id: 2, amount: 200, type: 'credit' } as never,
      { id: 3, amount: 50, type: 'debit' } as never,
    ]
    expect(store.totalAmount).toBe(150)
  })
})

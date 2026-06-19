import api from './api'
import type {
  Transaction,
  PaginatedTransactions,
  TransactionFilters,
  CreateTransactionData,
  ImportResult,
} from '@/types'

export const transactionService = {
  async list(filters: TransactionFilters = {}) {
    const response = await api.get<PaginatedTransactions>('/transactions', { params: filters })
    return response.data
  },

  async get(id: number) {
    const response = await api.get<{ data: Transaction }>(`/transactions/${id}`)
    return response.data.data
  },

  async create(data: CreateTransactionData) {
    const response = await api.post<{ data: Transaction }>('/transactions', data)
    return response.data.data
  },

  async update(id: number, data: Partial<CreateTransactionData>) {
    const response = await api.put<{ data: Transaction }>(`/transactions/${id}`, data)
    return response.data.data
  },

  async delete(id: number) {
    await api.delete(`/transactions/${id}`)
  },

  async import(file: File, autoCategorize = true) {
    const formData = new FormData()
    formData.append('file', file)
    formData.append('auto_categorize', String(autoCategorize))

    const response = await api.post<{ data: ImportResult }>('/transactions/import', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data.data
  },

  async analyze(id: number, scope: 'basic' | 'detailed' = 'basic') {
    const response = await api.post(`/transactions/${id}/analyze`, { scope })
    return response.data.data
  },

  async categories() {
    const response = await api.get<{ data: import('@/types').Category[] }>('/categories')
    return response.data.data
  },
}

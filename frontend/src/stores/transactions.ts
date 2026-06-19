import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Transaction, TransactionFilters, PaginatedTransactions, CreateTransactionData } from '@/types'
import { transactionService } from '@/services/transactions'

export const useTransactionStore = defineStore('transactions', () => {
  const transactions = ref<Transaction[]>([])
  const currentPage = ref(1)
  const totalPages = ref(1)
  const total = ref(0)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const categories = ref<import('@/types').Category[]>([])
  const filters = ref<TransactionFilters>({})

  const totalAmount = computed(() =>
    transactions.value
      .filter(t => t.type === 'debit')
      .reduce((sum, t) => sum + Number(t.amount), 0)
  )

  async function fetch(newFilters?: TransactionFilters) {
    loading.value = true
    error.value = null
    if (newFilters) filters.value = { ...filters.value, ...newFilters }

    try {
      const result: PaginatedTransactions = await transactionService.list({
        ...filters.value,
        page: currentPage.value,
        per_page: 20,
      })
      transactions.value = result.data
      currentPage.value = result.meta.current_page
      totalPages.value = result.meta.last_page
      total.value = result.meta.total
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Failed to load transactions'
    } finally {
      loading.value = false
    }
  }

  async function create(data: CreateTransactionData) {
    const transaction = await transactionService.create(data)
    transactions.value.unshift(transaction)
    total.value++
    return transaction
  }

  async function update(id: number, data: Partial<CreateTransactionData>) {
    const updated = await transactionService.update(id, data)
    const index = transactions.value.findIndex(t => t.id === id)
    if (index !== -1) transactions.value[index] = updated
    return updated
  }

  async function remove(id: number) {
    await transactionService.delete(id)
    transactions.value = transactions.value.filter(t => t.id !== id)
    total.value--
  }

  async function fetchCategories() {
    if (categories.value.length > 0) return
    categories.value = await transactionService.categories()
  }

  function setPage(page: number) {
    currentPage.value = page
    fetch()
  }

  return {
    transactions,
    currentPage,
    totalPages,
    total,
    loading,
    error,
    categories,
    filters,
    totalAmount,
    fetch,
    create,
    update,
    remove,
    fetchCategories,
    setPage,
  }
})

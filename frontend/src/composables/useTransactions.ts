import { computed, onMounted, ref } from 'vue'
import { useTransactionStore } from '@/stores/transactions'
import { useUiStore } from '@/stores/ui'
import type { CreateTransactionData, TransactionFilters } from '@/types'

export function useTransactions(autoLoad = true) {
  const store = useTransactionStore()
  const uiStore = useUiStore()
  const submitting = ref(false)

  const transactions = computed(() => store.transactions)
  const loading = computed(() => store.loading)
  const error = computed(() => store.error)
  const totalPages = computed(() => store.totalPages)
  const currentPage = computed(() => store.currentPage)

  if (autoLoad) {
    onMounted(() => store.fetch())
  }

  async function createTransaction(data: CreateTransactionData) {
    submitting.value = true
    try {
      const transaction = await store.create(data)
      uiStore.addToast('success', 'Transação criada com sucesso!')
      uiStore.closeTransactionModal()
      return transaction
    } catch {
      uiStore.addToast('error', 'Erro ao criar transação.')
      throw new Error('Failed to create transaction')
    } finally {
      submitting.value = false
    }
  }

  async function deleteTransaction(id: number) {
    try {
      await store.remove(id)
      uiStore.addToast('success', 'Transação removida.')
    } catch {
      uiStore.addToast('error', 'Erro ao remover transação.')
    }
  }

  function applyFilters(filters: TransactionFilters) {
    store.fetch(filters)
  }

  return {
    transactions,
    loading,
    error,
    totalPages,
    currentPage,
    submitting,
    createTransaction,
    deleteTransaction,
    applyFilters,
    setPage: store.setPage,
  }
}

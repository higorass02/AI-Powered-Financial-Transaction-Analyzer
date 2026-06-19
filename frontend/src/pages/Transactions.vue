<script setup lang="ts">
import { onMounted, ref } from 'vue'
import TransactionTable from '@/components/transactions/TransactionTable.vue'
import TransactionFilters from '@/components/transactions/TransactionFilters.vue'
import TransactionForm from '@/components/transactions/TransactionForm.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { useTransactions } from '@/composables/useTransactions'
import { useTransactionStore } from '@/stores/transactions'
import type { TransactionFilters as TFilters } from '@/types'

const {
  transactions,
  loading,
  totalPages,
  currentPage,
  deleteTransaction,
  setPage,
} = useTransactions()

const store = useTransactionStore()
const showForm = ref(false)

onMounted(() => store.fetchCategories())

function handleFilter(filters: TFilters) {
  store.fetch(filters)
}

function handleImport() {
  // trigger file input
  const input = document.createElement('input')
  input.type = 'file'
  input.accept = '.csv'
  input.onchange = async (e) => {
    const file = (e.target as HTMLInputElement).files?.[0]
    if (file) {
      const { transactionService } = await import('@/services/transactions')
      await transactionService.import(file)
      store.fetch()
    }
  }
  input.click()
}
</script>

<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold text-gray-900">Transações</h1>
      <div class="flex gap-2">
        <button
          class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50"
          @click="handleImport"
        >
          Importar CSV
        </button>
        <button
          class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"
          @click="showForm = true"
        >
          + Nova Transação
        </button>
      </div>
    </div>

    <TransactionFilters @filter="handleFilter" />

    <div v-if="showForm" class="bg-white rounded-xl border border-gray-200 p-4">
      <h2 class="font-semibold text-gray-800 mb-4">Nova Transação</h2>
      <TransactionForm @close="showForm = false" @saved="showForm = false" />
    </div>

    <LoadingSpinner v-if="loading" text="Carregando..." />

    <template v-else>
      <TransactionTable
        :transactions="transactions"
        :loading="loading"
        @delete="deleteTransaction"
      />

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex justify-center gap-2">
        <button
          v-for="page in totalPages"
          :key="page"
          :class="[
            'px-3 py-1.5 rounded-lg text-sm',
            page === currentPage
              ? 'bg-blue-600 text-white'
              : 'border border-gray-300 text-gray-600 hover:bg-gray-50',
          ]"
          @click="setPage(page)"
        >
          {{ page }}
        </button>
      </div>
    </template>
  </div>
</template>

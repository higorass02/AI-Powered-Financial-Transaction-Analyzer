<script setup lang="ts">
import { onMounted, ref } from 'vue'
import SummaryCards from '@/components/dashboard/SummaryCards.vue'
import SpendingChart from '@/components/analytics/SpendingChart.vue'
import TrendChart from '@/components/analytics/TrendChart.vue'
import TransactionTable from '@/components/transactions/TransactionTable.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { useAnalyticsStore } from '@/stores/analytics'
import { useTransactionStore } from '@/stores/transactions'
import { useUiStore } from '@/stores/ui'

const analyticsStore = useAnalyticsStore()
const transactionStore = useTransactionStore()
const uiStore = useUiStore()

onMounted(async () => {
  await Promise.all([
    analyticsStore.fetchSummary(),
    analyticsStore.fetchSpendingByCategory(),
    analyticsStore.fetchMonthlyTrend(),
    transactionStore.fetch(),
    transactionStore.fetchCategories(),
  ])
})

async function handleDelete(id: number) {
  await transactionStore.remove(id)
}
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold text-gray-900">Dashboard</h1>
      <button
        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"
        @click="uiStore.openTransactionModal"
      >
        + Nova Transação
      </button>
    </div>

    <LoadingSpinner v-if="analyticsStore.loading" text="Carregando dados..." />

    <template v-else>
      <SummaryCards :summary="(analyticsStore.summary as any)" />

      <div class="grid lg:grid-cols-2 gap-4">
        <SpendingChart :data="(analyticsStore.spendingByCategory as any[])" />
        <TrendChart :data="(analyticsStore.monthlyTrend as any[])" />
      </div>

      <div>
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Transações Recentes</h2>
        <TransactionTable
          :transactions="transactionStore.transactions.slice(0, 10)"
          :loading="transactionStore.loading"
          @delete="handleDelete"
        />
      </div>
    </template>
  </div>
</template>

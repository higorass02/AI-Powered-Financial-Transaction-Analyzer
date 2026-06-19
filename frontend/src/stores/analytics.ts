import { defineStore } from 'pinia'
import { ref } from 'vue'
import { analyticsService } from '@/services/analytics'

export const useAnalyticsStore = defineStore('analytics', () => {
  const summary = ref<Record<string, unknown> | null>(null)
  const spendingByCategory = ref<unknown[]>([])
  const monthlyTrend = ref<unknown[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchSummary() {
    loading.value = true
    try {
      summary.value = await analyticsService.dashboardSummary()
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Failed to load summary'
    } finally {
      loading.value = false
    }
  }

  async function fetchSpendingByCategory(from?: string, to?: string) {
    loading.value = true
    try {
      spendingByCategory.value = await analyticsService.spendingByCategory(from, to)
    } finally {
      loading.value = false
    }
  }

  async function fetchMonthlyTrend(months = 12) {
    loading.value = true
    try {
      const result = await analyticsService.monthlyTrend(months)
      monthlyTrend.value = result.data
    } finally {
      loading.value = false
    }
  }

  return {
    summary,
    spendingByCategory,
    monthlyTrend,
    loading,
    error,
    fetchSummary,
    fetchSpendingByCategory,
    fetchMonthlyTrend,
  }
})

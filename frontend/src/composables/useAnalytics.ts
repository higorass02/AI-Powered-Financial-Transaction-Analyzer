import { computed, onMounted } from 'vue'
import { useAnalyticsStore } from '@/stores/analytics'

export function useAnalytics() {
  const store = useAnalyticsStore()

  const summary = computed(() => store.summary)
  const spendingByCategory = computed(() => store.spendingByCategory)
  const monthlyTrend = computed(() => store.monthlyTrend)
  const loading = computed(() => store.loading)

  onMounted(() => {
    store.fetchSummary()
    store.fetchSpendingByCategory()
    store.fetchMonthlyTrend()
  })

  return { summary, spendingByCategory, monthlyTrend, loading }
}

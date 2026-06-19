import api from './api'

export const analyticsService = {
  async dashboardSummary() {
    const response = await api.get('/dashboard/summary')
    return response.data.data
  },

  async spendingByCategory(fromDate?: string, toDate?: string) {
    const response = await api.get('/analytics/spending-by-category', {
      params: { from_date: fromDate, to_date: toDate },
    })
    return response.data.data
  },

  async monthlyTrend(months = 12, includeForecast = false) {
    const response = await api.get('/analytics/monthly-trend', {
      params: { months, include_forecast: includeForecast },
    })
    return response.data
  },
}

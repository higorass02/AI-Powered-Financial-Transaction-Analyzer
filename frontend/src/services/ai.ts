import api from './api'
import type { AIInsights } from '@/types'

export const aiService = {
  async insights(period: 'day' | 'week' | 'month' = 'month') {
    const response = await api.get<{ data: AIInsights }>('/ai/insights', { params: { period } })
    return response.data.data
  },

  async recommendations(period: 'month' | 'quarter' | 'year' = 'month') {
    const response = await api.get('/ai/recommendations', { params: { period } })
    return response.data.data
  },
}

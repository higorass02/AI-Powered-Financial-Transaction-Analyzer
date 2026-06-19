export interface AIAnalysis {
  id: number
  transaction_id: number
  category_suggested: string | null
  confidence: number
  is_anomaly: boolean
  anomaly_level: 'low' | 'medium' | 'high' | null
  anomaly_reason: string | null
  reasoning: string | null
  recommendations: Recommendation[] | null
  needs_review: boolean
  user_feedback: string | null
  model_used: string | null
  created_at: string
}

export interface AnomalyItem {
  transaction_id: number
  description: string
  amount: number
  confidence: number
  reason: string
  severity: 'low' | 'medium' | 'high'
}

export interface Recommendation {
  category?: string
  action: string
  current_cost: number
  potential_savings: number
  reasoning: string
  items?: Array<{ name: string; cost: number }>
}

export interface AIInsights {
  anomalies: AnomalyItem[]
  recommendations: Recommendation[]
}

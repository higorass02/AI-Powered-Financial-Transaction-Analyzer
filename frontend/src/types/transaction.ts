import type { Category } from './category'
import type { AIAnalysis } from './ai'
import type { PaginationMeta } from './pagination'

export type TransactionType = 'debit' | 'credit' | 'pix'
export type TransactionStatus = 'pending' | 'approved' | 'failed' | 'canceled'

export interface Transaction {
  id: number
  user_id: number
  category_id: number | null
  description: string
  amount: number
  type: TransactionType
  status: TransactionStatus
  date: string
  notes: string | null
  metadata: Record<string, unknown> | null
  category?: Category
  ai_analysis?: AIAnalysis
  created_at: string
  updated_at: string
  deleted_at: string | null
}

export interface TransactionFilters {
  category_id?: number
  type?: TransactionType
  from_date?: string
  to_date?: string
  sort_by?: 'date' | 'amount'
  sort_order?: 'asc' | 'desc'
  search?: string
  page?: number
  per_page?: number
}

export interface PaginatedTransactions {
  data: Transaction[]
  meta: PaginationMeta
  links: {
    first: string
    last: string
    prev: string | null
    next: string | null
  }
}

export interface CreateTransactionData {
  description: string
  amount: number
  type: TransactionType
  category_id?: number | null
  date: string
  notes?: string
}

export interface ImportResult {
  imported: number
  errors: number
  error_details: Array<{ line: number; error: string }>
}

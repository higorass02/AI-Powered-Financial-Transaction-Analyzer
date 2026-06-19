import { format, parseISO, isValid, startOfMonth, endOfMonth } from 'date-fns'
import { ptBR } from 'date-fns/locale'

export function formatDate(date: string | Date): string {
  const d = typeof date === 'string' ? parseISO(date) : date
  if (!isValid(d)) return '-'
  return format(d, 'dd/MM/yyyy', { locale: ptBR })
}

export function formatDateTime(date: string | Date): string {
  const d = typeof date === 'string' ? parseISO(date) : date
  if (!isValid(d)) return '-'
  return format(d, "dd/MM/yyyy 'às' HH:mm", { locale: ptBR })
}

export function formatMonth(date: string): string {
  const d = parseISO(date + '-01')
  return format(d, 'MMM yyyy', { locale: ptBR })
}

export function currentMonthRange(): { from: string; to: string } {
  const now = new Date()
  return {
    from: format(startOfMonth(now), 'yyyy-MM-dd'),
    to: format(endOfMonth(now), 'yyyy-MM-dd'),
  }
}

export function today(): string {
  return format(new Date(), 'yyyy-MM-dd')
}

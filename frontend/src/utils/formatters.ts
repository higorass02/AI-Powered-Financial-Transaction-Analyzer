export function truncate(str: string, maxLength = 50): string {
  return str.length > maxLength ? str.slice(0, maxLength) + '...' : str
}

export function capitalize(str: string): string {
  return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase()
}

export function transactionTypeLabel(type: string): string {
  const labels: Record<string, string> = {
    debit: 'Débito',
    credit: 'Crédito',
    pix: 'Pix',
  }
  return labels[type] ?? type
}

export function confidenceLabel(confidence: number): string {
  if (confidence >= 0.9) return 'Alta'
  if (confidence >= 0.7) return 'Média'
  return 'Baixa'
}

export function anomalyLevelLabel(level: string): string {
  const labels: Record<string, string> = {
    low: 'Baixo',
    medium: 'Médio',
    high: 'Alto',
  }
  return labels[level] ?? level
}

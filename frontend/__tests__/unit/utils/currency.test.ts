import { describe, it, expect } from 'vitest'
import { formatCurrency, parseCurrency } from '@/utils/currency'

describe('formatCurrency', () => {
  it('formats a number as BRL currency', () => {
    expect(formatCurrency(1500)).toContain('1.500')
    expect(formatCurrency(1500)).toContain('R$')
  })

  it('handles decimal values', () => {
    const result = formatCurrency(45.50)
    expect(result).toContain('45')
  })

  it('handles string input', () => {
    const result = formatCurrency('1234.56')
    expect(result).toContain('1.234')
  })
})

describe('parseCurrency', () => {
  it('parses BRL formatted string', () => {
    expect(parseCurrency('R$ 1.234,56')).toBeCloseTo(1234.56, 1)
  })
})

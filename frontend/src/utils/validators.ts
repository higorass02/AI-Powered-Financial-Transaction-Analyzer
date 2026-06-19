export function isValidEmail(email: string): boolean {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
}

export function isValidAmount(value: string | number): boolean {
  const num = typeof value === 'string' ? parseFloat(value) : value
  return !isNaN(num) && num > 0 && num <= 100000
}

export function isNotFutureDate(date: string): boolean {
  return new Date(date) <= new Date()
}

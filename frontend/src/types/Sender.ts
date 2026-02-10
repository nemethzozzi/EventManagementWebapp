/**
 * Üzenet küldők
 */
export const SENDER = {
  USER: 'user',
  BOT: 'bot',
  AGENT: 'agent',
} as const

export type SenderType = (typeof SENDER)[keyof typeof SENDER]

import type { StatusType } from './Status.ts'
import type { Message } from './Message.ts'

export interface Conversation {
  id: number
  user: {
    name: string
  }
  status: StatusType
  messages: Message[]
}

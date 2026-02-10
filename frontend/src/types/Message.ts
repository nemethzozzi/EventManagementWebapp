import type {SenderType} from "./Sender.ts";

/**
 * Üzenet
 */
export interface Message {
    id: number,
    content: string,
    sender_type: SenderType,
    sender?: {
        name: string
    }
    created_at: string,
}
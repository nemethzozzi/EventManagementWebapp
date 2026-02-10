import type {RoleType} from "./Role.ts";

/**
 * Felhasználó típus
 */
export interface User {
    id: number,
    name: string,
    email: string,
    password: string,
    role: RoleType,
}
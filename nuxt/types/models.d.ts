export {}
declare global {
    export namespace models {

        export interface Provider {
            // columns
            id: number
            user_id: number
            avatar: string|null
            name: string
            payload: string[]
            created_at: Date|null
            updated_at: Date|null
        }
        export type Providers = Provider[]
        export type ProviderResults = Modify<api.MetApiResults, { data: Providers }>

        export interface User {
            // columns
            id: number
            email: string
            name: string|null
            avatar: string|null
            created_at: Date|null
            updated_at: Date|null
        }
        export type Users = User[]
        export type UserResult = Modify<api.MetApiResults, { data: User }>
        export type UserResults = Modify<api.MetApiResults, { data: Users }>

    }
}

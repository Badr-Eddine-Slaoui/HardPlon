export interface PaginatedData<T> {
    data: T,
    current_page: number,
    last_page: number,
    next_page_url: string | null,
    prev_page_url: string | null,
    total: number,
    per_page: number,
    from: number,
    to: number
}
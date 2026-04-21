export default function usePagination(
    fetch: (page: number, per_page: number) => Promise<void>,
    initialPage = 1,
    initialPerPage = 5
) {
    const page = ref(initialPage)
    const perPage = ref(initialPerPage)

    const fetchData = async (newPage = page.value) => {
        page.value = newPage
        await fetch(page.value, perPage.value)
    }

    const changePerPage = async (value: number) => {
        perPage.value = value
        page.value = 1
        await fetch(page.value, perPage.value)
    }

    return {
        page,
        perPage,
        fetchData,
        changePerPage
    }
}
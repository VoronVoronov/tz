<template>
  <div class="container py-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="card-title">История транзакций</h5>
          <div class="search-box">
            <input 
              type="text" 
              v-model="searchQuery" 
              class="form-control" 
              placeholder="Поиск по описанию..."
              @input="handleSearch"
            >
          </div>
        </div>

        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th @click="sort('created_at')" style="cursor: pointer">
                  Дата 
                  <i class="bi" :class="getSortIcon('created_at')"></i>
                </th>
                <th>Тип</th>
                <th>Сумма</th>
                <th>Описание</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="transaction in sortedTransactions" :key="transaction.id">
                <td>{{ formatDate(transaction.created_at) }}</td>
                <td>
                  <span :class="{'text-success': transaction.type === 'deposit', 'text-danger': transaction.type === 'withdrawal'}">
                    {{ transaction.type === 'deposit' ? 'Пополнение' : 'Списание' }}
                  </span>
                </td>
                <td>${{ transaction.amount }}</td>
                <td>{{ transaction.description }}</td>
              </tr>
            </tbody>
          </table>
          <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
              <li v-for="link in paginationLinks" :key="link.label" 
                  class="page-item" 
                  :class="{ 'active': link.active, 'disabled': !link.url }">
                <a class="page-link" 
                   href="#" 
                   @click.prevent="link.url && goToPage(link)"
                   v-html="link.label">
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { useAuth } from '../stores/auth'

const auth = useAuth()
const transactions = ref([])
const searchQuery = ref('')
const sortField = ref('created_at')
const sortDirection = ref('desc')
const currentPage = ref(1)
const lastPage = ref(1)
const paginationLinks = ref([])
let searchDebounceTimer = null

const fetchTransactions = async (page = 1) => {
  try {
    const params = new URLSearchParams({
      page: page.toString(),
      search: searchQuery.value,
      sort_field: sortField.value,
      sort_direction: sortDirection.value
    })

    const response = await axios.get(`/api/transactions?${params}`)
    transactions.value = response.data.data
    currentPage.value = response.data.meta.current_page
    lastPage.value = response.data.meta.last_page
    paginationLinks.value = response.data.meta.links
  } catch (error) {
    console.error('Ошибка получения транзакций:', error)
  }
}

const handleSearch = () => {
  currentPage.value = 1
  if (searchDebounceTimer) {
    clearTimeout(searchDebounceTimer)
  }
  searchDebounceTimer = setTimeout(() => {
    fetchTransactions(1)
  }, 300)
}

const sort = (field: string) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }

  currentPage.value = 1
  fetchTransactions(1)
}

const goToPage = (link) => {
  if (!link.url) return
  
  const url = new URL(link.url)
  const page = url.searchParams.get('page')
  if (page) {
    fetchTransactions(parseInt(page))
  }
}

const getSortIcon = (field: string) => {
  if (sortField.value !== field) return 'bi-arrow-down-up'
  return sortDirection.value === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down'
}

const sortedTransactions = computed(() => {
  let filtered = [...transactions.value]
  
  if (searchQuery.value) {
    filtered = filtered.filter(t => 
      t.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }
  
  return filtered.sort((a, b) => {
    const modifier = sortDirection.value === 'asc' ? 1 : -1
    if (a[sortField.value] < b[sortField.value]) return -1 * modifier
    if (a[sortField.value] > b[sortField.value]) return 1 * modifier
    return 0
  })
})

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString()
}

onMounted(() => {
  if (auth.isAuthenticated()) {
    fetchTransactions()
  }
})

onUnmounted(() => {
  if (searchDebounceTimer) {
    clearTimeout(searchDebounceTimer)
  }
})
</script>

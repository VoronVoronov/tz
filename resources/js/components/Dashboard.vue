<template>
  <div class="container py-4">
    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Текущий баланс</h5>
            <h2 class="mb-4">${{ balance }}</h2>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Последние транзакции</h5>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Дата</th>
                    <th>Тип</th>
                    <th>Сумма</th>
                    <th>Описание</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="transaction in recentTransactions" :key="transaction.id">
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
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { useAuth } from '../stores/auth'

const auth = useAuth()
const balance = ref(0)
const recentTransactions = ref([])
let updateInterval = null

const fetchData = async () => {
  if (!auth.isAuthenticated()) {
    return
  }

  try {
    const [balanceResponse, transactionsResponse] = await Promise.all([
      axios.get('/api/balance', {
        headers: {
          'Authorization': `Bearer ${auth.token.value}`
        }
      }),
      axios.get('/api/transactions/recent', {
        headers: {
          'Authorization': `Bearer ${auth.token.value}`
        }
      })
    ])
    balance.value = balanceResponse.data.data.amount
    recentTransactions.value = transactionsResponse.data.data
  } catch (error) {
    console.error('Ошибка получения данных:', error)
    if (error.response?.status === 401) {
      auth.logout()
    }
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString()
}

onMounted(() => {
  fetchData()
  updateInterval = setInterval(fetchData, 10000)
})

onUnmounted(() => {
  if (updateInterval) {
    clearInterval(updateInterval)
  }
})
</script>

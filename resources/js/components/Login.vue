<template>
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">Вход</div>
        <div class="card-body">
          <form @submit.prevent="handleSubmit">
            <div class="mb-3">
              <label for="email" class="form-label">Электронная почта</label>
              <input
                type="email"
                class="form-control"
                id="email"
                v-model="email"
                required
              >
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Пароль</label>
              <input
                type="password"
                class="form-control"
                id="password"
                v-model="password"
                required
              >
            </div>
            <button type="submit" class="btn btn-primary" :disabled="loading">
              {{ loading ? 'Загрузка...' : 'Войти' }}
            </button>
            <div v-if="error" class="alert alert-danger mt-3">
              {{ error }}
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuth } from '../stores/auth'

const auth = useAuth()
const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

const handleSubmit = async () => {
  loading.value = true
  error.value = ''
  
  try {
    const success = await auth.login(email.value, password.value)
    if (!success) {
      error.value = 'Неверный логин или пароль'
    }
  } catch (e) {
    error.value = 'Произошла ошибка'
  } finally {
    loading.value = false
  }
}
</script>

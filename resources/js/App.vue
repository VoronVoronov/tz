<template>
  <div class="container-fluid">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Система управления балансом</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <template v-if="auth.isAuthenticated()">
              <li class="nav-item">
                <a 
                  class="nav-link" 
                  :class="{ active: currentView === 'dashboard' }"
                  href="#"
                  @click.prevent="currentView = 'dashboard'"
                >
                  Главная
                </a>
              </li>
              <li class="nav-item">
                <a 
                  class="nav-link" 
                  :class="{ active: currentView === 'history' }"
                  href="#"
                  @click.prevent="currentView = 'history'"
                >
                  История транзакций
                </a>
              </li>
            </template>
          </ul>
          <ul class="navbar-nav">
            <template v-if="auth.isAuthenticated()">
              <li class="nav-item">
                <span class="nav-link">{{ auth.user?.email }}</span>
              </li>
              <li class="nav-item">
                <a 
                  class="nav-link" 
                  href="#"
                  @click.prevent="handleLogout"
                >
                  Выйти
                </a>
              </li>
            </template>
          </ul>
        </div>
      </div>
    </nav>

    <component v-if="auth.isAuthenticated()" :is="currentComponent" />
    <Login v-else @success="handleLoginSuccess" />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAuth } from './stores/auth'
import Dashboard from './components/Dashboard.vue'
import TransactionHistory from './components/TransactionHistory.vue'
import Login from './components/Login.vue'

const auth = useAuth()
const currentView = ref('dashboard')

const currentComponent = computed(() => {
  switch (currentView.value) {
    case 'history':
      return TransactionHistory
    default:
      return Dashboard
  }
})

const handleLogout = () => {
  auth.logout()
}

const handleLoginSuccess = () => {
  currentView.value = 'dashboard'
}

onMounted(async () => {
  if (auth.token.value) {
    await auth.fetchUser()
  }
})
</script>

<style>
.navbar-nav .nav-link {
  cursor: pointer;
}
</style>

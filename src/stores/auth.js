import { defineStore } from 'pinia'
import axios from 'axios'
import { useErrorStore } from './error'

const API_URL = 'https://dev-api.aiscreen.io/api/v1'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('auth_token'),
    loading: false
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    
    getAuthHeader: (state) => ({
      Authorization: `Bearer ${state.token}`
    })
  },

  actions: {
    async login(email, password) {
      this.loading = true
      const errorStore = useErrorStore()
      
      try {
        const response = await axios.post(`${API_URL}/login`, {
          email,
          password,
          remember_me: 1
        })
        
        this.token = response.data.token
        localStorage.setItem('auth_token', this.token)
        
        // Configure axios default header
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        
        return response.data
      } catch (error) {
        errorStore.setError('Login failed. Please check your credentials.')
        throw error
      } finally {
        this.loading = false
      }
    },

    logout() {
      this.token = null
      localStorage.removeItem('auth_token')
      delete axios.defaults.headers.common['Authorization']
    },

    initializeAuth() {
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
      }
    }
  }
})

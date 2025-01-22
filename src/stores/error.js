import { defineStore } from 'pinia'

export const useErrorStore = defineStore('error', {
  state: () => ({
    message: null
  }),

  actions: {
    setError(message) {
      this.message = message
      setTimeout(() => {
        this.clearError()
      }, 5000)
    },

    clearError() {
      this.message = null
    }
  }
})

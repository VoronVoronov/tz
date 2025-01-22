import { defineStore } from 'pinia'
import axios from 'axios'
import { useErrorStore } from './error'
import { useAuthStore } from './auth'

const API_URL = 'https://dev-api.aiscreen.io/api/v1'

export const useTemplatesStore = defineStore('templates', {
  state: () => ({
    templates: [],
    loading: false,
    currentTemplate: null,
    filters: {
      tags: [],
      name: ''
    },
    draft: JSON.parse(localStorage.getItem('templateDraft')) || null,
    tags: []
  }),

  getters: {
    filteredTemplates: (state) => {
      let filtered = [...state.templates]

      if (state.filters.name) {
        filtered = filtered.filter(template => 
          template.name.toLowerCase().includes(state.filters.name.toLowerCase())
        )
      }

      if (state.filters.tags.length > 0) {
        filtered = filtered.filter(template => 
          template.tags && template.tags.some(tag => state.filters.tags.includes(tag))
        )
      }



      return filtered
    },

    getTags: (state) => {
      return state.tags
    },

    getFilters: (state) => {
        return state.filters
    },

    getTemplates: (state) => {
      return state.templates
    }
  },

  actions: {
    async fetchTemplates(filter = null) {
      this.loading = true
      const errorStore = useErrorStore()
      const authStore = useAuthStore()
      const params = {
        'page[number]': 1,
        'page[size]': 20
      }
      if(filter !== null){
        console.log(filter)
        if(filter.name != null) {
          params["filter[name]"] = filter.name
        }
        if(filter.tags != null) {
          params["filter[tags]"] = filter.tags
        }
      }
      try {
        const response = await axios.get(`${API_URL}/canvas_templates`, {
          params: params,
          headers: {
            Authorization: `Bearer ${authStore.token}`
          }
        })
        this.templates = response.data
      } catch (error) {
        errorStore.setError('Failed to fetch templates')
        console.error('Error fetching templates:', error)
      } finally {
        this.loading = false
      }
    },

    async fetchTemplate(id) {
      const errorStore = useErrorStore()
      const authStore = useAuthStore()
      try {
        const response = await axios.get(`${API_URL}/canvas_templates/${id}`, {
          headers: {
            Authorization: `Bearer ${authStore.token}`
          }
        })
        this.currentTemplate = response.data
        return response.data
      } catch (error) {
        errorStore.setError('Failed to fetch template details')
        console.error('Error fetching template:', error)
      }
    },

    async createTemplate(templateData) {
      const errorStore = useErrorStore()
      const authStore = useAuthStore()
      const formData = new FormData()
      
      Object.entries(templateData).forEach(([key, value]) => {
        if (key === 'tags' && value !== null){
          if(value.length > 0) {
            value.forEach(value => {
              formData.append('tags[]', value)
            })
          }
        } else if (value !== null && value !== undefined) {
          if(key === 'preview_image'){
            if(typeof value !== 'string') {
              formData.append(key, value)
            }
          }else {
            formData.append(key, value)
          }
        }
      })

      try {
        const response = await axios.post(
          `${API_URL}/canvas_templates`,
          formData,
          {
            headers: {
              'Content-Type': 'multipart/form-data',
              Authorization: `Bearer ${authStore.token}`
            }
          }
        )
        this.templates.push(response.data)
        this.clearDraft()
        return response.data
      } catch (error) {
        errorStore.setError('Failed to create template')
        throw error
      }
    },

    async updateTemplate(id, templateData) {
      const errorStore = useErrorStore()
      const authStore = useAuthStore()
      const formData = new FormData()
      console.log(templateData)
      Object.entries(templateData).forEach(([key, value]) => {
        if (key === 'tags' && value !== null){
          if(value.length > 0) {
            value.forEach(value => {
              formData.append('tags[]', value)
            })
          }
        } else if (value !== null && value !== undefined) {
          if(key === 'preview_image'){
            if(typeof value !== 'string') {
              formData.append(key, value)
            }
          }else {
            formData.append(key, value)
          }
        }
      })

      try {
        const response = await axios.post(
          `${API_URL}/canvas_templates/${id}?_method=PATCH`,
          formData,
          {
            headers: {
              'Content-Type': 'multipart/form-data',
              Authorization: `Bearer ${authStore.token}`
            }
          }
        )
        const index = this.templates.findIndex(t => t.id === id)
        if (index !== -1) {
          this.templates[index] = response.data
        }
        this.clearDraft()
        return response.data
      } catch (error) {
        errorStore.setError('Failed to update template')
        throw error
      }
    },

    async deleteTemplate(id) {
      const errorStore = useErrorStore()
      const authStore = useAuthStore()
      try {
        await axios.delete(`${API_URL}/canvas_templates`,
           {
           data: {
             id: id
           },
          headers: {
            Authorization: `Bearer ${authStore.token}`
          }
        })
        this.templates = this.templates.filter(t => t.id !== id)
      } catch (error) {
        errorStore.setError('Failed to delete template')
        throw error
      }
    },

    setSearchFilter(search) {
      this.filters.name = search
    },

    setTagsFilter(tags) {
      this.filters.tags = tags
    },

    saveDraft(templateData) {
      this.draft = templateData
      localStorage.setItem('templateDraft', JSON.stringify(templateData))
    },

    clearDraft() {
      this.draft = null
      localStorage.removeItem('templateDraft')
    },

    async getTagsFromBackend(){
      const errorStore = useErrorStore()
      const authStore = useAuthStore()
      try {
        const response = await axios.get(`${API_URL}/canvas_templates/tags/list`,
            {
              headers: {
                Authorization: `Bearer ${authStore.token}`
              }
            })
        this.tags = response.data
      } catch (error) {
        errorStore.setError('Failed to delete template')
        throw error
      }
    }
  }
})

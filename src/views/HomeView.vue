<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-semibold text-gray-900">Templates</h1>
      <router-link
        to="/template/new"
        class="btn btn-primary"
      >
        Add Template
      </router-link>
    </div>

    <!-- Search and Filters -->
    <div class="space-y-4">
      <input
        type="text"
        v-model="searchQuery"
        placeholder="Search templates..."
        class="input"
        @input="debouncedSearch"
      />

      <div class="flex flex-wrap gap-2">
        <button
          v-for="tag in store.getTags"
          :key="tag"
          @click="toggleTag(tag)"
          :class="[
            'px-3 py-1 rounded-full text-sm font-medium transition-colors duration-200',
            selectedTags.includes(tag)
              ? 'bg-primary-100 text-primary-800 hover:bg-primary-200'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          ]"
        >
          {{ tag }}
        </button>
      </div>
    </div>

    <!-- Templates Grid -->
    <div v-if="!store.loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="template in store.getTemplates"
        :key="template.id"
        class="card group"
      >
        <div class="aspect-video relative overflow-hidden rounded-t-lg bg-gray-100">
          <img
            v-if="template.preview_image"
            :src="template.preview_image"
            :alt="template.name"
            class="w-full h-full object-cover"
          />
          <div v-else class="flex items-center justify-center h-full text-gray-400">
            No preview
          </div>
          
          <!-- Hover Actions -->
          <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center space-x-4">
            <router-link
              :to="{ name: 'template', params: { id: template.id }}"
              class="btn btn-primary"
            >
              Edit
            </router-link>
            <button
              @click="confirmDelete(template)"
              class="btn bg-red-600 text-white hover:bg-red-700"
            >
              Delete
            </button>
          </div>
        </div>

        <div class="p-4 space-y-2">
          <h3 class="font-medium text-gray-900">{{ template.name }}</h3>
          <p class="text-sm text-gray-500">{{ template.description }}</p>
          
          <div class="flex flex-wrap gap-1">
            <span
              v-for="tag in template.tags"
              :key="tag"
              class="px-2 py-0.5 bg-gray-100 text-xs rounded-full text-gray-600"
            >
              {{ tag }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-else class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
    </div>

    <!-- Empty State -->
    <div
      v-if="!store.loading && store.templates.length === 0"
      class="text-center py-12 bg-white rounded-lg shadow-sm"
    >
      <h3 class="text-lg font-medium text-gray-900">No templates yet</h3>
      <p class="mt-2 text-gray-500">Get started by creating your first template</p>
      <router-link
        to="/template/new"
        class="btn btn-primary mt-4 inline-block"
      >
        Create Template
      </router-link>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="templateToDelete" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-medium text-gray-900">Delete Template</h3>
        <p class="mt-2 text-gray-500">
          Are you sure you want to delete "{{ templateToDelete.name }}"? This action cannot be undone.
        </p>
        <div class="mt-4 flex justify-end space-x-3">
          <button
            @click="templateToDelete = null"
            class="btn btn-secondary"
          >
            Cancel
          </button>
          <button
            @click="deleteTemplate"
            class="btn bg-red-600 text-white hover:bg-red-700"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useTemplatesStore } from '../stores/templates'
import debounce from 'lodash.debounce'

const store = useTemplatesStore()
const searchQuery = ref('')
const selectedTags = ref([])
const templateToDelete = ref(null)

onMounted(() => {
  store.fetchTemplates()
  store.getTagsFromBackend()
})

const debouncedSearch = debounce((e) => {
  store.setSearchFilter(e.target.value)
  store.fetchTemplates(store.getFilters)
}, 300)

const toggleTag = (tag) => {
  const index = selectedTags.value.indexOf(tag)
  if (index === -1) {
    selectedTags.value.push(tag)
  } else {
    selectedTags.value.splice(index, 1)
  }
  store.setTagsFilter(selectedTags.value)
  store.fetchTemplates(store.getFilters)
}

const confirmDelete = (template) => {
  templateToDelete.value = template
}

const deleteTemplate = async () => {
  if (templateToDelete.value) {
    try {
      await store.deleteTemplate(templateToDelete.value.id)
      templateToDelete.value = null
    } catch (error) {
      console.error('Failed to delete template:', error)
    }
  }
}
</script>

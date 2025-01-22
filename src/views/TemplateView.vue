<template>
  <div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-semibold text-gray-900">
        {{ isEditing ? 'Edit Template' : 'New Template' }}
      </h1>
      <router-link
        to="/"
        class="text-gray-600 hover:text-gray-900"
      >
        Back to Templates
      </router-link>
    </div>

    <form @submit.prevent="saveTemplate" class="space-y-6 bg-white p-6 rounded-lg shadow">
      <!-- Name -->
      <div>
        <label for="name" class="label">Name *</label>
        <input
          id="name"
          v-model="template.name"
          required
          class="input"
          :class="{ 'border-red-500': errors.name }"
          type="text"
          @input="validateName"
        />
        <p v-if="errors.name" class="mt-1 text-sm text-red-600">
          {{ errors.name }}
        </p>
      </div>

      <!-- Description -->
      <div>
        <label for="description" class="label">Description</label>
        <textarea
          id="description"
          v-model="template.description"
          class="input"
          rows="4"
        ></textarea>
      </div>

      <!-- Dimensions -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label for="width" class="label">Width</label>
          <input
            id="width"
            v-model="template.width"
            type="number"
            class="input"
            required
          />
        </div>
        <div>
          <label for="height" class="label">Height</label>
          <input
            id="height"
            v-model="template.height"
            type="number"
            class="input"
            required
          />
        </div>
      </div>

      <!-- Preview Image -->
      <div>
        <label for="preview" class="label">Preview Image</label>
        <div class="mt-2 flex items-center space-x-4">
          <img
            v-if="previewUrl"
            :src="previewUrl"
            alt="Template preview"
            class="h-32 w-32 object-cover rounded"
          />
          <input
            id="preview"
            type="file"
            accept="image/*"
            @change="handleImageUpload"
            class="input"
          />
        </div>
      </div>

      <!-- Tags -->
      <div>
        <label class="label">Tags</label>
        <div class="space-y-2">
          <div class="flex items-center space-x-2">
            <input
              v-model="tagInput"
              @keydown.enter.prevent="addTag"
              placeholder="Add tag and press Enter"
              class="input"
            />
          </div>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="tag in template.tags"
              :key="tag"
              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800"
            >
              {{ tag }}
              <button
                type="button"
                @click="removeTag(tag)"
                class="ml-1 text-primary-600 hover:text-primary-800"
              >
                ×
              </button>
            </span>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end space-x-3">
        <router-link
          to="/"
          class="btn btn-secondary"
        >
          Cancel
        </router-link>
        <button
          type="submit"
          class="btn btn-primary"
          :disabled="loading"
        >
          {{ loading ? 'Saving...' : 'Save Template' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTemplatesStore } from '../stores/templates'

const route = useRoute()
const router = useRouter()
const store = useTemplatesStore()

const template = ref({
  name: '',
  description: '',
  width: '',
  height: '',
  preview_image: null,
  tags: [],
  objects: null,
  type: ''
})
const tagInput = ref('')
const loading = ref(false)
const errors = ref({})
const previewUrl = ref('')

const isEditing = computed(() => !!route.params.id)

// Load template data for editing
onMounted(async () => {
  if (isEditing.value) {
    try {
      const data = await store.fetchTemplate(route.params.id)
      template.value = { ...data }
      if (data.preview_image) {
        previewUrl.value = data.preview_image
      }
    } catch (error) {
      console.error('Failed to load template:', error)
    }
  } else if (store.draft) {
    // Load draft for new template
    template.value = { ...store.draft }
    if (store.draft.preview_image) {
      previewUrl.value = URL.createObjectURL(store.draft.preview_image)
    }
  }
})

// Save draft when template changes
watch(template, (newValue) => {
  if (!isEditing.value) {
    store.saveDraft(newValue)
  }
}, { deep: true })

const validateName = () => {
  if (!template.value.name.trim()) {
    errors.value.name = 'Name is required'
  } else {
    errors.value.name = null
  }
}

const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    template.value.preview_image = file
    previewUrl.value = URL.createObjectURL(file)
  }
}

const addTag = () => {
  if (tagInput.value.trim() && !template.value.tags.includes(tagInput.value.trim())) {
    template.value.tags = [...(template.value.tags || []), tagInput.value.trim()]
  }
  tagInput.value = ''
}

const removeTag = (tag) => {
  template.value.tags = template.value.tags.filter(t => t !== tag)
}

const saveTemplate = async () => {
  validateName()
  if (errors.value.name) return

  loading.value = true
  try {
    if (isEditing.value) {
      await store.updateTemplate(route.params.id, template.value)
    } else {
      await store.createTemplate(template.value)
    }
    router.push('/')
  } catch (error) {
    console.error('Failed to save template:', error)
  } finally {
    loading.value = false
  }
}
</script>

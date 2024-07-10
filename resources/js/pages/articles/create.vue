<script setup>
const form = ref({
  lang: 'fr_FR',
})

const langs = ref([])
const operators = ref([])
const errors = ref([])
const hasError = ref(false)
const error = ref('')
const successMessage = ref('')
const hasSuccess = ref(false)

const submitForm = async () => {
  const response = await $api('/admin/articles', {
    method: 'POST',
    body: form.value,
    onResponseError({ response }) {
      errors.value = response._data.errors
      error.value = response._data.message
      hasError.value = true
    },
  })

  errors.value = {}
  successMessage.value = response.message
  hasSuccess.value = response.success
  form.value = {}
}

const fetchOperators = async () => {
  const res = await $api('/admin/fetch/operator')

  operators.value = res
}

const fetchLangs = async () => {
  const res = await $api('/admin/fetch/langs')

  langs.value = res
}

onMounted(() => {
  fetchLangs()
})
</script>

<template>
  <div>
    <VSnackbar
      v-model="hasSuccess"
      location="top end"
      color="success"
    >
      <VIcon icon="tabler-exclamation-circle" />
      {{ successMessage }}
    </VSnackbar>

    <VSnackbar
      v-model="hasError"
      location="top end"
      color="error"
    >
      <VIcon icon="tabler-exclamation-circle" />
      {{ error }}
    </VSnackbar>

    <VCard title="New Article">
      <template #append>
        <div class="mt-n4 me-n2">
          <VBtn :to="{ name: 'articles' }">
            <VIcon
              variant="tonal"
              icon="tabler-list"
              start
            />
            Articles
          </VBtn>
        </div>
      </template>
      <VCardText>
        <VForm @submit.prevent="submitForm">
          <VRow>
            <!-- 👉 Languages -->
            <VCol
              cols="12"
              md="2"
            >
              <AppSelect
                v-model="form.lang" 
                label="Language"
                :items="langs"
                :error-messages="errors.lang"
                item-value="default_locale"
                item-title="english_name"
                placeholder="Select Language"
              />
            </VCol>
            
            <!-- 👉 Title -->
            <VCol
              cols="12"
              md="10"
            >
              <AppTextField
                v-model="form.title"
                label="Title"
                placeholder="Write title"
                :error-messages="errors.title"
              />
            </VCol>

            <!-- Content -->
            <VCol cols="12">
              <VLabel class="mb-2">
                Content
              </VLabel>
              <TiptapEditor
                v-model="form.content"
                model-value=""
                label="Content 1"
                placeholder="Write Content"
                class="border rounded"
              />
            </VCol>

            <VCol
              cols="12"
              class="d-flex gap-4 justify-end"
            >
              <VBtn type="submit">
                Save
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </div>
</template>

<!-- eslint-disable camelcase -->
<script setup>
const form = ref({})
const langs = ref([])
const route = useRoute('users-edit-id')
const router = useRouter()

const parentTags = ref([])
const errors = ref([])
const hasError = ref(false)
const error = ref('')
const successMessage = ref('')
const hasSuccess = ref(false)

const submitForm = async () => {
  const response = await $api(`/admin/tags/${route.params.id}`, {
    method: 'PUT',
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

}

const getEditData = async id => {
  const response = await $api(`/admin/tags/${id}/edit`)
  const { tag, translation } = response.data
  
  const data = {
    name: tag.name,
    in_url: tag.in_url,
    parent_id: tag.parent_id == 0 ? null : tag.parent_id,
    index_page: tag.index_page,
    content1: translation.content1 == '' ? null : translation.content1,
    content2: translation.content2 == '' ? null : translation.content2,
    lang: translation.lang,
    title: translation.title,
  }

  form.value = data
}

const fetchTags = async () => {
  const res = await $api('/admin/fetch/tags')

  parentTags.value = res
}

const fetchLangs = async () => {
  const res = await $api('/admin/fetch/langs')
  
  langs.value = res
}

onMounted(() => {
  fetchTags()
  fetchLangs()
  getEditData(route.params.id)
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

    <VCard title="Edit Tag">
      <template #append>
        <div class="mt-n4 me-n2">
          <VBtn :to="{ name: 'tags' }">
            <VIcon
              variant="tonal"
              icon="tabler-list"
              start
            />
            Tags 
          </VBtn>
        </div>
      </template>
      <VCardText>
        <VForm @submit.prevent="submitForm">
          <VRow>
            <!-- 👉 Parent -->
            <VCol
              cols="12"
              md="2"
            >
              <AppSelect
                v-model="form.lang" 
                label="Languages"
                :items="langs"
                :error-messages="errors.lang"
                item-value="default_locale"
                item-title="english_name"
                placeholder="Select Language"
                required
              />
            </VCol>
            <!-- 👉 Name -->
            <VCol
              cols="12"
              md="5"
            >
              <AppTextField
                v-model="form.name"
                label="Name"
                placeholder="Tag Name"
                :error-messages="errors.name"
              />
            </VCol>

            <!-- 👉 Parent -->
            <VCol
              cols="12"
              md="5"
            >
              <AppSelect
                v-model="form.parent_id" 
                label="Parent Tag"
                :items="parentTags"
                :error-messages="errors.parent_id"
                item-value="id"
                item-title="name"
                placeholder="Select Parent"
                name="parent_id"
                required
              />
            </VCol>

            <!-- 👉 Title -->
            <VCol
              cols="12"
              md="6"
            >
              <AppTextField
                v-model="form.title"
                label="Title"
                placeholder="Write title"
                :error-messages="errors.title"
              />
            </VCol>

            <VCol
              cols="12"
              md="6"
              class="d-flex pt-10"
            >
              <VCheckbox
                v-model="form.in_url"
                :value="form.in_url"
                label="Show in URL"
                class="me-10"
              />
              <VCheckbox 
                v-model="form.index_page"
                :value="form.index_page"
                label="Index Page" 
              />
            </VCol>

            <!-- Content 1 -->
            <VCol cols="12">
              <VLabel class="mb-2">
                Content 1 
              </VLabel>
              <TiptapEditor
                v-model="form.content1"
                :model-value="form.content1"
                label="Content 1"
                placeholder="Write Content"
                class="border rounded"
              />
            </VCol>

            <!-- Content 2 -->
            <VCol cols="12">
              <VLabel class="mb-2">
                Content 2  
              </VLabel>
              <TiptapEditor
                v-model="form.content2"
                :model-value="form.content2"
                placeholder="Write Content"
                class="border rounded"
              />
            </VCol>

            <VCol
              cols="12"
              class="d-flex gap-4 justify-end"
            >
              <VBtn type="submit">
                Update 
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </div>
</template>

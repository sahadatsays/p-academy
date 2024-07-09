<script setup>
const form = ref({})
const langs = ref([])
const route = useRoute('ta-tags-edit-id')
const router = useRouter()

const types = [
  {
    label: 'News',
    name: 'news',
  },
  {
    label: 'Technique',
    name: 'technique',
  }, 
]

const parentTags = ref([])
const errors = ref([])
const hasError = ref(false)
const error = ref('')
const successMessage = ref('')
const hasSuccess = ref(false)

const submitForm = async () => {
  const response = await $api(`/admin/patags/${route.params.id}`, {
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
  const response = await $api(`/admin/patags/${id}/edit`)
  
  form.value = response.data 
}

const fetchTags = async () => {
  const res = await $api('/admin/fetch/patags')

  parentTags.value = res
}


onMounted(() => {
  fetchTags()
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

    <VCard title="Edit PA Tag">
      <template #append>
        <div class="mt-n4 me-n2">
          <VBtn :to="{ name: 'pa-tags' }">
            <VIcon
              variant="tonal"
              icon="tabler-list"
              start
            />
            PA Tags 
          </VBtn>
        </div>
      </template>
      <VCardText>
        <VForm @submit.prevent="submitForm">
          <VRow>
            <!-- 👉 Parent -->
            <VCol
              cols="12"
              md="6"
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
            <!-- 👉 Name -->
            <VCol
              cols="12"
              md="6"
            >
              <AppTextField
                v-model="form.name"
                label="Name"
                placeholder="Tag Name"
                :error-messages="errors.name"
              />
            </VCol>

            <!-- 👉 Title -->
            <!--
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
            -->

            <!-- 👉 type -->
            <VCol
              cols="12"
              md="6"
            >
              <AppSelect
                v-model="form.type" 
                label="Parent Tag"
                :items="types"
                :error-messages="errors.type"
                item-value="name"
                item-title="label"
                placeholder="Select Type"
                required
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

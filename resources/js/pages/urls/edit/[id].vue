<script setup>
const form = ref({})

const urlList = ref([])
const searchLoading = ref(false)
const searchQuery = ref("")

const errors = ref([])
const hasError = ref(false)
const error = ref("")
const successMessage = ref("")
const hasSuccess = ref(false)
const route = useRoute("urls-edit-id")

const submitForm = async () => {
  const response = await $api(`/admin/url-301/${route.params.id}`, {
    method: "PUT",
    body: form.value,
    onResponseError({ response }) {
      errors.value = response._data.data
      error.value = response._data.message
      hasError.value = true
    },
  })

  errors.value = {}
  successMessage.value = response.message
  hasSuccess.value = response.success

//   form.value = {};
}

const fetchUrls = async () => {
  const response = await $api(`/admin/fetch/site/urls?search=${searchQuery.value}`)
  
  urlList.value = response ?? []
}

const getEditData = async () => {
  const response = await $api(`/admin/url-301/${route.params.id}/edit`)

  const formData = {
    alias: response.alias,
    reason: response.reason,
    urlsite_id: response.url_id,
  }

  form.value = formData
}

onMounted(() => {
  fetchUrls()
  getEditData()
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

    <VCard title="Add URL301">
      <template #append>
        <div class="mt-n4 me-n2">
          <VBtn :to="{ name: 'urls' }">
            <VIcon
              variant="tonal"
              icon="tabler-list"
              start
            />
            URls
          </VBtn>
        </div>
      </template>
      <VCardText>
        <VForm @submit.prevent="submitForm">
          <VRow>
            <!-- 👉 Alias -->
            <VCol
              cols="12"
              md="4"
            >
              <AppTextField
                v-model="form.alias"
                placeholder="Alias"
                :error-messages="errors.alias"
                label="Alias"
              />
            </VCol>

            <!-- 👉 URL -->
            <VCol
              cols="12"
              md="4"
            >
              <AppSelect
                v-model="form.urlsite_id"
                v-model:search-input="searchQuery"
                :items="urlList"
                item-title="url"
                item-value="id"
                label="Select URL"
                :loading="searchLoading"
                hide-no-data
                hide-details
                clearable
                :menu-props="{ maxWidth: '300px' }"
                @update:search-input="fetchUrls" 
              >
                <template #prepend-item>
                  <VListItem>
                    <VListItemContent>
                      <VTextField
                        v-model="searchQuery"
                        label="Search"
                        clearable
                        @input="fetchUrls"
                      />
                    </VListItemContent>
                  </VListItem>
                </template>
              </AppSelect>
            </VCol>

            <!-- 👉 Reason -->
            <VCol
              cols="12"
              md="4"
            >
              <AppTextField
                v-model="form.reason"
                :error-messages="errors.reason"
                placeholder="Reason"
                label="Reason"
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

<style scoped></style>

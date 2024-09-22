<script setup>
const form = ref({});

const urlList = ref([]);
const searchLoading = ref(false);
const searchQuery = ref("");

const errors = ref([]);
const hasError = ref(false);
const error = ref("");
const successMessage = ref("");
const hasSuccess = ref(false);

const submitForm = async () => {
  const response = await $api("/admin/url-301", {
    method: "POST",
    body: form.value,
    onResponseError({ response }) {
      errors.value = response._data.data;
      error.value = response._data.message;
      hasError.value = true;
    },
  });

  errors.value = {};
  successMessage.value = response.message;
  hasSuccess.value = response.success;
  form.value = {};
};

const fetchUrls = async () => {
  const response = await $api(`/admin/fetch/site/urls?search=${searchQuery.value}`);
  console.log(response);
  
  urlList.value = response ?? [];
};

onMounted(() => {
    fetchUrls()
});
</script>

<template>
  <div>
    <VSnackbar v-model="hasSuccess" location="top end" color="success">
      <VIcon icon="tabler-exclamation-circle" />
      {{ successMessage }}
    </VSnackbar>

    <VSnackbar v-model="hasError" location="top end" color="error">
      <VIcon icon="tabler-exclamation-circle" />
      {{ error }}
    </VSnackbar>

    <VCard title="Add URL301">
      <template #append>
        <div class="mt-n4 me-n2">
          <VBtn :to="{ name: 'urls' }">
            <VIcon variant="tonal" icon="tabler-list" start />
            URls
          </VBtn>
        </div>
      </template>
      <VCardText>
        <VForm @submit.prevent="submitForm">
          <VRow>
            <!-- 👉 Alias -->
            <VCol cols="12" md="4">
              <AppTextField
                v-model="form.alias"
                placeholder="Alias"
                :error-messages="errors.alias"
                label="Alias"
              />
            </VCol>

            <!-- 👉 URL -->
            <VCol cols="12" md="4">
              <AppSelect
                v-model="form.urlsite_id"
                :items="urlList"
                item-title="url"
                item-value="id"
                label="Select URL"
                :loading="searchLoading"
                hide-no-data
                hide-details
                clearable
                :search-input.sync="searchQuery"
                @update:search-input="fetchUrls"
                :menu-props="{ maxWidth: '300px' }" 
              >
                <template v-slot:prepend-item>
                  <v-list-item>
                    <v-list-item-content>
                      <v-text-field
                        v-model="searchQuery"
                        label="Search"
                        clearable
                        @input="fetchUrls"
                      />
                    </v-list-item-content>
                  </v-list-item>
                </template>
              </AppSelect>
            </VCol>

            <!-- 👉 Reason -->
            <VCol cols="12" md="4">
              <AppTextField
                v-model="form.reason"
                :error-messages="errors.reason"
                placeholder="Reason"
                label="Reason"
              />
            </VCol>

            <VCol cols="12" class="d-flex gap-4 justify-end">
              <VBtn type="submit"> Save </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </div>
</template>

<style scoped></style>

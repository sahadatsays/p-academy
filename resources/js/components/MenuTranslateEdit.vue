<script setup>
const props = defineProps({
  translation: {
    type: Object,
    required: true,
  },
});

const errors = ref([]);
const hasError = ref(false);
const error = ref("");
const successMessage = ref("");
const hasSuccess = ref(false);

const translationForm = ref({
  id: props.translation.id,
  title: props.translation.title,
  content: props.translation.content,
});

const updateTranslation = async (translationId) => {
  const res = await $api(`/admin/menus/translation/${translationId}`, {
    method: 'put',
    body: translationForm.value,
    onResponseError({ response }) {
      errors.value = response._data.data;
      error.value = response._data.message;
      hasError.value = true;
    },
  });

  successMessage.value = res.message;
  hasSuccess.value = res.success;
};
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

    <VForm @submit.prevent="updateTranslation(translationForm.id)">
      <VRow>
        <!-- 👉 Name -->
        <VCol cols="12" md="12">
          <AppTextField
            v-model="translationForm.title"
            label="Title"
            placeholder="Title"
            :error-messages="errors.title"
          />
        </VCol>
      </VRow>
      <VBtn type="submit" class="mt-4">Update</VBtn>
    </VForm>
  </div>
</template>

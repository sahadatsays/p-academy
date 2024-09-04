<script setup>
const form = ref({
  date_debut: new Date(),
  date_fin: new Date(),
})

const types = ref(['Freeroll', 'Freezeout', 'Rebuy'])

const operators = ref([])
const errors = ref([])
const hasError = ref(false)
const error = ref('')
const successMessage = ref('')
const hasSuccess = ref(false)

const submitForm = async () => {
  const response = await $api('/admin/tournaments', {
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

onMounted(() => {
  fetchOperators()
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

    <VCard title="New Tournament">
      <template #append>
        <div class="mt-n4 me-n2">
          <VBtn :to="{ name: 'tournois' }">
            <VIcon
              variant="tonal"
              icon="tabler-list"
              start
            />
            Tournois
          </VBtn>
        </div>
      </template>
      <VCardText>
        <VForm @submit.prevent="submitForm">
          <VRow>
            <!-- 👉 Title -->
            <VCol
              cols="12"
              md="4"
            >
              <AppTextField
                v-model="form.titre"
                placeholder="Title"
                :error-messages="errors.titre"
              />
            </VCol>

            <!-- 👉 Operator -->
            <VCol
              cols="12"
              md="4"
            >
              <AppSelect
                v-model="form.operateur" 
                :items="operators"
                :error-messages="errors.operateur"
                item-value="id"
                item-title="name"
                placeholder="Select Operator"
                name="operateur"
                required
              />
            </VCol>

            <!-- 👉 Type -->
            <VCol
              cols="12"
              md="4"
            >
              <AppSelect 
                v-model="form.typetournoi"
                :items="types"
                :error-messages="errors.typetournoi"
                placeholder="Select Type"
                name="type"
                required
              />
            </VCol>

            <!-- 👉 Buying -->
            <VCol
              cols="12"
              md="6"
            >
              <AppTextField
                v-model="form.buyin"
                :error-messages="errors.buyin"
                placeholder="Buying"
              />
            </VCol>

            <!-- 👉 Password -->
            <VCol
              cols="12"
              md="6"
            >
              <AppTextField
                v-model="form.password"
                :error-messages="errors.password"
                placeholder="******"
              />
            </VCol>

            <!-- 👉 Added OP -->
            <VCol
              cols="12"
              md="6"
            >
              <AppTextField
                v-model="form.added_op"
                :error-messages="errors.added_op"
                placeholder="Added OP"
              />
            </VCol>

            <!-- 👉 Artile ID -->
            <VCol
              cols="12"
              md="6"
            >
              <AppTextField
                v-model="form.article_id"
                :error-messages="errors.article_id"
                placeholder="Article ID"
              />
            </VCol>

            <!-- 👉 Start Date -->
            <VCol
              cols="12"
              md="6"
            >
              <AppDateTimePicker
                v-model="form.date_debut"
                :model-value="form.date_debut"
                placeholder="Start Date"
                :error-messages="errors.date_debut"
                prepend-inner-icon="tabler-calendar"
                :config="{ enableTime: true, dateFormat: 'Y-m-d H:i', minDate: 'today' }"
              />
            </VCol>

            <!-- 👉 End Date -->
            <VCol
              cols="12"
              md="6"
            >
              <AppDateTimePicker
                v-model="form.date_fin"
                :model-value="form.date_fin"
                placeholder="End Date"
                :error-messages="errors.date_fin"
                prepend-inner-icon="tabler-calendar"
                :config="{ enableTime: true, dateFormat: 'Y-m-d H:i', minDate: 'today' }"
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

<script setup>
const form = ref({
  titre: '',
  type: '',
  buying: '',
  password: '',
  addedOp: '',
  articleId: '',
})

const operators = ref([])
const errors = ref([])

const submitForm = async () => {
  const response = await $api('/admin/tournaments', {
    method: 'POST',
    body: form.value,
    onResponseError({ response }) {
      errors.value = response._data.errors
      console.log(errors.value)
    },
  })

  console.log(response)
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
    <VCard>
      <VCardItem>
        <VCardTitle>
          New Tournament
        </VCardTitle>
      </VCardItem>
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
                :items="['item 1', 'item 2']"
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
                v-model="form.buying"
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
                placeholder="******"
              />
            </VCol>

            <!-- 👉 Added OP -->
            <VCol
              cols="12"
              md="6"
            >
              <AppTextField
                v-model="form.addedOp"
                placeholder="Added OP"
              />
            </VCol>

            <!-- 👉 Artile ID -->
            <VCol
              cols="12"
              md="6"
            >
              <AppTextField
                v-model="form.articleId"
                placeholder="Article ID"
              />
            </VCol>

            <!-- 👉 Start Date -->
            <VCol
              cols="12"
              md="6"
            >
              <AppDateTimePicker
                v-model="form.startDate"
                placeholder="Start Date"
                prepend-inner-icon="tabler-calendar"
                :config="{ enableTime: true, dateFormat: 'Y-m-d H:i' }"
              />
            </VCol>

            <!-- 👉 End Date -->
            <VCol
              cols="12"
              md="6"
            >
              <AppDateTimePicker
                v-model="form.endDate"
                placeholder="End Date"
                prepend-inner-icon="tabler-calendar"
                :config="{ enableTime: true, dateFormat: 'Y-m-d H:i' }"
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

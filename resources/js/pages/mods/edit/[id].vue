<!-- eslint-disable camelcase -->
<script setup>
const form = ref({
  status: 0,
  lang: 'fr_FR',
  position: '',
})

const langs = ref([])
const route = useRoute('mods-edit-id')
const router = useRouter()

const errors = ref([])
const hasError = ref(false)
const error = ref('')
const successMessage = ref('')
const hasSuccess = ref(false)

const statusList = [
  {
    label: 'Publish',
    value: 1,
  },
  {
    label: 'Unpublish',
    value: 0,
  }, 
  {
    label: 'Recycle Bin',
    value: 3,
  }, 
]

const positionList = [
  {
    label: 'Position',
    value: '',
  },
  {
    label: 'Hidden',
    value: 'hidden',
  },
  {
    label: 'Hidden',
    value: 'hidden',
  },
  {
    label: 'Sidebar1',
    value: 'sidebar1',
  },
  {
    label: 'Sidebar 2',
    value: 'sidebar2',
  },
  {
    label: 'Content 2',
    value: 'content2',
  },
  {
    label: 'Top Index',
    value: 'topindex',
  },
]

const submitForm = async () => {
  const response = await $api(`/admin/modules/${route.params.id}`, {
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
  const response = await $api(`/admin/modules/${id}/edit`)
  const { module, moduleTranslation } = response.data

  form.value = {
    name: module.name,
    position: module.position,
    order: module.order,
    status: module.status,
    show_title: module.showTtitle,
    title: moduleTranslation.title,
    lang: moduleTranslation.lang,
    content: moduleTranslation.content,
  }
}

const fetchLangs = async () => {
  const res = await $api('/admin/fetch/langs')

  langs.value = res
}


onMounted(() => {
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

    <VCard title="New Module">
      <template #append>
        <div class="mt-n4 me-n2">
          <VBtn :to="{ name: 'mods' }">
            <VIcon
              variant="tonal"
              icon="tabler-list"
              start
            />
            Modules
          </VBtn>
        </div>
      </template>
      <VCardText>
        <VForm @submit.prevent="submitForm">
          <VRow>
            <!-- 👉 Name -->
            <VCol
              cols="12"
              md="6"
            >
              <AppTextField
                v-model="form.name"
                label="Name"
                placeholder="Module Name"
                :error-messages="errors.name"
              />
            </VCol>

            <!-- 👉 Position -->
            <VCol
              cols="12"
              md="6"
            >
              <AppSelect
                v-model="form.position" 
                label="Position"
                :items="positionList"
                :error-messages="errors.position"
                item-value="value"
                item-title="label"
              />
            </VCol>

            <!-- 👉 Order -->
            <VCol
              cols="12"
              md="4"
            >
              <AppTextField
                v-model="form.order"
                label="Order"
                type="number"
                placeholder="Module Order"
                :error-messages="errors.order"
              />
            </VCol>

            <!-- 👉 Status -->
            <VCol
              cols="12"
              md="4"
            >
              <AppSelect
                v-model="form.status" 
                label="Status"
                :items="statusList"
                :error-messages="errors.status"
                item-value="value"
                item-title="label"
              />
            </VCol>
            <!-- 👉 Show title -->
            <VCol
              cols="12"
              md="4"
              class="d-flex pt-10"
            >
              <VCheckbox 
                v-model="form.show_title"
                :model-data="form.show_title"
                label="Show Title" 
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
           

            <!-- 👉 type -->
            <VCol
              cols="12"
              md="6"
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

            <!-- 👉 Content 1 -->
            <VCol cols="12">
              <VLabel class="mb-2">
                Content
              </VLabel>
              <TiptapEditor
                v-model="form.content"
                :model-value="form.content"
                label="Content"
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

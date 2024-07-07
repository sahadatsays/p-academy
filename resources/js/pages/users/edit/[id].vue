<!-- eslint-disable camelcase -->
<script setup>
const form = ref({})

const route = useRoute('users-edit-id')
const router = useRouter()

const types = ref(['Freeroll', 'Freezeout', 'Rebuy'])

const groups = ref([])
const inGroups = ref([])
const errors = ref([])
const hasError = ref(false)
const error = ref('')
const successMessage = ref('')
const hasSuccess = ref(false)

const submitForm = async () => {
  const response = await $api(`/admin/users/${route.params.id}`, {
    method: 'PUT',
    body: { ...form.value, groups: inGroups.value  },
    onResponseError({ response }) {
      errors.value = response._data.errors
      error.value = response._data.message
      hasError.value = true
    },
  })

  errors.value = {}
  successMessage.value = response.message
  hasSuccess.value = response.success
  
//   router.push({ name: 'tournois' })
}

const fetchGroups = async () => {
  const res = await $api('/admin/fetch/groups')

  groups.value = res
}

const getEditData = async id => {
  const response = await $api(`/admin/users/${id}/edit`)

  inGroups.value = response.groupes.map(item => {
    return item.id
  })
  
  const data = (({ groupes, createdAt, lastActivity, activated, activatedAt, lastLogin, name, ...user }) => user)(response)
  
  form.value = data
}

onMounted(() => {
  getEditData(route.params.id)
  fetchGroups()
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

    <VCard title="Edit Users">
      <template #append>
        <div class="mt-n4 me-n2">
          <VBtn :to="{ name: 'users' }">
            <VIcon
              variant="tonal"
              icon="tabler-list"
              start
            />
            Users 
          </VBtn>
        </div>
      </template>
      <VCardText>
        <VForm @submit.prevent="submitForm">
          <VRow>
            <!-- 👉 Username -->
            <VCol
              cols="12"
              md="4"
            >
              <AppTextField
                v-model="form.username"
                label="Username"
                placeholder="Username"
                :error-messages="errors.username"
              />
            </VCol>

            <!-- 👉 First Name -->
            <VCol
              cols="12"
              md="4"
            >
              <AppTextField
                v-model="form.firstName"
                placeholder="First Name"
                label="First Name"
                :error-messages="errors.firstName"
              />
            </VCol>

            <!-- 👉 Last Name -->
            <VCol
              cols="12"
              md="4"
            >
              <AppTextField
                v-model="form.lastName"
                label="Last Name"
                placeholder="Last Name"
                :error-messages="errors.lastName"
              />
            </VCol>

            <!-- 👉 Email -->
            <VCol
              cols="12"
              md="4"
            >
              <AppTextField
                v-model="form.email"
                label="Email"
                placeholder="Email"
                :error-messages="errors.email"
              />
            </VCol>

            <!-- 👉 Password -->
            <VCol
              cols="12"
              md="4"
            >
              <AppTextField
                v-model="form.password"
                label="Password"
                type="password"
                placeholder="Password"
                :error-messages="errors.password"
              />
            </VCol>

            <!-- 👉 Confirm Password -->
            <VCol
              cols="12"
              md="4"
            >
              <AppTextField
                v-model="form.confirm_password"
                label="Confirm Password"
                type="password"
                placeholder="Confirm Password"
                :error-messages="errors.confirm_password"
              />
            </VCol>

            <!-- 👉 Confirm Password -->
            <VCol 
              cols="12"
              class="d-flex"
            >
              <VLabel>
                Groups : 
              </VLabel>
              
              <VCheckbox
                v-for="group in groups"
                :key="group.id"
                v-model="inGroups" 
                checked
                :label="group.name"
                :value="group.id"
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

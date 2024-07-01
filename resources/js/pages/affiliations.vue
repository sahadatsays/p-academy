<script setup>
const options = ref({
  id: '',
  username: '',
  name: '',
  email: '',
  activated: '',
})

const affiliations = ref([])
const totalAffiliations = ref(0)
const loading = ref(false)
const perPage = ref(0)
const search = ref('')

// headers
const headers = [
  {
    title: 'Room',
    key: 'room',
  },
  {
    title: 'User ID',
    key: 'userid',
  },
  {
    title: 'Username',
    key: 'username',
  },
  {
    title: 'Pseudo Room',
    key: 'pseudo_room',
  },
  {
    title: 'Status',
    key: 'statut',
  },
  {
    title: 'Date',
    key: 'user.createdAt',
  },
]

const fetchAffiliations = async () => {
  loading.value = true

  const response = await $api('/admin/affiliations', {
    query: options.value,
    onResponseError({ response }) {
      console.log(response)
    },
  })

  // assign Response
  affiliations.value = response.data 
  totalAffiliations.value = response.meta.total
  perPage.value = response.meta.per_page
  loading.value = false
}

watch(options, fetchAffiliations, { deep: true })
</script>

<template>
  <div>
    <VCard title="Affiliations">
      <VCardText>
        <VRow>
          <VCol
            cols="12"
            offset-md="8"
            md="4"
          >
            <AppTextField
              v-model="search"
              density="compact"
              placeholder="Search ..."
              append-inner-icon="tabler-search"
              single-line
              hide-details
              dense
              outlined
            /> 
          </VCol>
        </VRow>
      </VCardText>

      <!-- 👉 Data Table  -->
      <VDataTableServer
        v-model:options="options"
        :search="search"
        :items-per-page="perPage"
        :headers="headers"
        :items="affiliations"
        loading-text="Loading... Please wait"
        :loading="loading"
        :items-length="totalAffiliations"
        class="text-no-wrap"
      >
        <template #body.prepend>
          <tr>
            <td>
              <AppTextField
                v-model="options.room"
                density="compact"
                append-inner-icon="tabler-search"
                single-line
                hide-details
                dense
                outlined
                style="width: 5rem;"
              /> 
            </td>
            <td>
              <AppTextField 
                v-model="options.userid"
                append-inner-icon="tabler-search"
                density="compact"
                single-line
                hide-details
                dense
                outlined
              />
            </td>
            <td>
              <AppTextField 
                v-model="options.username"
                density="compact"
                append-inner-icon="tabler-search"
                single-line
                hide-details
                dense
                outlined
              />
            </td>
            <td>
              <AppTextField 
                v-model="options.pseudo_room"
                density="compact"
                append-inner-icon="tabler-search"
                single-line
                hide-details
                dense
                outlined
              />
            </td>
            <td>
              <AppTextField 
                v-model="options.status"
                density="compact"
                append-inner-icon="tabler-search"
                single-line
                hide-details
                dense
                outlined
              />
            </td>
            <td />
          </tr>
        </template>
        <template #item.userid="{ item }">
          <div class="d-flex align-center">
            <span>{{ item.user.id }}</span>
          </div>
        </template>

        <!-- username -->
        <template #item.username="{ item }">
          <div class="d-flex align-center">
            <span>{{ item.user.username }}</span>
          </div>
        </template>
      </VDataTableServer>
    </VCard>
  </div>
</template>

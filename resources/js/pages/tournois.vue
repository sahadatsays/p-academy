<script setup>
const options = ref({})

const tournamentsList = ref([])
const totalTournaments = ref(0)
const loading = ref(false)
const perPage = ref(0)
const search = ref('')

// headers
const headers = [
  {
    title: 'ID',
    key: 'id',
  },
  {
    title: 'Title',
    key: 'title',
  },
  {
    title: 'Operator',
    key: 'operator.name',
  },
  {
    title: 'Since',
    key: 'since',
  },
  {
    title: 'Tournament Type',
    key: 'type_tournament',
  },
  {
    title: 'Password',
    key: 'password',
  },
  {
    title: 'Added OP',
    key: 'added_op',
  },
  {
    title: 'Start Date',
    key: 'start_date',
  },
  {
    title: 'End Date',
    key: 'end_date',
  },
  {
    title: 'Article ID',
    key: 'article_id',
  },
  {
    title: 'Actions',
    key: 'actions',
    sortable: false,
  },
]

const fetchTournaments = async () => {
  loading.value = true

  const response = await $api('/admin/tournaments', {
    query: options.value,
    onResponseError({ response }) {
      console.log(response)
    },
  })

  // assign Response
  tournamentsList.value = response.data 
  totalTournaments.value = response.meta.total
  perPage.value = response.meta.per_page
  loading.value = false
}

watch(options, fetchTournaments, { deep: true })
</script>

<template>
  <div>
    <VCard title="Tournois">
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
        :items="tournamentsList"
        loading-text="Loading... Please wait"
        :loading="loading"
        :items-length="totalTournaments"
        class="text-no-wrap"
      >
        <template #body.prepend>
          <tr>
            <td>
              <AppTextField
                v-model="options.id"
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
                v-model="options.title"
                append-inner-icon="tabler-search"
                density="compact"
                single-line
                hide-details
                dense
                outlined
              />
            </td>
            <td colspan="9" />
          </tr>
        </template>
        
        <!-- actions -->
        <template #item.actions="{ item }">
          <div class="d-flex align-center">
            <IconBtn>
              <VIcon icon="tabler-pencil" />
            </IconBtn>
          </div>
        </template>
      </VDataTableServer>
    </VCard>
  </div>
</template>

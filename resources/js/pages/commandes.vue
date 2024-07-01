<script setup>
const options = ref({
  id: '',
  username: '',
  name: '',
  email: '',
  activated: '',
})

const affiliations = ref([])
const totalMembers = ref(0)
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
    title: 'Username',
    key: 'user.username',
  },
  {
    title: 'nom',
    key: 'user.name',
  },
  {
    title: 'Email',
    key: 'user.email',
  },
  {
    title: 'PPA',
    key: 'ppa',
  },
  {
    title: 'Groupe',
    key: 'user.group',
    sortable: false,
  },
  {
    title: 'activé',
    key: 'activated',
  },
  {
    title: 'crée le',
    key: 'user.createdAt',
  },
  {
    title: 'actif le',
    key: 'user.activatedAt',
  },
  {
    title: '#',
    key: 'actions',
    sortable: false,
  },
]

const fetchMembers = async () => {
  loading.value = true

  const response = await $api('/admin/members', {
    query: options.value,
    onResponseError({ response }) {
      console.log(response)
    },
  })

  // assign Response
  memberList.value = response.data 
  totalMembers.value = response.meta.total
  perPage.value = response.meta.per_page
  loading.value = false
}

watch(options, fetchMembers, { deep: true })
</script>

<template>
  <div>
    <VCard title="Commandes">
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
        :items="memberList"
        loading-text="Loading... Please wait"
        :loading="loading"
        :items-length="totalMembers"
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
                v-model="options.username"
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
                v-model="options.name"
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
                v-model="options.email"
                density="compact"
                append-inner-icon="tabler-search"
                single-line
                hide-details
                dense
                outlined
              />
            </td>
            <td colspan="2" />
            <td>
              <AppSelect
                v-model="options.activated"
                :items="['None', 'Active', 'Inactive']"
                density="compact"
                placeholder="Select"
                item-title="title"
                item-value="value"
                style="width: 5rem;"
              />
            </td>
            <td colspan="3" />
          </tr>
        </template>
        <template #item.user.group="{ item }">
          <div class="d-flex align-center">
            <div 
              v-for="group in item.user.groupes" 
              :key="group.id" 
              class="d-flex flex-column ms-3"
            >
              <VChip label>
                {{ group.name }}
              </VChip>
            </div>
          </div>
        </template>
        <!-- activated -->
        <template #item.activated="{ item }">
          <div class="d-flex align-center">
            <VChip v-if="item.user.activated">
              <VIcon icon="tabler-circle-check text-2xl" />
            </VChip>
          </div>
        </template>
        <!-- actions -->
        <template #item.actions="{ item }">
          <div class="d-flex align-center">
            <IconBtn>
              <VIcon icon="tabler-external-link" />
            </IconBtn>
            <IconBtn>
              <VIcon icon="tabler-pencil" />
            </IconBtn>
            <IconBtn>
              <VIcon icon="tabler-user-filled" />
            </IconBtn>
          </div>
        </template>
      </VDataTableServer>
    </VCard>
  </div>
</template>

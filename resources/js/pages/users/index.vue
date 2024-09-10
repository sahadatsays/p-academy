<script setup>
const options = ref({})

const dataList = ref([])
const totalData = ref(0)
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
    key: 'username',
  },
  {
    title: 'Name',
    key: 'name',
  },
  {
    title: 'First Name',
    key: 'firstName',
  },
  {
    title: 'Email',
    key: 'email',
  },
  {
    title: 'Groups',
    key: 'groupes',
    sortable: false,
    maxWidth: '100px',
  },
  {
    title: 'activé',
    key: 'activated',
  },
  {
    title: 'Created At',
    key: 'createdAt',
  },
  {
    title: 'Last Login',
    key: 'lastLogin',
  },
  {
    title: '#',
    key: 'actions',
    sortable: false,
  },
]

const fetchData = async () => {
  loading.value = true

  const response = await $api('/admin/users', {
    query: options.value,
    onResponseError({ response }) {
      console.log(response)
    },
  })

  // assign Response
  dataList.value = response.data 
  totalData.value = response.meta.total
  perPage.value = response.meta.per_page
  loading.value = false
}

watch(options, fetchData, { deep: true })
</script>

<template>
  <div>
    <VCard title="Users">
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
        :items="dataList"
        loading-text="Loading... Please wait"
        :loading="loading"
        :items-length="totalData"
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
                v-model="options.first_name"
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
            <td>
              <AppTextField 
                v-model="options.group"
                density="compact"
                append-inner-icon="tabler-search"
                single-line
                hide-details
                dense
                outlined
              />
            </td>
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
        <template #item.groupes="{ item }">
          <div class="d-flex align-center">
            <div 
              v-for="group in item.groupes" 
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
            <VChip v-if="item.activated">
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
            <IconBtn :to="{ name: 'users-edit-id', params: { id: item.id } }">
              <VIcon icon="tabler-pencil" />
            </IconBtn>
          </div>
        </template>
      </VDataTableServer>
    </VCard>
  </div>
</template>

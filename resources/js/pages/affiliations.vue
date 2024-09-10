<script setup>
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'


const options = ref({})
const toast = useToast()
const affiliations = ref([])
const totalAffiliations = ref(0)
const loading = ref(false)
const perPage = ref(0)
const search = ref("")

const editableStatus = {
  "Demande reçue": "blue",
  "Demande en cours": "blue",
  "Compte Affilié": "green",
  "Compte non-affilié": "gray",
  "Compte affiliable (Unibet)": "blue",
  "Compte introuvable": "red",
  Anomalie: "red",
}

const router = useRouter()

const statusList = [
  {
    value: "Demande reçue",
    text: "Demande reçue",
  },
  {
    value: "Demande en cours",
    text: "Demande en cours",
  },
  {
    value: "Compte Affilié",
    text: "Compte Affilié",
  },
  {
    value: "Compte non-affilié",
    text: "Compte non-affilié",
  },
  {
    value: "Compte affiliable (Unibet)",
    text: "Compte affiliable (Unibet)",
  },
  {
    value: "Compte introuvable",
    text: "Compte introuvable",
  },
  {
    value: "Anomalie",
    text: "Anomalie",
  },
]

// headers
const headers = [
  {
    title: "Room",
    key: "room",
  },
  {
    title: "User ID",
    key: "userid",
  },
  {
    title: "Username",
    key: "username",
  },
  {
    title: "Pseudo Room",
    key: "pseudo_room",
  },
  {
    title: "Status",
    key: "statut",
  },
  {
    title: "Date",
    key: "user.createdAt",
  },
]

const fetchAffiliations = async () => {
  loading.value = true

  const response = await $api("/admin/affiliations", {
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

const isVisible = ref(false)
const affialiteId = ref("")
const affStatus = ref("")

const editDialog = (id, status) => {
  affialiteId.value = id
  affStatus.value = status
  isVisible.value = !isVisible.value
}

const statusUpdate = async () => {
  const res = await $api('/admin/affiliations/update-status', {
    method: 'POST',
    body: { idaffil: affialiteId.value, astatut: affStatus.value },
    onResponseError({ response }) {
      console.log(response)
    },
  })

  toast.success('Status has been updated.')
  isVisible.value = !isVisible.value
  fetchAffiliations()
}
</script>

<template>
  <template>
    <VDialog
      v-model="isVisible"
      width="500"
    >
      <!-- Dialog close btn -->
      <DialogCloseBtn @click="isVisible = !isVisible" />

      <!-- Dialog Content -->
      <VCard>
        <VCardText>
          <AppSelect
            v-model="affStatus"
            :items="statusList"
            item-title="text"
            item-value="value"
            placeholder="Select Status"
          />
        </VCardText>

        <VCardText class="d-flex justify-end">
          <VBtn @click="statusUpdate">
            Update
          </VBtn>
        </VCardText>
      </VCard>
    </VDialog>
  </template>

  <div>
    <VCard title="Affiliations">
      <VCardText>
        <VRow>
          <VCol
            cols="12"
            md="6"
          >
            <VBtn>Importer PPA</VBtn>
            <VBtn class="ml-3">
              Importer PPA
            </VBtn>
          </VCol>

          <VCol
            cols="12"
            offset-md="2"
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
                style="width: 5rem"
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

        <!-- status -->
        <template #item.statut="{ item }">
          <div class="d-flex align-center">
            <VBtn
              variant="text"
              class="border-b"
              @click="editDialog(item.id, item.statut)"
            >
              {{ item.statut }}
            </VBtn>
          </div>
        </template>
      </VDataTableServer>
    </VCard>
  </div>
</template>

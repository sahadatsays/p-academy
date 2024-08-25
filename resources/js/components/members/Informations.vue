<script setup>
import AppDateTimePicker from '@/@core/components/app-form-elements/AppDateTimePicker.vue';

const props = defineProps({
  member: {
    type: Object,
    required: true,
  },
  user: {
    type: Object,
    required: true,
  },
  status: {
    type: Object,
    required: true,
  },
});

const successMessage = ref('')
const hasSuccess = ref(false)
const isShow = ref(false)

const memberStatusForm = ref({
    level: '',
    ending_at: '',
    motifchange: ''
})

const levels = [
    {
        value: 'membre',
        title: 'Member'
    },
    {
        value: 'silver',
        title: 'Silver'
    },
    {
        value: 'gold',
        title: 'Gold'
    }
]

const statusUpdate = async (userId) => {
    const response = await $api(`/admin/members/status/${userId}`, {
        method: 'PUT',
        body: memberStatusForm.value
    });

    successMessage.value = response.message
    hasSuccess.value = response.success
}

watchEffect(() => {
    memberStatusForm.value.level = props.status.level 
    memberStatusForm.value.ending_at = props.status.ending_at 
    memberStatusForm.value.motifchange = props.status.motifchange
})


</script>

<template>
    <VSnackbar
      v-model="hasSuccess"
      location="top end"
      color="success"
    >
      <VIcon icon="tabler-exclamation-circle" />
      {{ successMessage }}
    </VSnackbar>

  <v-table class="border" >
    <thead>
      <tr>
        <th>Identifier</th>
        <th>Nom</th>
        <th>Email</th>
        <th>Solde PPA</th>
        <th>Solde Punibet</th>
        <th>Solde Pevbc</th>
        <th>Inscrit le</th>
        <th>Actif le</th>
      </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ user.username }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ member.ppa }}</td>
            <td>{{ member.punibet }}</td>
            <td>{{ member.pevbc }}</td>
            <td>{{ user.createdAt }}</td>
            <td>{{ user.lastActivity }}</td>
        </tr>
    </tbody>
  </v-table>
  <v-divider class="my-4"/>
  <p>Statut du membre</p>
  <v-table class="border">
    <thead>
      <tr>
        <th>Level</th>
        <th>Ending At</th>
        <th>Period</th>
        <th>#</th>
      </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ status.level }}</td>
            <td>{{ status.ending_at }}</td>
            <td>{{ user.plan }}</td>
            <td>
                <v-btn size="small" @click="() => isShow = !isShow">
                    Modifier
                </v-btn>
            </td>
        </tr>
        <tr v-if="isShow">
            <td>
                <VSelect 
                :items="levels" 
                item-value="value"
                item-title="title"
                v-model="memberStatusForm.level"
                 />
            </td>
            <td>
            <AppDateTimePicker
              class="calendar-date-picker"
              v-model="memberStatusForm.ending_at"
            />    
            </td>
            <td>
                <v-text-field 
                v-model="memberStatusForm.motifchange"
                />  
            </td>
            <td>
                <v-btn size="small" @click="statusUpdate(user.id)">
                    Enregistrer
                </v-btn>
            </td>
        </tr>
    </tbody>
  </v-table>
</template>

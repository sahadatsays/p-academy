<!-- eslint-disable camelcase -->
<script setup>
import MenuTranslateEdit from '../../../components/MenuTranslateEdit.vue';
const form = ref({
  status: 0,
  lang: "fr_FR",
  order: 0,
  parent_id: null,
  url_externe: null,
  target_blank: false,
  obfuscate: false,
  urlsite_id: null,
  order: 0,
});

const langs = ref([]);
const menuTranslations = ref([]);

const parentUrlsList = ref([]);
const route = useRoute("menus-edit-id");
const currentTab = ref("infos");

const parentMenus = ref([]);
const errors = ref([]);
const hasError = ref(false);
const error = ref("");
const successMessage = ref("");
const hasSuccess = ref(false);

const statusList = [
  {
    label: "Publish",
    value: 1,
  },
  {
    label: "Unpublish",
    value: 0,
  },
  {
    label: "Recycle Bin",
    value: 3,
  },
];

const submitForm = async () => {
  const response = await $api(`/admin/menus/${route.params.id}`, {
    method: "PUT",
    body: form.value,
    onResponseError({ response }) {
      errors.value = response._data.errors;
      error.value = response._data.message;
      hasError.value = true;
    },
  });

  errors.value = {};
  successMessage.value = response.message;
  hasSuccess.value = response.success;
};

const fetchLangs = async () => {
  const res = await $api("/admin/fetch/langs");

  langs.value = res;
};

const fetchMenus = async () => {
  const res = await $api("/admin/fetch/menus");

  parentMenus.value = res;
};

const fetchUrls = async () => {
  const res = await $api("/admin/fetch/urls");

  parentUrlsList.value = res.data;
};

const getEditData = async (id) => {
  const response = await $api(`/admin/menus/${id}/edit`);
  const { menu, translations } = response.data;

  const data = {
    name: menu.name,
    parent_id: menu.parent.id,
    url_externe: menu.url_externe,
    order: menu.order,
    status: menu.status,
    status: menu.status,
    target_blank: menu.targetBlank,
    url_externe: menu.url,
    obfuscate: menu.obfuscate,
  };
  menuTranslations.value = translations;
  form.value = data;
};

onMounted(() => {
  fetchLangs();
  fetchMenus();
  getEditData(route.params.id);

  // fetchUrls()
});
</script>

<template>
  <div>
    <VSnackbar v-model="hasSuccess" location="top end" color="success">
      <VIcon icon="tabler-exclamation-circle" />
      {{ successMessage }}
    </VSnackbar>

    <VSnackbar v-model="hasError" location="top end" color="error">
      <VIcon icon="tabler-exclamation-circle" />
      {{ error }}
    </VSnackbar>

    <VCard title="Edit Menu">
      <template #append>
        <div class="mt-n4 me-n2">
          <VBtn :to="{ name: 'menus' }">
            <VIcon variant="tonal" icon="tabler-list" start />
            Menus
          </VBtn>
        </div>
      </template>
      <VTabs v-model="currentTab">
        <VTab>Information</VTab>
        <VTab v-for="translation in menuTranslations">
          {{ translation.lang }}
        </VTab>
      </VTabs>

      <VCardText>
        <VWindow v-model="currentTab">
          <VWindowItem value="infos">
            <VForm @submit.prevent="submitForm">
              <VRow>
                <!-- 👉 Name -->
                <VCol cols="12" md="4">
                  <AppTextField
                    v-model="form.name"
                    label="Name"
                    placeholder="Menu Name"
                    :error-messages="errors.name"
                  />
                </VCol>

                <!-- 👉 URL -->
                <VCol cols="12" md="4">
                  <AppAutocomplete
                    v-model="form.parent_id"
                    label="Parent"
                    :items="parentMenus"
                    :error-messages="errors.parent_id"
                    item-value="id"
                    item-title="name"
                    placeholder="Select parent menu"
                  />
                </VCol>

                <!-- 👉 Url Externe -->
                <VCol cols="12" md="4">
                  <AppTextField
                    v-model="form.url_externe"
                    label="Url Externe"
                    placeholder="Url Externe"
                    :error-messages="errors.url_externe"
                  />
                </VCol>

                <!-- 👉 Order -->
                <VCol cols="12" md="2">
                  <AppTextField
                    v-model="form.order"
                    label="Order"
                    type="number"
                    placeholder="Module Order"
                    :error-messages="errors.order"
                  />
                </VCol>

                <!-- 👉 Status -->
                <VCol cols="12" md="2">
                  <AppSelect
                    v-model="form.status"
                    label="Status"
                    :items="statusList"
                    :error-messages="errors.status"
                    item-value="value"
                    item-title="label"
                  />
                </VCol>
                <!-- 👉 Target blank -->
                <VCol cols="12" md="2" class="d-flex pt-10">
                  <VCheckbox v-model="form.target_blank" label="Target blank" />
                </VCol>

                <!-- 👉 Obfuscate -->
                <VCol cols="12" md="2" class="d-flex pt-10">
                  <VCheckbox v-model="form.obfuscate" label="Obfuscate" />
                </VCol>

                <VCol cols="12" class="d-flex gap-4 justify-end">
                  <VBtn type="submit"> Update </VBtn>
                </VCol>
              </VRow>
            </VForm>
          </VWindowItem>
          <VWindowItem
            v-for="(translation, index) in menuTranslations"
            :value="translation.lang"
          >
            <MenuTranslateEdit :translation="translation" :key="index"  />
          </VWindowItem>
        </VWindow>
      </VCardText>
    </VCard>
  </div>
</template>

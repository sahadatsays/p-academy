<!-- eslint-disable camelcase -->
<script setup>
import { reactive } from "vue";
import TranslationEdit from "../../../components/TranslationEdit.vue";
import DisplayRulesRow from "@/components/DisplayRulesRow.vue";

const form = ref({
  status: 0,
  lang: "fr_FR",
  position: "",
  rules: "",
});

const rulesRow = reactive([
  {
    display: 0,
    type: "",
    ids: [],
    withChild: false,
  },
]);

const langs = ref([]);
const route = useRoute("mods-edit-id");
const router = useRouter();

const errors = ref([]);
const hasError = ref(false);
const error = ref("");
const successMessage = ref("");
const hasSuccess = ref(false);
const moduleTranslationList = ref([]);
const currentTab = ref("properties");

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

const ruleList = [
  { title: "Public", value: "" },
  { title: "Admin", value: "admin" },
  { title: "Member", value: "member" },
];

const positionList = [
  {
    label: "Position",
    value: "",
  },
  {
    label: "Hidden",
    value: "hidden",
  },
  {
    label: "Hidden",
    value: "hidden",
  },
  {
    label: "Sidebar1",
    value: "sidebar1",
  },
  {
    label: "Sidebar 2",
    value: "sidebar2",
  },
  {
    label: "Content 2",
    value: "content2",
  },
  {
    label: "Top Index",
    value: "topindex",
  },
];

const submitForm = async () => {
  const response = await $api(`/admin/modules/${route.params.id}`, {
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

const getEditData = async (id) => {
  const response = await $api(`/admin/modules/${id}/edit`);
  const { module, moduleTranslations } = response.data;

  form.value = {
    name: module.name,
    position: module.position,
    order: module.order,
    status: module.status,
    show_title: module.showTtitle,
    rules: module.rules,
  };

  moduleTranslationList.value = moduleTranslations;
};

const fetchLangs = async () => {
  const res = await $api("/admin/fetch/langs");

  langs.value = res;
};

onMounted(() => {
  fetchLangs();
  getEditData(route.params.id);
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

    <VCard title="Edit Module">
      <template #append>
        <div class="mt-n4 me-n2">
          <VBtn :to="{ name: 'mods' }">
            <VIcon variant="tonal" icon="tabler-list" start />
            Modules
          </VBtn>
        </div>
      </template>
      <VTabs v-model="currentTab">
        <VTab>Properties</VTab>
        <VTab v-for="translation in moduleTranslationList">
          {{ translation.lang }}
        </VTab>
      </VTabs>

      <VCardText>
        <VWindow v-model="currentTab">
          <VWindowItem value="properties">
            <VForm @submit.prevent="submitForm">
              <VRow class="mb-4">
                <!-- 👉 Name -->
                <VCol cols="12" md="6">
                  <AppTextField
                    v-model="form.name"
                    label="Name"
                    placeholder="Module Name"
                    :error-messages="errors.name"
                  />
                </VCol>

                <!-- 👉 Position -->
                <VCol cols="12" md="3">
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
                <VCol cols="12" md="3">
                  <AppTextField
                    v-model="form.order"
                    label="Order"
                    type="number"
                    placeholder="Module Order"
                    :error-messages="errors.order"
                  />
                </VCol>

                <!-- 👉 Status -->
                <VCol cols="12" md="5">
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
                <VCol cols="12" md="3" class="d-flex pt-10">
                  <VCheckbox
                    v-model="form.show_title"
                    :model-data="form.show_title"
                    label="Show Title"
                  />
                </VCol>

                <!-- 👉 Status -->
                <VCol cols="12" md="4">
                  <AppSelect
                    v-model="form.rules"
                    label="Rules"
                    :items="ruleList"
                    :error-messages="errors.rules"
                    item-value="value"
                    item-title="title"
                  />
                </VCol>
              </VRow>
              <v-divider class="my-4" />
              <VRow>
                <VCol cols="12" md="4" class="flex">
                  <v-select
                    :model-value="1"
                    :items="[
                      { label: 'Show', value: 1 },
                      { label: 'Hide', value: 0 },
                    ]"
                    item-title="label"
                    item-value="value"
                  ></v-select>
                </VCol>
                <VCol cols="12" md="6">
                  <span>On all pages</span>
                </VCol>
              </VRow>
              <v-divider class="my-3" />
              <DisplayRulesRow v-for="(row, index) in rulesRow" :key="index" :form-row="row" />
              <VBtn type="submit" variant="flat" class="mt-5"> Update </VBtn>
            </VForm>
          </VWindowItem>
          <VWindowItem
            v-for="(translation, index) in moduleTranslationList"
            :value="translation.lang"
          >
            <TranslationEdit :translation="translation" :key="index" />
          </VWindowItem>
        </VWindow>
      </VCardText>
    </VCard>
  </div>
</template>

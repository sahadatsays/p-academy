<script setup>
import  UrlSites  from '@/views/pages/urls/urlSites.vue'
import  Alias301  from '@/views/pages/urls/Alias301.vue'
import  Errors404  from '@/views/pages/urls/Errors404.vue'

const currentTab = ref("url-sites");
</script>

<template>
  <div>
    <VCard title="URL Sites">
      <VTabs v-model="currentTab">
        <VTab>URL Sites</VTab>
        <VTab>Alias 301</VTab>
        <VTab>Erreurs 404</VTab>
      </VTabs>

      <VCardText>
        <VWindow v-model="currentTab">
            <VWindowItem value="url-sites">
               <UrlSites />
            </VWindowItem>
            <VWindowItem value="alias-301">
                <Alias301 />
            </VWindowItem>
            <VWindowItem value="errors-404">
                <Errors404 />
            </VWindowItem>
        </VWindow>
      </VCardText>
    </VCard>
  </div>
</template>

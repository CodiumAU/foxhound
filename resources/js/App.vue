<template>
  <AppHeader />
  <AppSidebar />

  <div class="w-full h-full min-h-full lg:ps-64 flex flex-1">
    <PageSidebar />

    <div class="pt-10 px-4 sm:px-6 md:px-8 grow">
      <RouterView />
    </div>
  </div>
</template>

<script lang="ts" setup>
import AppHeader from './components/AppHeader.vue'
import AppSidebar from './components/AppSidebar.vue'
import PageSidebar from './components/PageSidebar.vue'
</script>

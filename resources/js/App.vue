<template>
  <AppHeader />

  <Suspense>
    <AppSidebar />
  </Suspense>

  <div class="w-full h-full min-h-full lg:ps-64 flex flex-1">
    <Suspense>
      <RouterView />
    </Suspense>
  </div>
</template>

<script lang="ts" setup>
import { useRoute } from 'vue-router'
import AppHeader from './components/AppHeader.vue'
import AppSidebar from './components/AppSidebar.vue'
import { watch } from 'vue'
import { HSDropdown, HSOverlay } from 'preline'

const route = useRoute()

watch(route, () => {
  HSOverlay.autoInit()
  HSDropdown.autoInit()
})
</script>

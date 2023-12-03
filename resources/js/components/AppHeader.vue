<template>
  <header
    class="sticky top-0 inset-x-0 flex flex-wrap sm:justify-start sm:flex-nowrap z-[48] w-full bg-white border-b text-sm py-2.5 sm:py-4 lg:ps-64 dark:bg-gray-800 dark:border-gray-700"
  >
    <nav
      class="flex basis-full items-center w-full mx-auto px-4 md:px-6"
      aria-label="Global"
    >
      <div class="me-5 lg:me-0 lg:hidden flex items-center gap-2">
        <AppHeaderButton
          data-hs-overlay="#application-sidebar"
          aria-controls="application-sidebar"
          aria-label="Toggle navigation"
        >
          <span class="sr-only">Toggle Navigation</span>
          <Bars3Icon class="h-5 w-5 flex-shrink-0" />
        </AppHeaderButton>

        <a
          class="flex-none text-2xl font-serif font-semibold dark:text-white"
          href="#"
          aria-label="Brand"
        >
          foxhound
        </a>
      </div>

      <div
        class="w-full flex items-center justify-end ms-auto sm:justify-between sm:gap-x-3 sm:order-3"
      >
        <div class="ms-auto flex flex-row items-center justify-end gap-2">
          <AppHeaderModeToggle />
        </div>
      </div>
    </nav>
  </header>
</template>

<script lang="ts" setup>
import { Bars3Icon } from '@heroicons/vue/20/solid'
import AppHeaderModeToggle from './AppHeaderModeToggle.vue'
import AppHeaderButton from './AppHeaderButton.vue'
</script>

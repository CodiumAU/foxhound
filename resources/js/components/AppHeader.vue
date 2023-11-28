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
          class="flex-none text-xl font-semibold dark:text-white"
          href="#"
          aria-label="Brand"
        >
          Interceptor
        </a>
      </div>

      <div
        class="w-full flex items-center justify-end ms-auto sm:justify-between sm:gap-x-3 sm:order-3"
      >
        <div class="sm:hidden">
          <AppHeaderButton>
            <MagnifyingGlassIcon class="flex-shrink-0 h-5 w-5" />
          </AppHeaderButton>
        </div>

        <div class="hidden sm:block">
          <label for="icon" class="sr-only">Search</label>
          <div class="relative">
            <div
              class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-4"
            >
              <MagnifyingGlassIcon
                class="flex-shrink-0 h-5 w-5 text-gray-400"
              />
            </div>
            <input
              id="icon"
              type="text"
              name="icon"
              class="py-2 px-4 ps-11 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600"
              placeholder="Search"
            />
          </div>
        </div>

        <div class="flex flex-row items-center justify-end gap-2">
          <AppHeaderModeToggle />
        </div>
      </div>
    </nav>
  </header>
</template>

<script lang="ts" setup>
import { MagnifyingGlassIcon, Bars3Icon } from '@heroicons/vue/20/solid'
import AppHeaderModeToggle from './AppHeaderModeToggle.vue'
import AppHeaderButton from './AppHeaderButton.vue'
</script>

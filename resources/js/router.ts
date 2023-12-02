import { createRouter, createWebHistory } from 'vue-router'
import ChannelsSingle from './pages/ChannelsSingle.vue'

export const router = createRouter({
  history: createWebHistory('foxhound'),
  routes: [
    { path: '/', redirect: '/channels/mail' },
    {
      path: '/channels/:channel/:uuid?',
      name: 'channels.single',
      props: true,
      component: ChannelsSingle,
    },
  ],
})
